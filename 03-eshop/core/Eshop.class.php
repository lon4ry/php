<?php
class Eshop{
    private static PDO $db;

    static function init(array $db): void{
        $dsn = "mysql:host={$db['HOST']};dbname={$db['NAME']};charset=utf8mb4";
        self::$db = new PDO($dsn, $db['USER'], $db['PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    /* Вызов хранимой процедуры. Возвращает строки результата, если они есть */
    private static function call(string $procedure, array $params = []): array{
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $stmt = self::$db->prepare("CALL $procedure($placeholders)");
        $stmt->execute($params);
        $rows = $stmt->columnCount() ? $stmt->fetchAll() : [];
        $stmt->closeCursor();
        return $rows;
    }

    private static function toBook(array $row): Book{
        $book = new Book($row['title'], $row['author'], (float)$row['price'], (int)$row['pubyear']);
        $book->id = (int)$row['id'];
        if (isset($row['quantity'])) {
            $book->quantity = (int)$row['quantity'];
        }
        return $book;
    }

    /* Каталог */

    static function addItemToCatalog(Book $book): bool{
        self::call('spAddItemToCatalog', [$book->title, $book->author, $book->price, $book->pubyear]);
        return true;
    }

    static function getItemsFromCatalog(): IteratorIterator{
        $books = array_map([self::class, 'toBook'], self::call('spGetCatalog'));
        return new IteratorIterator(new ArrayIterator($books));
    }

    /* Корзина */

    static function addItemToBasket(int $id): void{
        if (self::call('spGetItemsForBasket', [(string)$id])) {
            Basket::add($id);
        }
    }

    static function removeItemFromBasket(int $id): void{
        Basket::remove($id);
    }

    static function getItemsFromBasket(): IteratorIterator{
        $items = Basket::items();
        $books = [];
        if ($items) {
            foreach (self::call('spGetItemsForBasket', [implode(',', array_keys($items))]) as $row) {
                $book = self::toBook($row);
                $book->quantity = $items[$book->id];
                $books[] = $book;
                unset($items[$book->id]);
            }
            // Товары, которых уже нет в каталоге, убираем из корзины
            foreach (array_keys($items) as $missingId) {
                Basket::remove($missingId);
            }
        }
        return new IteratorIterator(new ArrayIterator($books));
    }

    /* Заказы */

    static function saveOrder(Order $order): bool{
        $order->id = Basket::orderId();
        $order->items = iterator_to_array(self::getItemsFromBasket(), false);
        if (!$order->items) {
            return false;
        }
        self::$db->beginTransaction();
        try {
            self::call('spSaveOrder', [$order->id, $order->customer, $order->email, $order->phone, $order->address]);
            foreach ($order->items as $book) {
                self::call('spSaveOrderedItems', [$order->id, $book->id, $book->quantity]);
            }
            self::$db->commit();
        } catch (Throwable $e) {
            self::$db->rollBack();
            throw $e;
        }
        Basket::create(); // новая пустая корзина с новым order-id
        return true;
    }

    static function getOrders(): IteratorIterator{
        $orders = [];
        foreach (self::call('spGetOrders') as $row) {
            $order = new Order($row['customer'], $row['email'], $row['phone'], $row['address']);
            $order->id = $row['order_id'];
            $order->created = $row['created'];
            $order->items = array_map([self::class, 'toBook'], self::call('spGetOrderedItems', [$order->id]));
            $orders[] = $order;
        }
        return new IteratorIterator(new ArrayIterator($orders));
    }

    /* Пользователи (админы) */

    static function userAdd(User $user): bool{
        self::call('spSaveAdmin', [$user->login, self::createHash($user->password), $user->email]);
        return true;
    }

    static function userCheck(User $user): bool{
        return (bool)self::call('spGetAdmin', [$user->login]);
    }

    static function userGet(User $user): User{
        $rows = self::call('spGetAdmin', [$user->login]);
        if (!$rows) {
            throw new RuntimeException("Пользователь {$user->login} не найден");
        }
        $found = new User($rows[0]['login'], $rows[0]['password'], $rows[0]['email']);
        $found->id = (int)$rows[0]['id'];
        $found->created = $rows[0]['created'];
        return $found;
    }

    static function createHash(string $password): string{
        return password_hash($password, PASSWORD_DEFAULT);
    }

    static function isAdmin(): bool{
        return isset($_SESSION['admin']);
    }

    static function logIn(User $user): bool{
        if (!self::userCheck($user)) {
            return false;
        }
        $found = self::userGet($user);
        if (!password_verify($user->password, $found->password)) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['admin'] = $found->login;
        return true;
    }

    static function logOut(): void{
        $_SESSION = [];
        session_destroy();
    }
}

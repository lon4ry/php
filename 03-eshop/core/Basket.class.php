<?php
/*
    Корзина хранится в куке eshop в виде JSON:
    {"order-id": "567fhgfhr6", "2": 1, "15": 2}
    order-id - идентификатор заказа, остальные ключи - id товаров, значения - количество.
*/
class Basket{
    const COOKIE = 'eshop';
    const ORDER_KEY = 'order-id';
    const LIFETIME = 60 * 60 * 24 * 30;
    const MAX_QUANTITY = 99;
    const MAX_ITEMS = 50;

    private static array $basket = [];

    static function init(): void{
        isset($_COOKIE[self::COOKIE]) ? self::read() : self::create();
    }

    static function create(): void{
        self::$basket = [self::ORDER_KEY => bin2hex(random_bytes(5))];
        self::save();
    }

    static function read(): void{
        $data = json_decode($_COOKIE[self::COOKIE], true);
        if (!is_array($data) || !is_string($data[self::ORDER_KEY] ?? null)) {
            self::create();
            return;
        }
        self::$basket = [self::ORDER_KEY => Cleaner::str($data[self::ORDER_KEY])];
        foreach ($data as $id => $quantity) {
            if (is_int($id) && $id > 0 && is_int($quantity) && $quantity > 0 && count(self::$basket) <= self::MAX_ITEMS) {
                self::$basket[$id] = min($quantity, self::MAX_QUANTITY);
            }
        }
    }

    static function save(): void{
        setcookie(self::COOKIE, json_encode(self::$basket), [
            'expires' => time() + self::LIFETIME,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    static function add(int $id, int $quantity = 1): void{
        if (!isset(self::$basket[$id]) && count(self::items()) >= self::MAX_ITEMS) {
            return;
        }
        self::$basket[$id] = min((self::$basket[$id] ?? 0) + $quantity, self::MAX_QUANTITY);
        self::save();
    }

    static function remove(int $id): void{
        unset(self::$basket[$id]);
        self::save();
    }

    static function orderId(): string{
        return self::$basket[self::ORDER_KEY];
    }

    /** @return array<int, int> id товара => количество */
    static function items(): array{
        $items = self::$basket;
        unset($items[self::ORDER_KEY]);
        return $items;
    }

    static function count(): int{
        return array_sum(self::items());
    }
}

<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /create_order');
    exit;
}

$order = new Order(
    Cleaner::str($_POST['customer'] ?? ''),
    Cleaner::str($_POST['email'] ?? ''),
    Cleaner::str($_POST['phone'] ?? ''),
    Cleaner::str($_POST['address'] ?? ''),
);

$errors = [];
if ($order->customer === '' || $order->phone === '' || $order->address === '') {
    $errors[] = 'Заполните все поля формы.';
}
if (!filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Укажите корректный email.';
}
if (Basket::count() === 0) {
    $errors[] = 'Корзина пуста.';
}

if (!$errors && Eshop::saveOrder($order)):
?>
<h1>Заказ оформлен</h1>
<p>Номер заказа: <b><?= h($order->id) ?></b>, сумма: <?= number_format($order->total(), 2, ',', ' ') ?> руб.</p>
<p>Вернуться в <a href='/catalog'>каталог</a></p>
<?php else: ?>
<h1>Заказ не оформлен</h1>
<?php foreach ($errors ?: ['Не удалось сохранить заказ.'] as $error): ?>
<p><?= h($error) ?></p>
<?php endforeach; ?>
<p><a href='/create_order'>Вернуться к оформлению</a> или в <a href='/catalog'>каталог</a></p>
<?php endif; ?>

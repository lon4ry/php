<?php
$orders = Eshop::getOrders();
$hasOrders = false;
?>
<h1>Поступившие заказы:</h1>
<a href='/admin'>Назад в админку</a>
<?php foreach ($orders as $order): $hasOrders = true; ?>
<hr>
<h2>Заказ номер: <?= h($order->id) ?></h2>
<p><b>Заказчик</b>: <?= h($order->customer) ?></p>
<p><b>Email</b>: <?= h($order->email) ?></p>
<p><b>Телефон</b>: <?= h($order->phone) ?></p>
<p><b>Адрес доставки</b>: <?= h($order->address) ?></p>
<p><b>Дата размещения заказа</b>: <?= h(date('d.m.Y H:i', strtotime($order->created))) ?></p>

<h3>Купленные товары:</h3>
<table>
<tr>
	<th>N п/п</th>
	<th>Название</th>
	<th>Автор</th>
	<th>Год издания</th>
	<th>Цена, руб.</th>
	<th>Количество</th>
</tr>
<?php foreach ($order->items as $n => $book): ?>
<tr>
	<td><?= $n + 1 ?></td>
	<td><?= h($book->title) ?></td>
	<td><?= h($book->author) ?></td>
	<td><?= $book->pubyear ?></td>
	<td><?= number_format($book->price, 2, ',', ' ') ?></td>
	<td><?= $book->quantity ?></td>
</tr>
<?php endforeach; ?>
</table>
<p>Всего товаров в заказе на сумму: <?= number_format($order->total(), 2, ',', ' ') ?> руб.</p>
<?php endforeach; ?>
<?php if (!$hasOrders): ?>
<hr>
<p>Заказов пока нет.</p>
<?php endif; ?>

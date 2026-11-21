<?php
$books = Eshop::getItemsFromBasket();
$total = 0;
$n = 0;
?>
<p>Вернуться в <a href='/catalog'>каталог</a></p>
<h1>Ваша корзина</h1>
<table>
<tr>
	<th>N п/п</th>
	<th>Название</th>
	<th>Автор</th>
	<th>Год издания</th>
	<th>Цена, руб.</th>
	<th>Количество</th>
	<th>Удалить</th>
</tr>
<?php foreach ($books as $book): $total += $book->price * $book->quantity; ?>
<tr>
	<td><?= ++$n ?></td>
	<td><?= h($book->title) ?></td>
	<td><?= h($book->author) ?></td>
	<td><?= $book->pubyear ?></td>
	<td><?= number_format($book->price, 2, ',', ' ') ?></td>
	<td><?= $book->quantity ?></td>
	<td>
		<form action="/remove_item_from_basket" method="post">
			<input type="hidden" name="id" value="<?= $book->id ?>">
			<input type="submit" value="Удалить">
		</form>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?php if ($n === 0): ?>
<p>Корзина пуста.</p>
<?php else: ?>
<p>Всего товаров в корзине на сумму: <?= number_format($total, 2, ',', ' ') ?> руб.</p>

<div style="text-align:center">
	<input type="button" value="Оформить заказ!"
                      onclick="location.href='/create_order'" />
</div>
<?php endif; ?>

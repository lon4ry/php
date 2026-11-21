<?php
$books = Eshop::getItemsFromCatalog();
?>
<h1>Каталог товаров</h1>
<p class='admin'><a href='admin'>админка</a></p>
<p>Товаров в <a href='basket'>корзине</a>: <?= Basket::count() ?></p>
<table>
<tr>
	<th>Название</th>
	<th>Автор</th>
	<th>Год издания</th>
	<th>Цена, руб.</th>
	<th>В корзину</th>
</tr>
<?php foreach ($books as $book): ?>
<tr>
	<td><?= h($book->title) ?></td>
	<td><?= h($book->author) ?></td>
	<td><?= $book->pubyear ?></td>
	<td><?= number_format($book->price, 2, ',', ' ') ?></td>
	<td>
		<form action="/add_item_to_basket" method="post">
			<input type="hidden" name="id" value="<?= $book->id ?>">
			<input type="submit" value="В корзину">
		</form>
	</td>
</tr>
<?php endforeach; ?>
</table>

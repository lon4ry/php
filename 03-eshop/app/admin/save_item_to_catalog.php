<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/add_item_to_catalog');
    exit;
}

$book = new Book(
    Cleaner::str($_POST['title'] ?? ''),
    Cleaner::str($_POST['author'] ?? ''),
    (float)str_replace(',', '.', Cleaner::str($_POST['price'] ?? '')),
    Cleaner::uint($_POST['pubyear'] ?? 0),
);

$isValid = $book->title !== ''
    && $book->author !== ''
    && $book->price > 0 && $book->price < 100000000 // DECIMAL(10, 2)
    && $book->pubyear >= 1000 && $book->pubyear <= (int)date('Y');

if ($isValid && Eshop::addItemToCatalog($book)) {
    header('Location: /admin/add_item_to_catalog');
    exit;
}
?>
<h1>Товар не добавлен</h1>
<p>Проверьте данные: название и автор обязательны, год издания — четыре цифры не позже текущего года, цена больше нуля.</p>
<p><a href='/admin/add_item_to_catalog'>Вернуться к форме</a></p>

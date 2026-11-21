<?php
$id = Cleaner::uint($_POST['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
    Eshop::removeItemFromBasket($id);
}
header('Location: /basket');
exit;

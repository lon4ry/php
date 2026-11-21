<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /enter');
    exit;
}

$user = new User(Cleaner::str($_POST['login'] ?? ''), (string)($_POST['password'] ?? ''));

if ($user->login !== '' && Eshop::logIn($user)) {
    header('Location: /admin');
    exit;
}
?>
<h1>Неверный логин или пароль</h1>
<p><a href='/enter'>Попробовать ещё раз</a></p>

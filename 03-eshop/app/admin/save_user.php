<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/create_user');
    exit;
}

$user = new User(
    Cleaner::str($_POST['login'] ?? ''),
    (string)($_POST['password'] ?? ''),
    Cleaner::str($_POST['email'] ?? ''),
);

$error = match (true) {
    $user->login === '' || mb_strlen($user->login) > 50 => 'Логин обязателен, не длиннее 50 символов.',
    mb_strlen($user->password) < 6 => 'Пароль должен быть не короче 6 символов.',
    !filter_var($user->email, FILTER_VALIDATE_EMAIL) => 'Укажите корректный email.',
    Eshop::userCheck($user) => 'Пользователь с таким логином уже существует.',
    default => null,
};

if ($error === null && Eshop::userAdd($user)) {
    header('Location: /admin');
    exit;
}
?>
<h1>Пользователь не создан</h1>
<p><?= h($error ?? 'Не удалось сохранить пользователя.') ?></p>
<p><a href='/admin/create_user'>Вернуться к форме</a></p>

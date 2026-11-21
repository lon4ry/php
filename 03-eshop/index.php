<?php
error_reporting(E_ALL);
// Буфер вывода: страницы отправляют header()/setcookie() после подключения шапки
ob_start();

require_once 'core/init.php';

require_once 'app/__header.php';
require_once 'app/__router.php';
require_once 'app/__footer.php';

<?php
const CORE_DIR = 'core/';
const APP_DIR = 'app/';
const ADMIN_DIR = APP_DIR . 'admin/';

/*
    ////////////////////////////////////
    ////// ЭТОТ БЛОК ДЛЯ ОТЛАДКИ //////
    ///////////////////////////////////
*/
set_include_path(get_include_path() . PATH_SEPARATOR . CORE_DIR . PATH_SEPARATOR . APP_DIR . PATH_SEPARATOR . ADMIN_DIR);
// Стандартный spl_autoload приводит имя класса к нижнему регистру,
// поэтому на Linux Eshop.class.php не находится. Подключаем файл с именем как у класса.
spl_autoload_register(function (string $class) {
    require_once __DIR__ . "/$class.class.php";
});

const ERROR_LOG = ADMIN_DIR . 'error.log';
const ERROR_MSG = 'Срочно обратитесь к администратору! admin@email.info';
function errors_log($msg, $file, $line){
    $dt = date('d-m-Y H:i:s');
    $message = "$dt - $msg in $file:$line\n";
    error_log($message, 3, ERROR_LOG);
    echo ERROR_MSG;
}
function error_handler($no, $msg, $file, $line) {
    errors_log($msg, $file, $line);
}
set_error_handler('error_handler');
function exception_handler($e) {
    errors_log($e->getMessage(), $e->getFile(), $e->getLine());
}
set_exception_handler('exception_handler');
/*
    //////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
*/
define('DB', [
    'HOST' => getenv('DB_HOST') ?: 'localhost',
    'USER' => getenv('DB_USER') ?: 'root',
    'PASS' => getenv('DB_PASS') ?: '',
    'NAME' => getenv('DB_NAME') ?: 'eshop',
]);

/* Экранирование данных при выводе в HTML */
function h($value): string{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

Eshop::init(DB);
Basket::init();

/* Раздел /admin доступен только залогиненному администратору */
$requestPath = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
if (str_starts_with($requestPath, '/admin') && !Eshop::isAdmin()) {
    header('Location: /enter');
    exit;
}

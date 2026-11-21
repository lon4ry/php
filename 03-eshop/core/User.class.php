<?php
class User{
    public int $id = 0;
    public string $created = '';

    function __construct(
        public string $login = '',
        public string $password = '', // открытый пароль из формы или хеш из БД
        public string $email = '',
    ){}
}

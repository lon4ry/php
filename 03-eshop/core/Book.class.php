<?php
class Book{
    public int $id = 0;
    public string $title = '';
    public string $author = '';
    public float $price = 0;
    public int $pubyear = 0;
    public int $quantity = 1; // количество в корзине или заказе

    function __construct(string $title = '', string $author = '', float $price = 0, int $pubyear = 0){
        $this->title = $title;
        $this->author = $author;
        $this->price = $price;
        $this->pubyear = $pubyear;
    }
}

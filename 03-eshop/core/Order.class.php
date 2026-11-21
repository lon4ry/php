<?php
class Order{
    public string $id = '';      // идентификатор заказа из корзины (order-id)
    public string $created = '';
    /** @var Book[] */
    public array $items = [];

    function __construct(
        public string $customer = '',
        public string $email = '',
        public string $phone = '',
        public string $address = '',
    ){}

    function total(): float{
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->price * $item->quantity;
        }
        return $sum;
    }
}

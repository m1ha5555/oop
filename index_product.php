<?php

class Product {
    private string $name;
    private int $price;
    private string $description;
    private static int $totalProducts = 0;

    public function __construct(string $name, int $price, string $description) {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        self::$totalProducts++;
    }

    // Геттеры и сеттеры
    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function setPrice(int $price): void {
        $this->price = $price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    // Геттер для информации о товаре (возвращает, а не выводит)
    public function getInfo(): string {
        return "Товар: {$this->name}, Цена: {$this->price} руб, Описание: {$this->description}";
    }

    // Статический геттер для общего количества товаров
    public static function getTotalProducts(): int {
        return self::$totalProducts;
    }
}

class Laptop extends Product {
    private string $processor;

    public function __construct(string $name, int $price, string $description, string $processor) {
        parent::__construct($name, $price, $description);
        $this->processor = $processor;
    }

    public function getProcessor(): string {
        return $this->processor;
    }

    public function setProcessor(string $processor): void {
        $this->processor = $processor;
    }

    // Переопределяем метод, используя родительский
    public function getInfo(): string {
        return parent::getInfo() . ", Процессор: {$this->processor}";
    }
}

// Использование
$product = new Product('Комп', 200, 'крутой комп');
echo $product->getInfo();
echo '<hr>';

$laptop = new Laptop('Ноутбук Acer', 25000, 'Игровой ноутбук', 'Intel i5');
echo $laptop->getInfo();
echo '<hr>';

// Изменяем цену через сеттер
$laptop->setPrice(23500);
echo "После изменения цены: <br>";
echo $laptop->getInfo();
echo '<hr>';

// Статический метод вызываем через класс
echo 'Всего товаров создано: ' . Product::getTotalProducts();
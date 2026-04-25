<?php

class Book {
    public string $title;
    public string $author;
    public int $year;
    protected float $lateFee;
    
    public function __construct(string $title, string $author, int $year, float $lateFee) {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->lateFee = $lateFee;
    }

    public function getTitle() : string {
        return $this->title;
    }

    public function getAuthor() : string {
        return $this->author;
    }

    public function getYear() : int {
        return $this->year;
    }

    public function getLateFee() : float {
        return $this->lateFee;
    }

    public function getInfo(): string {
        return "{$this->title}: {$this->author} ({$this->year})";
    }

    public function calculateLateFee(int $days): float {
        return $days * $this->lateFee;
    }
}
class EBook extends Book {
    private float $fileSize;
    private float $lateFeeMultiplier;
 
    public function __construct(string $title, string $author, int $year, float $lateFee, float $fileSize, float $lateFeeMultiplier) {
        parent::__construct($title, $author, $year, $lateFee);  // ← ИСПРАВЛЕНО
        $this->fileSize = $fileSize;
        $this->lateFeeMultiplier = $lateFeeMultiplier;
    }

    public function getFileSize(): float {
        return $this->fileSize;
    }
    
    public function calculateLateFee(int $days): float {
        return $days * $this->lateFee * $this->lateFeeMultiplier;
    }

    public function getInfo(): string {
        return "{$this->title}: {$this->author} ({$this->year}, электронная книга, размер: {$this->fileSize} МБ)";  // ← ИСПРАВЛЕНО
    }    
}

class Library {
    private array $books = [];
    public static int $totalBooks = 0;

    public function addBook(Book $book): void {
        $this->books[] = $book;
        self::$totalBooks++;
    }

    public function removeBook(int $index): void {  // ← ИСПРАВЛЕНО
        if (isset($this->books[$index])) {
            unset($this->books[$index]);
            $this->books = array_values($this->books);
        }
    }

    public function getAllBooks(): array {
        return $this->books;
    }

    public static function getTotalBooksCount(): int {
        return self::$totalBooks;
    }

    public function getTotalLateFee(int $days): float {
        $sum = 0;
        foreach ($this->books as $book) {
            $sum += $book->calculateLateFee($days);
        }
        return $sum;
    }

    public function getCatalog(): string {  // ← ПЕРЕИМЕНОВАНО
        $catalog = '';
        $num = 1;
        foreach ($this->books as $book) {
            $catalog .= "{$num}. {$book->getInfo()} <br>";
            $num++;
        }
        return $catalog;
    }
}

// Использование
$book = new Book('Война и мир', 'Лев Толстой', 1869, 10);
$book2 = new EBook('1984', 'Джордж Оруэлл', 1949, 10, 2.5, 0.7);  // ← ИСПРАВЛЕНО

$library = new Library();
$library->addBook($book);
$library->addBook($book2);

echo $library->getCatalog();  // ← ИСПРАВЛЕНО
echo "<hr>";

echo 'Общий штраф за 5 дней просрочки: <br>';
foreach ($library->getAllBooks() as $book) {
    echo '-' . $book->getTitle() . ':' . $book->calculateLateFee(5) . 'руб.<br>';
}
echo "<br>";
echo 'ИТОГО:' . $library->getTotalLateFee(5) . 'руб.';
echo "<hr>";
echo 'Всего книг в системе:' . Library::getTotalBooksCount();  // ← ИСПРАВЛЕНО
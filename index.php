<?php

//Library

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
        parent::__construct($title, $author, $year, $lateFee);
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
        return "{$this->title}: {$this->author} ({$this->year}, электронная книга, размер: {$this->fileSize} МБ)";
    }    
}

class Library {

    private array $books = [];
    public static int $totalBooks = 0;

    public function addBook(Book $book): void {
        $this->books[] = $book;
        self::$totalBooks++;
    }

    public function removeBook(int $index): void {
        if (isset($this->books[$index])) {
            unset($this->books[$index]);
            $this->books = array_values($this->books);
            self::$totalBooks--;
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

    public function getCatalog(): string {
        $catalog = '';
        $num = 1;
        foreach ($this->books as $book) {
            $catalog .= "{$num}. {$book->getInfo()} <br>";
            $num++;
        }
        return $catalog;
    }

    public function findBooksByAuthor(string $author) : string {
        $num = 1;
        $authorList = '';
        foreach ($this->books as $book) {
            if( $book->getAuthor() === $author ) {
                $authorList .= "{$num}. {$book->getInfo()} <br>";
                $num++;
            }             
        }

        return $authorList;

    }
}

// Тестирование
$book = new Book('Война и мир', 'Лев Толстой', 1869, 10);
$book2 = new EBook('1984', 'Джордж Оруэлл', 1949, 10, 2.5, 0.7); 
$book3 = new Book('Детство', 'Лев Толстой', 1852, 10);

$library = new Library();
$library->addBook($book);
$library->addBook($book2);
$library->addBook($book3);

echo $library->getCatalog(); 
echo "<hr>";

echo 'Общий штраф за 5 дней просрочки: <br>';
foreach ($library->getAllBooks() as $book) {
    echo '-' . $book->getTitle() . ':' . $book->calculateLateFee(5) . 'руб.<br>';
}

echo "<br>";
echo 'ИТОГО:' . $library->getTotalLateFee(5) . 'руб.';
echo "<hr>";
echo 'Книги автора: Лев Толстой <br>';
echo ($library->findBooksByAuthor('Лев Толстой')) ?? 'Нет книг данного автора!';
echo "<hr>";
echo 'Всего книг в системе:' . Library::getTotalBooksCount();
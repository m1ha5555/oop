<?php

declare(strict_types=1);

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

    public function findBooksByAuthor(string $author) : array {
        return array_filter($this->books, function($book) use ($author) {
            return $book->getAuthor() === $author;
        });
    }

    public function findBooksAfterYear(int $year) : array {
        return array_filter($this->books, function($book) use ($year) {
            return $book->getYear() > $year;
        });
    }

    public function sortBooksByYear(bool $ascending = true) : void {
        
        usort($this->books, function($a, $b) use ($ascending) {
            if ($ascending) {
                return $a->getYear() <=> $b->getYear();
            } else {
                return $b->getYear() <=> $a->getYear();
            }
        });
    }

    public function findBooksInYearRange(int $startYear, int $endYear): array {
        return array_filter($this->books, function($book) use ($startYear, $endYear) {
            return $book->getYear() >= $startYear && $book->getYear() <= $endYear;
        });
    }

    public function sortBooksByAuthor() : void {
        usort($this->books, function($a, $b) {
            return strcmp($a->getAuthor(), $b->getAuthor());
        });
    }

    public function sortBooksByTitle() : void {
        usort($this->books, function($a, $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });
    }
        
    public function getStatistics(): array {
        if (empty($this->books)) {
            return [
                'total_books' => 0,
                'oldest_year' => null,
                'newest_year' => null,
                'unique_authors' => 0,
                'total_late_fee_per_day' => 0.0
            ];
        }
        
        $years = array_map(fn($book) => $book->getYear(), $this->books);
        $authors = array_map(fn($book) => $book->getAuthor(), $this->books);
        $totalLateFee = array_sum(array_map(fn($book) => $book->getLateFee(), $this->books));
        
        return [
            'total_books' => count($this->books),
            'oldest_year' => min($years),
            'newest_year' => max($years),
            'unique_authors' => count(array_unique($authors)),
            'total_late_fee_per_day' => $totalLateFee
        ];
    }

}

class Reader {

    private int $id;
    private string $name;
    private string $email;
    private string $registeredAt;

    public function __construct( int $id, string $name, string $email, string $registeredAt ){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->registeredAt = new DateTime($registeredAt);
    }
 
    public function getId() : int {
        return $this->id;
    }
 
    public function getName() : string {
        return $this->name;
    }
 
    public function getEmail() : string {
        return $this->email;
    }
 
    public function getRegisteredAt() : DateTime {
        return $this->registeredAt;
    }

    public function getInfo() : string {
        return "#{$this->id}: {$this->name} ({$this->email})";
    }

    public function getRegistrationDateFormatted(string $format = 'd.m.Y'): string {
        return $this->registeredAt->format($format);
    }

}

class Loan {
    private Book $book;
    private Reader $reader;
    private DateTime $borrowedAt;
    private DateTime $dueDate;
    private ?DateTime $returnedAt = null;  // тип с ? и сразу null

    public function __construct(Book $book, Reader $reader, DateTime $borrowedAt) {
        $this->book = $book;
        $this->reader = $reader;
        $this->borrowedAt = $borrowedAt;
        // Важно: клонируем, чтобы не изменить исходную дату
        $this->dueDate = (clone $borrowedAt)->add(new DateInterval('P14D'));
        $this->returnedAt = null;
    }

    public function getBook(): Book {
        return $this->book;
    }

    public function getReader(): Reader {
        return $this->reader;
    }

    public function getBorrowedAt(): DateTime {
        return $this->borrowedAt;
    }

    public function getDueDate(): DateTime {
        return $this->dueDate;
    }

    public function getReturnedAt(): ?DateTime {
        return $this->returnedAt;
    }

    public function isOverdue(): bool {
        // Если книга уже возвращена — не просрочена
        if ($this->returnedAt !== null) {
            return false;
        }
        
        $now = new DateTime();
        return $this->dueDate < $now;

        //return $this->returnedAt === null && $this->dueDate < new DateTime();
    }

    public function returnBook(): void {
        $this->returnedAt = new DateTime();
    }

    public function getFine(): float {
        if (!$this->isOverdue()) {
            return 0.0;
        }
        
        $now = new DateTime();
        $diff = $now->diff($this->dueDate);
        $daysOverdue = $diff->days;
        
        return $daysOverdue * $this->book->calculateLateFee(1);
    }
}
 

// Тестирование

$library = new Library();
$library->addBook(new Book('Война и мир', 'Лев Толстой', 1869, 10));
$library->addBook(new Book('Детство', 'Лев Толстой', 1852, 10));
$library->addBook(new Book('1984', 'Джордж Оруэлл', 1949, 8));
$library->addBook(new Book('Скотный двор', 'Оруэлл Дж.', 1945, 7));
$library->addBook(new EBook('Краткая история времени', 'Хокинг С.', 1988, 12, 5.2, 0.6));
$library->addBook(new Book('Анна Каренина', 'Лев Толстой', 1877, 10));
$library->addBook(new Book('Гарри Поттер', 'Джордж Оруэлл', 1997, 9));

echo "<h3>Исходный порядок:</h3>";
echo $library->getCatalog();
echo "<hr>";

// Сортируем по году (от старых к новым)
$library->sortBooksByYear();
echo "<h3>После сортировки по году (старые → новые):</h3>";
echo $library->getCatalog();
echo "<hr>";

// Общий штраф
echo '<h3>Общий штраф за 5 дней просрочки: </h3>';
foreach ($library->getAllBooks() as $book) {
    echo '-' . $book->getTitle() . ':' . $book->calculateLateFee(5) . 'руб.<br>';
}
echo '<h4>ИТОГО:' . $library->getTotalLateFee(5) . ' руб.</h4>';
echo "<hr>";

// Сортируем по автору
$library->sortBooksByAuthor();
echo "<h3>После сортировки по автору (А→Я):</h3>";
echo $library->getCatalog();
echo "<hr>";

// Сортируем по Названию
$library->sortBooksByTitle();
echo "<h3>После сортировки по названию (А→Я):</h3>";
echo $library->getCatalog();
echo "<hr>";

echo "<h3>Книги 1940-1980 годов:</h3>";
$midCentury = $library->findBooksInYearRange(1940, 1980);
foreach ($midCentury as $book) {
    echo "- " . $book->getInfo() . "<br>";
}

// Книги после определенного года
echo '<h3>Книги после 1900 г.:</h3>';
$modernBooks = $library->findBooksAfterYear(1900);
if (count($modernBooks) > 0) {
    foreach ($modernBooks as $book) {
        echo "- " . $book->getInfo() . "<br>";
    }
} else {
    echo "Нет книг после данного года!";
}
echo "<hr>";

echo '<h3>Книги автора: Лев Толстой</h3>';
$foundBooks = $library->findBooksByAuthor('Лев Толстой');
if (count($foundBooks) > 0) {
    foreach ($foundBooks as $book) {
        echo "- " . $book->getInfo() . "<br>";
    }
} else {
    echo "Нет книг данного автора!";
}
echo "<hr>";
echo '<h3>Всего книг в системе:' . Library::getTotalBooksCount() . '</h3>';

// Статистика
echo "<h3>Статистика библиотеки:</h3>";
$stats = $library->getStatistics();
echo "<pre>";
print_r($stats);
echo "</pre>";
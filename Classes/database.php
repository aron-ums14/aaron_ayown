<?php

class Database{
    function opencon(): PDO{
        return new PDO("mysql:host=localhost;
        dbname=dbs_inf242",
        username: "root", 
        password: "");
    }


    function viewBorrowers() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM borrowers")->fetchAll();
    }

    function insertUser($username, $password_hash) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO Users (username, password_hash, created_at) 
                                   VALUES (?, ?, NOW())");
            $stmt->execute([$username, $password_hash]);
            $user_id = $con->lastInsertId();
            $con->commit();
            return $user_id;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBorrower($firstname, $lastname, $email, $phone, $member_since, $is_active) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO borrowers 
                (borrower_firstname, borrower_lastname, borrower_email, borrower_phone_number, borrower_member_since, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $email, $phone, $member_since, $is_active]);
            $borrower_id = $con->lastInsertId();
            $con->commit();
            return $borrower_id;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBorrowerUser($borrower_id, $user_id) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO BorrowerUser (borrower_id, user_id) VALUES (?, ?)");
            $stmt->execute([$borrower_id, $user_id]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBorrowerAddress($borrowerid, $housenumber, $street, $barangay, $city, $province, $postalcode, $is_active) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO BorrowerAddress 
                (borrower_id, ba_house_number, ba_street, ba_barangay, ba_city, ba_province, ba_postal_code, is_primary) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$borrowerid, $housenumber, $street, $barangay, $city, $province, $postalcode, $is_active]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBooks($book_title, $book_isbn, $book_publication_year, $book_edition, $book_publisher) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO books 
                (book_title, book_isbn, book_publication_year, book_edition, book_publisher) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$book_title, $book_isbn, $book_publication_year, $book_edition, $book_publisher]);
            $book_id = $con->lastInsertId();
            $con->commit();
            return $book_id;

        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }

            if ($e->errorInfo[1] == 1062) {
                throw new Exception("Duplicate ISBN: Book already exists.");
            }

            throw $e;
        }
    }

    function getBooks() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM books")->fetchAll();
    }

    function insertBookCopy($book_id, $status) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO bookcopy (book_id, status) VALUES (?, ?)");
            $stmt->execute([$book_id, $status]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBookAuthor($book_id, $author_id) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO bookauthors (book_id, author_id) VALUES (?, ?)");
            $stmt->execute([$book_id, $author_id]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function insertBookGenre($book_id, $genre_id) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO bookgenre (genre_id, book_id) VALUES (?, ?)");
            $stmt->execute([$genre_id, $book_id]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function getAuthors() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM authors")->fetchAll();
    }

    function getGenres() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM genres")->fetchAll();
    }

    function viewBooks() {
        $con = $this->opencon();
        return $con->query("
            SELECT
                Books.book_id,
                Books.book_title,
                Books.book_isbn,
                Books.book_publication_year,
                Books.book_publisher,
                COUNT(BookCopy.copy_id) AS Copies,
                SUM(CASE WHEN BookCopy.status = 'Available' THEN 1 ELSE 0 END) AS Available_Copies
            FROM Books
            LEFT JOIN BookCopy ON Books.book_id = BookCopy.book_id
            GROUP BY Books.book_id
        ")->fetchAll();
    }

    function viewloans() {
        $con = $this->opencon();
        return $con->query("SELECT 
            loan.loan_id,
            CONCAT(borrowers.borrower_firstname, ' ', borrowers.borrower_lastname) AS borrower_name,
            loan.loan_status,
            loan.loan_date,
            users.username AS processed_by
        FROM 
            loan
        INNER JOIN borrowers ON loan.borrower_id = borrowers.borrower_id
        LEFT JOIN users ON loan.processed_by_user_id = users.user_id
        ORDER BY loan.loan_date DESC")->fetchAll();
    }

    function countBook() {
        $con = $this->opencon();
        return $con->query("SELECT COUNT(*) AS total_books FROM Books")->fetchColumn();
    }

    function updateBook($book_id, $title, $isbn, $year, $publisher) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("
                UPDATE Books
                SET book_title = ?, book_isbn = ?, book_publication_year = ?, book_publisher = ?
                WHERE book_id = ?
            ");
            $stmt->execute([$title, $isbn, $year, $publisher, $book_id]);
            $con->commit();
            return true;

        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function countBookCopies() {
        $con = $this->opencon();
        return $con->query("SELECT COUNT(*) AS total_copies FROM bookcopy")->fetchColumn();
    }

    function countOpenLoans() {
        $con = $this->opencon();
        return $con->query("SELECT COUNT(*) FROM loan WHERE loan_status = 'Open'")->fetchColumn();
    }

    function countOverdueItems() {
        $con = $this->opencon();
        return $con->query("
            SELECT COUNT(*) 
            FROM loan 
            WHERE loan_status = 'Open' 
            AND loan_date < DATE_SUB(CURDATE(), INTERVAL 14 DAY)
        ")->fetchColumn();
    }

    //this is ==AUTHOR FUNCTION STRUCTURE===
    
    function insertAuthor($firstname, $lastname, $birth_year, $nationality){
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO authors 
                (author_firstname, author_lastname, author_birth_year, author_nationality) 
                VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $birth_year, $nationality]);
            $author_id = $con->lastInsertId();
            $con->commit();
            return $author_id;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function deleteAuthor($authorId) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            
            // Delete the author from the authors table
            $stmt = $con->prepare("DELETE FROM authors WHERE author_id = ?");
            $stmt->execute([$authorId]);
            
            $con->commit();
            return true;
            
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function viewauthors() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM Authors ORDER BY author_lastname, author_firstname")->fetchAll();
    }

    function countAuthors() {
        $con = $this->opencon();
        return $con->query("SELECT COUNT(*) AS total_authors FROM Authors")->fetchColumn();
    }

    
// this is the ==GENRE FUNCTION STRUCTURE==
    function insertGenre($genre_name){
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO genres (genre_name) VALUES (?)");
            $stmt->execute([$genre_name]);
            $genre_id = $con->lastInsertId();
            $con->commit();
            return $genre_id;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function deleteGenre($genreId) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();
            
            //this line of code Delete the genre from the genres table
            $stmt = $con->prepare("DELETE FROM genres WHERE genre_id = ?");
            $stmt->execute([$genreId]);
            
            $con->commit();
            return true;
            
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollback();
            }
            throw $e;
        }
    }

    function viewgenres() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM Genres ORDER BY genre_name")->fetchAll();
    }

    function countGenres() {
        $con = $this->opencon();
        return $con->query("SELECT COUNT(*) AS total_genres FROM Genres")->fetchColumn();
    }

    // A BOOK FUNCTIONS 

    function deletebooks($book_id){
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            // this function deletes a book
            $stmtCopies = $con->prepare("DELETE FROM BookCopy WHERE book_id = ?");
            $stmtCopies->execute([$book_id]);

            // this function deletes book-author relationships
            $stmtBA = $con->prepare("DELETE FROM BookAuthors WHERE book_id = ?");
            $stmtBA->execute([$book_id]);

            // this function deletes book-genre relationships
            $stmtGenre = $con->prepare("DELETE FROM BookGenre WHERE book_id = ?");
            $stmtGenre->execute([$book_id]);

            // this function Delete the book itself
            $stmtBook = $con->prepare("DELETE FROM Books WHERE book_id = ?");
            $stmtBook->execute([$book_id]);

            $con->commit();
            return true;

        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    // fix these code
    function getActiveBorrowers
    (){$con = $this ->opencon();
    return $con ->query("SELECT borrower_id, CONCAT(borrower_firstname, ' '
    ,borrower_lastname) AS borrower_name FROM borrowers WHERE is_active = 1 order BY borrower_name")-> fetchAll();

}
function getAvailableCopies(){
    $con = $this->opencon();
    return $con->query("
        SELECT
            bookcopy.copy_id, 
            books.book_id, 
            books.book_title, 
            bookcopy.status 
        FROM bookcopy 
        JOIN books ON bookcopy.book_id = books.book_id
        WHERE bookcopy.status = 'AVAILABLE'
        ORDER BY books.book_title, bookcopy.copy_id
    ")->fetchAll();
}

function createLoanWithItems($borrower_id, $processed_by_user_id, $copy_ids, $li_duedate, $condition_out) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            // 1. Create the main loan record
            $insertLoanStmt = $con->prepare("INSERT INTO loan(borrower_id, processed_by_user_id, loan_status, loan_date) VALUES (?, ?, 'OPEN', NOW())");
            $insertLoanStmt->execute([$borrower_id, $processed_by_user_id]);
            $loan_id = $con->lastInsertId();

            // 2. Prepare statements for the loop
            $checkCopyStmt = $con->prepare("SELECT status FROM BookCopy WHERE copy_id = ?");
            $insertLoanItemStmt = $con->prepare("INSERT INTO LoanItem (loan_id, copy_id, li_duedate, condition_out) VALUES (?, ?, ?, ?)");
            $updateCopyStmt = $con->prepare("UPDATE BookCopy SET status = 'ON_LOAN' WHERE copy_id = ?");

            foreach ($copy_ids as $copy_id) {
                $checkCopyStmt->execute([$copy_id]);
                $copy = $checkCopyStmt->fetch();

                if (!$copy) throw new Exception("Copy ID $copy_id does not exist.");
                if ($copy['status'] !== 'AVAILABLE') throw new Exception("Copy ID $copy_id is not available.");

                $insertLoanItemStmt->execute([$loan_id, $copy_id, $li_duedate, $condition_out]);
                $updateCopyStmt->execute([$copy_id]);
            }

            $con->commit();
            return $loan_id;
        } catch (Exception $e) {
            if ($con->inTransaction()) $con->rollBack();
            throw $e;
        }
    }
    
}
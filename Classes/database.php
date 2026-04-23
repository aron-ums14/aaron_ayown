<?php

class database {

    function opencon(): PDO {
        return new PDO("mysql:host=localhost;dbname=dbs_inf242", "root", "");
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
}
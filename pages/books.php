<?php
session_start();
require_once('../Classes/database.php');
$con = new database();

$bookstatus = null;
$bookmessage = '';

// Handle Add Book
if(isset($_POST['add_books'])){
    $booktitle = $_POST['book_title'];
    $bookisbns = $_POST['book_isbn'];
    $book_pub_year = $_POST['book_publication_year'];
    $bookedition = $_POST['book_edition'];
    $bookpublisher = $_POST['book_publisher'];

    try {
        $con->insertBooks($booktitle, $bookisbns, $book_pub_year, $bookedition, $bookpublisher);
        $bookstatus = 'success';
        $bookmessage = 'Book added successfully';
    } catch(Exception $e) {
        $bookstatus = 'error';
        $bookmessage = $e->getMessage();
    }
}

// Handle Add Book Copy
if(isset($_POST['add_copy'])){
    $book_id = $_POST['book_id'];
    $status = $_POST['status'];

    try {
        $con->insertBookCopy($book_id, $status);
        $bookstatus = 'success';
        $bookmessage = 'Book copy added successfully';
    } catch(Exception $e) {
        $bookstatus = 'error';
        $bookmessage = $e->getMessage();
    }
}

// Handle Assign Author
if(isset($_POST['assign_author'])){
    $book_id = $_POST['book_id'];
    $author_id = $_POST['author_id'];

    try {
        $con->insertBookAuthor($book_id, $author_id);
        $bookstatus = 'success';
        $bookmessage = 'Author assigned to book successfully';
    } catch(Exception $e) {
        $bookstatus = 'error';
        $bookmessage = $e->getMessage();
    }
}

// Handle Assign Genre
if(isset($_POST['assign_genre'])){
    $book_id = $_POST['book_id'];
    $genre_id = $_POST['genre_id'];

    try {
        $con->insertBookGenre($book_id, $genre_id);
        $bookstatus = 'success';
        $bookmessage = 'Genre assigned to book successfully';
    } catch(Exception $e) {
        $bookstatus = 'error';
        $bookmessage = $e->getMessage();
    }
}

// Handle Edit Book
if(isset($_POST['update_book'])){
    $book_id = $_POST['book_id'];
    $booktitle = $_POST['book_title'];
    $bookisbns = $_POST['book_isbn'];
    $book_pub_year = $_POST['book_publication_year'];
    $bookpublisher = $_POST['book_publisher'];

    try {
        $con->updateBook($book_id, $booktitle, $bookisbns, $book_pub_year, $bookpublisher);
        $bookstatus = 'success';
        $bookmessage = 'Book updated successfully';
    } catch(Exception $e) {
        $bookstatus = 'error';
        $bookmessage = $e->getMessage();
    }
}

// Fetch books, authors, and genres
$allbooks = $con->viewbooks();
$books = $con->getBooks();
$authors = $con->getAuthors();
$genres = $con->getGenres();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Books — Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="../sweetalert/dist/sweetalert2.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBooks">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navBooks" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link" href="borrowers.php">Borrowers</a></li>
        <li class="nav-item"><a class="nav-link" href="authors-genres.php">Authors &amp; Genres</a></li>
        <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
        <li class="nav-item"><a class="nav-link" href="return.php">Return</a></li>
        <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <span class="badge badge-soft">Role: ADMIN</span>
        <a class="btn btn-sm btn-outline-secondary" href="login.php">Logout</a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
  <div class="row g-3">
    <!-- Left Column - Forms -->
    <div class="col-12 col-lg-4">
      <div class="card p-4">
        <h5 class="mb-1">Add Book</h5>
        <p class="small-muted mb-3">Creates a row in <b>Books</b>.</p>
        <form action="" method="POST">
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input class="form-control" name="book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" type="number" min="1500" max="2100">
          </div>
          <div class="mb-3">
            <label class="form-label">Edition</label>
            <input class="form-control" name="book_edition" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" placeholder="optional">
          </div>
          <button class="btn btn-primary w-100" type="submit" name="add_books">Save Book</button>
        </form>
      </div>

      <div class="card p-4 mt-3">
        <h6 class="mb-2">Add Copy</h6>
        <p class="small-muted mb-3">Creates a row in <b>BookCopy</b>.</p>
        <form action="" method="POST">
          <div class="mb-3">
            <label class="form-label">Book <span class="text-danger">*</span></label>
            <select class="form-select" name="book_id" required>
              <option value="">Select book</option>
              <?php foreach($books as $book): ?>
              <option value="<?php echo $book['book_id']; ?>">[<?php echo $book['book_id']; ?>] <?php echo htmlspecialchars($book['book_title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select" name="status" required>
              <option value="">Select status</option>
              <option value="AVAILABLE">AVAILABLE</option>
              <option value="ON_LOAN">ON_LOAN</option>
              <option value="LOST">LOST</option>
              <option value="DAMAGED">DAMAGED</option>
              <option value="REPAIR">REPAIR</option>
            </select>
          </div>
          <button class="btn btn-outline-primary w-100" type="submit" name="add_copy">Add Copy</button>
        </form>
      </div>
    </div>

    <!-- Right Column - Table & Assign Forms -->
    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
          <div>
            <h5 class="mb-1">Books List</h5>
            <div class="small-muted"><?php echo count($allbooks) . ' book' . (count($allbooks) != 1 ? 's' : '') . ' in database'; ?></div>
          </div>
          <div class="d-flex gap-2">
            <input class="form-control" style="max-width: 260px;" placeholder="Search title / ISBN...">
            <button class="btn btn-outline-secondary">Search</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Year</th>
                <th>Publisher</th>
                <th>Copies</th>
                <th>Available</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($allbooks as $book) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($book['book_id']) . '</td>';
                echo '<td>' . htmlspecialchars($book['book_title']) . '</td>';
                echo '<td>' . htmlspecialchars($book['book_isbn'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($book['book_publication_year'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($book['book_publisher'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($book['Copies'] ?? 0) . '</td>';
                echo '<td class="text-center"><span class="badge bg-success">' . ($book['Available_Copies'] ?? 0) . '</span></td>';
                echo '<td class="text-end">';
                echo '<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBookModal"
                  data-book-id="' . htmlspecialchars($book['book_id']) . '"
                  data-book-title="' . htmlspecialchars($book['book_title'], ENT_QUOTES) . '"
                  data-book-isbn="' . htmlspecialchars($book['book_isbn'] ?? '', ENT_QUOTES) . '"
                  data-book-year="' . htmlspecialchars($book['book_publication_year'] ?? '', ENT_QUOTES) . '"
                  data-book-publisher="' . htmlspecialchars($book['book_publisher'] ?? '', ENT_QUOTES) . '">Edit</button>';
                echo '</td>';
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>

        <hr class="my-4">

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Author to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookAuthors</b>.</p>
              <form action="" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php foreach($books as $book): ?>
                    <option value="<?php echo $book['book_id']; ?>"><?php echo htmlspecialchars($book['book_title']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="author_id" required>
                    <option value="">Select author</option>
                    <?php foreach($authors as $author): ?>
                    <option value="<?php echo $author['author_id']; ?>"><?php echo htmlspecialchars($author['author_firstname'] . ' ' . $author['author_lastname']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit" name="assign_author">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (book_id, author_id).</div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Genre to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookGenre</b>.</p>
              <form action="" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php foreach($books as $book): ?>
                    <option value="<?php echo $book['book_id']; ?>"><?php echo htmlspecialchars($book['book_title']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="genre_id" required>
                    <option value="">Select genre</option>
                    <?php foreach($genres as $genre): ?>
                    <option value="<?php echo $genre['genre_id']; ?>"><?php echo htmlspecialchars($genre['genre_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit" name="assign_genre">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (genre_id, book_id).</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <input type="hidden" name="book_id" id="edit-book-id">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="book_title" id="edit-book-title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" id="edit-book-isbn">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" id="edit-book-year" type="number" min="1500" max="2100">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" id="edit-book-publisher">
          </div>
          <button class="btn btn-primary w-100" type="submit" name="update_book">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../sweetalert/dist/sweetalert2.min.js"></script>

<script>
const bookStatus = <?php echo json_encode($bookstatus) ?>;
const bookMessage = <?php echo json_encode($bookmessage) ?>;

if(bookStatus == 'success') {
  Swal.fire({
    icon: 'success',
    title: 'Success',
    text: bookMessage,
    confirmButtonText: 'OK'
  });
} else if(bookStatus == 'error') {
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: bookMessage,
    confirmButtonText: 'OK'
  });
}
</script>

<script>
const editModal = document.getElementById('editBookModal');
 
editModal.addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  if (!btn) return;
 
  document.getElementById('edit-book-id').value = btn.getAttribute('data-book-id') || '';
  document.getElementById('edit-book-title').value = btn.getAttribute('data-book-title') || '';
  document.getElementById('edit-book-isbn').value = btn.getAttribute('data-book-isbn') || '';
  document.getElementById('edit-book-year').value = btn.getAttribute('data-book-year') || '';
  document.getElementById('edit-book-publisher').value = btn.getAttribute('data-book-publisher') || '';
});
</script>
</body>
</html>
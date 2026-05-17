<?php
session_start();
require_once('../classes/database.php');
$con = new database();

$book_create_status = '';
$book_create_message = '';
$error_message = '';

// Add Book
if (isset($_POST['add_book'])) {
    try {
        $book_title = trim($_POST['book_title']);
        $book_isbn = trim($_POST['book_isbn']);
        $book_publication_year = $_POST['book_publication_year'] ? (int)$_POST['book_publication_year'] : null;
        $book_edition = trim($_POST['book_edition']);
        $book_publisher = trim($_POST['book_publisher']);

        $con->insertBooks($book_title, $book_isbn, $book_publication_year, $book_edition, $book_publisher);
        
        $book_create_status = 'success';
        $book_create_message = 'Book added successfully!';
    } catch (Exception $e) { 
        $book_create_status = 'error';
        $book_create_message = $e->getMessage();
    }
}

// Add Book Copy
if (isset($_POST['add_copy'])) {
    try {
        $book_id = (int)$_POST['book_id'];
        $book_status = $_POST['status'];
        
        $con->insertBookCopy($book_id, $book_status);
        
        $book_create_status = 'success';
        $book_create_message = 'Book copy added successfully!';
    } catch (PDOException $e) { 
        $book_create_status = 'error';
        $book_create_message = $e->getMessage();
    }
}

// Delete Book
if (isset($_POST['delete_books'])) {
    $book_id = (int)$_POST['book_id'];
    $book_title = $_POST['book_title'];

    try {
        $con->deletebooks($book_id);
        $_SESSION['success_message'] = '"' . htmlspecialchars($book_title) . '" deleted successfully';
        header('Location: books.php');
        exit();
    } catch (PDOException $e) {
        $error_message = "Cannot delete this book: " . $e->getMessage();
    }
}
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
    <a class="navbar-brand fw-semibold" href="admin-dashboard.html">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBooks">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navBooks" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="admin-dashboard.html">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link" href="borrowers.html">Borrowers</a></li>
        <li class="nav-item"><a class="nav-link" href="authors-genres.php">Authors & Genres</a></li>
        <li class="nav-item"><a class="nav-link" href="checkout.html">Checkout</a></li>
        <li class="nav-item"><a class="nav-link" href="return.html">Return</a></li>
        <li class="nav-item"><a class="nav-link" href="catalog.html">Catalog</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark">Role: ADMIN</span>
        <a class="btn btn-sm btn-outline-secondary" href="login.html">Logout</a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
  <!-- Success/Error Messages -->
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?php echo htmlspecialchars($error_message); unset($error_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($book_create_status === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> <?php echo htmlspecialchars($book_create_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($book_create_status === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?php echo htmlspecialchars($book_create_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-3">
    <!-- Left Column - Forms -->
    <div class="col-12 col-lg-4">
      <div class="card p-4">
        <h5 class="mb-1">Add Book</h5>
        <p class="small text-muted mb-3">Creates a row in <b>Books</b>.</p>
        <form action="" method="POST">
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input class="form-control" name="book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" type="number" min="1500" max="2100">
          </div>
          <div class="mb-3">
            <label class="form-label">Edition</label>
            <input class="form-control" name="book_edition">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher">
          </div>
          <button name="add_book" class="btn btn-primary w-100" type="submit">Save Book</button>
        </form>
      </div>

      <div class="card p-4 mt-3">
        <h6 class="mb-2">Add Copy</h6>
        <p class="small text-muted mb-3">Creates a row in <b>BookCopy</b>.</p>
        <form action="" method="POST">
          <div class="mb-3">
            <label class="form-label">Book <span class="text-danger">*</span></label>
            <select class="form-select" name="book_id" required>
              <option value="">Select book</option>
              <?php 
              $allbooks = $con->viewbooks();
              foreach($allbooks as $book): ?>
                <option value="<?php echo $book['book_id']; ?>">
                  [<?php echo $book['book_id']; ?>] <?php echo htmlspecialchars($book['book_title']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select" name="status" required>
              <option value="AVAILABLE">AVAILABLE</option>
              <option value="ON_LOAN">ON_LOAN</option>
              <option value="LOST">LOST</option>
              <option value="DAMAGED">DAMAGED</option>
              <option value="REPAIR">REPAIR</option>
            </select>
          </div>
          <button name="add_copy" class="btn btn-outline-primary w-100" type="submit">Add Copy</button>
        </form>
      </div>
    </div>

    <!-- Right Column - Table & Assign Forms -->
    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
          <div>
            <h5 class="mb-1">Books List</h5>
            <div class="small text-muted"><?php echo count($con->viewbooks()); ?> books found</div>
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
              $viewbooks = $con->viewbooks();
              foreach($viewbooks as $row): ?>
              <tr>
                <td><?php echo $row['book_id']; ?></td>
                <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                <td><?php echo htmlspecialchars($row['book_isbn'] ?? ''); ?></td>
                <td><?php echo $row['book_publication_year'] ?: '-'; ?></td>
                <td><?php echo htmlspecialchars($row['book_publisher'] ?? ''); ?></td>
                <td><?php echo $row['Copies'] ?: 0; ?></td>
                <td>
                  <?php if(($row['Available_Copies'] ?? 0) > 0): ?>
                    <span class="badge bg-success"><?php echo $row['Available_Copies']; ?></span>
                  <?php else: ?>
                    <span class="badge bg-danger">0</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" 
                          data-bs-toggle="modal" 
                          data-bs-target="#editBookModal"
                          data-book-id="<?php echo $row['book_id']; ?>"
                          data-book-title="<?php echo htmlspecialchars($row['book_title'], ENT_QUOTES); ?>"
                          data-book-isbn="<?php echo htmlspecialchars($row['book_isbn'] ?? '', ENT_QUOTES); ?>"
                          data-book-year="<?php echo $row['book_publication_year'] ?? ''; ?>"
                          data-book-edition="<?php echo htmlspecialchars($row['book_edition'] ?? '', ENT_QUOTES); ?>"
                          data-book-publisher="<?php echo htmlspecialchars($row['book_publisher'] ?? '', ENT_QUOTES); ?>">
                    Edit
                  </button>
                  <button class="btn btn-sm btn-outline-danger" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteBookModal"
                          data-book-id="<?php echo $row['book_id']; ?>"
                          data-book-title="<?php echo htmlspecialchars($row['book_title'], ENT_QUOTES); ?>">
                    Delete
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <hr class="my-4">

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Author to Book</h6>
              <p class="small text-muted mb-3">Creates a row in <b>BookAuthors</b>.</p>
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php foreach($viewbooks as $book): ?>
                      <option value="<?php echo $book['book_id']; ?>">
                        [<?php echo $book['book_id']; ?>] <?php echo htmlspecialchars($book['book_title']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="author_id" required>
                    <option value="">Select author</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Genre to Book</h6>
              <p class="small text-muted mb-3">Creates a row in <b>BookGenre</b>.</p>
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php foreach($viewbooks as $book): ?>
                      <option value="<?php echo $book['book_id']; ?>">
                        [<?php echo $book['book_id']; ?>] <?php echo htmlspecialchars($book['book_title']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="genre_id" required>
                    <option value="">Select genre</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
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
      <form method="POST" action="">
        <div class="modal-body">
          <input type="hidden" name="book_id" id="edit_book_id">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="book_title" id="edit_book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" id="edit_book_isbn">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" id="edit_book_year" type="number" min="1500" max="2100">
          </div>
          <div class="mb-3">
            <label class="form-label">Edition</label>
            <input class="form-control" name="book_edition" id="edit_book_edition">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" id="edit_book_publisher">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_book" class="btn btn-primary">Update Book</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Book Modal -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="delete_book_title"></strong>?</p>
        <p class="text-danger small">This action cannot be undone and will also delete all associated copies.</p>
        <form method="POST">
          <input type="hidden" name="book_id" id="delete_book_id">
          <input type="hidden" name="book_title" id="delete_book_title_input">
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="delete_books" class="btn btn-danger">Delete Book</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<script src="../sweetalert/dist/sweetalert2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Delete Modal
  const deleteModal = document.getElementById('deleteBookModal');
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const bookId = button.getAttribute('data-book-id');
    const bookTitle = button.getAttribute('data-book-title');
    
    document.getElementById('delete_book_id').value = bookId;
    document.getElementById('delete_book_title_input').value = bookTitle;
    document.getElementById('delete_book_title').textContent = bookTitle;
  });

  // Edit Modal
  const editModal = document.getElementById('editBookModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    
    document.getElementById('edit_book_id').value = button.getAttribute('data-book-id');
    document.getElementById('edit_book_title').value = button.getAttribute('data-book-title');
    document.getElementById('edit_book_isbn').value = button.getAttribute('data-book-isbn');
    document.getElementById('edit_book_year').value = button.getAttribute('data-book-year');
    document.getElementById('edit_book_edition').value = button.getAttribute('data-book-edition');
    document.getElementById('edit_book_publisher').value = button.getAttribute('data-book-publisher');
  });
});
</script>
</body>
</html>
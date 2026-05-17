<?php
require_once('../classes/database.php');
$con = new database();

$authorStatus = null;
$authorMessage = '';
$genreStatus = null;
$genreMessage = '';

if (isset($_POST['add_author'])){
  $firstname = trim($_POST['author_firstname'] ?? '');
  $lastname = trim($_POST['author_lastname'] ?? '');
  $birthYearInput = trim($_POST['author_birth_year'] ?? '');
  $nationalityInput = trim($_POST['author_nationality'] ?? '');

  $birthYear = $birthYearInput === '' ? null: (int) $birthYearInput;

  $nationality = $nationalityInput === '' ? null: $nationalityInput;

  if ($firstname === '' || $lastname === ''){
    $authorStatus = 'error';
    $authorMessage = 'Author first name and last name are required';
  } else{
    try{
      $con->insertAuthor($firstname, $lastname, $birthYear, $nationality);
      $authorStatus = 'success';
      $authorMessage = 'Author is added successfully.';

    } catch(Exception $e){
      $authorStatus = 'error';
      $authorMessage = 'Error adding author: '. $e->getMessage();
    }
  }
}

if (isset($_POST['add_genre'])){
  $genreName = trim($_POST['genre_name'] ?? '');
    if($genreName === ''){
      $genreStatus = 'error';
      $genreMessage = 'Genre name is required.';
    } else{
      try{
        $con->insertGenre($genreName);
        $genreStatus = 'success';
        $genreMessage = 'Genre is added successfully.';
      }catch(Exception $e){
        $genreStatus = 'error';
        $genreMessage = 'Error adding genre: '. $e->getMessage();
      }
    }
  }

if(isset($_POST['delete_author'])){
    try {
        $con->deleteAuthor($_POST['author_id']);
        $authorStatus = 'success';
        $authorMessage = 'Author deleted successfully.';
    } catch (Exception $e) {
        $authorStatus = 'error';
        $authorMessage = 'Cannot delete author. ' . $e->getMessage();
    }
}

if(isset($_POST['delete_genre'])){
    try {
        $con->deleteGenre($_POST['genre_id']);
        $genreStatus = 'success';
        $genreMessage = 'Genre deleted successfully.';
    } catch (Exception $e) {
        $genreStatus = 'error';
        $genreMessage = 'Cannot delete genre. ' . $e->getMessage();
    }
}

$allAuthors = $con->viewAuthors();
$allGenres = $con->viewGenres();

$countAuthors = $con->countAuthors();
$countGenres = $con->countGenres();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Authors and Genres - Admin (Teaching Demo)</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdminStatic">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navAdminStatic" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link active" href="authors-genres.html">Authors &amp; Genres</a></li>
        <li class="nav-item"><a class="nav-link" href="borrowers.php">Borrowers</a></li>
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

    <div class="col-12 col-lg-6">
      <div class="card p-4 h-100">
        <h5 class="mb-1">Add Author</h5>
        <p class="small-muted mb-3">Sample form for the Authors table.</p>

        <?php if($authorStatus === 'error') { ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $authorMessage?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php } ?>
        <?php if($authorStatus === 'success') { ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $authorMessage?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php }?>

        <form action="#" method="POST" class="row g-2">
          <div class="col-12 col-md-6">
            <label class="form-label">First Name</label>
            <input class="form-control" name="author_firstname" placeholder="e.g., Jose" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Last Name</label>
            <input class="form-control" name="author_lastname" placeholder="e.g., Rizal" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Birth Year</label>
            <input class="form-control" name="author_birth_year" type="number" min="1" max="2100" placeholder="optional" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Nationality</label>
            <input class="form-control" name="author_nationality" placeholder="optional" />
          </div>
          <div class="col-12">
            <button class="btn btn-primary w-100" type="submit" name="add_author">Save Author</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card p-4 h-100">
        <h5 class="mb-1">Add Genre</h5>
        <p class="small-muted mb-3">Sample form for the Genres table.</p>

        <?php if($genreStatus === 'error') { ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $genreMessage?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php } ?>
        <?php if($genreStatus === 'success') { ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $genreMessage?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php }?>

        <form action="#" method="POST" class="row g-2">
          <div class="col-12">
            <label class="form-label">Genre Name</label>
            <input class="form-control" name="genre_name" placeholder="e.g., Classic" required />
          </div>
          <div class="col-12">
            <button class="btn btn-outline-primary w-100" type="submit" name="add_genre">Save Genre</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Authors List</h5>
          <span class="small-muted"><?php echo $countAuthors?></span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Author ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birth Year</th>
                <th>Nationality</th>
              </tr>
            </thead>
            <tbody>
            
            <?php if(empty($allAuthors)) { ?>
             <tr>
              <td colspan="5" class="text-center small-muted" > No authors yet.</td>
            </tr>
            <?php }
            else{
              foreach($allAuthors as $author){ ?>


            <tr>
                <td><?php echo $author['author_id']?></td>
                <td><?php echo ucfirst(htmlspecialchars($author['author_firstname']))?></td>
                <td><?php echo ucfirst(htmlspecialchars($author['author_lastname']))?></td>
                <td><?php echo ucfirst(htmlspecialchars($author['author_birth_year']))?></td>
                <td><?php echo ucfirst(htmlspecialchars($author['author_nationality']))?></td>

                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            data-bs-toggle="modal" data-bs-target="#deleteAuthorModal"
                            data-id="<?= $author['author_id'] ?>" 
                            data-name="<?= htmlspecialchars($author['author_firstname'].' '.$author['author_lastname']) ?>">
                        Delete
                    </button>
                </td>

              </tr>
              
            <?php } 
            } ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Genres List</h5>
          <span class="small-muted"><?php echo $countGenres?></span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Genre ID</th>
                <th>Genre Name</th>
              </tr>
            </thead>
            <tbody>
            
             <?php if(empty($allGenres)) { ?>
             <tr>
              <td colspan="5" class="text-center small-muted" > No genres yet.</td>
            </tr>
            <?php }
            else{
              foreach($allGenres as $genres){ ?>

              <tr>
                <td><?php echo $genres['genre_id']?></td>
                <td><?php echo ucfirst(htmlspecialchars($genres['genre_name']))?></td>

                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            data-bs-toggle="modal" data-bs-target="#deleteGenreModal"
                            data-id="<?= $genres['genre_id'] ?>" 
                            data-name="<?= htmlspecialchars($genres['genre_name']) ?>">
                        Delete
                    </button>
                </td>

              </tr>
              
            <?php } //foreach
            } //else ?>
             
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="deleteAuthorModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Delete Author</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="display_author_name"></strong>?</p>
        <input type="hidden" name="author_id" id="input_author_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="delete_author" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="deleteGenreModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Delete Genre</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="display_genre_name"></strong>?</p>
        <input type="hidden" name="genre_id" id="input_genre_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="delete_genre" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('deleteAuthorModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('input_author_id').value = btn.getAttribute('data-id');
    document.getElementById('display_author_name').textContent = btn.getAttribute('data-name');
});

document.getElementById('deleteGenreModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('input_genre_id').value = btn.getAttribute('data-id');
    document.getElementById('display_genre_name').textContent = btn.getAttribute('data-name');
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
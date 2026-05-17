<?php

require_once '../Classes/database.php';
$con = new database();

// Get all dashboard metrics
$bookcount = $con->countBook();
$bookcopycount = $con->countBookCopies();
$openloanscount = $con->countOpenLoans();
$overduecount = $con->countOverdueItems();
$recentloans = $con->viewloans();

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard — Library</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navAdmin" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link active" href="admin-dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link" href="borrowers.php">Borrowers</a></li>
        <li class="nav-item"><a class="nav-link" href="authors-genres.php">Authors & Genres</a></li>
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
    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <h5 class="mb-1">Quick Overview</h5>
        <p class="small-muted mb-4">These are live values connected to your database.</p>

        <div class="row g-3">
          <!-- Total Books -->
          <div class="col-6 col-md-3">
            <div class="border rounded p-3 bg-white">
              <div class="small-muted">Total Books</div>
              <div class="fs-4 fw-semibold"><?php echo isset($bookcount) ? $bookcount : '0'; ?></div>
            </div>
          </div>
          
          <!-- Total Copies -->
          <div class="col-6 col-md-3">
            <div class="border rounded p-3 bg-white">
              <div class="small-muted">Total Copies</div>
              <div class="fs-4 fw-semibold"><?php echo isset($bookcopycount) ? $bookcopycount : '0'; ?></div>
            </div>
          </div>
          
          <!-- Open Loans -->
          <div class="col-6 col-md-3">
            <div class="border rounded p-3 bg-white">
              <div class="small-muted">Open Loans</div>
              <div class="fs-4 fw-semibold"><?php echo isset($openloanscount) ? $openloanscount : '0'; ?></div>
            </div>
          </div>
          
          <!-- Overdue Items -->
          <div class="col-6 col-md-3">
            <div class="border rounded p-3 bg-white">
              <div class="small-muted">Overdue Items</div>
              <div class="fs-4 fw-semibold"><?php echo isset($overduecount) ? $overduecount : '0'; ?></div>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <h6 class="mb-2">Recent Loans (with Processor)</h6>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Loan ID</th>
                <th>Borrower</th>
                <th>Status</th>
                <th>Loan Date</th>
                <th>Processed By (User)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (is_array($recentloans) && count($recentloans) > 0): ?>
                <?php foreach ($recentloans as $loan): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($loan['loan_id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($loan['borrower_name'] ?? ''); ?></td>
                    <td>
                      <?php if (isset($loan['loan_status']) && strtolower($loan['loan_status']) === 'open'): ?>
                        <span class="badge" style="background-color: #ffc107; color: #000;">OPEN</span>
                      <?php elseif (isset($loan['loan_status']) && strtolower($loan['loan_status']) === 'closed'): ?>
                        <span class="badge" style="background-color: #28a745; color: #fff;">CLOSED</span>
                      <?php else: ?>
                        <span class="badge" style="background-color: #6c757d; color: #fff;"><?php echo htmlspecialchars($loan['loan_status'] ?? 'Unknown'); ?></span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo isset($loan['loan_date']) ? date('Y-m-d', strtotime($loan['loan_date'])) : ''; ?></td>
                    <td><?php echo htmlspecialchars($loan['processed_by'] ?? 'N/A'); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted">No loans found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card p-4">
        <h6 class="mb-3">Admin Shortcuts</h6>
        <div class="d-grid gap-2">
          <a class="btn btn-primary" href="checkout.php">Process Checkout</a>
          <a class="btn btn-outline-primary" href="return.php">Process Return</a>
          <a class="btn btn-outline-secondary" href="books.php">Manage Books</a>
          <a class="btn btn-outline-secondary" href="borrowers.php">Manage Borrowers</a>
        </div>
        <hr class="my-4">
        <div class="small-muted">
          Reminder: every checkout must record <b>processed_by_user_id</b> (ADMIN).
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
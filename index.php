<?php
include 'koneksi.php';

$search        = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? 'all';
$group_mode    = $_GET['group_mode'] ?? 'year-month';

$query = "SELECT t.*, s.name as status_name 
          FROM transactions t 
          LEFT JOIN statuses s ON t.status = s.id 
          WHERE 1=1";

if ($search !== '') {
    $search_esc = $conn->real_escape_string($search);
    $query .= " AND (t.productName LIKE '%$search_esc%' OR t.customerName LIKE '%$search_esc%' OR t.productID LIKE '%$search_esc%')";
}

if ($status_filter !== 'all') {
    $status_esc = (int)$status_filter;
    $query .= " AND t.status = $status_esc";
}

$query .= " ORDER BY t.transactionDate DESC";
$result = $conn->query($query);

$status_result = $conn->query("SELECT * FROM statuses");
$statuses = [];
while ($s = $status_result->fetch_assoc()) {
    $statuses[] = $s;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: #212529; color: #fff; z-index: 100; }
        .sidebar .brand { font-size: 1.1rem; font-weight: bold; padding: 20px; background: #1a1e21; border-bottom: 1px solid #373b3e; }
        .sidebar .nav-link { color: #adb5bd; padding: 12px 20px; margin: 4px 10px; border-radius: 6px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 260px; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<div class="d-flex flex-column flex-md-row">
    <div class="sidebar shadow">
        <div class="brand d-flex align-items-center gap-2">
            <i class="bi bi-layers-fill text-primary fs-4"></i>
            <span>Dashboard</span>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="index.php" class="nav-link active">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a href="add.php" class="nav-link">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Transaksi
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content w-100">
        <nav class="navbar navbar-expand navbar-light bg-white rounded shadow-sm px-4 mb-4">
            <span class="navbar-brand mb-0 h5 text-secondary">
                <i class="bi bi-house-door me-2"></i> Manajemen Data Transaksi
            </span>
        </nav>

        <div class="card border-0 shadow-sm p-3 mb-4">
            <form method="GET" action="index.php" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Cari produk / customer..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status_filter" onchange="this.form.submit()">
                        <option value="all">Semua Status</option>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= $status_filter == $st['id'] ? 'selected' : '' ?>><?= $st['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select border-primary text-primary fw-semibold" name="group_mode" onchange="this.form.submit()">
                        <option value="year-month" <?= $group_mode == 'year-month' ? 'selected' : '' ?>>Tahun & Bulan</option>
                        <option value="year" <?= $group_mode == 'year' ? 'selected' : '' ?>>Tahun</option>
                        <option value="month" <?= $group_mode == 'month' ? 'selected' : '' ?>>Bulan</option>
                        <option value="status" <?= $group_mode == 'status' ? 'selected' : '' ?>>Status</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-outline-primary w-100 mb-1">Filter</button>
                </div>
            </form>
        </div>

        <?php
        $grouped = [];
        $total_amount = 0;
        $total_count = 0;

        while ($row = $result->fetch_assoc()) {
            $total_count++;
            
            if ($row['status'] == 0) {
                $total_amount += $row['amount'];
            }

            $date = new DateTime($row['transactionDate']);
            $year = $date->format('Y');
            $month = $date->format('F');

            if ($group_mode === 'year') {
                $key = "Tahun: $year";
            } elseif ($group_mode === 'month') {
                $key = "Bulan: $month";
            } elseif ($group_mode === 'status') {
                $key = "Status: " . $row['status_name'];
            } else {
                $key = "Periode: $year - $month";
            }

            $grouped[$key][] = $row;
        }
        ?>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-primary text-white p-3">
                    <h6 class="text-uppercase mb-1">Total Transaksi</h6>
                    <h3 class="fw-bold mb-0"><?= $total_count ?> Transaksi</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-success text-white p-3">
                    <h6 class="text-uppercase mb-1">Total Amount</h6>
                    <h3 class="fw-bold mb-0">Rp <?= number_format($total_amount, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <?php if (empty($grouped)): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-folder2-open display-4 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data transaksi ditemukan.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($grouped as $group_name => $rows): ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-collection me-2"></i> <?= $group_name ?></span>
                    <span class="badge bg-light text-dark"><?= count($rows) ?> Item</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Transaction Date</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><span class="fw-semibold"><?= htmlspecialchars($r['productID']) ?></span></td>
                                        <td><?= htmlspecialchars($r['productName']) ?></td>
                                        <td>Rp <?= number_format($r['amount'], 0, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($r['customerName']) ?></td>
                                        <td>
                                            <span class="badge <?= $r['status'] == 0 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $r['status_name'] ?>
                                            </span>
                                        </td>
                                        <td><?= $r['transactionDate'] ?></td>
                                        <td class="text-center">
                                            <a href="detail.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info text-white me-1" title="Detail"><i class="bi bi-eye"></i></a>
                                            <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

</body>
</html>
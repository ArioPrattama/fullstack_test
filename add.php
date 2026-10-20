<?php
include 'koneksi.php';

$status_result = $conn->query("SELECT * FROM statuses");
$statuses = [];
while ($s = $status_result->fetch_assoc()) {
    $statuses[] = $s;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID       = $_POST['productID'];
    $productName     = $_POST['productName'];
    $amount          = $_POST['amount'];
    $customerName    = $_POST['customerName'];
    $status          = $_POST['status'];
    $transactionDate = str_replace('T', ' ', $_POST['transactionDate']) . ':00';
    $createBy        = 'admin';
    $createOn        = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO transactions (productID, productName, amount, customerName, status, transactionDate, createBy, createOn) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsisss", $productID, $productName, $amount, $customerName, $status, $transactionDate, $createBy, $createOn);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 700px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white fw-bold py-3">
            <i class="bi bi-plus-circle me-2"></i> Form Tambah Transaksi Baru
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Product ID</label><input type="text" class="form-control" name="productID" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" name="productName" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="1000" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Customer Name</label><input type="text" class="form-control" name="customerName" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= $st['id'] ?>"><?= $st['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Transaction Date</label><input type="datetime-local" class="form-control" name="transactionDate" required></div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary px-4">Kembali</a>
                    <button type="submit" class="btn btn-success px-4">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
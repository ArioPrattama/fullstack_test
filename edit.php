<?php
include 'koneksi.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

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

    $update_stmt = $conn->prepare("UPDATE transactions SET productID=?, productName=?, amount=?, customerName=?, status=?, transactionDate=? WHERE id=?");
    $update_stmt->bind_param("ssdsssi", $productID, $productName, $amount, $customerName, $status, $transactionDate, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}

$formatted_date = date('Y-m-d\TH:i', strtotime($data['transactionDate']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
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
        <div class="card-header bg-warning text-dark fw-bold py-3">
            <i class="bi bi-pencil-square me-2"></i> Form Edit Transaksi #<?= $data['id'] ?>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Product ID</label><input type="text" class="form-control" name="productID" value="<?= htmlspecialchars($data['productID']) ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" name="productName" value="<?= htmlspecialchars($data['productName']) ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" step="0.01" class="form-control" name="amount" value="<?= $data['amount'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Customer Name</label><input type="text" class="form-control" name="customerName" value="<?= htmlspecialchars($data['customerName']) ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= $st['id'] ?>" <?= $data['status'] == $st['id'] ? 'selected' : '' ?>><?= $st['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Transaction Date</label><input type="datetime-local" class="form-control" name="transactionDate" value="<?= $formatted_date ?>" required></div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Perbarui Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
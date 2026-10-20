<?php
include 'koneksi.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT t.*, s.name as status_name FROM transactions t LEFT JOIN statuses s ON t.status = s.id WHERE t.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white fw-bold py-3">
            <i class="bi bi-info-circle me-2"></i> Detail Informasi Transaksi
        </div>
        <div class="card-body p-4">
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr><th class="w-40 bg-light">ID Transaksi</th><td><?= $data['id'] ?></td></tr>
                    <tr><th class="bg-light">Product ID</th><td><?= htmlspecialchars($data['productID']) ?></td></tr>
                    <tr><th class="bg-light">Product Name</th><td><?= htmlspecialchars($data['productName']) ?></td></tr>
                    <tr><th class="bg-light">Amount</th><td>Rp <?= number_format($data['amount'], 0, ',', '.') ?></td></tr>
                    <tr><th class="bg-light">Customer Name</th><td><?= htmlspecialchars($data['customerName']) ?></td></tr>
                    <tr><th class="bg-light">Status</th><td><span class="badge <?= $data['status'] == 0 ? 'bg-success' : 'bg-danger' ?>"><?= $data['status_name'] ?></span></td></tr>
                    <tr><th class="bg-light">Transaction Date</th><td><?= $data['transactionDate'] ?></td></tr>
                    <tr><th class="bg-light">Created By</th><td><?= htmlspecialchars($data['createBy']) ?></td></tr>
                    <tr><th class="bg-light">Created On</th><td><?= $data['createOn'] ?></td></tr>
                </tbody>
            </table>
            <a href="index.php" class="btn btn-secondary w-100 py-2">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
</body>
</html>
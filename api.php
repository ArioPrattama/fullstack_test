<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
include 'koneksi.php';

$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $query = "SELECT t.*, s.name as status_name FROM transactions t LEFT JOIN statuses s ON t.status = s.id ORDER BY t.transactionDate DESC";
    $result = $conn->query($query);
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    $statusResult = $conn->query("SELECT * FROM statuses");
    $statuses = [];
    while ($row = $statusResult->fetch_assoc()) {
        $statuses[] = $row;
    }

    echo json_encode(["data" => $transactions, "status" => $statuses]);
} 
elseif ($action === 'detail') {
    $id = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
} 
elseif ($action === 'add') {
    $productID       = $_POST['productID'];
    $productName     = $_POST['productName'];
    $amount          = $_POST['amount'];
    $customerName    = $_POST['customerName'];
    $status          = $_POST['status'];
    $transactionDate = $_POST['transactionDate'];
    $createBy        = $_POST['createBy'] ?? 'admin';
    $createOn        = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO transactions (productID, productName, amount, customerName, status, transactionDate, createBy, createOn) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsisss", $productID, $productName, $amount, $customerName, $status, $transactionDate, $createBy, $createOn);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Data berhasil ditambahkan"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menambah data"]);
    }
} 
elseif ($action === 'update') {
    $id              = $_POST['id'];
    $productID       = $_POST['productID'];
    $productName     = $_POST['productName'];
    $amount          = $_POST['amount'];
    $customerName    = $_POST['customerName'];
    $status          = $_POST['status'];
    $transactionDate = $_POST['transactionDate'];

    $stmt = $conn->prepare("UPDATE transactions SET productID=?, productName=?, amount=?, customerName=?, status=?, transactionDate=? WHERE id=?");
    $stmt->bind_param("ssdsssi", $productID, $productName, $amount, $customerName, $status, $transactionDate, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Data berhasil diperbarui"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal memperbarui data"]);
    }
}
?>
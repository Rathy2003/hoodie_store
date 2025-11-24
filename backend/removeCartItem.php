<?php 
include 'getTotalPayment.php';

session_start();

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$key = $data['key'];

if (isset($_SESSION['cartItems'][$key])) {
    unset($_SESSION['cartItems'][$key]);
    $_SESSION['cartItems'] = array_values($_SESSION['cartItems']);
}

$count = count($_SESSION['cartItems']);
$total = getTotalPayment($_SESSION['cartItems']);

echo json_encode([
    'cartItems' => $_SESSION['cartItems'],
    'count' => $count,
    'total' => $total
]);

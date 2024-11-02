<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    if ($type === 'supplier') {
        deleteSupplier($pdo, $id);
    } elseif ($type === 'computer_part') {
        deleteComputerPart($pdo, $id);
    } elseif ($type === 'customer') {
        deleteCustomer($pdo, $id);
    } elseif ($type === 'order') {
        deleteOrder($pdo, $id);
    }
    header("Location: index.php"); // Redirect to the main page after deletion
    exit();
}

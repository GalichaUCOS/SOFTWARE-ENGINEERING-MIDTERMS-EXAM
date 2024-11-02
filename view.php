<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    if ($type === 'supplier') {
        $supplier = getSupplierByID($pdo, $id);
    } elseif ($type === 'computer_part') {
        $computerPart = getComputerPartByID($pdo, $id);
    } elseif ($type === 'customer') {
        $customer = getCustomerByID($pdo, $id);
    } elseif ($type === 'order') {
        $order = getOrderByID($pdo, $id);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View <?php echo ucfirst($type); ?></title>
</head>
<body>
    <h1>Details of <?php echo ucfirst($type); ?></h1>
    <?php if ($type === 'supplier'): ?>
        <p>Supplier Name: <?php echo $supplier['supplier_name']; ?></p>
        <p>Contact Info: <?php echo $supplier['contact_info']; ?></p>
        <p>Address: <?php echo $supplier['address']; ?></p>
    <?php elseif ($type === 'computer_part'): ?>
        <p>Part Name: <?php echo $computerPart['part_name']; ?></p>
        <p>Price: <?php echo $computerPart['price']; ?></p>
        <p>Stock: <?php echo $computerPart['stock']; ?></p>
        <p>Supplier: <?php echo getSupplierByID($pdo, $computerPart['supplier_id'])['supplier_name']; ?></p>
    <?php elseif ($type === 'customer'): ?>
        <p>First Name: <?php echo $customer['first_name']; ?></p>
        <p>Last Name: <?php echo $customer['last_name']; ?></p>
        <p>Email: <?php echo $customer['email']; ?></p>
        <p>Phone Number: <?php echo $customer['phone_number']; ?></p>
    <?php elseif ($type === 'order'): ?>
        <p>Order Date: <?php echo $order['order_date']; ?></p>
        <p>Customer: <?php echo getCustomerByID($pdo, $order['customer_id'])['first_name'] . ' ' . getCustomerByID($pdo, $order['customer_id'])['last_name']; ?></p>
        <p>Computer Part: <?php echo getComputerPartByID($pdo, $order['computer_part_id'])['part_name']; ?></p>
        <p>Quantity: <?php echo $order['quantity']; ?></p>
    <?php endif; ?>
    <a href="index.php">Back to Home</a>
</body>
</html>

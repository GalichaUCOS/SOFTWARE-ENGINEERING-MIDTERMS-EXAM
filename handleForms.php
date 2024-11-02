<?php
// handleForms.php

include 'dbConfig.php'; // Ensure this includes your PDO connection
include 'models.php'; // Include your models

// Handle form submissions for both insert and update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle supplier insertion
    if (isset($_POST['insert_supplier'])) {
        $supplier_name = $_POST['supplier_name'];
        $contact_info = $_POST['contact_info'];
        $address = $_POST['address'];
        insertSupplier($pdo, $supplier_name, $contact_info, $address);
    }

    // Handle customer insertion
    if (isset($_POST['insert_customer'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        insertCustomer($pdo, $first_name, $last_name, $email, $phone_number);
    }

    // Handle computer part insertion
    if (isset($_POST['insert_computer_part'])) {
        $part_name = $_POST['part_name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $supplier_id = $_POST['supplier_id'];
        insertComputerPart($pdo, $part_name, $price, $stock, $supplier_id);
    }

    // Handle order insertion
    if (isset($_POST['insert_order'])) {
        $order_date = $_POST['order_date'];
        $customer_id = $_POST['customer_id'];
        $computer_part_id = $_POST['computer_part_id'];
        $quantity = $_POST['quantity'];
        insertOrder($pdo, $order_date, $customer_id, $computer_part_id, $quantity, $price);
    }

    // Handle updates for suppliers
    if (isset($_POST['updateSupplierBtn'])) {
        $id = $_POST['id'];
        $supplier_name = $_POST['supplier_name'];
        $contact_info = $_POST['contact_info'];
        $address = $_POST['address'];
        updateSupplier($pdo, $id, $supplier_name, $contact_info, $address);
    }

    // Handle updates for computer parts
    if (isset($_POST['updateComputerPartBtn'])) {
        $id = $_POST['id'];
        $part_name = $_POST['part_name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $supplier_id = $_POST['supplier_id'];
        updateComputerPart($pdo, $id, $part_name, $price, $stock, $supplier_id);
    }

    // Handle updates for customers
    if (isset($_POST['updateCustomerBtn'])) {
        $id = $_POST['id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        updateCustomer($pdo, $id, $first_name, $last_name, $email, $phone_number);
    }

    // Handle updates for orders
    if (isset($_POST['updateOrderBtn'])) {
        $id = $_POST['id'];
        $order_date = $_POST['order_date'];
        $customer_id = $_POST['customer_id'];
        $computer_part_id = $_POST['computer_part_id'];
        $quantity = $_POST['quantity'];
        updateOrderWithTracking($pdo, $id, $order_date, $customer_id, $computer_part_id, $quantity);
    }

    // Redirect to index.php after handling the form submission
    header('Location: index.php');
    exit(); // Make sure to exit after the redirect
}

// Fetch data for displaying
$customers = getAllCustomers($pdo);
$suppliers = getAllSuppliers($pdo);
$computerParts = getAllComputerParts($pdo);
$orders = getAllOrdersWithDetails($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Management System</title>
</head>
<body>

<h1>Item Management System</h1>

<!-- Insert forms for Suppliers, Customers, Computer Parts, and Orders -->

<!-- Latest Order Receipt -->
<h2>Latest Order Receipt</h2>
<?php if (!empty($orders)): ?>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Part Name</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Actions</th> <!-- Actions column added -->
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['first_name'] . ' ' . $order['last_name'] ?></td>
                <td><?= $order['part_name'] ?></td>
                <td><?= $order['quantity'] ?></td>
                <td><?= number_format($order['total_price'], 2) ?></td>
                <td>
                    <a href="edit.php?type=order&id=<?= $order['order_id'] ?>">Edit</a> | 
                    <a href="delete.php?type=order&id=<?= $order['order_id'] ?>" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No orders available.</p>
<?php endif; ?>

<!-- Display Customers Table -->
<h2>Customers</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Actions</th> <!-- Actions column added -->
    </tr>
    <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= $customer['id'] ?></td>
            <td><?= $customer['first_name'] ?></td>
            <td><?= $customer['last_name'] ?></td>
            <td><?= $customer['email'] ?></td>
            <td><?= $customer['phone_number'] ?></td>
            <td>
                <a href="edit.php?type=customer&id=<?= $customer['id'] ?>">Edit</a> | 
                <a href="delete.php?type=customer&id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Display Suppliers Table -->
<h2>Suppliers</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Supplier Name</th>
        <th>Contact Info</th>
        <th>Address</th>
        <th>Actions</th> <!-- Actions column added -->
    </tr>
    <?php foreach ($suppliers as $supplier): ?>
        <tr>
            <td><?= $supplier['id'] ?></td>
            <td><?= $supplier['supplier_name'] ?></td>
            <td><?= $supplier['contact_info'] ?></td>
            <td><?= $supplier['address'] ?></td>
            <td>
                <a href="edit.php?type=supplier&id=<?= $supplier['id'] ?>">Edit</a> | 
                <a href="delete.php?type=supplier&id=<?= $supplier['id'] ?>" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Display Computer Parts Table -->
<h2>Computer Parts</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Part Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Supplier ID</th>
        <th>Actions</th> <!-- Actions column added -->
    </tr>
    <?php foreach ($computerParts as $part): ?>
        <tr>
            <td><?= $part['id'] ?></td>
            <td><?= $part['part_name'] ?></td>
            <td><?= number_format($part['price'], 2) ?></td>
            <td><?= $part['stock'] ?></td>
            <td><?= $part['supplier_id'] ?></td>
            <td>
                <a href="edit.php?type=computer_part&id=<?= $part['id'] ?>">Edit</a> | 
                <a href="delete.php?type=computer_part&id=<?= $part['id'] ?>" onclick="return confirm('Are you sure you want to delete this part?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
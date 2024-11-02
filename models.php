<?php
// models.php

// Function to insert a new supplier
function insertSupplier($pdo, $supplier_name, $contact_info, $address) {
    $stmt = $pdo->prepare("INSERT INTO Suppliers (supplier_name, contact_info, address) VALUES (?, ?, ?)");
    return $stmt->execute([$supplier_name, $contact_info, $address]);
}

function deleteSupplier($pdo, $id) {
    // First, delete all computer parts associated with the supplier
    $stmt = $pdo->prepare("SELECT id FROM ComputerParts WHERE supplier_id = :supplier_id");
    $stmt->execute(['supplier_id' => $id]);
    $computerParts = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete orders associated with each computer part before deleting the computer parts
    if ($computerParts) {
        $placeholders = implode(',', array_fill(0, count($computerParts), '?'));
        $stmt = $pdo->prepare("DELETE FROM orders WHERE computer_part_id IN ($placeholders)");
        $stmt->execute($computerParts);

        // Then delete the computer parts
        $stmt = $pdo->prepare("DELETE FROM ComputerParts WHERE supplier_id = :supplier_id");
        $stmt->execute(['supplier_id' => $id]);
    }

    // Finally, delete the supplier
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Function to Update a supplier
function updateSupplier($pdo, $id, $supplier_name, $contact_info, $address) {
    $stmt = $pdo->prepare("UPDATE Suppliers SET supplier_name = ?, contact_info = ?, address = ? WHERE id = ?");
    return $stmt->execute([$supplier_name, $contact_info, $address, $id]);
}

// Function to insert a new computer part
function insertComputerPart($pdo, $part_name, $price, $stock, $supplier_id) {
    $stmt = $pdo->prepare("INSERT INTO ComputerParts (part_name, price, stock, supplier_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$part_name, $price, $stock, $supplier_id]);
}

function deleteComputerPart($pdo, $id) {
    // Delete orders associated with the computer part
    $stmt = $pdo->prepare("DELETE FROM orders WHERE computer_part_id = :computer_part_id");
    $stmt->execute(['computer_part_id' => $id]);

    // Then delete the computer part
    $stmt = $pdo->prepare("DELETE FROM ComputerParts WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

//Function to update a computer part
function updateComputerPart($pdo, $id, $part_name, $price, $stock, $supplier_id) {
    $stmt = $pdo->prepare("UPDATE ComputerParts SET part_name = ?, price = ?, stock = ?, supplier_id = ? WHERE id = ?");
    return $stmt->execute([$part_name, $price, $stock, $supplier_id, $id]);
}

// Function to insert a new customer
function insertCustomer($pdo, $first_name, $last_name, $email, $phone_number) {
    $stmt = $pdo->prepare("INSERT INTO Customers (first_name, last_name, email, phone_number) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$first_name, $last_name, $email, $phone_number]);
}

function deleteCustomer($pdo, $id) {
    // Delete orders associated with the customer
    $stmt = $pdo->prepare("DELETE FROM orders WHERE customer_id = :customer_id");
    $stmt->execute(['customer_id' => $id]);

    // Then delete the customer
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Function to update a customer
function updateCustomer($pdo, $id, $first_name, $last_name, $email, $phone_number) {
    $stmt = $pdo->prepare("UPDATE Customers SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?");
    return $stmt->execute([$first_name, $last_name, $email, $phone_number, $id]);
}

function insertOrder($pdo, $order_date, $customer_id, $computer_part_id, $quantity, $added_by) {
    $stmt = $pdo->prepare("
        INSERT INTO Orders (order_date, customer_id, computer_part_id, quantity, added_by, last_updated) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    return $stmt->execute([$order_date, $customer_id, $computer_part_id, $quantity, $added_by]);
}

function deleteOrder($pdo, $id) {
    // Delete the order directly
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute(['id' => $id]);
}



// Function to update an order with tracking
function updateOrderWithTracking($pdo, $id, $order_date, $customer_id, $computer_part_id, $quantity) {
    try {
        $stmt = $pdo->prepare("UPDATE Orders SET order_date = ?, customer_id = ?, computer_part_id = ?, quantity = ?, last_updated = NOW() WHERE id = ?");
        return $stmt->execute([$order_date, $customer_id, $computer_part_id, $quantity, $id]);
    } catch (Exception $e) {
        error_log("Error updating order: " . $e->getMessage());
        return false; // Handle error appropriately
    }
}

// Function to get all suppliers
function getAllSuppliers($pdo) {
    $stmt = $pdo->query("SELECT * FROM Suppliers");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all computer parts
function getAllComputerParts($pdo) {
    $stmt = $pdo->query("SELECT * FROM ComputerParts");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all customers
function getAllCustomers($pdo) {
    $stmt = $pdo->query("SELECT * FROM Customers");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all orders
function getAllOrders($pdo) {
    $stmt = $pdo->query("SELECT * FROM Orders");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get a supplier by ID
function getSupplierById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM Suppliers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get a computer part by ID
function getComputerPartById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM ComputerParts WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get a customer by ID
function getCustomerById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM Customers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get an order by ID
function getOrderById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllOrdersWithDetails($pdo) {
    $stmt = $pdo->prepare("
        SELECT o.id AS order_id, 
               o.order_date, 
               c.first_name AS customer_first_name, 
               c.last_name AS customer_last_name, 
               cp.part_name, 
               o.quantity, 
               (o.quantity * cp.price) AS total_price, 
               u.first_name AS added_by_first_name, 
               u.last_name AS added_by_last_name,
               o.last_updated
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        JOIN ComputerParts cp ON o.computer_part_id = cp.id
        JOIN Users u ON o.added_by = u.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function registerUser($pdo, $first_name, $last_name, $email, $password, $address, $age) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO Users (first_name, last_name, email, password, address, age) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$first_name, $last_name, $email, $hashedPassword, $address, $age]);
}

function authenticateUser($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
function getUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT username FROM Users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertOrderWithTracking($pdo, $order_date, $customer_id, $computer_part_id, $quantity, $added_by) {
    $stmt = $pdo->prepare("INSERT INTO Orders (order_date, customer_id, computer_part_id, quantity, added_by) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$order_date, $customer_id, $computer_part_id, $quantity, $added_by]);
}

function getAllOrdersWithUserTracking($pdo) {
    $stmt = $pdo->prepare("SELECT o.*, u.first_name AS added_by_name FROM Orders o JOIN Users u ON o.added_by = u.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

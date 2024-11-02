<?php
require_once 'dbConfig.php';
require_once 'models.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if a form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a supplier
    if (isset($_POST['add_supplier'])) {
        $supplier_name = $_POST['supplier_name'];
        $contact_info = $_POST['contact_info'];
        $address = $_POST['address'];
        insertSupplier($pdo, $supplier_name, $contact_info, $address);
    }

    // Handle adding a computer part
    if (isset($_POST['add_computer_part'])) {
        $part_name = $_POST['part_name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $supplier_id = $_POST['supplier_id'];
        insertComputerPart($pdo, $part_name, $price, $stock, $supplier_id);
    }

    // Handle adding a customer
    if (isset($_POST['add_customer'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        insertCustomer($pdo, $first_name, $last_name, $email, $phone_number);
    }

    // Handle adding an order
    if (isset($_POST['add_order'])) {
        $order_date = $_POST['order_date'];
        $customer_id = $_POST['customer_id'];
        $computer_part_id = $_POST['computer_part_id'];
        $quantity = $_POST['quantity'];
        $added_by = $_SESSION['user_id'];

        // Check the stock of the selected computer part
        $stmt = $pdo->prepare("SELECT stock FROM ComputerParts WHERE id = :computer_part_id");
        $stmt->execute(['computer_part_id' => $computer_part_id]);
        $part = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($part && $part['stock'] >= $quantity) {
            // Insert the order with the added_by user ID
            insertOrder($pdo, $order_date, $customer_id, $computer_part_id, $quantity, $added_by);

            // Update stock after successful order
            $newStock = $part['stock'] - $quantity;
            $updateStmt = $pdo->prepare("UPDATE ComputerParts SET stock = :new_stock WHERE id = :computer_part_id");
            $updateStmt->execute(['new_stock' => $newStock, 'computer_part_id' => $computer_part_id]);
        } else {
            $error_message = "Insufficient stock for the selected part.";
        }
    }

    // Handle order updates
    if (isset($_POST['update_order'])) {
        $order_id = $_POST['order_id'];
        $order_date = $_POST['order_date'];
        $customer_id = $_POST['customer_id'];
        $computer_part_id = $_POST['computer_part_id'];
        $quantity = $_POST['quantity'];
        $updated_by = $_SESSION['user_id'];

        $stmt = $pdo->prepare("UPDATE Orders SET order_date = ?, customer_id = ?, computer_part_id = ?, quantity = ?, added_by = ?, last_updated = NOW() WHERE id = ?");
        $stmt->execute([$order_date, $customer_id, $computer_part_id, $quantity, $updated_by, $order_id]);
    }
}

// Handle deletions
if (isset($_GET['delete'])) {
    $delete_type = $_GET['type'];
    $id = $_GET['id'];

    if ($delete_type === 'supplier') {
        deleteSupplier($pdo, $id);
    } elseif ($delete_type === 'computer_part') {
        deleteComputerPart($pdo, $id);
    } elseif ($delete_type === 'customer') {
        deleteCustomer($pdo, $id);
    } elseif ($delete_type === 'order') {
        deleteOrder($pdo, $id);
    }
}

// Retrieve data to display
$suppliers = getAllSuppliers($pdo);
$computerParts = getAllComputerParts($pdo);
$customers = getAllCustomers($pdo);
$orders = getAllOrdersWithDetails($pdo); // This function should include user tracking

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Parts Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Computer Parts Order Management System</h1>

        <!-- Display Logged-In User -->
        <div class="user-info">
            <p>Logged in as: <?php echo htmlspecialchars($_SESSION['first_name']); ?></p>
            <form method="POST" action="logout.php">
                <input type="submit" value="Logout" name="logout">
            </form>
        </div>

        <!-- Add Supplier Form -->
        <h2>Add Supplier</h2>
        <form method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="submit" name="add_supplier" value="Add Supplier">
        </form>

        <!-- Suppliers Table -->
        <h2>Suppliers</h2>
        <table>
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Contact Info</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($supplier['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['contact_info']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['address']); ?></td>
                        <td>
                            <a href="edit.php?type=supplier&id=<?php echo $supplier['id']; ?>">Edit</a>
                            <a href="?delete=true&type=supplier&id=<?php echo $supplier['id']; ?>" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add Computer Part Form -->
        <h2>Add Computer Part</h2>
        <form method="POST">
            <input type="text" name="part_name" placeholder="Part Name" required>
            <input type="number" name="price" placeholder="Price" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <select name="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['supplier_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="add_computer_part" value="Add Computer Part">
        </form>

        <!-- Computer Parts Table -->
        <h2>Computer Parts</h2>
        <table>
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($computerParts as $part): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($part['part_name']); ?></td>
                        <td><?php echo htmlspecialchars($part['price']); ?></td>
                        <td><?php echo htmlspecialchars($part['stock']); ?></td>
                        <td><?php echo htmlspecialchars(getSupplierById($pdo, $part['supplier_id'])['supplier_name']); ?></td>
                        <td>
                            <a href="edit.php?type=computer_part&id=<?php echo $part['id']; ?>">Edit</a>
                            <a href="?delete=true&type=computer_part&id=<?php echo $part['id']; ?>" onclick="return confirm('Are you sure you want to delete this part?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add Customer Form -->
        <h2>Add Customer</h2>
        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="submit" name="add_customer" value="Add Customer">
        </form>

        <!-- Customers Table -->
        <h2>Customers</h2>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone_number']); ?></td>
                        <td>
                            <a href="edit.php?type=customer&id=<?php echo $customer['id']; ?>">Edit</a>
                            <a href="?delete=true&type=customer&id=<?php echo $customer['id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add Order Form -->
        <h2>Add Order</h2>
        <form method="POST">
            <input type="date" name="order_date" required>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="computer_part_id" required>
                <option value="">Select Computer Part</option>
                <?php foreach ($computerParts as $part): ?>
                    <option value="<?php echo $part['id']; ?>"><?php echo htmlspecialchars($part['part_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="submit" name="add_order" value="Add Order">
        </form>

<!-- Orders Table -->
<h2>Orders</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Customer</th>
            <th>Part</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Added By</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td><?= htmlspecialchars($order['customer_first_name'] . ' ' . $order['customer_last_name']) ?></td>
                <td><?= htmlspecialchars($order['part_name']) ?></td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                <td><?= htmlspecialchars($order['added_by_first_name'] . ' ' . $order['added_by_last_name']) ?></td>
                <td><?= htmlspecialchars($order['last_updated']) ?></td>
                <td>
                    <a href="edit.php?type=order&id=<?php echo $order['order_id']; ?>">Edit</a>
                    <a href="?delete=true&type=order&id=<?php echo $order['order_id']; ?>" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>
</body>
</html>

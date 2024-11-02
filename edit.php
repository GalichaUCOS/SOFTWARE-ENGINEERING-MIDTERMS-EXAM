<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    if ($type === 'supplier') {
        $supplier = getSupplierByID($pdo, $id);
    } elseif ($type === 'computer_part') {  // Fixed type name to 'computer_part'
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
    <title>Edit <?php echo ucfirst($type); ?></title>
</head>
<body>
    <h1>Edit <?php echo ucfirst($type); ?></h1>
    <form action="handleForms.php" method="POST">
        <?php if ($type === 'supplier'): ?>
            <input type="hidden" name="id" value="<?php echo $supplier['id']; ?>">
            <input type="text" name="supplier_name" value="<?php echo $supplier['supplier_name']; ?>" required>
            <input type="text" name="contact_info" value="<?php echo $supplier['contact_info']; ?>">
            <input type="text" name="address" value="<?php echo $supplier['address']; ?>">
            <input type="submit" name="updateSupplierBtn" value="Update Supplier">
        <?php elseif ($type === 'computer_part'): ?>
            <input type="hidden" name="id" value="<?php echo $computerPart['id']; ?>">
            <input type="text" name="part_name" value="<?php echo $computerPart['part_name']; ?>" required>
            <input type="number" step="0.01" name="price" value="<?php echo $computerPart['price']; ?>" required>
            <input type="number" name="stock" value="<?php echo $computerPart['stock']; ?>" required>
            <select name="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php 
                $suppliers = getAllSuppliers($pdo);
                foreach ($suppliers as $supplier) {
                    $selected = $supplier['id'] == $computerPart['supplier_id'] ? 'selected' : '';
                    echo "<option value='{$supplier['id']}' $selected>{$supplier['supplier_name']}</option>";
                }
                ?>
            </select>
            <input type="submit" name="updateComputerPartBtn" value="Update Computer Part">
        <?php elseif ($type === 'customer'): ?>
            <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
            <input type="text" name="first_name" value="<?php echo $customer['first_name']; ?>" required>
            <input type="text" name="last_name" value="<?php echo $customer['last_name']; ?>" required>
            <input type="email" name="email" value="<?php echo $customer['email']; ?>" required>
            <input type="text" name="phone_number" value="<?php echo $customer['phone_number']; ?>">
            <input type="submit" name="updateCustomerBtn" value="Update Customer">
        <?php elseif ($type === 'order'): ?>
            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
            <input type="datetime-local" name="order_date" value="<?php echo date('Y-m-d\TH:i', strtotime($order['order_date'])); ?>" required>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php 
                $customers = getAllCustomers($pdo);
                foreach ($customers as $customer) {
                    $selected = $customer['id'] == $order['customer_id'] ? 'selected' : '';
                    echo "<option value='{$customer['id']}' $selected>{$customer['first_name']} {$customer['last_name']}</option>";
                }
                ?>
            </select>
            <select name="computer_part_id" required>
                <option value="">Select Computer Part</option>
                <?php 
                $computerParts = getAllComputerParts($pdo);
                foreach ($computerParts as $part) {
                    $selected = $part['id'] == $order['computer_part_id'] ? 'selected' : '';
                    echo "<option value='{$part['id']}' $selected>{$part['part_name']}</option>";
                }
                ?>
            </select>
            <input type="number" name="quantity" value="<?php echo $order['quantity']; ?>" required>
            <input type="submit" name="updateOrderBtn" value="Update Order">
        <?php endif; ?>
    </form>
</body>
</html>
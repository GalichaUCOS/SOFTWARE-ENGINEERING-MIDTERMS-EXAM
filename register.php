<?php
require 'dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Prepare the SQL statement to insert the user
    $stmt = $pdo->prepare("INSERT INTO Users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    
    // Execute the statement and check for success
    if ($stmt->execute([$first_name, $last_name, $email, $password])) {
        header("Location: login.php"); // Redirect to login after successful registration
        exit();
    } else {
        $error = "There was an error registering your account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your styles.css -->
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

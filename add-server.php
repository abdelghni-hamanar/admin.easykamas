<?php
// Include the database configuration file
require_once 'config/database.php';

// Initialize session
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $server_name = trim($_POST['server_name']);
    $price = trim($_POST['price']);
    $status = trim($_POST['status']);

    // Check if the server name already exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM servers WHERE server_name = :server_name");
        $stmt->bindParam(':server_name', $server_name);
        $stmt->execute();
        $server_exists = $stmt->fetchColumn();

        if ($server_exists) {
            $error = "Server name already exists.";
        } else {
            // Insert the new server into the database
            $stmt = $pdo->prepare("INSERT INTO servers (server_name, price, status) VALUES (:server_name, :price, :status)");
            $stmt->bindParam(':server_name', $server_name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            $success = "Server added successfully!";
        }
    } catch (PDOException $e) {
        $error = "Error adding server: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Server</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .form-container {
            margin-top: 50px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .btn {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<?php include_once 'includes/navbar.php'; ?>

<div class="container">
    <h2>Add Server</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="server_name">Server Name</label>
                <input type="text" name="server_name" id="server_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Incomplet">Incomplet</option>
                    <option value="Stock complet">Stock complet</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Server</button>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>

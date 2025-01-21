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

// Fetch servers from the database
$servers = [];
try {
    $stmt = $pdo->query("SELECT * FROM servers");
    $servers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching servers: " . $e->getMessage();
}

// Handle form submission for deleting a server
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_server'])) {
    $server_id = $_POST['server_id'];

    // Check if server exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM servers WHERE id = :id");
        $stmt->bindParam(':id', $server_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Delete server
            $stmt = $pdo->prepare("DELETE FROM servers WHERE id = :id");
            $stmt->bindParam(':id', $server_id);
            $stmt->execute();
            $success = "Server deleted successfully!";
        } else {
            $error = "Server not found.";
        }
    } catch (PDOException $e) {
        $error = "Error deleting server: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management</title>
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
    <h2>Server Management</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form action="" method="POST">
            <h3>Delete Server</h3>
            <div class="form-group">
                <label for="server_id">Select Server</label>
                <select name="server_id" id="server_id" class="form-control" required>
                    <option value="">-- Select Server --</option>
                    <?php foreach ($servers as $server): ?>
                        <option value="<?php echo $server['id']; ?>">
                            <?php echo htmlspecialchars($server['server_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="delete_server" class="btn btn-danger">Delete Server</button>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>

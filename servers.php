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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $server_id = $_POST['server_id'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Update the server details
    try {
        $stmt = $pdo->prepare("UPDATE servers SET price = :price, status = :status WHERE id = :id");
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $server_id);
        $stmt->execute();
        $success = "Server details updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating server: " . $e->getMessage();
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
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
<script>
    // Auto-fill the price and status when a server is selected
    document.getElementById('server_id').addEventListener('change', function() {
        const selectedServer = <?php echo json_encode($servers); ?>.find(server => server.id == this.value);
        if (selectedServer) {
            document.getElementById('price').value = selectedServer.price;
            document.getElementById('status').value = selectedServer.status;
        } else {
            document.getElementById('price').value = '';
            document.getElementById('status').value = '';
        }
    });
</script>
</body>
</html>

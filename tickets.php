<?php
// Include your database connection
require_once 'config/database.php';

// Fetch ventetickets that are not paid, along with user information
$query = "
    SELECT 
        vt.id AS ticket_id, 
        vt.char_name AS character_name, 
        vt.price_server AS price, 
        vt.total AS total, 
        vt.status AS ticket_status, 
        u.email AS user_email, 
        u.full_name AS user_full_name, 
        u.phone AS user_phone, 
        u.adresse AS user_address, 
        u.role AS user_role, 
        s.server_name AS server_name 
    FROM ventetickets vt
    JOIN users u ON vt.id_user = u.id
    JOIN servers s ON vt.id_server = s.id
    WHERE vt.status != 'payé'
";

$stmt = $pdo->prepare($query);
$stmt->execute();

// Update the ticket status if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status'];
    
    // Prepare the update query
    $update_query = "UPDATE ventetickets SET status = :status WHERE id = :ticket_id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':ticket_id', $ticket_id);
    
    // Execute the update query
    if ($update_stmt->execute()) {
        // Redirect to the same page to see the update
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo '<p class="text-danger text-center">Error updating status.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventetickets - Unpaid Tickets</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include('includes/navbar.php'); ?>

    <!-- Main Content -->
    <div class="container">
        <h2 class="text-center">Unpaid Ventetickets</h2>
        
        <?php
        // Check if any results are returned
        if ($stmt->rowCount() > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>ID</th><th>Character Name</th><th>Price</th><th>Total</th><th>Status</th><th>Email</th><th>Full Name</th><th>Phone</th><th>Address</th><th>Role</th><th>Server</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            
            // Loop through and display each row
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['ticket_id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['character_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                echo '<td>' . htmlspecialchars($row['total']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ticket_status']) . '</td>';
                echo '<td>' . htmlspecialchars($row['user_email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['user_full_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['user_phone']) . '</td>';
                echo '<td>' . htmlspecialchars($row['user_address']) . '</td>';
                echo '<td>' . htmlspecialchars($row['user_role']) . '</td>';
                echo '<td>' . htmlspecialchars($row['server_name']) . '</td>';
                
                // Add the dropdown and Update button for status
                echo '<td>
                        <form action="" method="POST">
                            <input type="hidden" name="ticket_id" value="' . htmlspecialchars($row['ticket_id']) . '">
                            <select name="status" class="form-control">';
                
                // Status options (you can add or modify these as needed)
                $statuses = ['en attente de livraison', 'paiement en cours', 'payé', 'annulé'];
                foreach ($statuses as $status) {
                    $selected = $row['ticket_status'] == $status ? 'selected' : '';
                    echo "<option value=\"$status\" $selected>$status</option>";
                }
                
                echo '</select>
                    <button type="submit" name="update_status" class="btn btn-primary mt-2">Update</button>
                </form>
                </td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p class="text-center">No tickets found.</p>';
        }
        ?>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

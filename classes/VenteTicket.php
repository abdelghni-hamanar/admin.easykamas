<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by verifying if a session variable (e.g., 'user_id') is set
if (empty($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Database connection
require_once '../config/database.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $server_id = $_POST['server'] ?? null;
    $char_name = $_POST['character_name'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $user_id = $_SESSION['user_id'];

    // Ensure all required fields are filled
    if ($server_id && $char_name && $quantity > 0) {
        try {
            // Fetch the server price based on the selected server ID
            $stmt = $pdo->prepare("SELECT price FROM servers WHERE id = ?");
            $stmt->execute([$server_id]);
            $server = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($server) {
                $price_server = $server['price'];

                // Calculate the total by multiplying quantity with server price
                $total = $quantity * $price_server;

                // Insert the new VenteTicket into the database, including the price_server
                $insertStmt = $pdo->prepare("
                    INSERT INTO ventetickets (id_server, id_user, char_name,price_server, quantity, total)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$server_id, $user_id, $char_name, $price_server, $quantity, $total]);

                // Redirect to a confirmation or success page
                header("Location: ../vente-kamas.php");
                exit();
            } else {
                echo "Invalid server selection.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill in all required fields.";
    }
} else {
    // Redirect to the form page if accessed directly
    header("Location: ../vente-kamas.php");
    exit();
}
?>

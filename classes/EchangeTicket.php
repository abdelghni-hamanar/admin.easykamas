<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to home page if the session is not active
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include the database configuration file
require_once '../config/database.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $user_id = $_SESSION['user_id'];
    $id_server1 = $_POST['mainServer'];
    $char1_name = $_POST['charName'];
    $quantity_1 = $_POST['quantity'];
    $id_server_2 = $_POST['exchangeServer'];
    $char2_name = $_POST['exchangeCharName'];
    $quantity_2 = $_POST['receiveAmount'];

    // Debugging output to check form values
    // echo "Debug Info:<br>";
    // echo "User ID: $user_id<br>";
    // echo "Server 1 ID: $id_server1<br>";
    // echo "Character 1 Name: $char1_name<br>";
    // echo "Quantity 1: $quantity_1<br>";
    // echo "Server 2 ID: $id_server_2<br>";
    // echo "Character 2 Name: $char2_name<br>";
    // echo "Quantity 2 (Receive Amount): $quantity_2<br>";

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("
            INSERT INTO echangetickets (user_id, id_server1, char1_name, quantity_1, id_server_2, char2_name, quantity_2, status)
            VALUES (:user_id, :id_server1, :char1_name, :quantity_1, :id_server_2, :char2_name, :quantity_2, 'en attente de livraison')
        ");

        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_server1', $id_server1, PDO::PARAM_INT);
        $stmt->bindParam(':char1_name', $char1_name, PDO::PARAM_STR);
        $stmt->bindParam(':quantity_1', $quantity_1, PDO::PARAM_INT);
        $stmt->bindParam(':id_server_2', $id_server_2, PDO::PARAM_INT);
        $stmt->bindParam(':char2_name', $char2_name, PDO::PARAM_STR);
        $stmt->bindParam(':quantity_2', $quantity_2, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Ticket created successfully!";
            // Redirect to a success page or display a success message
            header('Location: ../index.php');
            exit();
        } else {
            echo "Failed to create ticket.";
        }

    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

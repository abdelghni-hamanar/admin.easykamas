<?php
// Start session at the very top of the script
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: ../login.php");
    exit();
}

// Include the database connection file
require_once '../config/database.php'; // Adjust the path if necessary

// Initialize variables from session
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$address = ''; // You can add address from the session or database if needed
$phone = ''; // You can add phone from the session or database if needed

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $new_full_name = !empty($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $new_address = !empty($_POST['address']) ? trim($_POST['address']) : '';
    $new_phone = !empty($_POST['phone']) ? trim($_POST['phone']) : '';
    $new_password = !empty($_POST['password']) ? trim($_POST['password']) : '';

    // Update session data if any fields are updated
    if ($new_full_name) {
        $_SESSION['full_name'] = $new_full_name;
        $full_name = $new_full_name;
    }
    if ($new_address) {
        // You can update the session or database based on your needs
        $address = $new_address;
    }
    if ($new_phone) {
        // You can update the session or database based on your needs
        $phone = $new_phone;
    }

    // Update password if a new one is provided
    if ($new_password) {
        // Make sure the password is sufficiently strong
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Prepare the SQL query to update the password
        $query = "UPDATE users SET password = :password WHERE id = :user_id";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // Update session data after successful password change
            $_SESSION['password'] = $hashed_password;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Prepare the SQL query to update other fields
    $query = "UPDATE users SET full_name = :full_name, adresse = :address, phone = :phone WHERE id = :user_id";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Optional: You can display a success message or redirect after the update
        header("Location: ../profile.php"); // Redirect to profile page after successful update
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


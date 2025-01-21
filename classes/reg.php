<?php
// Include the database configuration file
require_once '../config/database.php';

// Initialize variables
$username = $email = $password = $full_name = $phone = $ville = $adresse = "";
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $adresse = trim($_POST['adresse']);
    $password = trim($_POST['password']);
    
    // Basic validation
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password)) $errors[] = "Password is required.";
    if (empty($full_name)) $errors[] = "Full name is required.";
    
    // Check if there are no validation errors
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Prepare an insert statement
            $stmt = $pdo->prepare("INSERT INTO users ( email, full_name, phone, adresse, role, password) VALUES (:email, :full_name, :phone, :adresse, :role, :password)");
            
            // Bind parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindValue(':role', 'customer'); // Default role
            $stmt->bindParam(':password', $hashed_password);
            
            // Execute the statement
            if ($stmt->execute()) {
                // Registration successful
                header("Location: ../login.php?message=Registration successful");
                exit();
            } else {
                $errors[] = "An error occurred while saving your information.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<?php
// Include the database configuration file
require_once '../config/database.php';

// Initialize session
session_start();

// Initialize variables
$email = $password = "";
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Basic validation
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password)) $errors[] = "Password is required.";
    
    // Check if there are no validation errors
    if (empty($errors)) {
        try {
            // Prepare a select statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            
            // Bind parameters
            $stmt->bindParam(':email', $email);
            
            // Execute the statement
            $stmt->execute();
            
            // Check if user exists
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Check if the user role is admin
                    if ($user['role'] === 'admin') {
                        // Password is correct and user is an admin, start a new session
                        session_regenerate_id(true); // Regenerate session ID for security
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['adresse'] = $user['adresse'];
                        $_SESSION['phone'] = $user['phone'];
                        
                        // Redirect to a welcome page
                        header("Location: ../index.php");
                        exit();
                    } else {
                        $errors[] = "Access denied. Admins only.";
                    }
                } else {
                    $errors[] = "Incorrect password.";
                }
            } else {
                $errors[] = "No account found with that email.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }

    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../index.php");
        exit();
    }
}

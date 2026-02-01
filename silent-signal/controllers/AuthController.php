<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        // Initialize user model if you have database setup
        // $database = new Database();
        // $this->db = $database->getConnection();
        // $this->user = new User($this->db);
    }

    /**
     * Show combined auth page (login/signup)
     */
    public function showAuth() {
        $pageTitle = "Login / Sign Up - Silent Signal";
        $isHome = false;
        require_once VIEW_PATH . 'auth.php';
    }

    /**
     * Process login form submission
     */
    public function processLogin() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $email_or_phone = $_POST['email_phone'];
            $password = $_POST['password'];

            // Validate inputs
            if(empty($email_or_phone) || empty($password)) {
                $_SESSION['error'] = "All fields are required!";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            }

            // TODO: Implement actual authentication with database
            // For now, demo credentials
            if($email_or_phone == 'admin@silentsignal.com' && $password == 'admin123') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = 'Admin User';
                $_SESSION['user_email'] = 'admin@silentsignal.com';
                $_SESSION['user_role'] = 'admin';
                $_SESSION['success'] = "Login successful!";
                
                header("Location: " . BASE_URL . "index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Invalid credentials! Try admin@silentsignal.com / admin123";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            }
        }
    }

    /**
     * Process signup form submission
     */
    public function processSignup() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get posted data
            $fname = $_POST['fname'] ?? '';
            $lname = $_POST['lname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone_number = $_POST['phone_number'] ?? '';
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate inputs
            if(empty($fname) || empty($lname) || empty($email) || 
               empty($phone_number) || empty($role) || empty($password)) {
                $_SESSION['signup_error'] = "All fields are required!";
                header("Location: " . BASE_URL . "index.php?action=auth&form=signup");
                exit();
            }

            // Validate email format
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['signup_error'] = "Invalid email format!";
                header("Location: " . BASE_URL . "index.php?action=auth&form=signup");
                exit();
            }

            // TODO: Check if email already exists in database
            // TODO: Create the user in database

            // For now, just redirect with success message
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "index.php?action=home");
        exit();
    }
}
?>

<?php
// controllers/AuthController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        // Initialize user model if you have database setup
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
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

        // Attempt login
            if($this->user->login($email_or_phone, $password)) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_email'] = $this->user->email;
                $_SESSION['user_role'] = $this->user->role;
                $_SESSION['success'] = "Login successful!";
                
                // Redirect to dashboard or home page
                header("Location: " . BASE_URL . "index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Invalid credentials!";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            }

        // Demo credentials array for easy testing
        // $demoUsers = [
        //     [
        //         'email' => 'admin@silentsignal.com',
        //         'password' => 'admin123',
        //         'id' => 1,
        //         'name' => 'Admin User',
        //         'role' => 'admin'
        //     ],
        //     [
        //         'email' => 'user@silentsignal.com',
        //         'password' => 'user123',
        //         'id' => 2,
        //         'name' => 'Juan Dela Cruz',
        //         'role' => 'pwd'
        //     ],
        //     [
        //         'email' => 'family@silentsignal.com',
        //         'password' => 'family123',
        //         'id' => 3,
        //         'name' => 'Maria Santos',
        //         'role' => 'family'
        //     ]
        // ];

        // // Check credentials
        // $loggedIn = false;
        // foreach ($demoUsers as $user) {
        //     if ($email_or_phone == $user['email'] && $password == $user['password']) {
        //         $_SESSION['user_id'] = $user['id'];
        //         $_SESSION['user_name'] = $user['name'];
        //         $_SESSION['user_email'] = $user['email'];
        //         $_SESSION['user_role'] = $user['role'];
        //         $_SESSION['success'] = "Login successful!";
        //         $loggedIn = true;
                
        //         // Redirect based on role
        //         if ($user['role'] == 'admin') {
        //             header("Location: " . BASE_URL . "index.php?action=admin-dashboard");
        //         } else {
        //             header("Location: " . BASE_URL . "index.php?action=dashboard");
        //         }
        //         exit();
        //     }
        // }

        // if (!$loggedIn) {
        //     $_SESSION['error'] = "Invalid credentials!";
        //     header("Location: " . BASE_URL . "index.php?action=auth");
        //     exit();
        // }
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

             // Check if email already exists
            $this->user->email = $_POST['email'];
            if($this->user->emailExists()) {
                $_SESSION['error'] = "Email already exists!";
                header("Location: " . BASE_URL . "index.php?action=auth&form=signup");
                exit();
            }

            // Create the user
            if($this->user->create()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            } else {
                $_SESSION['error'] = "Unable to register. Please try again.";
                header("Location: " . BASE_URL . "index.php?action=auth&form=signup");
                exit();
            }
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

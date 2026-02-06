<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function showAuth() {
        $pageTitle = "Login / Sign Up - Silent Signal";
        require_once VIEW_PATH . 'auth.php';
    }

    public function processLogin() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $identifier = trim($_POST['email_phone']);
            $password   = $_POST['password'];

            if (empty($identifier) || empty($password)) {
                $_SESSION['error'] = "All fields are required!";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            }

            if ($this->user->login($identifier, $password)) {
                $_SESSION['user_id']    = $this->user->id;
                $_SESSION['user_fname'] = $this->user->fname;
                $_SESSION['user_lname'] = $this->user->lname;
                $_SESSION['user_email'] = $this->user->email;
                $_SESSION['user_phone'] = $this->user->phone_number;
                $_SESSION['user_role']  = $this->user->role;

                $redirect = ($this->user->role === 'admin')
                    ? "admin-dashboard"
                    : "dashboard";

                header("Location: " . BASE_URL . "index.php?action=" . $redirect);
                exit();
            }

            $_SESSION['error'] = "Invalid credentials!";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }

    public function processSignup() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $this->user->fname         = trim($_POST['fname']);
            $this->user->lname         = trim($_POST['lname']);
            $this->user->email         = trim($_POST['email']);
            $this->user->phone_number  = trim($_POST['phone_number']);
            $this->user->role          = $_POST['role'];
            $this->user->password      = $_POST['password'];

            if (
                empty($this->user->fname) ||
                empty($this->user->lname) ||
                empty($this->user->email) ||
                empty($this->user->phone_number) ||
                empty($this->user->role) ||
                empty($this->user->password)
            ) {
                $_SESSION['signup_error'] = "All fields are required!";
                header("Location: " . BASE_URL . "index.php?action=auth&mode=signup");
                exit();
            }

            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['signup_error'] = "Invalid email format!";
                header("Location: " . BASE_URL . "index.php?action=auth&mode=signup");
                exit();
            }

            if ($this->user->emailExists()) {
                $_SESSION['signup_error'] = "Email already exists!";
                header("Location: " . BASE_URL . "index.php?action=auth&mode=signup");
                exit();
            }

            if ($this->user->phoneExists()) {
                $_SESSION['signup_error'] = "Phone number already registered!";
                header("Location: " . BASE_URL . "index.php?action=auth&mode=signup");
                exit();
            }
            

            if ($this->user->create()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: " . BASE_URL . "index.php?action=auth");
                exit();
            }

            $_SESSION['signup_error'] = "Registration failed. Try again.";
            header("Location: " . BASE_URL . "index.php?action=auth&mode=signup");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "index.php?action=home");
        exit();
    }
}

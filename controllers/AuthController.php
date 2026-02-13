<?php
// controllers/AuthController.php
// Handles authentication: login, signup, logout

class AuthController {
    
    private $db;
    private $user;
    
    public function __construct() {
        // Load dependencies
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/User.php';
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }
    
    /**
     * Show auth page (login/signup)
     */
    public function showAuth() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard();
        }
        
        $pageTitle = "Login - Silent Signal";
        require_once VIEW_PATH . 'auth.php';
    }
    
    /**
     * Process login
     */
    public function processLogin() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        $email_or_phone = trim($_POST['email_phone'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate inputs
        if (empty($email_or_phone) || empty($password)) {
            $_SESSION['error'] = "All fields are required!";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Attempt login using User model
        if ($this->user->login($email_or_phone, $password)) {
            // Set session variables
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_name'] = $this->user->fname . ' ' . $this->user->lname;
            $_SESSION['user_email'] = $this->user->email;
            $_SESSION['user_role'] = $this->user->role;
            
            $_SESSION['success'] = "Login successful! Welcome back, " . $this->user->fname . "!";
            
            // Redirect based on role
            $this->redirectToDashboard();
        } else {
            $_SESSION['error'] = "Invalid email/phone or password.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    /**
     * Process signup
     */
    public function processSignup() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Get form data
        $fname = trim($_POST['fname'] ?? '');
        $lname = trim($_POST['lname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'pwd';
        
        // Validate inputs
        $errors = [];
        
        if (empty($fname)) {
            $errors[] = "First name is required.";
        }
        
        if (empty($lname)) {
            $errors[] = "Last name is required.";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            // Check if email exists
            $this->user->email = $email;
            if ($this->user->emailExists()) {
                $errors[] = "Email already registered.";
            }
        }
        
        if (!empty($phone)) {
            $this->user->phone_number = $phone;
            if ($this->user->phoneExists()) {
                $errors[] = "Phone number already registered.";
            }
        }
        
        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }
        
        // Validate role
        $allowedRoles = ['pwd', 'family'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'pwd';
        }
        
        // If errors, redirect back
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Set user data
        $this->user->fname = $fname;
        $this->user->lname = $lname;
        $this->user->email = $email;
        $this->user->phone_number = $phone;
        $this->user->password = $password;
        $this->user->role = $role;
        
        // Create user
        if ($this->user->create()) {
            // Auto-login after signup
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_name'] = $fname . ' ' . $lname;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            
            // Create empty medical profile for PWD users
            if ($role === 'pwd') {
                require_once __DIR__ . '/../models/MedicalProfile.php';
                $medicalProfile = new MedicalProfile();
                $profileData = [
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'email' => $email,
                    'phone' => $phone,
                    'date_of_birth' => null,
                    'gender' => '',
                    'pwd_id' => '',
                    'street_address' => '',
                    'city' => '',
                    'province' => '',
                    'zip_code' => '',
                    'disability_type' => '',
                    'blood_type' => '',
                    'allergies' => [],
                    'medications' => [],
                    'medical_conditions' => [],
                    'emergency_contacts' => [],
                    'sms_template' => '',
                    'medication_reminders' => []
                ];
                $medicalProfile->saveProfile($this->user->id, $profileData);
                
                $_SESSION['success'] = "Registration successful! Please complete your medical profile.";
                header("Location: " . BASE_URL . "index.php?action=medical-profile");
                exit();
            } else {
                $_SESSION['success'] = "Registration successful! Welcome to Silent Signal!";
                $this->redirectToDashboard();
            }
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    /**
     * Process logout
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect to home
        header("Location: " . BASE_URL . "index.php?action=home");
        exit();
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        $role = $_SESSION['user_role'] ?? 'pwd';
        
        switch ($role) {
            case 'admin':
                header("Location: " . BASE_URL . "index.php?action=admin-dashboard");
                break;
            case 'family':
                header("Location: " . BASE_URL . "index.php?action=family-dashboard");
                break;
            default:
                header("Location: " . BASE_URL . "index.php?action=dashboard");
        }
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}
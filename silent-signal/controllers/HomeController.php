<?php
// controllers/HomeController.php

class HomeController {
    
    public function index() {
        // Set page variables
        $pageTitle = "Home - Silent Signal";
        $isHome = true; // Important: This tells the header we're on the home page
        
        // Load any data needed for the home page
        $heroTitle = "Emergency Communication Made Accessible for the Deaf and Mute";
        $heroDescription = "A PWD-focused emergency alert and monitoring system <br>
                    designed for deaf and mute individuals to communicate, <br>
                    stay safe, and get help during emergencies.";
        $features = [
            ['icon'=>'<i class="ri-alarm-warning-line"></i>', 'title'=>'Emergency Alert System', 'desc'=>'Send an SOS with one tap. Automatically shares your GPS location, medical information, and status via SMS.'],
            ['icon'=>'<i class="ri-alert-line"></i>', 'title'=>'Disaster Monitoring & Auto-Alert', 'desc'=>'Stay informed about disasters in your area with real-time alerts.'],
            ['icon'=>'<i class="ri-team-line"></i>', 'title'=>'Family Check-In System', 'desc'=>'Let your family know you are safe — without speaking.'],
            ['icon'=>'<i class="fa-regular fa-message"></i>', 'title'=>'Visual Communication Hub', 'desc'=>'Communicate clearly during emergencies using visual tools.'],
            ['icon'=>'<i class="fa-regular fa-user"></i>', 'title'=>'Medical Profile & Pre-Registration', 'desc'=>'Important medical details ready when responders need them.'],
        ];
        $howItWorks = [
            ['number'=>'1', 'title'=>'Register & Set Up Profile', 'desc'=>'Add medical info and emergency contacts.'],
            ['number'=>'2', 'title'=>'Monitor & Stay Alert', 'desc'=>'Receive disaster alerts and location updates.'],
            ['number'=>'3', 'title'=>'Get Help Instantly', 'desc'=>'One tap sends SOS with your location.'],
        ];

        // Load the view
        require_once VIEW_PATH . 'includes/home-header.php';
        require_once VIEW_PATH . 'home.php';
        require_once VIEW_PATH . 'includes/home-footer.php';
    }
    
    public function submitContact() {
        // Handle contact form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            
            // Here you would process the form data
            // For now, we'll just redirect back
            header('Location: ' . BASE_URL . 'index.php?action=home&success=1');
            exit;
        }
    }
}
?>

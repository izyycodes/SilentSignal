<?php
// views/login.php
// Note: config is already loaded by index.php
// $pageTitle and $isHome are set by controller
require_once VIEW_PATH . 'includes/home-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/login.css">


<!-- Main Container -->
<div class="main-container login-container">
    <!-- Back to Home Link -->
     <div>
         <a href="<?php echo BASE_URL; ?>index.php?action=home" class="back-to-home">
             <i class="ri-arrow-left-line"></i> Back to Home
         </a>
     </div>
    
     <div class="form-container">
         <!-- Login Form Section -->
         <div class="form-section">
             <h2>Login</h2>
             
             <!-- Social Login -->
             <div class="social-login">
                 <button class="social-btn" title="Facebook">
                     <i class="fab fa-facebook-f"></i>
                 </button>
                 <button class="social-btn" title="Google">
                     <i class="fab fa-google"></i>
                 </button>
                 <button class="social-btn" title="GitHub">
                     <i class="fab fa-github"></i>
                 </button>
             </div>
     
             <div class="divider">or use your email account:</div>
     
             <!-- Display Messages -->
             <?php
             if(isset($_SESSION['error'])) {
                 echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                 unset($_SESSION['error']);
             }
             if(isset($_SESSION['success'])) {
                 echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                 unset($_SESSION['success']);
             }
             ?>
     
             <!-- Login Form -->
             <form action="<?php echo BASE_URL; ?>index.php?action=process_login" method="POST">
                 <div class="form-group">
                     <label for="email_phone">Email/Phone Number</label>
                     <input type="text" id="email_phone" name="email_phone" required>
                 </div>
     
                 <div class="form-group">
                     <label for="password">Password</label>
                     <input type="password" id="password" name="password" required>
                 </div>
     
                 <button type="submit" class="submit-btn">LOGIN</button>
             </form>
         </div>
     
         <!-- Welcome Section -->
         <div class="welcome-section">
             <h1>Hello, Friend!</h1>
             <p>Enter your details and start your journey with accessible and reliable emergency support.</p>
             <a href="<?php echo BASE_URL; ?>index.php?action=signup" class="welcome-btn">SIGN UP</a>
         </div>

     </div>
</div>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

</body>
</html>
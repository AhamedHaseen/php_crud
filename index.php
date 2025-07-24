<?php
// Check if user is already logged in, redirect to dashboard
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Get current date for dynamic content
$currentYear = date('Y');
$currentDate = date('F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD Admin Dashboard - User Management System</title>
    <meta name="description" content="Complete PHP CRUD application with MySQL database integration and Bootstrap UI for managing users with advanced categorization and reporting features.">
    <meta name="keywords" content="PHP, CRUD, MySQL, Bootstrap, Admin Dashboard, User Management">
    <meta name="author" content="Admin CRUD System">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗄️</text></svg>">
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="position-fixed top-0 start-0 w-100 h-100 bg-primary d-flex justify-content-center align-items-center" style="z-index: 9999;">
        <div class="text-center text-white">
            <div class="loading mb-3"></div>
            <h5>Loading Dashboard...</h5>
        </div>
    </div>

    <div class="container" id="mainContent" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card welcome-card">
                    <div class="card-body text-center p-5">
                        <!-- Main Header -->
                        <i class="fas fa-database fa-4x main-icon mb-4"></i>
                        <h1 class="display-4 mb-2 welcome-title">PHP CRUD Admin Dashboard</h1>
                        <p class="lead mb-4 text-muted">
                            Complete user management system with MySQL database integration
                        </p>
                        <div class="text-muted mb-4">
                            <small><i class="fas fa-calendar"></i> Today: <?php echo $currentDate; ?></small>
                        </div>

                        <!-- Features Section -->
                        <div class="row text-start mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="feature-list">
                                    <h5>
                                        <i class="fas fa-check-circle text-success"></i> Core Features
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-users"></i> Complete User Management (CRUD)</li>
                                        <li><i class="fas fa-chart-bar"></i> Advanced Reports & Analytics</li>
                                        <li><i class="fas fa-filter"></i> Smart Filtering & Search</li>
                                        <li><i class="fas fa-toggle-on"></i> Real-time Status Management</li>
                                        <li><i class="fas fa-shield-alt"></i> Secure Authentication</li>
                                        <li><i class="fas fa-mobile-alt"></i> Responsive Design</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="feature-list">
                                    <h5>
                                        <i class="fas fa-layer-group text-info"></i> User Categories
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <span class="badge bg-warning custom-badge">New Users</span> 
                                            <small class="text-muted">(Last 7 days)</small>
                                        </li>
                                        <li>
                                            <span class="badge bg-primary custom-badge">Regular Users</span> 
                                            <small class="text-muted">(7-30 days)</small>
                                        </li>
                                        <li>
                                            <span class="badge bg-info custom-badge">Old Users</span> 
                                            <small class="text-muted">(30+ days)</small>
                                        </li>
                                        <li>
                                            <span class="badge bg-success custom-badge">Active</span> / 
                                            <span class="badge bg-secondary custom-badge">Inactive</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Technology Stack -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="feature-list">
                                    <h5>
                                        <i class="fas fa-code text-primary"></i> Technology Stack
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3 col-6 mb-2">
                                            <span class="badge bg-info custom-badge">
                                                <i class="fab fa-php"></i> PHP 7.4+
                                            </span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <span class="badge bg-warning custom-badge">
                                                <i class="fas fa-database"></i> MySQL 5.7+
                                            </span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <span class="badge bg-primary custom-badge">
                                                <i class="fab fa-bootstrap"></i> Bootstrap 5
                                            </span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <span class="badge bg-success custom-badge">
                                                <i class="fab fa-js"></i> Chart.js
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center mb-4">
                            <a href="login.php" class="btn btn-primary btn-lg btn-custom">
                                <i class="fas fa-sign-in-alt"></i> Access Admin Panel
                            </a>
                            <a href="database.sql" class="btn btn-outline-secondary btn-lg btn-custom" download>
                                <i class="fas fa-download"></i> Download Database
                            </a>
                        </div>

                        <!-- Quick Stats Preview -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-2">
                                <div class="text-center">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h6 class="text-muted">Complete CRUD Operations</h6>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="text-center">
                                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                    <h6 class="text-muted">Real-time Analytics</h6>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="text-center">
                                    <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                                    <h6 class="text-muted">Secure & Protected</h6>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Login Information -->
                        <div class="login-info">
                            <h6 class="mb-3">
                                <i class="fas fa-key text-primary"></i> Default Login Credentials
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Username:</strong> <code>admin</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Password:</strong> <code>admin123</code>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Make sure XAMPP is running with Apache and MySQL services
                                </small>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">
                                © <?php echo $currentYear; ?> PHP CRUD Admin Dashboard. 
                                Built with <i class="fas fa-heart text-danger"></i> using PHP & Bootstrap
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Loading screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loadingScreen').style.display = 'none';
                document.getElementById('mainContent').style.display = 'block';
                
                // Add entrance animation
                document.getElementById('mainContent').style.opacity = '0';
                document.getElementById('mainContent').style.transform = 'translateY(20px)';
                
                setTimeout(function() {
                    document.getElementById('mainContent').style.transition = 'all 0.5s ease';
                    document.getElementById('mainContent').style.opacity = '1';
                    document.getElementById('mainContent').style.transform = 'translateY(0)';
                }, 100);
            }, 1000);
        });

        // Add interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Hover effects for badges
            const badges = document.querySelectorAll('.custom-badge');
            badges.forEach(badge => {
                badge.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });
                badge.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Button click effects
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add typing effect to title (optional)
            const title = document.querySelector('.welcome-title');
            if (title) {
                const text = title.textContent;
                title.textContent = '';
                let i = 0;
                
                function typeWriter() {
                    if (i < text.length) {
                        title.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeWriter, 50);
                    }
                }
                
                setTimeout(typeWriter, 1500);
            }
        });

        // Check server status (optional)
        function checkServerStatus() {
            fetch('config.php')
                .then(response => {
                    if (response.ok) {
                        console.log('✅ Server is running properly');
                    }
                })
                .catch(error => {
                    console.warn('⚠️ Server might not be running:', error);
                });
        }

        // Call server check after page load
        setTimeout(checkServerStatus, 2000);
    </script>

    <!-- Ripple effect CSS -->
    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>

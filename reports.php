<?php
require_once 'config.php';
requireLogin();

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['total'] = $stmt->fetch()['count'];

// Active/Inactive users
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
while ($row = $stmt->fetch()) {
    $stats[$row['status']] = $row['count'];
}

// New users (last 7 days)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stats['new_7_days'] = $stmt->fetch()['count'];

// New users (last 30 days)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['new_30_days'] = $stmt->fetch()['count'];

// Old users (30+ days)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE registration_date <= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['old_users'] = $stmt->fetch()['count'];

// Users by age groups
$stmt = $pdo->query("
    SELECT 
        CASE 
            WHEN age < 18 THEN 'Under 18'
            WHEN age BETWEEN 18 AND 25 THEN '18-25'
            WHEN age BETWEEN 26 AND 35 THEN '26-35'
            WHEN age BETWEEN 36 AND 45 THEN '36-45'
            WHEN age BETWEEN 46 AND 55 THEN '46-55'
            ELSE '55+'
        END as age_group,
        COUNT(*) as count
    FROM users 
    GROUP BY age_group
    ORDER BY MIN(age)
");
$ageGroups = $stmt->fetchAll();

// Monthly registration trend (last 6 months)
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(registration_date, '%Y-%m') as month,
        COUNT(*) as count
    FROM users 
    WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(registration_date, '%Y-%m')
    ORDER BY month
");
$monthlyTrend = $stmt->fetchAll();

// Most active users (by last login)
$stmt = $pdo->query("
    SELECT name, email, last_login, status
    FROM users 
    WHERE last_login IS NOT NULL
    ORDER BY last_login DESC 
    LIMIT 10
");
$activeUsers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-tachometer-alt"></i> Admin Panel
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a class="nav-link" href="add_user.php">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                        <a class="nav-link active" href="reports.php">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Reports & Analytics</h2>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                    
                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h3><?php echo $stats['total']; ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-check fa-2x mb-2"></i>
                                    <h3><?php echo $stats['active'] ?? 0; ?></h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                                    <h3><?php echo $stats['new_7_days']; ?></h3>
                                    <p class="mb-0">New (7 days)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-clock fa-2x mb-2"></i>
                                    <h3><?php echo $stats['old_users']; ?></h3>
                                    <p class="mb-0">Old Users (30+ days)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <!-- User Status Chart -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-pie-chart"></i> User Status Distribution
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="statusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Age Groups Chart -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bar-chart"></i> Age Group Distribution
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="ageChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monthly Trend Chart -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-line-chart"></i> Monthly Registration Trend
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="trendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Tables -->
                    <div class="row">
                        <!-- User Categories -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list"></i> User Categories
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Count</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-warning">New Users (7 days)</span></td>
                                                <td><?php echo $stats['new_7_days']; ?></td>
                                                <td><?php echo $stats['total'] > 0 ? round(($stats['new_7_days'] / $stats['total']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary">Regular Users</span></td>
                                                <td><?php echo $stats['new_30_days'] - $stats['new_7_days']; ?></td>
                                                <td><?php echo $stats['total'] > 0 ? round((($stats['new_30_days'] - $stats['new_7_days']) / $stats['total']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-info">Old Users (30+ days)</span></td>
                                                <td><?php echo $stats['old_users']; ?></td>
                                                <td><?php echo $stats['total'] > 0 ? round(($stats['old_users'] / $stats['total']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td><?php echo $stats['active'] ?? 0; ?></td>
                                                <td><?php echo $stats['total'] > 0 ? round((($stats['active'] ?? 0) / $stats['total']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-secondary">Inactive</span></td>
                                                <td><?php echo $stats['inactive'] ?? 0; ?></td>
                                                <td><?php echo $stats['total'] > 0 ? round((($stats['inactive'] ?? 0) / $stats['total']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Most Active Users -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-trophy"></i> Most Active Users
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Last Login</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($activeUsers as $user): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                    <td><?php echo formatDate($user['last_login']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($user['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [<?php echo $stats['active'] ?? 0; ?>, <?php echo $stats['inactive'] ?? 0; ?>],
                    backgroundColor: ['#28a745', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Age Groups Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo "'" . implode("','", array_column($ageGroups, 'age_group')) . "'"; ?>],
                datasets: [{
                    label: 'Users',
                    data: [<?php echo implode(',', array_column($ageGroups, 'count')); ?>],
                    backgroundColor: '#667eea'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [<?php echo "'" . implode("','", array_column($monthlyTrend, 'month')) . "'"; ?>],
                datasets: [{
                    label: 'New Registrations',
                    data: [<?php echo implode(',', array_column($monthlyTrend, 'count')); ?>],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

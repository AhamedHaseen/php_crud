<?php
require_once 'config.php';
requireLogin();

// Handle delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    redirect('users.php?msg=deleted');
}

// Handle status update
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
    $stmt->execute([$id]);
    redirect('users.php?msg=status_updated');
}

// Get filter parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$whereClause = "WHERE 1=1";
$params = [];

if ($filter !== 'all') {
    if ($filter === 'new') {
        $whereClause .= " AND registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($filter === 'old') {
        $whereClause .= " AND registration_date <= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } elseif ($filter === 'active') {
        $whereClause .= " AND status = 'active'";
    } elseif ($filter === 'inactive') {
        $whereClause .= " AND status = 'inactive'";
    }
}

if ($search) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
}

$stmt = $pdo->prepare("SELECT * FROM users $whereClause ORDER BY registration_date DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .action-btn {
            padding: 5px 10px;
            font-size: 12px;
            margin: 2px;
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
                        <a class="nav-link active" href="users.php">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a class="nav-link" href="add_user.php">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                        <a class="nav-link" href="reports.php">
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
                        <h2>Manage Users</h2>
                        <a href="add_user.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                    
                    <!-- Messages -->
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php 
                            switch($_GET['msg']) {
                                case 'deleted': echo 'User deleted successfully!'; break;
                                case 'status_updated': echo 'User status updated successfully!'; break;
                                case 'added': echo 'User added successfully!'; break;
                                case 'updated': echo 'User updated successfully!'; break;
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="filter" class="form-label">Filter by Category</label>
                                    <select class="form-select" id="filter" name="filter">
                                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Users</option>
                                        <option value="new" <?php echo $filter === 'new' ? 'selected' : ''; ?>>New Users (Last 7 days)</option>
                                        <option value="old" <?php echo $filter === 'old' ? 'selected' : ''; ?>>Old Users (30+ days)</option>
                                        <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active Users</option>
                                        <option value="inactive" <?php echo $filter === 'inactive' ? 'selected' : ''; ?>>Inactive Users</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-users"></i> Users List (<?php echo count($users); ?> users)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Age</th>
                                            <th>Status</th>
                                            <th>Category</th>
                                            <th>Registration</th>
                                            <th>Last Login</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i><br>
                                                No users found matching your criteria.
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td>
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['name']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                            <td><?php echo $user['age']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo getTimeDiff($user['registration_date']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($user['registration_date']); ?></td>
                                            <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></td>
                                            <td>
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-warning action-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="users.php?toggle_status=<?php echo $user['id']; ?>" 
                                                   class="btn btn-info action-btn" 
                                                   title="Toggle Status"
                                                   onclick="return confirm('Toggle user status?')">
                                                    <i class="fas fa-toggle-<?php echo $user['status'] == 'active' ? 'on' : 'off'; ?>"></i>
                                                </a>
                                                <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                                   class="btn btn-danger action-btn" 
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

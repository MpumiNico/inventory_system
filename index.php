<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include 'config.php';
//summary counts
$product_count = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$stock_count = $conn->query("SELECT COUNT(*) FROM stock")->fetch_row()[0];
$sales_count = $conn->query("SELECT COUNT(*) FROM sales")->fetch_row()[0];
$alert_count = $conn->query("SELECT COUNT(*) FROM stock WHERE quantity < min_threshold")->fetch_row()[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        .navbar {
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 16px rgba(31, 38, 135, 0.08);
            animation: fadeInDown 1s;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-primary {
            animation: fadeInUp 1s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .summary-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
            background: rgba(255,255,255,0.97);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .summary-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        }
        .summary-icon {
            font-size: 2.5rem;
        }
        .navbar-brand {
            background: transparent !important;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 animate__animated animate__fadeInDown">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#"><i class="bi bi-box-seam me-2"></i>Inventory System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-bag me-1"></i>Products</a></li>
                <li class="nav-item"><a class="nav-link" href="stock.php"><i class="bi bi-boxes me-1"></i>Stock</a></li>
                <li class="nav-item"><a class="nav-link" href="sales.php"><i class="bi bi-bar-chart-line me-1"></i>Sales</a></li>
                <li class="nav-item"><a class="nav-link" href="alert.php"><i class="bi bi-exclamation-triangle me-1"></i>Alerts</a></li>
                <li class="nav-item"><a class="nav-link text-danger d-flex align-items-center" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
                <li class="nav-item ms-3"><span class="badge bg-light text-primary p-2"><i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['username']; ?></span></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-primary text-center animate__animated animate__fadeInUp" role="alert">
                <h2>Welcome, </i> <?php echo $_SESSION['username']; ?> 👋</h2>
            </div>
        </div>
    </div>
    <div class="row g-4 justify-content-center mt-2 mb-5">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="summary-card p-4 text-center animate__animated animate__fadeInUp">
                <div class="summary-icon text-primary mb-2"><i class="bi bi-bag"></i></div>
                <h4 class="mb-1">Products</h4>
                <div class="display-6 fw-bold"><?php echo $product_count; ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="summary-card p-4 text-center animate__animated animate__fadeInUp" style="animation-delay:0.1s;">
                <div class="summary-icon text-success mb-2"><i class="bi bi-boxes"></i></div>
                <h4 class="mb-1">Stock Items</h4>
                <div class="display-6 fw-bold"><?php echo $stock_count; ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="summary-card p-4 text-center animate__animated animate__fadeInUp" style="animation-delay:0.2s;">
                <div class="summary-icon text-info mb-2"><i class="bi bi-bar-chart-line"></i></div>
                <h4 class="mb-1">Sales</h4>
                <div class="display-6 fw-bold"><?php echo $sales_count; ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="summary-card p-4 text-center animate__animated animate__fadeInUp" style="animation-delay:0.3s;">
                <div class="summary-icon text-danger mb-2"><i class="bi bi-exclamation-triangle"></i></div>
                <h4 class="mb-1">Alerts</h4>
                <div class="display-6 fw-bold"><?php echo $alert_count; ?></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

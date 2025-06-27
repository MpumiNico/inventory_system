<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        .card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            background: rgba(255,255,255,0.97);
        }
    </style>
</head>
<body>
<div class="container py-5 d-flex flex-column align-items-center">
    <a href="index.php" class="btn btn-outline-secondary mb-4 align-self-start animate__animated animate__fadeInLeft"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4 text-center"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Low Stock Notifications</h2>
        <?php
        $result = $conn->query("
            SELECT p.product_name, s.quantity, s.min_threshold
            FROM stock s
            JOIN products p ON s.product_id = p.product_id
            WHERE s.quantity < s.min_threshold
        ");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='alert alert-danger d-flex align-items-center animate__animated animate__fadeInDown mb-3'>
                        <i class='bi bi-exclamation-octagon-fill me-2'></i>
                        <div><strong>{$row['product_name']}</strong> is low on stock. Only <strong>{$row['quantity']}</strong> units left (Min: {$row['min_threshold']}).</div>
                      </div>";
            }
        } else {
            echo "<div class='alert alert-success animate__animated animate__fadeInDown'><i class='bi bi-check-circle-fill me-2'></i>No low stock alerts. All stock levels are healthy!</div>";
        }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

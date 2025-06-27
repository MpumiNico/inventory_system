<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Stock</title>
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
        .btn-primary {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px rgba(106,17,203,0.15);
        }
        table {
            background: white;
        }
    </style>
</head>
<body>
<div class="container py-5 d-flex flex-column align-items-center">
    <a href="index.php" class="btn btn-outline-secondary mb-4 align-self-start animate__animated animate__fadeInLeft"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    <div class="card p-4 mb-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4 text-center"><i class="bi bi-boxes me-2"></i>Update Stock</h2>
        <form method="post" class="row g-3">
            <div class="col-md-4">
                <input type="number" name="product_id" class="form-control" placeholder="Product ID" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="qty" class="form-control" placeholder="Quantity" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="min" class="form-control" placeholder="Min Threshold" required>
            </div>
            <div class="col-12">
                <button type="submit" name="update" class="btn btn-primary w-100"><i class="bi bi-arrow-repeat me-1"></i>Add/Update Stock</button>
            </div>
        </form>
        <?php
        // Handle form submission
        if (isset($_POST['update'])) {
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];
            $min = $_POST['min'];

            // Check if stock record exists
            $check = $conn->query("SELECT * FROM stock WHERE product_id = $product_id");
            if ($check->num_rows > 0) {
                $conn->query("UPDATE stock SET quantity = $qty, min_threshold = $min WHERE product_id = $product_id");
                echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Stock updated successfully!</div>";
            } else {
                $conn->query("INSERT INTO stock (product_id, quantity, min_threshold) VALUES ($product_id, $qty, $min)");
                echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Stock added successfully!</div>";
            }
        }
        ?>
    </div>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 900px;">
        <h3 class="mb-3"><i class="bi bi-clipboard-data me-2"></i>Current Stock</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Min Threshold</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Edit stock logic
                if (isset($_POST['update_stock'])) {
                    $id = intval($_POST['id']);
                    $qty = intval($_POST['qty']);
                    $min = intval($_POST['min']);
                    $stmt = $conn->prepare("UPDATE stock SET quantity=?, min_threshold=? WHERE product_id=?");
                    $stmt->bind_param("iii", $qty, $min, $id);
                    $stmt->execute();
                    echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Stock updated successfully!</div>";
                }
                // Delete stock logic
                if (isset($_GET['delete'])) {
                    $id = intval($_GET['delete']);
                    $conn->query("DELETE FROM stock WHERE product_id=$id");
                    echo "<div class='alert alert-danger mt-3 animate__animated animate__fadeInDown'>Stock deleted.</div>";
                }
                // Display edit form if edit param is set
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    $edit_result = $conn->query("SELECT s.*, p.product_name FROM stock s JOIN products p ON s.product_id = p.product_id WHERE s.product_id=$edit_id");
                    if ($edit_result && $edit_result->num_rows > 0) {
                        $edit_row = $edit_result->fetch_assoc();
                        ?>
                        <tr>
                            <form method="post">
                                <td><input type="hidden" name="id" value="<?php echo $edit_row['product_id']; ?>"><?php echo $edit_row['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($edit_row['product_name']); ?></td>
                                <td><input type="number" name="qty" class="form-control" value="<?php echo $edit_row['quantity']; ?>" required></td>
                                <td><input type="number" name="min" class="form-control" value="<?php echo $edit_row['min_threshold']; ?>" required></td>
                                <td><?php echo $edit_row['updated_at']; ?></td>
                                <td>
                                    <button type="submit" name="update_stock" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Save</button>
                                    <a href="stock.php" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Cancel</a>
                                </td>
                            </form>
                        </tr>
                        <?php
                    }
                }
                $result = $conn->query("SELECT s.*, p.product_name FROM stock s JOIN products p ON s.product_id = p.product_id");
                while ($row = $result->fetch_assoc()) {
                    // If editing this row, skip displaying it in the list
                    if (isset($_GET['edit']) && $_GET['edit'] == $row['product_id']) continue;
                    echo "<tr>
                            <td>{$row['product_id']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['min_threshold']}</td>
                            <td>{$row['updated_at']}</td>
                            <td>
                                <a href='?edit={$row['product_id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil'></i> Edit</a>
                                <a href='?delete={$row['product_id']}' onclick='return confirm(\'Delete this stock record?\')' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i> Delete</a>
                            </td>
                          </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

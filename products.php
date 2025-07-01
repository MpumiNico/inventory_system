<?php include 'config.php'; ?>
<?php mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
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
        .btn-danger {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-danger:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px rgba(255,0,0,0.10);
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
        <h2 class="mb-4 text-center"><i class="bi bi-bag-plus-fill me-2"></i>Add New Product</h2>
        <?php
        
        $suppliers = $conn->query("SELECT supplier_id, supplier_name FROM suppliers");
        ?>
        <form method="post" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Product Name" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="desc" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Price (e.g., 49.99)" required>
            </div>
            <div class="col-md-4">
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php while ($s = $suppliers->fetch_assoc()): ?>
                        <option value="<?php echo $s['supplier_id']; ?>"><?php echo htmlspecialchars($s['supplier_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" name="add" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i>Add Product</button>
            </div>
        </form>
        <?php
        
        if (isset($_POST['add'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $desc = $conn->real_escape_string($_POST['desc']);
            $price = $_POST['price'];
            $supplier_id = intval($_POST['supplier_id']);

            $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, supplier_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssdi", $name, $desc, $price, $supplier_id);
            $stmt->execute();
            echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Product added successfully!</div>";
        }

        
        if (isset($_GET['delete'])) {
            $id = intval($_GET['delete']);
            
            $conn->query("DELETE FROM sales WHERE product_id=$id");
            
            $conn->query("DELETE FROM stock WHERE product_id=$id");
            
            $conn->query("DELETE FROM products WHERE product_id=$id");
            echo "<div class='alert alert-danger mt-3 animate__animated animate__fadeInDown'>Product deleted.</div>";
        }
        ?>
    </div>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 900px;">
        <h3 class="mb-3"><i class="bi bi-list-ul me-2"></i>Product List</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Supplier</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                
                if (isset($_POST['update'])) {
                    $id = intval($_POST['id']);
                    $name = $conn->real_escape_string($_POST['name']);
                    $desc = $conn->real_escape_string($_POST['desc']);
                    $price = $_POST['price'];
                    $supplier_id = intval($_POST['supplier_id']);
                    $stmt = $conn->prepare("UPDATE products SET product_name=?, description=?, price=?, supplier_id=? WHERE product_id=?");
                    $stmt->bind_param("ssdii", $name, $desc, $price, $supplier_id, $id);
                    $stmt->execute();
                    echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Product updated successfully!</div>";
                }
                
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    $edit_result = $conn->query("SELECT * FROM products WHERE product_id=$edit_id");
                    if ($edit_result && $edit_result->num_rows > 0) {
                        $edit_row = $edit_result->fetch_assoc();
                        
                        $suppliers = $conn->query("SELECT supplier_id, supplier_name FROM suppliers");
                        ?>
                        <tr>
                            <form method="post">
                                <td><input type="hidden" name="id" value="<?php echo $edit_row['product_id']; ?>"><?php echo $edit_row['product_id']; ?></td>
                                <td><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_row['product_name']); ?>" required></td>
                                <td><input type="text" name="desc" class="form-control" value="<?php echo htmlspecialchars($edit_row['description']); ?>"></td>
                                <td><input type="number" step="0.01" name="price" class="form-control" value="<?php echo $edit_row['price']; ?>" required></td>
                                <td>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">Select Supplier</option>
                                        <?php while ($s = $suppliers->fetch_assoc()): ?>
                                            <option value="<?php echo $s['supplier_id']; ?>" <?php if ($edit_row['supplier_id'] == $s['supplier_id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['supplier_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="update" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Save</button>
                                    <a href="products.php" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Cancel</a>
                                </td>
                            </form>
                        </tr>
                        <?php
                    }
                }
                
                $result = $conn->query("SELECT p.*, s.supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        
                        if (isset($_GET['edit']) && $_GET['edit'] == $row['product_id']) continue;
                        echo "<tr>
                                <td>{$row['product_id']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['description']}</td>
                                <td>R {$row['price']}</td>
                                <td>" . (isset($row['supplier_name']) && $row['supplier_name'] ? htmlspecialchars($row['supplier_name']) .
                                    " <a href='supplier.php?supplier_id={$row['supplier_id']}' class='btn btn-info btn-sm ms-2'><i class='bi bi-eye'></i> View</a>"
                                    : "<span class='text-muted'>No Supplier</span>") . "</td>
                                <td>
                                    <a href='?edit={$row['product_id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil'></i> Edit</a>
                                    <a href='?delete={$row['product_id']}' onclick='return confirm(\"Delete this product?\")' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i> Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No products found.</td></tr>";
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

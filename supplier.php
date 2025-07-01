<?php
include 'config.php';


if (isset($_GET['supplier_id'])) {
    $supplier_id = intval($_GET['supplier_id']);
    $result = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $supplier_id");
    if ($result && $result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
    } else {
        echo "Supplier not found.";
        exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supplier Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
        .card { border-radius: 1.5rem; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2); background: rgba(255,255,255,0.97); }
    </style>
</head>
<body>
<div class="container py-5 d-flex flex-column align-items-center">
    <a href="supplier.php" class="btn btn-outline-secondary mb-4 align-self-start animate__animated animate__fadeInLeft"><i class="bi bi-arrow-left"></i> Back to Suppliers</a>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4 text-center"><i class="bi bi-person-badge me-2"></i>Supplier Information</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Name:</strong> <?php echo htmlspecialchars($supplier['supplier_name']); ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($supplier['email']); ?></li>
            <li class="list-group-item"><strong>Contact No:</strong> <?php echo htmlspecialchars($supplier['contact_no']); ?></li>
            <li class="list-group-item"><strong>ID:</strong> <?php echo $supplier['supplier_id']; ?></li>
        </ul>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    exit;
}


if (isset($_POST['add_supplier'])) {
    $name = $conn->real_escape_string($_POST['supplier_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact_no']);
    $conn->query("INSERT INTO suppliers (supplier_name, email, contact_no) VALUES ('$name', '$email', '$contact')");
    echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Supplier added successfully!</div>";
}

if (isset($_POST['update_supplier'])) {
    $id = intval($_POST['supplier_id']);
    $name = $conn->real_escape_string($_POST['supplier_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact_no']);
    $conn->query("UPDATE suppliers SET supplier_name='$name', email='$email', contact_no='$contact' WHERE supplier_id=$id");
    echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Supplier updated successfully!</div>";
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM suppliers WHERE supplier_id=$id");
    echo "<div class='alert alert-danger mt-3 animate__animated animate__fadeInDown'>Supplier deleted.</div>";
}


$edit_supplier = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_supplier = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Suppliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
        .card { border-radius: 1.5rem; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2); background: rgba(255,255,255,0.97); }
        .btn-primary { background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%); border: none; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-primary:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 4px 16px rgba(106,17,203,0.15); }
        .btn-danger { transition: transform 0.2s, box-shadow 0.2s; }
        .btn-danger:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 4px 16px rgba(255,0,0,0.10); }
        table { background: white; }
    </style>
</head>
<body>
<div class="container py-5 d-flex flex-column align-items-center">
    <a href="index.php" class="btn btn-outline-secondary mb-4 align-self-start animate__animated animate__fadeInLeft"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    <div class="card p-4 mb-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4 text-center"><i class="bi bi-person-plus-fill me-2"></i><?php echo $edit_supplier ? 'Edit Supplier' : 'Add New Supplier'; ?></h2>
        <form method="post" class="row g-3">
            <?php if ($edit_supplier): ?>
                <input type="hidden" name="supplier_id" value="<?php echo $edit_supplier['supplier_id']; ?>">
            <?php endif; ?>
            <div class="col-md-4">
                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['supplier_name']) : ''; ?>" required>
            </div>
            <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['email']) : ''; ?>" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="contact_no" class="form-control" placeholder="Contact No" value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['contact_no']) : ''; ?>" required>
            </div>
            <div class="col-12">
                <button type="submit" name="<?php echo $edit_supplier ? 'update_supplier' : 'add_supplier'; ?>" class="btn btn-primary w-100">
                    <i class="bi bi-<?php echo $edit_supplier ? 'check-circle' : 'plus-circle'; ?> me-1"></i><?php echo $edit_supplier ? 'Update Supplier' : 'Add Supplier'; ?>
                </button>
                <?php if ($edit_supplier): ?>
                    <a href="supplier.php" class="btn btn-secondary w-100 mt-2"><i class="bi bi-x-circle"></i> Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 900px;">
        <h3 class="mb-3"><i class="bi bi-list-ul me-2"></i>Supplier List</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM suppliers");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['supplier_id']}</td>
                                <td>{$row['supplier_name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['contact_no']}</td>
                                <td>
                                    <a href='supplier.php?supplier_id={$row['supplier_id']}' class='btn btn-info btn-sm me-1'><i class='bi bi-eye'></i> View</a>
                                    <a href='?edit={$row['supplier_id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil'></i> Edit</a>
                                    <a href='?delete={$row['supplier_id']}' onclick='return confirm(\'Delete this supplier?\')' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i> Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No suppliers found.</td></tr>";
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

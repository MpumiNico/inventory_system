<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Record Sales</title>
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
        <h2 class="mb-4 text-center"><i class="bi bi-cash-coin me-2"></i>Record a Sale</h2>
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <input type="number" name="product_id" class="form-control" placeholder="Product ID" required>
            </div>
            <div class="col-md-6">
                <input type="number" name="qty" class="form-control" placeholder="Quantity Sold" required>
            </div>
            <div class="col-12">
                <button type="submit" name="sell" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i>Record Sale</button>
            </div>
        </form>
        <?php
        if (isset($_POST['sell'])) {
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];
            $sale_date = date('Y-m-d');

            
            $conn->query("INSERT INTO sales (product_id, quantity_sold, sale_date) VALUES ($product_id, $qty, '$sale_date')");

            
            $conn->query("UPDATE stock SET quantity = quantity - $qty WHERE product_id = $product_id");

            echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Sale recorded successfully!</div>";
        }
        ?>
    </div>
    <div class="card p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i>Sales in the Last 30 Days</h3>
            <button id="download-pdf" class="btn btn-outline-primary"><i class="bi bi-file-earmark-arrow-down"></i> Download PDF</button>
        </div>
        <div class="mb-4">
            <canvas id="salesChart" height="100"></canvas>
        </div>
        <div class="table-responsive" id="sales-report-table">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Sale ID</th>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                
                if (isset($_POST['update'])) {
                    $id = intval($_POST['id']);
                    $qty = intval($_POST['qty']);
                    $date = $_POST['date'];
                    $stmt = $conn->prepare("UPDATE sales SET quantity_sold=?, sale_date=? WHERE sale_id=?");
                    $stmt->bind_param("isi", $qty, $date, $id);
                    $stmt->execute();
                    echo "<div class='alert alert-success mt-3 animate__animated animate__fadeInDown'>Sale updated successfully!</div>";
                }
                
                if (isset($_GET['delete'])) {
                    $id = intval($_GET['delete']);
                    $conn->query("DELETE FROM sales WHERE sale_id=$id");
                    echo "<div class='alert alert-danger mt-3 animate__animated animate__fadeInDown'>Sale deleted.</div>";
                }
                
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    $edit_result = $conn->query("SELECT s.*, p.product_name FROM sales s JOIN products p ON s.product_id = p.product_id WHERE s.sale_id=$edit_id");
                    if ($edit_result && $edit_result->num_rows > 0) {
                        $edit_row = $edit_result->fetch_assoc();
                        ?>
                        <tr>
                            <form method="post">
                                <td><input type="hidden" name="id" value="<?php echo $edit_row['sale_id']; ?>"><?php echo $edit_row['sale_id']; ?></td>
                                <td><?php echo htmlspecialchars($edit_row['product_name']); ?></td>
                                <td><input type="number" name="qty" class="form-control" value="<?php echo $edit_row['quantity_sold']; ?>" required></td>
                                <td><input type="date" name="date" class="form-control" value="<?php echo $edit_row['sale_date']; ?>" required></td>
                                <td>
                                    <button type="submit" name="update" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Save</button>
                                    <a href="sales.php" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Cancel</a>
                                </td>
                            </form>
                        </tr>
                        <?php
                    }
                }
                
                $chart_dates = [];
                $chart_quantities = [];
                $chart_data = $conn->query("SELECT sale_date, SUM(quantity_sold) as total_qty FROM sales WHERE sale_date >= CURDATE() - INTERVAL 30 DAY GROUP BY sale_date ORDER BY sale_date ASC");
                while ($c = $chart_data->fetch_assoc()) {
                    $chart_dates[] = $c['sale_date'];
                    $chart_quantities[] = $c['total_qty'];
                }
                $result = $conn->query("
                    SELECT s.sale_id, p.product_name, s.quantity_sold, s.sale_date
                    FROM sales s
                    JOIN products p ON s.product_id = p.product_id
                    WHERE s.sale_date >= CURDATE() - INTERVAL 30 DAY
                    ORDER BY s.sale_date DESC
                ");
                while ($row = $result->fetch_assoc()) {
                    
                    if (isset($_GET['edit']) && $_GET['edit'] == $row['sale_id']) continue;
                    echo "<tr>
                            <td>{$row['sale_id']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity_sold']}</td>
                            <td>{$row['sale_date']}</td>
                            <td>
                                <a href='?edit={$row['sale_id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil'></i> Edit</a>
                                <a href='?delete={$row['sale_id']}' onclick='return confirm(\'Delete this sale record?\')' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i> Delete</a>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>

const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_dates); ?>,
        datasets: [{
            label: 'Total Quantity Sold',
            data: <?php echo json_encode($chart_quantities); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Sales in the Last 30 Days' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

const downloadBtn = document.getElementById('download-pdf');
downloadBtn.addEventListener('click', function() {
    const pdf = new window.jspdf.jsPDF('l', 'pt', 'a4');
    
    html2canvas(document.getElementById('salesChart')).then(function(canvas) {
        const imgData = canvas.toDataURL('image/png');
        pdf.text('Sales Report (Last 30 Days)', 40, 40);
        pdf.addImage(imgData, 'PNG', 40, 60, 700, 200);
        
        html2canvas(document.getElementById('sales-report-table')).then(function(tableCanvas) {
            const tableImg = tableCanvas.toDataURL('image/png');
            pdf.addPage();
            pdf.addImage(tableImg, 'PNG', 40, 40, 700, 400);
            pdf.save('sales_report.pdf');
        });
    });
});
</script>
</body>
</html>

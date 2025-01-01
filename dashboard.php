<?php 
session_start();
if(!isset($_SESSION['session_username'])){
    header("location:index.php");
    exit();
}

include('config.php'); // Koneksi ke database

// Menampilkan data customer
$query = "SELECT * FROM customers";
$result = $conn->query($query);

$total_customers = $result->num_rows;

// Mengambil data jumlah customer per hari
$query = "SELECT DATE(tanggal_pemasangan) as day, COUNT(*) as total FROM customers GROUP BY DATE(tanggal_pemasangan)";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Menyusun data untuk chart
$days = [];
$daily_customer_counts = [];

while ($row = $result->fetch_assoc()) {
    $days[] = $row['day']; // Menyimpan tanggal (format YYYY-MM-DD)
    $daily_customer_counts[] = $row['total']; // Menyimpan jumlah pelanggan
}

$username = $_SESSION['session_username'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DLI-billing Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid d-flex align-items-center">
            <!-- Hamburger button -->
            <button class="btn btn-outline-primary me-3" id="hamburgerToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-billing"></i> DLI-billing</a>
            
            <!-- Right Section -->
            <div class="ms-auto d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary position-relative" id="notifDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown">
                        <li><a class="dropdown-item" href="#">New invoice created</a></li>
                        <li><a class="dropdown-item" href="#">Customer feedback received</a></li>
                        <li><a class="dropdown-item" href="#">Payment overdue alert</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                    </ul>
                </div>

                <!-- Profile -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none" href="#" id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="assets/images/dli.png" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="d-flex">
        <nav class="sidebar bg-light p-3" id="sidebarMenu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href=""><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="costumer.php"><i class="fas fa-users"></i> Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-receipt"></i> Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fab fa-whatsapp"></i> WhatsApp API</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="content p-4">
		<h3 class="mb-3">Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-wallet fa-2x text-danger mb-3"></i>
                            <h5>Total Unpaid</h5>
                            <h3>234</h3>
                            <p class="text-danger">⬇ 5%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-wallet fa-2x text-primary mb-3"></i>
                            <h5>Total Paid</h5>
                            <h3>234</h3>
                            <p class="text-danger">⬇ 5%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-user-check fa-2x text-success mb-3"></i>
                            <h5>Total Customers</h5>
                            <h3><?php echo $total_customers; ?></h3>
                            <p class="text-primary">Open</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-dollar-sign fa-2x text-warning mb-3"></i>
                            <h5>Earnings</h5>
                            <h3>Rp.8.000.000</h3>
                            <p class="text-success">New</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="mt-5">
                <h4>Pertambahan Costumer</h4>
                <canvas id="customerChart" height="150"></canvas>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
<script>
    var ctx = document.getElementById('customerChart').getContext('2d');
    var customerChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($days); ?>, // Tampilkan tanggal
            datasets: [{
                label: 'Jumlah Customer',
                data: <?php echo json_encode($daily_customer_counts); ?>, // Jumlah customer per hari
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Jumlah Pelanggan'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>

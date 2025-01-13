<?php
session_start();

if (!isset($_SESSION['session_username'])) {
    header("location:index.php");
    exit();
}

include('condli.php'); // Koneksi ke database

// Menambah paket
if (isset($_POST['add_paket'])) {
    $nama = $_POST['nama'];
    $paket_mbps = $_POST['paket_mbps'];
    $harga = $_POST['harga'];

    $query = "INSERT INTO paket_mbps (nama, paket_mbps, harga) VALUES ('$nama', '$paket_mbps', '$harga')";
    if ($conn->query($query) === TRUE) {
        header("Location: paket.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Mengedit paket
if (isset($_POST['edit_paket'])) {
    $id = $_POST['id_paket'];
    $nama = $_POST['nama'];
    $paket_mbps = $_POST['paket_mbps'];
    $harga = $_POST['harga'];

    $query = "UPDATE paket_mbps SET nama = '$nama', paket_mbps = '$paket_mbps', harga = '$harga' WHERE id_paket = $id";
    if ($conn->query($query) === TRUE) {
        header("Location: paket.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Menghapus paket
if (isset($_POST['delete_paket'])) {
    $id = $_POST['id_paket'];

    $query = "DELETE FROM paket_mbps WHERE id_paket = $id";
    if ($conn->query($query) === TRUE) {
        header("Location: paket.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Mendapatkan data paket
$query = "SELECT * FROM paket_mbps";
$result = $conn->query($query);

$username = $_SESSION['session_username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dli-billing - Paket Kecepatan</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Menambahkan Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv8l6+kL6rM8EfiB9lEmHnyUP6YYKdz+JoA2J6aFqgZ1z3J2p2S6Huk8lIg" crossorigin="anonymous">
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

                <!-- Name -->
                <div class="name me-3">
                    <h5><?php echo htmlspecialchars($username); ?></h5>
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

    <div class="d-flex">
        <nav class="sidebar bg-light p-3" id="sidebarMenu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href=""><i class="fa-solid fa-computer"></i> Paket Kecepatan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="costumer.php"><i class="fas fa-users"></i> Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-receipt"></i> Invoices</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="#" id="whatsappDropdown" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fab fa-whatsapp"></i> WhatsApp API
                </a>
                <ul class="collapse list-unstyled" id="whatsappSubMenu">
                <li>
                    <a class="nav-link ms-3 submenu-link" href="setup.php"><i class="fas fa-cogs"></i> Setup</a>
                </li>
                <li>
                    <a class="nav-link ms-3 submenu-link" href="logs.php"><i class="fas fa-file-alt"></i> Logs</a>
                </li>
                </ul>
            </ul>
        </nav>
    </div>

    <div class="container mt-5">
    <h2 class="text-center">Manajemen Paket Kecepatan</h2>

    <!-- Tombol untuk Menambah Customer -->
    <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addPaketForm">Tambah</button>
    </div>


    <!-- Form Tambah Paket -->
    <div class="collapse" id="addPaketForm" style="max-width: 700px; margin: 0 auto;">
        <div class="card card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Paket:</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="paket_mbps" class="form-label">Kecepatan (Mbps):</label>
                    <input type="number" class="form-control" name="paket_mbps" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga:</label>
                    <input type="number" class="form-control" name="harga" required>
                </div>
                <button type="submit" name="add_paket" class="btn btn-primary">Tambah Paket</button>
            </form>
        </div>
    </div>

    <!-- Tabel Paket -->
    <h3 class="mt-5">List Paket</h3>
    <div class="table-responsive" style="max-width: 700px; margin: 0 auto;">
        <table class="table table-striped">
            <thead class="bg-secondary text-white">
            <tr>
                <th>ID</th>
                <th>Nama Paket</th>
                <th>Kecepatan (Mbps)</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_paket']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['paket_mbps']; ?></td>
                    <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <!-- Tombol Edit -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="collapse" data-bs-target="#editPaketForm<?php echo $row['id_paket']; ?>">Edit</button>
                    </td>
                </tr>

                <!-- Form Edit Paket -->
                <tr class="collapse" id="editPaketForm<?php echo $row['id_paket']; ?>">
                    <td colspan="5">
                        <div class="card card-body">
                            <form method="POST">
                                <input type="hidden" name="id_paket" value="<?php echo $row['id_paket']; ?>">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Paket:</label>
                                    <input type="text" class="form-control" name="nama" value="<?php echo $row['nama']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="paket_mbps" class="form-label">Kecepatan (Mbps):</label>
                                    <input type="number" class="form-control" name="paket_mbps" value="<?php echo $row['paket_mbps']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga:</label>
                                    <input type="number" class="form-control" name="harga" value="<?php echo $row['harga']; ?>" required>
                                </div>
                                <button type="submit" name="edit_paket" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Menambahkan Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Custom JS -->
<script src="js/script.js"></script>
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

</body>
</html>

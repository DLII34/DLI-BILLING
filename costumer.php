<?php
session_start();

if(!isset($_SESSION['session_username'])){
    header("location:index.php");
    exit();
}

include('config.php'); // Koneksi ke database

// Menambahkan data customer
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_wa = $_POST['no_wa'];
    $tanggal_pemasangan = $_POST['tanggal_pemasangan'];
    
    // Mengambil id_paket yang dipilih
    if (isset($_POST['id_paket']) && !empty($_POST['id_paket'])) {
        $id_paket = $_POST['id_paket'];
    } else {
        echo "<div class='alert alert-danger'>Pilih paket terlebih dahulu.</div>";
        exit(); // Menyudahi eksekusi jika id_paket tidak dipilih
    }

    // Query untuk menambahkan data customer
    $query = "INSERT INTO customers (nama, alamat, no_wa, tanggal_pemasangan, id_paket) 
              VALUES ('$nama', '$alamat', '$no_wa', '$tanggal_pemasangan', '$id_paket')";

    if ($conn->query($query) === TRUE) {
        echo "<div class='alert alert-success'>Data berhasil ditambahkan</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $query . "<br>" . $conn->error . "</div>";
    }
}

// Mengedit data customer
if (isset($_POST['edit_submit'])) {
    $id = $_POST['id_costumer'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_wa = $_POST['no_wa'];
    $tanggal_pemasangan = $_POST['tanggal_pemasangan'];
    $id_paket = $_POST['id_paket'];  // Paket yang dipilih saat edit
    
    $query = "UPDATE customers SET nama = '$nama', alamat = '$alamat', no_wa = '$no_wa', 
              tanggal_pemasangan = '$tanggal_pemasangan', id_paket = '$id_paket' 
              WHERE id_client = $id";

    if ($conn->query($query) === TRUE) {
        echo "<div class='alert alert-success'>Data berhasil diperbarui</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Menampilkan data customer dengan paket
$query = "SELECT customers.*, paket_mbps.nama AS paket_nama, paket_mbps.paket_mbps 
          FROM customers
          LEFT JOIN paket_mbps ON customers.id_paket = paket_mbps.id_paket";
$result = $conn->query($query);

// Menampilkan data paket
$paket_query = "SELECT * FROM paket_mbps";
$paket_result = $conn->query($paket_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dli-billing - Costumer</title>
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

                <!-- Profile -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none" href="#" id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="assets/images/dli.png" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
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
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="costumer.php"><i class="fas fa-users"></i> Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-receipt"></i> Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fab fa-whatsapp"></i> WhatsApp API</a>
                </li>
            </ul>
        </nav>


<div class="content p-4">

        <h2 class="text-center">Data Customer</h2>

        <!-- Tombol untuk Menambah Customer -->
        <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addCustomerForm">Tambah Customer</button>
        </div>

        <!-- Form Menambah Customer -->
    <div class="collapse" id="addCustomerForm">
        <div class="card card-body">
            <h3>Tambah Customer</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama:</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat:</label>
                    <input type="text" class="form-control" name="alamat" required>
                </div>
                <div class="mb-3">
                    <label for="no_wa" class="form-label">No WA:</label>
                    <input type="text" class="form-control" name="no_wa" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_pemasangan" class="form-label">Tanggal Pemasangan:</label>
                    <input type="date" class="form-control" name="tanggal_pemasangan" required>
                </div>
                <!-- Dropdown Paket Kecepatan -->
                <div class="mb-3">
                    <label for="paket" class="form-label">Pilih Paket Kecepatan:</label>
                    <select id="paket" name="id_paket" class="form-control" required>
                        <option value="">Pilih Paket Kecepatan</option>
                        <?php
                        if ($paket_result->num_rows > 0) {
                            while ($paket_row = $paket_result->fetch_assoc()) {
                                echo "<option value='{$paket_row['id_paket']}'>
                                        {$paket_row['nama']} ({$paket_row['paket_mbps']} Mbps) - Rp" . number_format($paket_row['harga'], 0, ',', '.') . 
                                    "</option>";
                            }
                        } else {
                            echo "<option value=''>Paket tidak tersedia</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Tambah Customer</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Customer -->
    <h3 class="mt-5">List Customer</h3>
    <table class="table table-striped">
        <thead class="bg-secondary text-white">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No WA</th>
                <th>Tanggal Pemasangan</th>
                <th>Paket Kecepatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_client']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td><?php echo $row['no_wa']; ?></td>
                    <td><?php echo $row['tanggal_pemasangan']; ?></td>
                    <td>
                        <?php
                        // Menampilkan nama paket dan kecepatan berdasarkan id_paket
                        echo $row['paket_nama'] . " (" . $row['paket_mbps'] . " Mbps)";
                        ?>
                    </td>
                    <td class="actions">
                    <!-- Edit Data -->
                    <a href="costumer.php?edit_id=<?php echo $row['id_client']; ?>" class="btn btn-warning btn-sm">View</a>
                    </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



    <?php
    // Mengedit Data
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $edit_query = "SELECT * FROM customers WHERE id_client = $edit_id";
        $edit_result = $conn->query($edit_query);
        $edit_row = $edit_result->fetch_assoc();
    ?>

<div class="form-container">
    <h3>Edit Customer</h3>
    <form method="POST">
        <!-- Hidden ID field -->
        <input type="hidden" name="id_costumer" value="<?php echo $edit_row['id_client']; ?>">

        <!-- Nama Field -->
        <div class="mb-3">
            <label for="nama" class="form-label">Nama:</label>
            <input type="text" class="form-control" name="nama" value="<?php echo $edit_row['nama']; ?>" required>
        </div>

        <!-- Alamat Field -->
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat:</label>
            <input type="text" class="form-control" name="alamat" value="<?php echo $edit_row['alamat']; ?>" required>
        </div>

        <!-- No WA Field -->
        <div class="mb-3">
            <label for="no_wa" class="form-label">No WA:</label>
            <input type="text" class="form-control" name="no_wa" value="<?php echo $edit_row['no_wa']; ?>" required>
        </div>

        <!-- Tanggal Pemasangan Field -->
        <div class="mb-3">
            <label for="tanggal_pemasangan" class="form-label">Tanggal Pemasangan:</label>
            <input type="date" id="tanggal_pemasangan" class="form-control" name="tanggal_pemasangan" value="<?php echo $edit_row['tanggal_pemasangan']; ?>" required>
        </div>

        <!-- Dropdown Paket Kecepatan -->
        <div class="mb-3">
    <label for="paket" class="form-label">Pilih Paket Kecepatan:</label>
    <select id="paket" name="id_paket" class="form-control" required>
        <option value="">Pilih Paket Kecepatan</option>
        <?php
        // Pastikan query mengambil paket
        if ($paket_result->num_rows > 0) {
            while ($paket_row = $paket_result->fetch_assoc()) {
                // Periksa apakah id_paket yang dipilih sama dengan id_paket pada customer yang sedang diedit
                $selected = ($paket_row['id_paket'] == $edit_row['id_paket']) ? 'selected' : ''; // Menandai paket yang dipilih
                echo "<option value='{$paket_row['id_paket']}' $selected>
                        {$paket_row['nama']} ({$paket_row['paket_mbps']} Mbps) - Rp" . number_format($paket_row['harga'], 0, ',', '.') . 
                    "</option>";
            }
        } else {
            echo "<option value=''>Paket tidak tersedia</option>";
        }
        ?>
    </select>
</div>



        <!-- Delete Button -->
        <a href="costumer.php?delete_id=<?php echo $edit_row['id_client']; ?>" 
           class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a>

        <!-- Update Button -->
        <button type="submit" name="edit_submit" class="btn btn-success">Update</button>
    </form>
</div>


    <?php } ?>

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
<script>
    $(document).ready(function() {
        // Inisialisasi jQuery UI Datepicker
        $("#tanggal_pemasangan").datepicker({
            dateFormat: 'yy-mm-dd', // Formatkan tanggal sesuai dengan format MySQL
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:2100", // Sesuaikan rentang tahun jika perlu
            showButtonPanel: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": true,        // Pagination aktif
            "searching": true,     // Fitur pencarian aktif
            "ordering": true,      // Pengurutan aktif
            "info": true           // Informasi aktif
        });
    });
</script>


</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>



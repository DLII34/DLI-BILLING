<?php 
session_start();

//atur koneksi ke database
$host_db    = "localhost";
$user_db    = "root";
$pass_db    = "";
$nama_db    = "dli_billing";
$koneksi    = mysqli_connect($host_db,$user_db,$pass_db,$nama_db);
//atur variabel
$err        = "";
$username   = "";
$rm         = 0;

if(isset($_COOKIE['cookie_username'])){
    $cookie_username = $_COOKIE['cookie_username'];
    $cookie_password = $_COOKIE['cookie_password'];

    $sql1 = "select * from login where username = '$cookie_username'";
    $q1   = mysqli_query($koneksi,$sql1);
    $r1   = mysqli_fetch_array($q1);
    if($r1['password'] == $cookie_password){
        $_SESSION['session_username'] = $cookie_username;
        $_SESSION['session_password'] = $cookie_password;
    }
}

if(isset($_SESSION['session_username'])){
    header("location:dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rm       = isset($_POST['rm']) ? $_POST['rm'] : 0; // Pastikan ada nilai

    if ($username == '' || $password == '') {
        $err .= "<li>Silakan masukkan username dan juga password.</li>";
    } else {
        $sql1 = "SELECT * FROM login WHERE username = '$username'";
        $q1   = mysqli_query($koneksi, $sql1);
        $r1   = mysqli_fetch_array($q1);

        if (!$r1) {
            $err .= "<li>Username <b>$username</b> tidak ditemukan.</li>";
        } elseif ($r1['password'] != md5($password)) {
            $err .= "<li>Password tidak sesuai.</li>";
        }

        if (empty($err)) {
            $_SESSION['session_username'] = $username;
            $_SESSION['session_password'] = md5($password);

            if ($rm == 1) {
                setcookie("cookie_username", $username, time() + (60 * 60 * 24 * 30), "/");
                setcookie("cookie_password", md5($password), time() + (60 * 60 * 24 * 30), "/");
            }
            header("location:dashboard.php");
            exit();
        }
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <title>Login Sek Lekk</title>
</head>
<body>
    <div class="wrapper">
        <div class="logo">
            <img src="https://i.ibb.co.com/nCpKbPW/Whats-App-Image-2024-12-04-at-06-33-38.jpg" alt="Logo">
        </div>
        <div class="text-center name">
            Log-In
        </div>
        <form id="loginform" class="form-horizontal" action="" method="post" role="form">
            <div class="form-field d-flex align-items-center">
                <input id="login-username" type="text" name="username" value="<?php echo $username ?>" placeholder="username">
            </div>
            <div class="form-field d-flex align-items-center">
                <input id="login-password" type="password" name="password" placeholder="password">
            </div>
            <button class="btn" name="login">Login</button>
        </form>
        <div style="padding-top:30px" class="panel-body" >
                <?php if($err){ ?>
                    <div id="login-alert" class="alert alert-danger col-sm-12">
                        <ul><?php echo $err ?></ul>
                    </div>
                <?php } ?>
        </div>
        <div class="input-group">
            <div class="checkbox">
                <label>
                    <input id="login-remember" type="checkbox" name="rm" value="1" <?php if($rm == '1') echo "checked"?>> remember me
                </label>
            </div>
        </div>
        <div class="text-center fs-6">
            <a href="#">Forget password?</a>
        </div>
    </div>
</body>
</html>
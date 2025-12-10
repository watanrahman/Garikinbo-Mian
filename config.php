<?php
    // Database Config
    $host = 'localhost';
    $db   = 'car_auction';
    $db_user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    // Admin Panel Config
    define('ADMIN_EMAIL', 'admin@gmail.com');
    define('ADMIN_PASS', 'admin');
    define('ADMIN_TOKEN', hash('sha256', ADMIN_EMAIL . ADMIN_PASS . 'SALT_KEY'));

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $db_user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Force MySQL to use Asia/Dhaka timezone
        $pdo->exec("SET time_zone = '+06:00'");
    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }

    // Define Asia/Dhaka timezone
    date_default_timezone_set('Asia/Dhaka');

    // File Root
    $main_url = "http://localhost/bid";
    $get_api = "http://localhost/bid/api";
    define('ROOT_PATH', __DIR__ . '/');


    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Get User Information
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users 
            WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['profile_status'] === 'banned') {
            if ($_SERVER['REQUEST_URI'] === "/bid/auth/profile.php") {
                // exit();
                
            }
            else {
                header("Location: " . $main_url . "/auth/profile.php");
            }
            // exit();
        }
    }


    // Session Auth
    function auth_check() {
        global $main_url;

        // Allow logged-in users
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return;
        }else if (isset($_SESSION['auth_token']) && $_SESSION['auth_token'] === ADMIN_TOKEN) {
            return;
        }else {
            header("Location: " . $main_url . "/auth/signin.php");
            exit;
        }

        // Otherwise redirect to signin

    }

    // Admin Session Auth
    function admin_token_check(){
        if (!isset($_SESSION['auth_token']) || $_SESSION['auth_token'] !== ADMIN_TOKEN) {
            header("Location: " . $main_url);
            exit;
        }        
    }


    // Infinity Free Server Information
    // Domain: garikinbo.ct.ws
    // Pass: CUMIuJKRlF
    // User: if0_40122104
?>

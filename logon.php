<?php
// Secure session settings (must be before session_start())
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);

session_start();
ob_start();
include_once("site-monitor.conf.php");

$conn = new mysqli(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD, NETZ_DATABASE);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$max_session_time = 36000; // 10 hours

// Get user input safely
$submitteduser = isset($_POST['user']) ? trim($_POST['user']) : '';
$submittedpass = isset($_POST['pass']) ? trim($_POST['pass']) : '';

// Check if username and password are provided
if (!empty($submitteduser) && !empty($submittedpass)) {
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE USERNAME = ?");
    $stmt->bind_param("s", $submitteduser);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password with password_hash()
        if (password_verify($submittedpass, $row['PASSWORD'])) {
            session_regenerate_id(true); // Prevent session fixation

            $_SESSION['user'] = $row['USERNAME'];
            $_SESSION['accesstype'] = $row['ACCESSTYPE'];
            $_SESSION['accesslevel'] = $row['ACCESSLEVEL'];
            $_SESSION['session_expires'] = time() + $max_session_time;
            $_SESSION['name'] = !empty($row['FULL_NAME']) ? $row['FULL_NAME'] : $row['USERNAME'];
  // **PROCESS USER STYLE LIKE THE ORIGINAL CODE**
    if (!empty($row['STYLE'])) {
        $options = explode(":", $row['STYLE']); // Split the stored style options

        // Assign values based on the parsed style options
        $_SESSION['style'] = isset($options[STYLESHEET]) ? $options[STYLESHEET] : "style/ultramarine.css";
        $_SESSION['support'] = isset($options[SUPPORT]) ? $options[SUPPORT] : "";
        
        // Assign menu display settings
        $_SESSION['menu1'] = isset($options[MENU1]) ? $options[MENU1] : "block";
        $_SESSION['menu2'] = isset($options[MENU2]) ? $options[MENU2] : "block";
        $_SESSION['menu3'] = isset($options[MENU3]) ? $options[MENU3] : "block";
        $_SESSION['menu4'] = isset($options[MENU4]) ? $options[MENU4] : "block";
        $_SESSION['menu5'] = isset($options[MENU5]) ? $options[MENU5] : "block";
    } else {
        // Default style if nothing is stored in the database
        $_SESSION['style'] = "style/ultramarine.css";
    }

            // Redirect to main page
            header("Location: main.php");
            exit();
        }
    }

    // Login failed
    $_SESSION['error'] = "Invalid username or password!";
    header("Location: logon.php");
    exit();
}

// If user is already logged in, redirect to main page
if (!empty($_SESSION['user'])) {
    header("Location: main.php");
    exit();
}

// If no login attempt, display the login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NETz Logon</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="style/ultramarine.css" type="text/css">
</head>
<body>
    <center>
        <img src="netz.jpg" width="216" height="64">
        <br /><br /><br />

        <form action="logon.php" method="post">
            <fieldset>
                <legend>Netz Login</legend>
                <label for="user">Username</label><br />
                <input name="user" type="text" id="user" required autofocus>
                <br />
                <label for="pass">Password</label><br />
                <input name="pass" type="password" id="pass" required>
                <br /><br />
                <input name="submit" type="submit" value="Login"><br>
		<a href="password-send.php" tabindex="5">lost password?</a>
            </fieldset>
        </form>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']); // Remove error message after displaying
        }
        ?>
    </center>
</body>
</html>

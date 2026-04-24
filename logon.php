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
// Set safe defaults first
$_SESSION['style']  = "style/ultramarine.css";
$_SESSION['support'] = "all";
$_SESSION['menu1'] = "block";
$_SESSION['menu2'] = "block";
$_SESSION['menu3'] = "block";
$_SESSION['menu4'] = "block";
$_SESSION['menu5'] = "block";

// Now override from DB if STYLE exists
if (!empty($row['STYLE'])) {
    $options = explode(":", $row['STYLE']);

    if (isset($options[0]) && $options[0] != "") $_SESSION['style']  = $options[0];
    if (isset($options[1]) && $options[1] != "") $_SESSION['support'] = $options[1];

    if (isset($options[2]) && $options[2] != "") $_SESSION['menu1'] = $options[2];
    if (isset($options[3]) && $options[3] != "") $_SESSION['menu2'] = $options[3];
    if (isset($options[4]) && $options[4] != "") $_SESSION['menu3'] = $options[4];
    if (isset($options[5]) && $options[5] != "") $_SESSION['menu4'] = $options[5];
    if (isset($options[6]) && $options[6] != "") $_SESSION['menu5'] = $options[6];
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

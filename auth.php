<?php
session_start();
include_once('site-monitor.conf.php'); // Required for DB connection details

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: logon.php");
    exit();
}

// Enforce session expiration check
if (isset($_SESSION['session_expires']) && time() > $_SESSION['session_expires']) {
    session_unset();
    session_destroy();
    header("Location: logon.php");
    exit();
}

// Extend session expiration (only if not expired)
$_SESSION['session_expires'] = time() + 36000; // Reset session timeout

// Database connection
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD, NETZ_DATABASE);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch the user's style setting from the database (procedural way)
$user = mysqli_real_escape_string($conn, $_SESSION['user']); // Escape user input
$sql = "SELECT STYLE FROM USERS WHERE USERNAME = '$user'";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $style_data = $row['STYLE'];

    // Extract the actual style value (same as logon.php)
    $style_parts = explode(":", $style_data);
    $_SESSION['style'] = isset($style_parts[0]) ? trim($style_parts[0]) : "style/ultramarine.css"; // Default if empty
}

// Close the database connection
mysqli_free_result($result);
mysqli_close($conn);
?>

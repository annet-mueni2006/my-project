
<?php

$host = "localhost";
$dbname = "annet_agriculture";
$username = "root";
$password = "";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Enable error reporting
    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Fetch database records as associative arrays
    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    // Use native prepared statements
    $conn->setAttribute(
        PDO::ATTR_EMULATE_PREPARES,
        false
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    die("Database connection failed. Please try again later.");

}

?>

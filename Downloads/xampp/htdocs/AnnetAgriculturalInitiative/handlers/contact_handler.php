
<?php

session_start();

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../contact.php");
    exit();

}

// Receive form data
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Check for empty fields
if ($full_name === '' || $email === '' || $message === '') {

    $_SESSION['contact_status'] = [
        'success' => false,
        'message' => 'Please fill in all fields.'
    ];

    header("Location: ../contact.php");
    exit();

}

// Validate input lengths
if (
    strlen($full_name) > 100 ||
    strlen($email) > 150 ||
    strlen($message) > 5000
) {

    $_SESSION['contact_status'] = [
        'success' => false,
        'message' => 'One or more fields exceed the allowed length.'
    ];

    header("Location: ../contact.php");
    exit();

}

// Validate email address
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['contact_status'] = [
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ];

    header("Location: ../contact.php");
    exit();

}

// Connect to database
require_once "../config/db_connect.php";

try {

    // Prepare SQL statement
    $sql = "INSERT INTO contact_messages
            (full_name, email, message)
            VALUES
            (:full_name, :email, :message)";

    $stmt = $conn->prepare($sql);

    // Execute query
    $stmt->execute([

        ':full_name' => $full_name,

        ':email' => $email,

        ':message' => $message

    ]);

    // Success message
    $_SESSION['contact_status'] = [

        'success' => true,

        'message' => 'Thank you! Your message has been sent successfully.'

    ];

} catch (PDOException $e) {

    // Log the actual database error privately
    error_log($e->getMessage());

    $_SESSION['contact_status'] = [

        'success' => false,

        'message' => 'Something went wrong. Please try again later.'

    ];

}

// Redirect back to contact page
header("Location: ../contact.php");

exit();

?>

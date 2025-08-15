<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(strip_tags($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags($_POST['subject']));
    $message = htmlspecialchars(strip_tags($_POST['message']));

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";

    if (empty($errors)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            // Send email notification
            $to = "davidmulei899@gmail.com";
            $headers = "From: $email" . "\r\n" .
                       "Reply-To: $email" . "\r\n" .
                       "X-Mailer: PHP/" . phpversion();
            
            $email_body = "You have received a new message from your portfolio:\n\n".
                          "Name: $name\n".
                          "Email: $email\n\n".
                          "Subject: $subject\n\n".
                          "Message:\n$message";
            
            mail($to, "Portfolio Contact: $subject", $email_body, $headers);
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
$conn->close();
?>
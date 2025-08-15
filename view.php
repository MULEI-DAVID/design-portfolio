<?php
require 'config.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();

if (!$message) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <a href="admin.php" class="text-blue-500 mb-4 inline-block">← Back to messages</a>
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Message from <?= htmlspecialchars($message['name']) ?></h1>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="mt-1"><?= htmlspecialchars($message['name']) ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1"><?= htmlspecialchars($message['email']) ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Subject</label>
                    <p class="mt-1"><?= htmlspecialchars($message['subject']) ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date</label>
                    <p class="mt-1"><?= date('M d, Y h:i A', strtotime($message['created_at'])) ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Message</label>
                    <p class="mt-1 whitespace-pre-line"><?= htmlspecialchars($message['message']) ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();
require_once('../../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sales = $_POST['sales'];
    $category = $_POST['category'];
    $inventory = $_POST['inventory'];
    $status = $_POST['status'];

    $targetDir = '../../sources/commodity/';
    $imagePath = '';

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        echo json_encode(['success' => false, 'message' => 'Image upload is required.']);
        exit;
    }

    $file = $_FILES['image'];
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid(). '.'. $fileExtension;
    $targetFile = $targetDir. $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
        echo json_encode(['success' => false,'message' => 'Only JPG, JPEG, and PNG files are allowed.']);
        exit;
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $imagePath = $targetDir. $fileName;
    } else {
        echo json_encode(['success' => false,'message' => 'Error uploading file.']);
        exit;
    }

    $insertStmt = $conn->prepare("INSERT INTO commodity (name, price, sales, category, inventory, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param('sdissss', $name, $price, $sales, $category, $inventory, $status, $imagePath);
    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'image_path' => $imagePath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting into database: ' . $insertStmt->error]);
    }
    $insertStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
<?php
session_start();
require_once('../../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sales = $_POST['sales'];
    $category = $_POST['category'];
    $inventory = $_POST['inventory'];
    $status = $_POST['status'];

    $targetDir = '../../sources/commodity/';
    $imagePath = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
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
    } else {
        $imagePath = $_POST['image'];
    }

    $updateStmt = $conn->prepare("UPDATE commodity SET name =?, price =?, sales =?, category =?, inventory =?, status =?, image =? WHERE id =?");
    $updateStmt->bind_param('sdissssi', $name, $price, $sales, $category, $inventory, $status, $imagePath, $id);
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'image_path' => $imagePath]);
    } else {
        echo json_encode(['success' => false,'message' => 'Error updating database: '. $updateStmt->error]);
    }
    $updateStmt->close();
} else {
    echo json_encode(['success' => false,'message' => 'Invalid request method.']);
}
?>
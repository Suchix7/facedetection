<?php
header('Content-Type: application/json');

// Check if image was uploaded
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded']);
    exit;
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Save the uploaded image
$uploadedFile = $_FILES['image']['tmp_name'];
$imagePath = 'uploads/detect_' . time() . '.jpg';
move_uploaded_file($uploadedFile, $imagePath);

// Use Python script for face detection
$command = "python face_detect.py " . escapeshellarg($imagePath);
$output = shell_exec($command);
$result = json_decode($output, true);

// Clean up the temporary image
unlink($imagePath);

if (!$result || !isset($result['success']) || !$result['success']) {
    echo json_encode(['success' => false, 'message' => 'Face detection failed']);
    exit;
}

echo json_encode($result);
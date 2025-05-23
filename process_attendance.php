<?php
// Disable error reporting for warnings
error_reporting(E_ALL & ~E_WARNING);

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
$imagePath = 'uploads/capture_' . time() . '.jpg';
move_uploaded_file($uploadedFile, $imagePath);

// Use Python script for face recognition
$command = "python recognize_face.py " . escapeshellarg($imagePath);
$output = shell_exec($command);
$result = json_decode($output, true);

// Clean up the temporary image
unlink($imagePath);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Face recognition failed - No response from recognition script']);
    exit;
}

if (!isset($result['success'])) {
    echo json_encode(['success' => false, 'message' => 'Face recognition failed - Invalid response format']);
    exit;
}

if (!$result['success']) {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Face recognition failed']);
    exit;
}

// Check if face was detected but not recognized
if (!isset($result['student_id'])) {
    echo json_encode([
        'success' => true,
        'message' => 'Face detected but not recognized',
        'face_location' => $result['face_location'] ?? null
    ]);
    exit;
}

// Load student info
$studentInfoPath = "students/{$result['student_id']}/info.json";
if (!file_exists($studentInfoPath)) {
    echo json_encode(['success' => false, 'message' => 'Student information not found']);
    exit;
}

$studentInfo = json_decode(file_get_contents($studentInfoPath), true);
if (!$studentInfo) {
    echo json_encode(['success' => false, 'message' => 'Invalid student information']);
    exit;
}

// Save attendance record
$attendanceFile = 'attendance.json';
$records = [];
if (file_exists($attendanceFile)) {
    $records = json_decode(file_get_contents($attendanceFile), true);
}

// Check if attendance already marked today
$today = date('Y-m-d');
$alreadyMarked = false;
foreach ($records as $record) {
    if (
        isset($record['student_id']) &&
        $record['student_id'] === $result['student_id'] &&
        date('Y-m-d', strtotime($record['time'])) === $today
    ) {
        $alreadyMarked = true;
        break;
    }
}

if ($alreadyMarked) {
    echo json_encode([
        'success' => true,
        'name' => $studentInfo['name'],
        'message' => 'Attendance already marked for today',
        'face_location' => $result['face_location'] ?? null,
        'records' => $records
    ]);
    exit;
}

$records[] = [
    'student_id' => $result['student_id'],
    'name' => $studentInfo['name'],
    'time' => date('Y-m-d H:i:s'),
    'confidence' => $result['confidence']
];

file_put_contents($attendanceFile, json_encode($records));

// Return success response
echo json_encode([
    'success' => true,
    'name' => $studentInfo['name'],
    'message' => 'Attendance marked successfully',
    'face_location' => $result['face_location'] ?? null,
    'records' => $records
]);
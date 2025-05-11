<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include "../Connect_DataBase.php";

$data = json_decode(file_get_contents('php://input'), true);
$response = ["success" => false, "messages" => []];

if (isset($data['studentID'], $data['lectureArray'], $data['sectionArray'])) {
    $studentID = intval($data['studentID']);
    $lectureArray = $data['lectureArray'];
    $sectionArray = $data['sectionArray'];
    $enrollDate = date("Y-m-d");

    // Prepare the statement for inserting into Enroll table
    $stmt = $conn->prepare("INSERT INTO Enroll (Student_ID, Course_Code, Enrollment_Date, Grade, LectureTime_ID, SectionTime_ID)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE Grade = VALUES(Grade), LectureTime_ID = VALUES(LectureTime_ID), SectionTime_ID = VALUES(SectionTime_ID)");
    
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $success = true;

    // Handle lectures
    foreach ($lectureArray as $lecture) {
        $courseCode = $lecture['Course_Code']; // Assuming the course code is provided
        $grade = $lecture['Grade']; // Assuming grade is provided or set to default
        $lectureTimeID = $lecture['LectureTime_ID']; // Assuming this value is provided
        $sectionTimeID = null; // No section time for lecture
        
        $stmt->bind_param("isssss", $studentID, $courseCode, $enrollDate, $grade, $lectureTimeID, $sectionTimeID);
        if (!$stmt->execute()) {
            $success = false;
            $response['messages'][] = "Lecture insert failed: " . $stmt->error;
        }
    }

    // Handle sections
    foreach ($sectionArray as $section) {
        $courseCode = $section['Course_Code']; // Assuming the course code is provided
        $grade = $section['Grade']; // Assuming grade is provided or set to default
        $lectureTimeID = null; // No lecture time for section
        $sectionTimeID = $section['SectionTime_ID']; // Assuming this value is provided
        
        $stmt->bind_param("isssss", $studentID, $courseCode, $enrollDate, $grade, $lectureTimeID, $sectionTimeID);
        if (!$stmt->execute()) {
            $success = false;
            $response['messages'][] = "Section insert failed: " . $stmt->error;
        }
    }

    $stmt->close();
    $response['success'] = $success;
} else {
    $response['messages'][] = "Invalid request payload.";
}

echo json_encode($response);
exit; // Ensure no extra output is sent
?>

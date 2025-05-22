<?php

error_reporting(E_ALL);

header('Content-Type: application/json');
include "../Connect_DataBase.php";

$data = json_decode(file_get_contents('php://input'), true);
$response = ["success" => false, "messages" => []];

if (isset($data['studentID'], $data['lectureArray'], $data['sectionArray'])) 
{
    $studentID = intval($data['studentID']);
    $lectureArray = $data['lectureArray'];
    $sectionArray = $data['sectionArray'];
    $enrollDate = date("Y-m-d");

    
    $stmt = $conn->prepare("INSERT INTO enrollment (Student_ID, Course_Code, Enrollment_Date, Grade, LectureTime_ID, SectionTime_ID)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE Grade = VALUES(Grade), LectureTime_ID = VALUES(LectureTime_ID), SectionTime_ID = VALUES(SectionTime_ID)");
    
    if (!$stmt) 
    {
        $errorResponse = ["success" => false, "message" => "Prepare failed: " . $conn->error];
        $json = json_encode($errorResponse);

        if ($json === false) {
            echo json_encode([
                "success" => false,
                "message" => "JSON encode error: " . json_last_error_msg()
            ]);
            exit;
        }

    echo $json;
    exit;

    }

    $success = true;

    // Handle lectures
    foreach ($lectureArray as $lecture)
    {
        $courseCode = $lecture['Course_Code']; 
        $grade = $lecture['Grade']; 
        $lectureTimeID = $lecture['LectureTime_ID'];
        $sectionTimeID = null;
        
        foreach ($sectionArray as $section) 
        {
            if($section['Course_Code'] === $courseCode)
            {
                $sectionTimeID = $section['SectionTime_ID'];
                break;
            }
        }

        $stmt->bind_param("isssii", $studentID, $courseCode, $enrollDate, $grade, $lectureTimeID, $sectionTimeID);
        if (!$stmt->execute()) 
        {
            $success = false;
            $response['messages'][] = "Section insert failed: " . $stmt->error;
        }
    }

    $stmt->close();
    $response['success'] = $success;
} 
else 
{
    $response['messages'][] = "Invalid request payload.";
}

$json = json_encode($response);

if ($json === false) 
{
    echo json_encode([
        "success" => false,
        "message" => "JSON encoding error: " . json_last_error_msg()
    ]);
    exit;
}
$conn->close();
echo $json;
exit;
?>

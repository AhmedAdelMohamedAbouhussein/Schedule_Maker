<?php
session_start(); // Required to use $_SESSION
include '../Connect_DataBase.php';

// Set the correct header before output
header('Content-Type: application/json');

// Get current student ID from session
$studentID = $_SESSION['ID']; 

$lectureDetails = [];  // Array to hold lecture details
$sectionDetails = [];  // Array to hold section details

// =================== First Query: Get Lecture and Section Data ===================

$sql = "
SELECT 
    E.Course_Code,
    C.Name AS Course_Name,
    LT.Day_of_Week AS Lecture_Day_of_Week,
    LT.Start_Time AS Lecture_Start_Time,
    LT.End_Time AS Lecture_End_Time,
    LT.LectureTime_ID,
    LT.Room AS Lecture_Room,
    ST.Day_of_Week AS Section_Day_of_Week,
    ST.Start_Time AS Section_Start_Time,
    ST.End_Time AS Section_End_Time,
    ST.SectionTime_ID,
    ST.Room AS Section_Room,
    L.Name AS Lecturer_Name,
    T.Name AS Tutor_Name
FROM Enrollment E
JOIN LectureTime LT ON E.LectureTime_ID = LT.LectureTime_ID
JOIN SectionTime ST ON E.SectionTime_ID = ST.SectionTime_ID
JOIN LecturerCourse LC ON E.Course_Code = LC.Course_Code AND E.LectureTime_ID = LC.Course_Lecture_ID
JOIN Lecturer L ON LC.Lecturer_ID = L.Lecturer_ID
JOIN TutorCourse TC ON E.Course_Code = TC.Course_Code AND E.SectionTime_ID = TC.Course_Section_ID
JOIN Tutor T ON TC.Tutor_ID = T.Tutor_ID
JOIN Course C ON E.Course_Code = C.Course_Code
JOIN Student S ON E.Student_ID = S.Student_ID
WHERE S.Student_ID = ?
GROUP BY E.Course_Code, LT.LectureTime_ID, ST.SectionTime_ID
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare statement failed (lecture query)']);
    exit();
}

$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['error' => 'Get result failed (lecture query)']);
    exit();
}

while ($row = $result->fetch_assoc()) 
{
    $lectureDetails[] = [
        'Course_Code'    => $row['Course_Code'],
        'Day_of_Week'    => $row['Lecture_Day_of_Week'],
        'Start_Time'     => $row['Lecture_Start_Time'],
        'End_Time'       => $row['Lecture_End_Time'],
        'LectureTime_ID' => $row['LectureTime_ID'],
        'Lecturer_Name'  => $row['Lecturer_Name'],
        'Name'           => $row['Course_Name'],
        'Room'           => $row['Lecture_Room']
    ];

    $sectionDetails[] = [
        'Course_Code'    => $row['Course_Code'],
        'Day_of_Week'    => $row['Section_Day_of_Week'],
        'Start_Time'     => $row['Section_Start_Time'],
        'End_Time'       => $row['Section_End_Time'],
        'SectionTime_ID' => $row['SectionTime_ID'],
        'Lecturer_Name'  => $row['Lecturer_Name'],
        'Name'           => $row['Course_Name'],
        'Room'           => $row['Section_Room'],
        'Tutor_Name'     => $row['Tutor_Name']
    ];
}

echo json_encode([
    'lectures' => $lectureDetails,
    'sections' => $sectionDetails
]);

$conn->close();
?>

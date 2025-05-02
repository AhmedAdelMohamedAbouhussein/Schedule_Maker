<?php
session_start(); // Required to use $_SESSION
include 'Connect_DataBase.php';

// Input: Student ID
$studentID = $_SESSION['ID']; // Or fetch from $_GET/$_POST

// Step 1: Get the student's department
$sql = "SELECT Department_Name FROM Department WHERE Department_ID = (SELECT Department_ID FROM Student WHERE Student_ID = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$departmentRow = $result->fetch_assoc();
$department = $departmentRow['Department_Name'];

// Step 2: Get all completed courses
$sql = "SELECT Course_Code FROM CompletedCourses WHERE Student_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$completedCourses = [];
while ($row = $result->fetch_assoc()) {
    $completedCourses[] = $row['Course_Code'];
}

if (!empty($completedCourses)) {
    // Step 3: Get first 6 recommended courses where completed course is a prerequisite
    $placeholders = implode(',', array_fill(0, count($completedCourses), '?'));
    $sql = "SELECT DISTINCT Course_Code FROM Prerequisite 
            WHERE Prerequisite_Code IN ($placeholders) 
            LIMIT 6";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($completedCourses)), ...$completedCourses);
    $stmt->execute();
    $result = $stmt->get_result();

    $recommendedCourses = [];
    while ($row = $result->fetch_assoc()) {
        $recommendedCourses[] = $row['Course_Code'];
    }
}
else {
    // Step 4: Assign courses based on the student's department
    $recommendedCourses = [];

    if ($department == 'Computer Science') {
        // Assign courses from CS101 to CS106
        $recommendedCourses = ['CS101', 'CS102', 'CS103', 'CS104', 'CS105', 'CS106'];
    } elseif ($department == 'Engineering') {
        // Assign courses from ENG101 to ENG106
        $recommendedCourses = ['ENG101', 'ENG102', 'ENG103', 'ENG104', 'ENG105', 'ENG106'];
    } elseif ($department == 'Law') {
        // Assign courses from LAW101 to LAW106
        $recommendedCourses = ['LAW101', 'LAW102', 'LAW103', 'LAW104', 'LAW105', 'LAW106'];
    } elseif ($department == 'Business') {
        // Assign courses from BUS101 to BUS106
        $recommendedCourses = ['BUS101', 'BUS102', 'BUS103', 'BUS104', 'BUS105', 'BUS106'];
    }
}

if (empty($recommendedCourses)) {
    exit; // No recommended courses
}

// Arrays to store results
$lectureSchedules = [];
$sectionSchedules = [];

// Step 5: Loop through recommended courses
foreach ($recommendedCourses as $courseCode) {

    // Get Course Name
    $sql = "SELECT Name FROM Course WHERE Course_Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $courseRow = $result->fetch_assoc();
    $courseName = $courseRow['Name'];

    // Get Lecturer info
    $sql = "SELECT L.Name AS LecturerName, LT.Day_of_Week, LT.Start_Time, LT.End_Time, LT.Room
            FROM LecturerCourse LC
            JOIN Lecturer L ON LC.Lecturer_ID = L.Lecturer_ID
            JOIN LectureTime LT ON LC.Course_Lecture_ID = LT.Lecture_ID
            WHERE LC.Course_Code = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $lectureSchedules[] = [
            'Course_Code' => $courseCode,
            'Name' => $courseName, // Added course name
            'Lecturer_Name' => $row['LecturerName'],
            'Day_of_Week' => $row['Day_of_Week'],
            'Start_Time' => $row['Start_Time'],
            'End_Time' => $row['End_Time'],
            'Room' => $row['Room']
        ];
    }

    // Get Tutor info
    $sql = "SELECT T.Name AS TutorName, ST.Day_of_Week, ST.Start_Time, ST.End_Time, ST.Room
            FROM TutorCourse TC
            JOIN Tutor T ON TC.Tutor_ID = T.Tutor_ID
            JOIN SectionTime ST ON TC.Course_Section_ID = ST.Section_ID
            WHERE TC.Course_Code = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sectionSchedules[] = [
            'Course_Code' => $courseCode,
            'Name' => $courseName, // Added course name
            'Tutor_Name' => $row['TutorName'],
            'Day_of_Week' => $row['Day_of_Week'],
            'Start_Time' => $row['Start_Time'],
            'End_Time' => $row['End_Time'],
            'Room' => $row['Room']
        ];
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script>
    const lectureSchedules = <?php echo json_encode($lectureSchedules); ?>;
    const sectionSchedules = <?php echo json_encode($sectionSchedules); ?>;

    console.log("Lectures:", lectureSchedules);
    console.log("Sections:", sectionSchedules);
</script>
</head>
<body>
    
</body>
</html>

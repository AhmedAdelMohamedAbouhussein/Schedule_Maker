<?php
session_start();
include '../Connect_DataBase.php';

header('Content-Type: application/json');

$studentID = $_SESSION['ID']; 

// Get the student's department
$sql = "SELECT Department_Name FROM Department WHERE Department_ID = (SELECT Department_ID FROM Student WHERE Student_ID = ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) 
{
    echo json_encode(['error' => 'Prepare statement failed (Department)']);
    exit();
}
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) 
{
    echo json_encode(['error' => 'Get result failed (Department)']);
    exit();
}
$departmentRow = $result->fetch_assoc();
if (!$departmentRow) 
{
    echo json_encode(['error' => 'Could not fetch department for student']);
    exit();
}
$department = $departmentRow['Department_Name'];
$stmt->close();

// Get all completed courses
$sql = "SELECT Course_Code FROM CompletedCourses WHERE Student_ID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) 
{
    echo json_encode(['error' => 'Prepare statement failed (CompletedCourses)']);
    exit();
}
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) 
{
    echo json_encode(['error' => 'Get result failed (CompletedCourses)']);
    exit();
}

$completedCourses = [];
while ($row = $result->fetch_assoc()) 
{
    $completedCourses[] = $row['Course_Code'];
}
$stmt->close();

$recommendedCourses = [];
if (!empty($completedCourses)) 
{
    $placeholdersIn = implode(',', array_fill(0, count($completedCourses), '?'));
    $placeholdersNotIn = implode(',', array_fill(0, count($completedCourses), '?'));

    $sql = "SELECT DISTINCT Course_Code FROM Prerequisite
            WHERE Prerequisite_Code IN ($placeholdersIn)
            AND Course_Code NOT IN ($placeholdersNotIn)
            LIMIT 6";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare statement failed (Prerequisite)']);
        exit();
    }

    // Bind parameters: first for IN clause, then for NOT IN clause
    $params = array_merge($completedCourses, $completedCourses);
    $types = str_repeat('s', count($params));  // assuming Course_Code is string

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['error' => 'Get result failed (Prerequisite)']);
        exit();
    }

    $recommendedCourses = [];
    while ($row = $result->fetch_assoc()) {
        $recommendedCourses[] = $row['Course_Code'];
    }
    $stmt->close();
} 
else
{
    // Map department names to course code prefixes
    $deptPrefixes = 
    [
        'Computer Science' => 'CS',
        'Engineering' => 'ENG',
        'Law' => 'LAW',
        'Business' => 'BUS'
    ];

    // Get prefix for current department or fallback to empty string
    $prefix = isset($deptPrefixes[$department]) ? $deptPrefixes[$department] : '';

    // Prepare SQL to get first 6 courses with no prerequisites and matching prefix
    $sql = "SELECT DISTINCT Course_Code FROM Prerequisite
            WHERE Prerequisite_Code IS NULL
            AND Course_Code LIKE ?
            LIMIT 6";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare statement failed (No Prerequisite)']);
        exit();
    }

    // Bind parameter with prefix + '%' for LIKE operator
    $likeParam = $prefix . '%';
    $stmt->bind_param("s", $likeParam);

    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) 
    {
        echo json_encode(['error' => 'Get result failed (No Prerequisite)']);
        exit();
    }

    $recommendedCourses = [];
    while ($row = $result->fetch_assoc()) {
        $recommendedCourses[] = $row['Course_Code'];
    }
    $stmt->close();
}

if (count($completedCourses) < 6 && count($recommendedCourses) < 6) {
    // Handle when there are completed courses to exclude
    if (!empty($completedCourses)) {
        $placeholdersNotIn = implode(',', array_fill(0, count($completedCourses), '?'));
        $sql = "SELECT DISTINCT Course_Code FROM Prerequisite
                WHERE Prerequisite_Code IS NULL
                AND Course_Code NOT IN ($placeholdersNotIn)
                LIMIT 6";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['error' => 'Prepare failed (Prerequisite NOT IN)']);
            exit();
        }

        $types = str_repeat('s', count($completedCourses)); // assuming Course_Code is string
        $stmt->bind_param($types, ...$completedCourses);
    } else {
        // No completed courses, so skip NOT IN clause
        $sql = "SELECT DISTINCT Course_Code FROM Prerequisite
                WHERE Prerequisite_Code IS NULL
                LIMIT 6";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['error' => 'Prepare failed (Prerequisite no NOT IN)']);
            exit();
        }
        // No params to bind
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['error' => 'Get result failed (Prerequisite)']);
        exit();
    }

    while ($row = $result->fetch_assoc()) {
        $recommendedCourses[] = $row['Course_Code'];
    }
    $stmt->close();
}


if (empty($recommendedCourses)) 
{
    echo json_encode(['lectureSchedules' => [], 'sectionSchedules' => []]);
    exit(); // Ensure we still output JSON even if empty
}

// Arrays to store results
$lectureSchedules = [];
$sectionSchedules = [];

// Step 5: Loop through recommended courses
foreach ($recommendedCourses as $courseCode) 
{
    // Get Course Name
    $sql = "SELECT Name FROM Course WHERE Course_Code = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare statement failed (Course Name)']);
        exit();
    }
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['error' => 'Get result failed (Course Name)']);
        exit();
    }
    $courseRow = $result->fetch_assoc();
    $courseName = $courseRow['Name'];
    $stmt->close();

    // Get Lecturer info
    $sql = "SELECT L.Name AS LecturerName, LT.Day_of_Week, LT.Start_Time, LT.End_Time, LT.Room, LT.LectureTime_ID
                FROM LecturerCourse LC
                JOIN Lecturer L ON LC.Lecturer_ID = L.Lecturer_ID
                JOIN LectureTime LT ON LC.Course_Lecture_ID = LT.Lecture_ID
                WHERE LC.Course_Code = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare statement failed (Lecturer)']);
        exit();
    }
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['error' => 'Get result failed (Lecturer)']);
        exit();
    }

    while ($row = $result->fetch_assoc()) {
        $lectureSchedules[] = [
            'Course_Code' => $courseCode,
            'Name' => $courseName, // Added course name
            'Lecturer_Name' => $row['LecturerName'],
            'Day_of_Week' => $row['Day_of_Week'],
            'Start_Time' => $row['Start_Time'],
            'End_Time' => $row['End_Time'],
            'Room' => $row['Room'],
            'LectureTime_ID' => $row['LectureTime_ID']
        ];
    }
    $stmt->close();

    // Get Tutor info
    $sql = "SELECT T.Name AS TutorName, ST.Day_of_Week, ST.Start_Time, ST.End_Time, ST.Room, ST.SectionTime_ID
                FROM TutorCourse TC
                JOIN Tutor T ON TC.Tutor_ID = T.Tutor_ID
                JOIN SectionTime ST ON TC.Course_Section_ID = ST.Section_ID
                WHERE TC.Course_Code = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare statement failed (Tutor)']);
        exit();
    }
    $stmt->bind_param("s", $courseCode);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['error' => 'Get result failed (Tutor)']);
        exit();
    }

    while ($row = $result->fetch_assoc()) {
        $sectionSchedules[] = [
            'Course_Code' => $courseCode,
            'Name' => $courseName, // Added course name
            'Tutor_Name' => $row['TutorName'],
            'Day_of_Week' => $row['Day_of_Week'],
            'Start_Time' => $row['Start_Time'],
            'End_Time' => $row['End_Time'],
            'Room' => $row['Room'],
            'SectionTime_ID' => $row['SectionTime_ID']
        ];
    }
    $stmt->close();
}
$conn->close();
echo json_encode(['lectureSchedules' => $lectureSchedules, 'sectionSchedules' => $sectionSchedules, 'studentID' => $studentID]);
exit();
?>
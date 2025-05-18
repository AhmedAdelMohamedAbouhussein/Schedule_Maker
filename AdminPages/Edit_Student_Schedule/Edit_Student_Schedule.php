<?php
session_start();
include '../../Connect_DataBase.php';

function hashPin($pin) 
{
    return password_hash((string)$pin, PASSWORD_DEFAULT);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    if (isset($_POST['studentID'])) 
    {
        $student_id = filter_input(INPUT_POST, 'studentID', FILTER_SANITIZE_NUMBER_INT);

        // Validate Student ID
        if (!preg_match('/^\d{9}$/', $student_id)) {
            echo "<script>
                alert('Invalid Student ID. Must be exactly 9 digits.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }


        // Check for duplicate ID
        $stmt = $conn->prepare("SELECT * FROM Student WHERE Student_ID = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) 
        {
            echo "<script>
                alert('Student ID doesn't exists.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }
        else
        {
            $_SESSION['ID'] = $student_id;
            header("Location: ../../RegisterCoursesPage/Registration.HTML");
            exit();
        }
    }
}

$conn->close();
?>

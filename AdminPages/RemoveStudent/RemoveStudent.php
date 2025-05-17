<?php
include '../../Connect_DataBase.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    if (isset($_POST['studentID']))
    {
        $student_id = $_POST['studentID'];

        if (!preg_match('/^\d{9}$/', $student_id)) 
        {
            echo "<script>
                alert('Invalid ID format. Must be exactly 9 digits.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        $check = mysqli_query($conn, "SELECT * FROM Student WHERE Student_ID = '$student_id'");
        if (mysqli_num_rows($check) === 0) {
            echo "<script>
                alert('ID doesnt exist.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        $delete = mysqli_query($conn, "DELETE FROM Student WHERE Student_ID = '$student_id'");
        if ($delete) 
        {
            echo "<script>
                    alert('Student Deleted Successfully.');
                    window.location.href = '../Add_Remove_Students/Add_Remove.html';
                </script>";
            exit();
        } 
        else 
        {
            echo "<script>
                    alert('Deletion Failed.');
                    window.location.href = '../Add_Remove_Students/Add_Remove.html';
                </script>";
            exit();
        }
    }
}
$conn->close();
?>

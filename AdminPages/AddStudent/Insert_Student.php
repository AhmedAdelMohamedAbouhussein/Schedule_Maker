<?php
include '../../Connect_DataBase.php';

function hashPin($pin) {
    return password_hash((string)$pin, PASSWORD_DEFAULT);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    if (isset($_POST['studentID'], $_POST['name'], $_POST['pin'], $_POST['term'], $_POST['departmentID'])) 
    {
        
        $student_id = filter_input(INPUT_POST, 'studentID', FILTER_SANITIZE_NUMBER_INT);
        $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
        $pin = filter_input(INPUT_POST, 'pin', FILTER_SANITIZE_NUMBER_INT);
        $term = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_NUMBER_INT);
        $department_id = filter_input(INPUT_POST, 'departmentID', FILTER_SANITIZE_NUMBER_INT);

        // Validate Student ID
        if (!preg_match('/^\d{9}$/', $student_id)) {
            echo "<script>
                alert('Invalid Student ID. Must be exactly 9 digits.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        // Validate PIN
        if (!preg_match('/^\d{6}$/', $pin)) {
            echo "<script>
                alert('Invalid PIN. Must be exactly 6 digits.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        // Validate Department ID
        if ($department_id < 1 || $department_id > 4) {
            echo "<script>
                alert('Invalid Department ID. Must be between 1 and 4.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        // Check for duplicate ID
        $check = mysqli_query($conn, "SELECT * FROM Student WHERE Student_ID = '$student_id'");
        if (mysqli_num_rows($check) > 0) 
        {
            echo "<script>
                alert('Student ID already exists.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
            exit();
        }

        // Insert into database
        $hashed_pin = hashPin($pin);
        $sql = "INSERT INTO Student (Student_ID, Name, PIN, Term, Department_ID) 
                VALUES ('$student_id', '$name', '$hashed_pin', $term, $department_id)";
        
        if (mysqli_query($conn, $sql)) 
        {
            echo "<script>
                alert('Student added successfully.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
        } else {
            echo "<script>
                alert('Failed to add student.');
                window.location.href = '../Add_Remove_Students/Add_Remove.html';
            </script>";
        }
    }
}

$conn->close();
?>

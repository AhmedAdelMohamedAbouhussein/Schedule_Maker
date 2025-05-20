<?php
include("../Connect_DataBase.php");

// Function to hash PINs
function hashPin($pin) {
    return password_hash((string)$pin, PASSWORD_DEFAULT);
}

// Students array
$students = [
    [241000001, 'John Smith', '563288', 1, 1],
    [241000002, 'Sarah Johnson', '430817', 2, 1],
    [231000001, 'Michael Williams', '805195', 3, 1],
    [231000002, 'Emma Brown', '114065', 4, 1],
    [221000001, 'David Davis', '975978', 5, 1],
    [221000002, 'Olivia Garcia', '383159', 6, 1],
    [211000001, 'James Martinez', '328391', 7, 1],
    [211000002, 'Sophia Rodriguez', '954519', 8, 1],
    [242000001, 'William Anderson', '870623', 1, 2],
    [242000002, 'Isabella Thomas', '923850', 2, 2],
    [232000001, 'Ethan Taylor', '800948', 3, 2],
    [232000002, 'Charlotte Moore', '397116', 4, 2],
    [222000001, 'Benjamin Jackson', '234825', 5, 2],
    [222000002, 'Mia White', '132921', 6, 2],
    [212000001, 'Elijah Harris', '554392', 7, 2],
    [212000002, 'Amelia Clark', '126565', 8, 2],
    [202000001, 'Lucas Lewis', '394355', 9, 2],
    [202000002, 'Lily Walker', '829702', 10, 2],
    [243000001, 'Daniel Allen', '245195', 1, 3],
    [243000002, 'Madison Young', '961928', 2, 3],
    [233000001, 'Alexander King', '262047', 3, 3],
    [233000002, 'Charlotte Scott', '886512', 4, 3],
    [223000001, 'Amelia Perez', '873789', 5, 3],
    [223000002, 'Mason Clark', '150611', 6, 3],
    [213000001, 'Harper Lee', '310308', 7, 3],
    [213000002, 'Sebastian Green', '947793', 8, 3],
    [244000001, 'Henry Allen', '850682', 1, 4],
    [244000002, 'Victoria Wright', '911916', 2, 4],
    [234000001, 'Matthew King', '837716', 3, 4],
    [234000002, 'Zoe Lopez', '677170', 4, 4],
    [224000001, 'Samuel Hill', '467760', 5, 4],
    [224000002, 'Grace Evans', '973057', 6, 4]
];

// Insert data into the database
foreach ($students as $student) {
    $student_id = $student[0];
    $name = mysqli_real_escape_string($conn, $student[1]);
    $pin = $student[2];
    $term = $student[3];
    $department_id = $student[4];

    $hashed_pin = hashPin($pin);

    $sql = "INSERT INTO Student (Student_ID, Name, PIN, Term, Department_ID) 
            VALUES ('$student_id', '$name', '$hashed_pin', $term, $department_id)";

    if (mysqli_query($conn, $sql)) {
        echo "Inserted $name successfully.<br>";
    } else {
        echo "Error inserting $name: " . mysqli_error($conn) . "<br>";
    }
}

?>
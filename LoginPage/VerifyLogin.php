<?php
session_start();
include("../Connect_DataBase.php"); // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['login'])) {
        // Sanitize inputs
        $ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_NUMBER_INT);
        $PIN = filter_input(INPUT_POST, 'PIN', FILTER_SANITIZE_NUMBER_INT);

        // Validate ID and PIN
        if (!preg_match('/^\d{9}$/', $ID)) {
            echo "<script>
                alert('Invalid ID format. Must be 9 digits.');
                window.location.href = 'LoginPage.HTML';
            </script>";
            exit();
        }

        if (!preg_match('/^\d{6}$/', $PIN)) {
            echo "<script>
                alert('Invalid PIN format. Must be 6 digits.');
                window.location.href = 'LoginPage.HTML';
            </script>";
            exit();
        }

        // Try Student login
        $stmt = $conn->prepare("SELECT * FROM Student WHERE Student_ID = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) 
        {
            $stmt->close();

            $hashedPIN = $row['PIN']; // Assuming PIN is hashed
            if (password_verify($PIN, $hashedPIN)) 
            {
                $_SESSION['ID'] = $ID;
                header("Location: ../RegisterCoursesPage/Registration.HTML");
                exit();
            }
            else 
            {
                echo "<script>
                    alert('Incorrect PIN for Student.');
                    window.location.href = 'LoginPage.HTML';
                </script>";
                exit();
            }
        }
        $stmt->close();

        // Try Admin login
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE Admin_ID = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();

            $hashedPIN = $row['PIN']; // Assuming PIN is hashed
            if (password_verify($PIN, $hashedPIN)) 
            {
                header("Location: ../AdminPages/Add_Remove_Students/Add_Remove.html");
                exit();
            } 
            else 
            {
                echo "<script>
                    alert('Incorrect PIN for Admin.');
                    window.location.href = 'LoginPage.HTML';
                </script>";
                exit();
            }
        }
        $stmt->close();

        // If reached here, ID not found in either table
        echo "<script>
            alert('ID does not exist in the system.');
            window.location.href = 'LoginPage.HTML';
        </script>";
        exit();
    }
}
?>

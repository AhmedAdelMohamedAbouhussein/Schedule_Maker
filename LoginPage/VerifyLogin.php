<?php
    session_start();
    include("../Connect_DataBase.php"); // Include the database connection file

    if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        if(isset($_POST['login'])) 
        {
            // Sanitize inputs
            $ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_NUMBER_INT);
            $PIN = filter_input(INPUT_POST, 'PIN', FILTER_SANITIZE_NUMBER_INT);

            // Validate ID and PIN
            if (!preg_match('/^\d{9}$/', $ID)) 
            {
                die("Invalid ID format");
            }

            if (!preg_match('/^\d{6}$/', $PIN)) 
            {
                die("Invalid PIN format");
            }
            
            $stmt = $conn->prepare("SELECT * FROM Student WHERE Student_ID = ?");
            $stmt->bind_param("i", $ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) 
            {
                $row = $result->fetch_assoc();
                if ($row) 
                {
                    $hashedPIN = $row['PIN']; // Assuming the hashed PIN is stored in the database

                    if(password_verify($PIN, $hashedPIN)) 
                    {
                        // Password is correct, proceed with login
                        $_SESSION['ID'] = $ID;
                        header("Location: ../RegisterCoursesPage/Registration.HTML"); // Redirect to the next page after login
                        exit(); // Ensure no further code is executed after the redirect
                    }
                    else 
                    {
                        // Password is incorrect, show error message
                        echo "<script>
                        alert('PIN Doesn\'t Exist.');
                        window.location.href = 'LoginPage.HTML'; // Redirect after alert
                        </script>";
                        exit(); // Ensure no further code is executed after the redirect
                    }
                } 
                else 
                {
                    // Password is incorrect, show error message
                    echo "<script>
                    alert('ID Doesn\'t Exist.');
                    window.location.href = 'LoginPage.HTML'; // Redirect after alert
                    </script>";
                    exit(); // Ensure no further code is executed after the redirect
                }
            } 
            else 
            {
                echo "<script>alert('Database query failed.');</script>";
            }
        }
    }
?>
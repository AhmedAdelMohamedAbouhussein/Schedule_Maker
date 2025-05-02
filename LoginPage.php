<?php
    session_start();
    include("Connect_DataBase.php"); // Include the database connection file

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
                        header("Location: ScheduleRegister.php"); // Redirect to the next page after login
                        exit(); // Ensure no further code is executed after the redirect
                    } 
                    else 
                    {
                        // Password is incorrect, show error message
                        echo "<script>alert('PIN Doesn\'t Exist.');</script>";
                    }
                } 
                else 
                {
                    echo "<script>alert('ID Doesn\\'t Exist.');</script>";
                }
            } 
            else 
            {
                echo "<script>alert('Database query failed.');</script>";
            }
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AASTMT Schedule Maker - Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root 
            {
                --primary-blue: #005BAC;
                --secondary-blue: #003366;
                --accent-yellow: #FFD700;
                --white: #FFFFFF;
            }

            body 
            {
                background-color: var(--white);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                color: var(--secondary-blue);
            }

            .navbar 
            {
                background-color: var(--primary-blue);
                width: 100%;
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1000;
                padding: 0.5rem 0;
            }

            .navbar img 
            {
                width: 150px;
                height: auto;
                margin-left: 20px;
            }

            .login-card 
            {
                background: var(--primary-blue);
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 400px;
                color: var(--white);
                margin-top: 100px;
            }

            .btn-accent 
            {
                background-color: var(--accent-yellow);
                color: var(--secondary-blue);
                border-radius: 50px;
                padding: 0.8rem 2rem;
                font-size: 1.1rem;
                font-weight: 600;
                border: none;
                transition: all 0.3s ease;
            }

            .btn-accent:hover 
            {
                background-color: #F2BA49;
                transform: translateY(-2px);
            }

            .form-control 
            {
                border-radius: 0.5rem;
            }

            .login-header 
            {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
                text-align: center;
                font-weight: bold;
            }

            .error-message 
            {
                color: #FF6347;
                text-align: center;
                margin-top: 10px;
                display: none;
            }

            footer 
            {
                margin-top: 2rem;
                text-align: center;
                font-size: 0.9rem;
                color: var(--secondary-blue);
            }

            footer a 
            {
                color: var(--secondary-blue);
                text-decoration: none;
            }

            footer a:hover 
            {
                text-decoration: underline;
            }
        </style>    
    </head>

    <body>
        <!-- Navbar with AASTMT Logo -->
        <nav class="navbar">
            <div class="container-fluid">                 
                <img src="https://campaigns.aast.edu/wp-content/uploads/2022/05/50-years-of-excellence-365x119.png" alt="AASTMT Logo">
            </div>
        </nav>

        <!-- Login Card -->
        <div class="login-card">
        
            <h2 class="login-header">User Login</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return fakeLogin();">
                <div class="mb-3">
                    <label for="ID" class="form-label">ID: </label>
                    <input type="text" class="form-control" id="ID" name="ID" required>
                </div>
                <div class="mb-3">
                    <label for="PIN" class="form-label">PIN :</label>
                    <input type="password" class="form-control" id="PIN" name="PIN" required>
                </div>
                <input type="submit" class="btn btn-accent w-100 mt-3" name="login" value="login">
            </form>
            <div id="error-message" class="error-message">Incorrect username or password.</div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 AASTMT. All rights reserved. | <a href="https://www.aast.edu">www.aast.edu</a></p>
        </footer>

        <script>
            function fakeLogin() 
            {
                let user = document.getElementById('ID').value.trim();
                let pass = document.getElementById('PIN').value.trim();

                // Username: exactly 9 digits
                const IDValid = /^[0-9]{9}$/.test(user);

                // Password: exactly 6 digits
                const PINValid = /^[0-9]{6}$/.test(pass);

                // Check if ID is valid (9 digits)
                if (!IDValid) 
                {
                    document.getElementById('error-message').innerText = "Invalid ID. It should be exactly 9 digits.";
                    document.getElementById('error-message').style.display = 'block';
                    return false; // Prevent form submission
                }
                // Check if PIN is valid (6 digits)
                else if (!PINValid) 
                {
                    document.getElementById('error-message').innerText = "Invalid PIN. It should be exactly 6 digits.";
                    document.getElementById('error-message').style.display = 'block';
                    return false; // Prevent form submission
                }
                else 
                {
                    document.getElementById('error-message').style.display = 'none'; // Hide error message if valid
                    return true;
                }
            }
        </script>
    </body>
</html>
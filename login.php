<?php
session_start(); 
include('connection.php');
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";

    $result = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username; 
                $_SESSION['accno'] = $account_no; 
                header("Location: account.php");
                exit();
            } else {
                $_SESSION['message'] = "Error: Incorrect password.";
            }
        } else {
            $_SESSION['message'] = "Error: Username does not exist.";
        }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>













<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #000; /* Black background */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        /* Header styling with glowing effect */
        .header {
            width: 100%;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #7FFF00; /* Light green */
            text-shadow: 0 0 10px #7FFF00, 0 0 20px #7FFF00, 0 0 30px #7FFF00, 0 0 40px #7FFF00; /* Glowing effect */
        }

        /* Container styling */
        .container {
            background: #1a1a1a; /* Dark gray */
            color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8); /* Shadow for depth */
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #7FFF00; /* Light green */
            text-shadow: 0 0 10px #7FFF00; /* Subtle glow for h1 */
        }

        /* Input field styling */
        .container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #666; /* Light gray border */
            border-radius: 5px;
            font-size: 16px;
            background-color: #333; /* Dark background for input */
            color: #fff; /* White text for inputs */
        }

        /* Button styling */
        .container button {
            background: #7FFF00; /* Light green */
            color: #000; /* Black text for contrast */
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
            box-shadow: 0 0 10px #7FFF00; /* Glowing effect */
        }

        .container button:hover {
            background: #32CD32; /* Darker green on hover */
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8); /* Stronger glow on hover */
            color: #fff; /* White text on hover for contrast */
        }

        /* Success and error messages */
        .message {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #28a745; /* Green for success */
        }

        .error {
            color: #dc3545; /* Red for errors */
        }

        /* Home Button/Text */
        .mainpage-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #7FFF00;
            color: black;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 0 10px #7FFF00;
            transition: all 0.3s;
        }

        .mainpage-btn:hover {
            background-color: #32CD32;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8);
        }
    </style>
</head>
<body>
    <!-- Home Button as Text -->
    <a href="index.php" class="mainpage-btn">Main page</a>

    <div class="header">
        PrimeVaultBank - Login
    </div>
    <div class="container">
        <h1>Login</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Displaying success or error messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
</body>
</html>


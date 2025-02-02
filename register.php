<?php
session_start(); 
include('connection.php');
error_reporting(0); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $passwordhash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = "Error: Username already exists.";
    }
    else{
        if ($username != "" && strlen($password) >= 8) {
            $QUERY = "INSERT INTO users (username, password) VALUES ('$username', '$passwordhash')";
            $res = mysqli_query($conn, $QUERY);
            $accountNumber = rand(1000000000, 9999999999);
            $initialBalance = 1000;
            $userId = mysqli_insert_id($conn);
            $QUERY2 = "INSERT INTO accounts (user_id, account_no, balance) VALUES ('$userId', '$accountNumber', '$initialBalance')";
            $res2 = mysqli_query($conn, $QUERY2);
        if ($res && $res2) {
            $_SESSION['message'] = "User Registered Successfully, PV account created!";
        } 
        else {
            $_SESSION['message'] = "Error: User Entry Unsuccessful: " . mysqli_error($conn);
        }
    } 
    else {
        $_SESSION['message'] = "Error: Password should be 8 digits long & alphanumeric";
    }
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
    <title>Registration Page</title>
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

        /* Paragraph and link styling */
        .container p {
            font-size: 14px;
            margin-top: 20px;
            color: #b3b3b3; /* Light gray for paragraph text */
        }

        .container a {
            color: #7FFF00; /* Light green */
            text-decoration: none;
        }

        .container a:hover {
            text-decoration: underline;
            color: #32CD32; /* Darker green on hover */
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
    <a href="index.php" class="mainpage-btn">Main page</a>
    <div class="header">
        PrimeVaultBank
    </div>
    <div class="container">
        <h1>Register your PV Account</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <button type="submit" name="register">Register</button>
        </form>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
</body>
</html>




<?php
session_start();
include('connection.php'); // Make sure the $conn variable is defined and connected to your database

// Check if the user is logged in, if not redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}

$username = $_SESSION['username']; // Fetch the logged-in username

// Simple query to fetch user_id
$query = "SELECT user_id FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$userid = $row['user_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212; /* Dark background */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        h1 {
            color: #7FFF00; /* Light green */
            text-shadow: 0 0 10px #7FFF00;
            margin-bottom: 30px;
        }

        .container {
            background-color: #1a1a1a; /* Dark gray */
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .container p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .logout-button {
            background-color: #7FFF00; /* Light green */
            color: black;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 0 10px #7FFF00;
        }

        .logout-button:hover {
            background-color: #32CD32; /* Darker green */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8);
        }
    </style>
</head>
<body>

    <h1>Welcome, <?php echo $username; ?>!</h1>
    <h1>Welcome, <?php echo $userid; ?>!</h1>
    <div class="container">
        <p>This is your account page. You are successfully logged in.</p>
        <p>Here you can manage your account details, check your balance, and more.</p>
        
        <!-- Logout button -->
        <a href="logout.php">
            <button class="logout-button">Logout</button>
        </a>
    </div>

</body>
</html>

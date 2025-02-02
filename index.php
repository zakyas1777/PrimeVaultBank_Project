<?php
session_start(); 
include('connection.php');
error_reporting(0); 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['register'])) {
    header("Location: register.php");
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['login'])) {
    header("Location: login.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrimeVault Bank</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #000; /* Black background */
            color: #fff;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .icon {
            position: absolute;
            color: rgba(127, 255, 0, 0.4); /* Light green with transparency */
            font-size: 5rem; /* Larger icons */
            animation: float 8s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) rotate(360deg);
                opacity: 0;
            }
        }

        .content {
            display: flex;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
            gap: 40px;
            justify-content: space-between;
            align-items: center;
        }

        .left {
            flex: 1;
        }

        .left h1 {
            font-size: 48px;
            color: #7FFF00; /* Light green */
            margin-bottom: 20px;
            text-shadow: 0 0 10px #7FFF00, 0 0 20px #7FFF00, 0 0 30px #7FFF00; /* Glowing effect */
        }

        .left p {
            font-size: 18px;
            line-height: 1.6;
            color: #fff; /* White text for better readability */
            font-weight: bold; /* Bold text */
        }

        .right {
            flex: 1;
            background: #1a1a1a; /* Dark gray container background */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8);
            text-align: center;
        }

        .right h2 {
            font-size: 28px;
            color: #7FFF00;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #7FFF00;
        }

        .btn {
            display: block;
            width: 100%;
            background: #333; /* Dark background for buttons */
            color: #7FFF00; /* Light green text */
            padding: 15px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            overflow: hidden;
            border: 2px solid #7FFF00; /* Border matches the text color */
            transition: color 0.3s ease, background-color 0.3s ease;
            cursor: pointer;
            box-shadow: 0 0 10px #7FFF00; /* Button glowing effect */
        }

        .btn:hover {
            background: #7FFF00; /* Light green background on hover */
            color: #000; /* Black text on hover */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
                text-align: center;
            }

            .left, .right {
                flex: none;
                width: 100%;
            }

            .left h1 {
                font-size: 36px;
            }

            .left p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="background">
        <!-- Add lock and dollar signs for animation -->
        <span class="icon" style="left: 10%; animation-delay: 0s;">🔒🔒</span>
        <span class="icon" style="left: 20%; animation-delay: 1s;">💲</span>
        <span class="icon" style="left: 30%; animation-delay: 2s;">🔒🔒</span>
        <span class="icon" style="left: 40%; animation-delay: 3s;">💲</span>
        <span class="icon" style="left: 50%; animation-delay: 4s;">🔒🔒</span>
        <span class="icon" style="left: 60%; animation-delay: 5s;">💲</span>
        <span class="icon" style="left: 70%; animation-delay: 6s;">🔒🔒</span>
        <span class="icon" style="left: 80%; animation-delay: 7s;">💲</span>
    </div>

    <div class="content">
        <!-- Left Section -->
        <div class="left">
            <h1>PrimeVault Bank</h1>
            <p>
                Welcome to PrimeVault Bank! A Secure and Reliable banking organization. 
                Manage your bank account, perform secure transactions, and take advantage 
				of our range of financial services in a user-friendly and secure environment.
            </p>
        </div>

        <!-- Right Section -->
        <div class="right">
            <h2>Get Started with PrimeVault PV Account</h2>
            <form method="GET">
                <button type="submit" name="register" class="btn">Register</button>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>




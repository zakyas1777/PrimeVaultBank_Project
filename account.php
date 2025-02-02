<?php
session_start();
include('connection.php');

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in username from the session
$username = $_SESSION['username'];

// Query to fetch user details and corresponding account details
$query = "
    SELECT u.user_id, a.account_no, a.balance 
    FROM users u
    INNER JOIN accounts a ON u.user_id = a.user_id
    WHERE u.username = '$username'
";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userid = $row['user_id'];
    $account_no = $row['account_no'];
    $balance = $row['balance'];
} else {
    $_SESSION['message'] = "Error: Account details could not be retrieved.";
    header("Location: login.php");
    exit();
}
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
            background-color: #121212;
            color: #fff;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #1a1a1a;
            padding: 20px;
            width: 250px;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.7);
        }

        .sidebar h2 {
            color: #7FFF00;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar .button {
            display: block;
            background-color: #7FFF00;
            color: black;
            border: none;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 0 10px #7FFF00;
            transition: all 0.3s;
        }

        .sidebar .button:hover {
            background-color: #32CD32;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8);
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
        }

        .container {
            background-color: #1a1a1a;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8);
            max-width: 600px;
            margin: 0 auto;
        }

        .container h1 {
            color: #7FFF00;
            text-shadow: 0 0 10px #7FFF00;
            margin-bottom: 30px;
            text-align: center;
        }

        .container p {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar with operations -->
    <div class="sidebar">
        <h2>Operations</h2>
        <a href="withdrawal.php" class="button">Withdrawal</a>
        <a href="deposit.php" class="button">Deposit</a>
        <a href="send.php" class="button">Send Money</a>
        <a href="view_transaction.php" class="button">View Transactions History</a>
        <a href="bank_statement.php" class="button">Print Bank Statement</a>
        <a href="index.php" class="button">Logout</a>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <div class="container">
            <h1>Welcome, <?php echo $username; ?>!</h1>
            <p><strong>Account Number:</strong> <?php echo $account_no; ?></p>
            <p><strong>Balance:</strong> $<?php echo number_format($balance, 2); ?></p>
            <p>This is your PV Account. Use the operations menu to perform actions.</p>
        </div>
    </div>
</body>
</html>

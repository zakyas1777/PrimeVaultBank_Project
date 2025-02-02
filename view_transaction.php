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

// Query to fetch the user's details
$query = "
    SELECT u.user_id, u.username
    FROM users u
    WHERE u.username = '$username'
";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id']; // Store the user_id to filter transactions
} else {
    $error_message = "Error: User not found.";
    echo $error_message;
    exit();
}

// Query to fetch the transaction history for the logged-in user
$transaction_query = "
    SELECT t.transaction_id, t.transaction_type, t.amount, t.transaction_date, t.sender_account_no, t.receiver_account_no
    FROM transactions t
    WHERE t.sender_user_id = '$user_id' OR t.receiver_user_id = '$user_id'
";
$transaction_result = mysqli_query($conn, $transaction_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 50px auto;
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8);
            max-width: 900px;
        }

        .container h1 {
            color: #7FFF00;
            text-shadow: 0 0 10px #7FFF00;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #333;
        }

        .message {
            color: #FF4500;
            font-size: 16px;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
        }

        .button-container a {
            background-color: #7FFF00;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 0 10px #7FFF00;
            transition: all 0.3s;
        }

        .button-container a:hover {
            background-color: #32CD32;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction History</h1>

        <?php if (isset($error_message)): ?>
            <div class="message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($transaction_result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Sender Account</th>
                        <th>Receiver Account</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaction = mysqli_fetch_assoc($transaction_result)): ?>
                        <tr>
                            <td><?php echo $transaction['transaction_id']; ?></td>
                            <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                            <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                            <td><?php echo $transaction['transaction_date']; ?></td>
                            <td><?php echo $transaction['sender_account_no']; ?></td>
                            <td><?php echo $transaction['receiver_account_no']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No transaction history found for your account.</p>
        <?php endif; ?>

        <div class="button-container">
            <a href="account.php" class="button">Go Back</a>
        </div>
    </div>
</body>
</html>

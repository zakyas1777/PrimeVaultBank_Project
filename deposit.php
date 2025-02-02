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

// Query to fetch account details for the logged-in user
$query = "
    SELECT u.user_id, a.account_no, a.balance, a.account_id
    FROM users u
    INNER JOIN accounts a ON u.user_id = a.user_id
    WHERE u.username = '$username'
";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $account_no = $row['account_no'];
    $balance = $row['balance'];
    $user_id = $row['user_id']; // Store the user_id to log transactions
    $account_id = $row['account_id']; // Store account_id for the transaction
} else {
    $account_no = "Not available";
    $balance = 0.00;
}

// Handle deposit request (previously debit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deposit_amount = floatval($_POST['deposit_amount']);  // Change from 'debit_amount' to 'deposit_amount'

    if ($deposit_amount > 0) {
        // Update the balance in the database by adding the deposit amount
        $new_balance = $balance + $deposit_amount;
        $update_query = "
            UPDATE accounts
            SET balance = $new_balance
            WHERE account_id = '$account_id'
        ";

        if (mysqli_query($conn, $update_query)) {
            // Insert transaction record into transactions table
            $transaction_query = "
                INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, sender_user_id, receiver_user_id, sender_account_no, receiver_account_no)
                VALUES ('$account_id', 'deposit', '$deposit_amount', NOW(), '$user_id', '$user_id', '$account_no', '$account_no')
            ";

            if (mysqli_query($conn, $transaction_query)) {
                $balance = $new_balance;
                $success_message = "Funds deposited successfully. New balance: $" . number_format($balance, 2);
            } else {
                $error_message = "Error: Could not log the transaction.";
            }
        } else {
            $error_message = "Error: Could not update balance.";
        }
    } else {
        $error_message = "Please enter a valid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #1a1a1a;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8);
            max-width: 400px;
            text-align: center;
        }

        .container h1 {
            color: #7FFF00;
            text-shadow: 0 0 10px #7FFF00;
            margin-bottom: 20px;
        }

        .container form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .button {
            display: inline-block;
            background-color: #7FFF00;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 0 10px #7FFF00;
            transition: all 0.3s;
        }

        .button:hover {
            background-color: #32CD32;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.8);
        }

        .message {
            margin-top: 20px;
            font-size: 16px;
            color: #FF4500;
        }

        .success {
            color: #7FFF00;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container a {
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deposit Funds</h1>
        <p>Account Number: <strong><?php echo $account_no; ?></strong></p>
        <p>Current Balance: <strong>$<?php echo number_format($balance, 2); ?></strong></p>

        <!-- Form to deposit funds -->
        <form method="post">
            <input type="number" name="deposit_amount" placeholder="Enter amount to deposit" step="0.01" required>
            <button type="submit" class="button">Deposit Funds</button>
        </form>

        <!-- Display messages -->
        <?php if (isset($error_message)): ?>
            <div class="message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Add a separate container for the Go Back button -->
        <div class="button-container">
            <a href="account.php" class="button">Go Back</a>
        </div>
    </div>
</body>
</html>

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

// Query to fetch user account details
$query = "
    SELECT u.user_id, a.account_no, a.account_id, a.balance 
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
    $account_id = $row['account_id']; // Ensure you have account_id for transactions
} else {
    echo "Error fetching account details.";
    exit();
}

// Handle withdrawal form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $withdrawal_amount = (float)$_POST['withdrawal_amount'];

    if ($withdrawal_amount > $balance) {
        $message = "Not enough funds.";
    } elseif ($withdrawal_amount <= 0) {
        $message = "Invalid withdrawal amount.";
    } else {
        // Deduct the withdrawal amount from the user's balance
        $new_balance = $balance - $withdrawal_amount;
        $update_query = "
            UPDATE accounts
            SET balance = $new_balance
            WHERE account_no = '$account_no'
        ";

        if (mysqli_query($conn, $update_query)) {
            // Log the withdrawal transaction in the transactions table
            $transaction_type = 'withdrawal';
            $transaction_date = date('Y-m-d H:i:s'); // Current date and time

            // Insert the transaction only if the account_id exists in accounts
            $check_account_query = "
                SELECT 1 FROM accounts WHERE account_id = '$account_id'
            ";
            $check_result = mysqli_query($conn, $check_account_query);

            if (mysqli_num_rows($check_result) > 0) {
                // Proceed with the transaction insertion if account_id exists
                $insert_transaction_query = "
                    INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, sender_user_id, receiver_user_id, sender_account_no, receiver_account_no)
                    VALUES ('$account_id', '$transaction_type', '$withdrawal_amount', '$transaction_date', '$user_id', '$user_id', '$account_no', '$account_no')
                ";

                if (mysqli_query($conn, $insert_transaction_query)) {
                    $message = "Withdrawal successful. You withdrew $$withdrawal_amount.";
                } else {
                    $message = "Error logging transaction.";
                }
            } else {
                $message = "Account does not exist for transaction.";
            }

            $balance = $new_balance; // Update balance to show the new value
        } else {
            $message = "Error processing withdrawal.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
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
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #7FFF00;
            text-shadow: 0 0 10px #7FFF00;
        }

        form {
            margin-top: 20px;
        }

        input[type="number"] {
            padding: 10px;
            width: calc(100% - 22px);
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #7FFF00;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        button:hover {
            background-color: #32CD32;
            transform: translateY(-2px);
        }

        .message {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Withdraw Money</h1>
        <p><strong>Account Number:</strong> <?php echo $account_no; ?></p>
        <p><strong>Current Balance:</strong> $<?php echo number_format($balance, 2); ?></p>

        <form method="POST">
            <input type="number" name="withdrawal_amount" placeholder="Enter amount to withdraw" required>
            <button type="submit">Withdraw</button>
        </form>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

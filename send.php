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

// Query to fetch the sender's account details
$query = "
    SELECT u.user_id, a.account_no, a.balance, a.account_id 
    FROM users u
    INNER JOIN accounts a ON u.user_id = a.user_id
    WHERE u.username = '$username'
";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $sender_account_no = $row['account_no'];
    $sender_balance = $row['balance'];
    $sender_user_id = $row['user_id']; // Capture user_id for transaction record
    $sender_account_id = $row['account_id']; // Capture account_id for transaction record
} else {
    $_SESSION['message'] = "Error: Account details could not be retrieved.";
    header("Location: account.php");
    exit();
}

// Handle send money request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_account_no = mysqli_real_escape_string($conn, $_POST['receiver_account_no']);
    $send_amount = floatval($_POST['send_amount']);

    // Validate CSRF token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token.";
    } else {
        if ($send_amount > 0) {
            if ($send_amount > $sender_balance) {
                $error_message = "Not enough funds to complete the transaction.";
            } else {
                // Check if the receiver account exists
                $receiver_query = "SELECT balance, user_id, account_id FROM accounts WHERE account_no = '$receiver_account_no'";
                $receiver_result = mysqli_query($conn, $receiver_query);
                $transaction_type = 'transfer';
                $transaction_date = date('Y-m-d H:i:s'); // Current date and time
                
                if ($receiver_result && mysqli_num_rows($receiver_result) > 0) {
                    $receiver_row = mysqli_fetch_assoc($receiver_result);
                    $receiver_balance = $receiver_row['balance'];
                    $receiver_user_id = $receiver_row['user_id']; // Capture receiver_user_id for transaction record
                    $receiver_account_id = $receiver_row['account_id']; // Capture receiver_account_id for transaction record

                    // Start transaction for consistency
                    mysqli_begin_transaction($conn);

                    try {
                        // Deduct amount from sender's balance
                        $new_sender_balance = $sender_balance - $send_amount;
                        $update_sender_query = "
                            UPDATE accounts
                            SET balance = $new_sender_balance
                            WHERE account_no = '$sender_account_no'
                        ";

                        // Add amount to receiver's balance
                        $new_receiver_balance = $receiver_balance + $send_amount;
                        $update_receiver_query = "
                            UPDATE accounts
                            SET balance = $new_receiver_balance
                            WHERE account_no = '$receiver_account_no'
                        ";

                        // Insert transaction record into transactions table
                        $transaction_query = "
                            INSERT INTO transactions 
                            (account_id, transaction_type, amount, transaction_date, sender_user_id, receiver_user_id, sender_account_no, receiver_account_no)
                            VALUES 
                            ('$sender_account_id', '$transaction_type', '$send_amount', NOW(), '$sender_user_id', '$receiver_user_id', '$sender_account_no', '$receiver_account_no')
                        ";

                        // Execute all queries
                        if (mysqli_query($conn, $update_sender_query) && mysqli_query($conn, $update_receiver_query) && mysqli_query($conn, $transaction_query)) {
                            // Commit the transaction
                            mysqli_commit($conn);

                            $success_message = "Transaction Successful! New Balance: $" . number_format($new_sender_balance, 2);
                            $sender_balance = $new_sender_balance; // Update local variable
                        } else {
                            // Rollback if any query fails
                            mysqli_rollback($conn);
                            $error_message = "Error: Transaction failed.";
                        }
                    } catch (Exception $e) {
                        // Rollback in case of error
                        mysqli_rollback($conn);
                        $error_message = "Error: Transaction failed.";
                    }
                } else {
                    $error_message = "Receiver account not found.";
                }
            }
        } else {
            $error_message = "Please enter a valid amount to send.";
        }
    }
}

// Generate a new CSRF token for the form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Money</title>
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

        .container form input[type="number"], 
        .container form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
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
        <h1>Send Money</h1>
        <p>Your Account Number: <strong><?php echo $sender_account_no; ?></strong></p>
        <p>Current Balance: <strong>$<?php echo number_format($sender_balance, 2); ?></strong></p>

        <!-- Form to send money -->
        <form method="post">
            <input type="text" name="receiver_account_no" placeholder="Receiver Account Number" required>
            <input type="number" name="send_amount" placeholder="Amount to Send" step="0.01" required>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="button">Transfer</button>
        </form>

        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

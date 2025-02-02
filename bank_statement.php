<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('fpdf.php'); // Include the FPDF library

// Start output buffering to prevent output errors
ob_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "primebank"; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function generateBankStatement($user_id, $account_no) {
    global $conn; // Access the database connection
    
    // Fetch the transaction history for the user
    $query = "
        SELECT transaction_type, amount, transaction_date, sender_account_no, receiver_account_no 
        FROM transactions 
        WHERE sender_user_id = $user_id OR receiver_user_id = $user_id
        ORDER BY transaction_date DESC
    ";

    $result = mysqli_query($conn, $query);

    // Check if the query was executed successfully
    if (!$result) {
        die('Error executing query: ' . mysqli_error($conn));
    }
    
    // Check if there are any transactions
    if (mysqli_num_rows($result) == 0) {
        die('No transactions found.');
    }

    // Create a new FPDF instance
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Add header information
    $pdf->Cell(0, 10, "                                                       PrimeVault Bank Statements");
    $pdf->Ln(10);  // Line break

    $pdf->Cell(0, 10, "Bank Statement for Account: $account_no", 0, 1, 'C');
    $pdf->Ln(10);  // Line break
    
    // Table headers
    $pdf->Cell(40, 10, "Transaction Type", 1, 0, 'C');
    $pdf->Cell(40, 10, "Amount", 1, 0, 'C');
    $pdf->Cell(60, 10, "Transaction Date", 1, 0, 'C');
    $pdf->Cell(50, 10, "Receiver/Sender Account", 1, 1, 'C');
    
    // Set font for the table rows
    $pdf->SetFont('Arial', '', 12);
    
    // Fetch each transaction and add it to the PDF
    while ($row = mysqli_fetch_assoc($result)) {
        $transaction_type = $row['transaction_type'];
        $amount = number_format($row['amount'], 2);
        $transaction_date = $row['transaction_date'];
        $account_no = ($transaction_type == 'transfer') ? ($row['receiver_account_no'] ?: $row['sender_account_no']) : '';

        $pdf->Cell(40, 10, ucfirst($transaction_type), 1, 0, 'C');
        $pdf->Cell(40, 10, "$" . $amount, 1, 0, 'C');
        $pdf->Cell(60, 10, $transaction_date, 1, 0, 'C');
        $pdf->Cell(50, 10, $account_no, 1, 1, 'C');
    }
    
    // Output the PDF
    $pdf->Output('D', "Bank_Statement_$account_no.pdf");  // 'D' forces download
}

// Example of calling the function
generateBankStatement(138, '7702556116');

// End output buffering and flush it
ob_end_flush();
?>

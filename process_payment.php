<?php
session_start();

// Database connection
$host = 'localhost'; // Hostname
$db = 'localhost'; // Replace with your actual database name
$user = 'root'; // Database username
$pass = 'root'; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . htmlspecialchars($e->getMessage()));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Please log in to make a payment.");
    }

    $recipient = $_POST['recipient'];
    $amount = $_POST['amount'];

    // Validate the amount
    if ($amount <= 0) {
        die("Invalid amount. Please enter a positive number.");
    }

    // Check if user has enough balance
    if ($amount > $_SESSION['balance']) {
        die("Insufficient balance.");
    }

    // Add logic to handle the payment transaction here (e.g., call API to process BTC transaction)
    // Simulate success (replace with actual payment processing logic)
    $success = true;

    if ($success) {
        // Begin transaction
        try {
            $pdo->beginTransaction();

            // Insert payment record (optional, for tracking)
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, transaction_type, status) VALUES (:user_id, :amount, 'debit', 'completed')");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'amount' => $amount,
            ]);

            // Update user balance
            $new_balance = $_SESSION['balance'] - $amount;
            $stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
            $stmt->execute([
                'balance' => $new_balance,
                'user_id' => $_SESSION['user_id']
            ]);

            // Commit transaction
            $pdo->commit();
            $_SESSION['balance'] = $new_balance; // Update session balance

            echo "Payment of $amount BTC to $recipient processed successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Payment failed: " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "Failed to process payment. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>

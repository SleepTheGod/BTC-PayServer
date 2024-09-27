<?php
session_start();

// Database connection
$host = 'localhost'; // Hostname
$db = 'localhost'; // Replace with your actual database name
$user = 'root'; // Database username
$pass = 'root'; // Database password

try {
    // Update the database name if needed
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . htmlspecialchars($e->getMessage()));
}

// Handle user login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['balance'] = $user['balance'];
        header("Location: dashboard.php"); // Redirect to dashboard
        exit;
    } else {
        echo "Invalid username or password.";
    }
}

// Handle transaction
if (isset($_POST['transaction'])) {
    if (!isset($_SESSION['user_id'])) {
        die("Please log in to make a transaction.");
    }

    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];

    // Validate amount and transaction type
    if ($transaction_type === 'debit' && $amount > $_SESSION['balance']) {
        die("Insufficient balance.");
    }

    // Begin transaction
    try {
        $pdo->beginTransaction();

        // Insert transaction record
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, transaction_type, status) VALUES (:user_id, :amount, :transaction_type, 'completed')");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'amount' => $amount,
            'transaction_type' => $transaction_type,
        ]);

        // Update user balance
        if ($transaction_type === 'debit') {
            $new_balance = $_SESSION['balance'] - $amount;
        } else {
            $new_balance = $_SESSION['balance'] + $amount;
        }

        $stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
        $stmt->execute([
            'balance' => $new_balance,
            'user_id' => $_SESSION['user_id']
        ]);

        // Commit transaction
        $pdo->commit();
        $_SESSION['balance'] = $new_balance; // Update session balance
        echo "Transaction completed successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Transaction failed: " . htmlspecialchars($e->getMessage());
    }
}
?>

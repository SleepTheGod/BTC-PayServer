<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient = $_POST['recipient'];
    $amount = $_POST['amount'];

    // Add logic to handle the payment transaction here (e.g., call API to process BTC transaction)
    // Simulate success
    $success = true;

    if ($success) {
        echo "Payment of $amount BTC to $recipient processed successfully!";
    } else {
        echo "Failed to process payment. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>

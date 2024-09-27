<?php
// Example: Connect to your DB and fetch user's transaction history
$transactions = [
    ['date' => '2024-09-27', 'amount' => '0.01 BTC', 'recipient' => 'Recipient Address', 'status' => 'Confirmed'],
    // Add more rows as needed
];

echo "<table>";
echo "<thead><tr><th>Date</th><th>Amount</th><th>Recipient</th><th>Status</th></tr></thead>";
echo "<tbody>";

foreach ($transactions as $transaction) {
    echo "<tr>";
    echo "<td>{$transaction['date']}</td>";
    echo "<td>{$transaction['amount']}</td>";
    echo "<td>{$transaction['recipient']}</td>";
    echo "<td>{$transaction['status']}</td>";
    echo "</tr>";
}

echo "</tbody></table>";
?>

This project is a Bitcoin pay server dashboard that allows users to manage their accounts, make payments, view transaction histories, and adjust account settings. The system is designed with both a frontend HTML interface and a backend PHP class to handle Bitcoin transactions.

Frontend (HTML): The HTML pages provide a user-friendly interface where users can log in, check their balance, make payments, view transaction history, and adjust settings. The main dashboard page (dashboard.html) displays a welcome message with the user’s current Bitcoin balance and links to other sections such as payment, transaction history, and account settings. There’s also a simple logout feature included via JavaScript.

Backend (PHP - Bitcoin Integration): A PHP class, based on the EasyBitcoin-PHP library, handles the connection to a Bitcoin server using RPC (Remote Procedure Call). The PHP script manages key Bitcoin API interactions such as fetching user info, processing transactions, and handling blockchain-related data. It connects securely using SSL and supports handling errors and HTTP status codes. This allows users to interact with the Bitcoin server securely and efficiently, making API calls like retrieving transaction data or submitting new payment requests.

SQL Database: The SQL schema manages users and their transactions. The users table stores essential user information like username, email, hashed passwords, and Bitcoin balances. The transactions table logs all Bitcoin transactions, including the transaction type (credit or debit), amount, and status. Lastly, the account_settings table stores user preferences and configuration data. This database setup allows secure storage and retrieval of user data and transactions, ensuring that users can track their Bitcoin activities and manage account settings efficiently.

Session and User Management: The project includes basic session handling features, such as logging in and out. The login/logout system clears session data (or could integrate backend session management) to ensure a secure user experience.

In essence, this pay server dashboard provides a complete interface for managing Bitcoin accounts, facilitating transactions, and maintaining user-specific settings, with a robust backend for interacting with the Bitcoin blockchain.







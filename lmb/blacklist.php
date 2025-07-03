<?php

// Database configuration
$host = 'localhost';
$dbName = 'lmb';
$username = 'root';
$password = ''; // Assuming no password

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbName :" . $e->getMessage());
}

function blacklistLoans($pdo) {
    try {
        $pdo->beginTransaction();

        // Define the query to select loans that are blocked and last updated more than a month ago
        $query = "SELECT * FROM loans WHERE status_id = 4 AND updated_at < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($loans as $loan) {
            // Update status_id to 7 (blacklist)
            $updateQuery = "UPDATE loans SET status_id = 7 WHERE loan_id = :loanId";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute(['loanId' => $loan['loan_id']]);

            echo "Loan ID {$loan['loan_id']} has been blacklisted.\n";
        }

        $pdo->commit();
        echo "Total blacklisted loans: " . count($loans) . "\n";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to blacklist loans: " . $e->getMessage() . "\n";
    }
}

// Call the function to blacklist eligible loans
blacklistLoans($pdo);

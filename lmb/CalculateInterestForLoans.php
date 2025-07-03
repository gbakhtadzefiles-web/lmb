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

// Define the function to calculate interest and update dates
function calculateInterest($loan, $pdo) {
    try {
        $pdo->beginTransaction();

        // Update status if next_payment_date is today and interest is not 0
        if ($loan['next_payment_date'] == date('Y-m-d') && $loan['interest'] != 0) {
            $updateStatusQuery = "UPDATE loans SET status_id = 3 WHERE loan_id = :loanId AND status_id = 1";
            $stmt = $pdo->prepare($updateStatusQuery);
            $stmt->execute(['loanId' => $loan['loan_id']]);
        }

        $interestAmount = ($loan['loan_amount'] * $loan['interest_rate']) / 100;
        $newNextPaymentDate = date('Y-m-d', strtotime('+10 days'));

        // Update the loan with new interest amount and next payment date
        $updateQuery = "UPDATE loans SET 
                        interest = interest + :interestAmount, 
                        next_payment_amount = next_payment_amount + :interestAmount, 
                        next_payment_date = :newNextPaymentDate
                        WHERE loan_id = :loanId";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            'interestAmount' => $interestAmount, 
            'newNextPaymentDate' => $newNextPaymentDate, 
            'loanId' => $loan['loan_id']
        ]);

        // Log the interest calculation
        $insertQuery = "INSERT INTO interestcalculations (loan_id, calculation_date, interest_amount)
                        VALUES (:loanId, CURDATE(), :interestAmount)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute(['loanId' => $loan['loan_id'], 'interestAmount' => $interestAmount]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to calculate interest for loan ID {$loan['loan_id']}: " . $e->getMessage() . "\n";
    }
}

// Fetch loans due today
$today = date('Y-m-d');
$query = "SELECT * FROM loans WHERE status_id IN (1, 3, 4) AND next_payment_date = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$today]);
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($loans as $loan) {
    calculateInterest($loan, $pdo);
}

echo "Status update and interest calculation completed for " . count($loans) . " loans.\n";

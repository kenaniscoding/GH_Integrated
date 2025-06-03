<?php
// Methodology how to automatically send email via SMTP steps
// 1) direct to this php and get the id number
// 2) get the email from the database
// 3) send the email
// 4) inform the admin on the welcome.php and return to the welcome.php
// 5) now do it for the slips table
// Method 1: Using PDO (Recommended)
try {
    // Database connection
    $host = 'localhost';
    $dbname = 'db3';
    $username = 'root';
    $password = 'onelasalle';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch all records
    $stmt = $pdo->query("SELECT * FROM makeup_slips");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display results
    foreach ($records as $record) {
        echo "ID: " . $record['id'] . "<br>";
        echo "First Name: " . $record['first_name'] . "<br>";
        echo "Last Name: " . $record['last_name'] . "<br>";
        echo "Grade Level: " . $record['grade_level'] . "<br>";
        echo "Section: " . $record['section'] . "<br>";
        echo "Subject: " . $record['subject'] . "<br>";
        echo "Teacher Name: " . $record['teacher_name'] . "<br>";
        echo "Start Date: " . $record['start_date'] . "<br>";
        echo "End Date: " . $record['end_date'] . "<br>";
        echo "Created At: " . $record['created_at'] . "<br>";
        echo "Email: " . $record['email'] . "<br>";
        echo "Reason: " . $record['reason'] . "<br>";
        echo "Status: " . $record['status'] . "<br>";
        echo "<hr>";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
// displayAsTable($pdo);
?>
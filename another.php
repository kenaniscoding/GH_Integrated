<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSGH Student Absence Request Form</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        
    </style>
</head>
<body>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "onelasalle", "db3");
// Check connection
if ($conn->connect_error) {
    die("<div class='error-message'>Connection failed: " . $conn->connect_error . "</div>");
}

?>

<div class="container">
    <div class="header">
        <h2>LSGH Excuse of Absence Form</h2>
    </div>
    
    <form id="absenceForm" method="POST" action="submit.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="relation">Do you want to fill up another make-up slip form for other subjects?</label>
            <select id="relation" name="relation" required>
                <option value="" disabled selected>Select One</option>
                <?php 
                    echo '<option value="yes">Yes</option>';
                    echo '<option value="no">No</option>';
                ?>
            </select>
        </div>

        <button type="submit" id="submitBtn">Submit</button>
    </form>

</div>

<script>
</script>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
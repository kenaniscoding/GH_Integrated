<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSGH Makeup Slip Form</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/makeup-styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "onelasalle", "db3");
// Check connection
if ($conn->connect_error) {
    die("<div class='error-message'>Connection failed: " . $conn->connect_error . "</div>");
}

// Get all distinct subjects from the database
$subjectsQuery = $conn->query("SELECT DISTINCT subject FROM teachers ORDER BY subject");
$subjectsInDB = [];
if ($subjectsQuery && $subjectsQuery->num_rows > 0) {
    while ($row = $subjectsQuery->fetch_assoc()) {
        $subjectsInDB[] = $row['subject'];
    }
}
?>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-popup">
        <div class="loading-spinner"></div>
        <div class="loading-text">Processing Your Request</div>
        <div class="loading-subtext">Please wait while we submit your makeup slip form. This may take a few moments...</div>
    </div>
</div>

<div class="container">
    <div class="header">
        <h2>LSGH Makeup Slip Form</h2>
    </div>
    
    <form id="makeupForm" method="POST" action="process_makeup.php" enctype="multipart/form-data">
        <!-- Data Privacy Agreement Checkbox -->
        <div class="form-check mb-3" style="display: flex; align-items: flex-start; gap: 10px;">
            <input class="form-check-input" type="checkbox" id="privacy_agree" name="privacy_agree" required style="margin-top: 4px;">
            <label class="form-check-label" for="privacy_agree" style="font-weight: 400; line-height: 1.4;">
                I agree to the <strong>Data Privacy Act</strong> terms and conditions regarding the 
                collection and use of my personal information.
            </label>
        </div>
        <!-- Student Information -->
        <div class="form-row">
            <div class="form-group">
                <label for="student_first_name">Student's First Name</label>
                <input type="text" id="student_first_name" name="student_first_name" required>
            </div>
            
            <div class="form-group">
                <label for="student_last_name">Student's Last Name</label>
                <input type="text" id="student_last_name" name="student_last_name" required>
            </div>
        </div>
        <!-- Subject and Teacher Selection -->
        <div class="form-group">
            <label for="subject">Subject</label>
            <select id="subject" name="subject" required>
                <option value="" disabled selected>Select a subject</option>
                <?php 
                if (!empty($subjectsInDB)) {
                    foreach ($subjectsInDB as $subject) {
                        echo '<option value="' . htmlspecialchars($subject) . '">' . htmlspecialchars($subject) . '</option>';
                    }
                } else {
                    // Fallback to original values if no data in DB
                    echo '<option value="English">English</option>';
                    echo '<option value="Filipino">Filipino</option>';
                    echo '<option value="Math">Math</option>';
                    echo '<option value="Science">Science</option>';
                    echo '<option value="AP">AP</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="teacher">Teacher's Name</label>
            <select id="teacher" name="teacher" required disabled>
                <option value="">Select subject first</option>
            </select>
        </div>


        <button type="submit" id="submitBtn">Submit Makeup Slip</button>
    </form>
    
</div>

<script src="js/makeup-script.js"></script>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
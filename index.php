<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSGH Student Absence Request Form</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom-styles.css">
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

?>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-popup">
        <div class="loading-spinner"></div>
        <div class="loading-text">Processing Your Request</div>
        <div class="loading-subtext">Please wait while we submit your absence form. This may take a few moments...</div>
    </div>
</div>

<div class="container">
    <div class="header">
        <h2>LSGH Excuse of Absence Form</h2>
    </div>
    
    <form id="absenceForm" method="POST" action="submit.php" enctype="multipart/form-data">
        <!-- Data Privacy Agreement Checkbox -->
        <div class="form-check mb-3" style="display: flex; align-items: flex-start; gap: 10px;">
        <input class="form-check-input" type="checkbox" id="privacy_agree" name="privacy_agree" required style="margin-top: 4px;">
        <label class="form-check-label" for="privacy_agree" style="font-weight: 400; line-height: 1.4;">
            I agree to the <strong>Data Privacy Act</strong> terms and conditions regarding the 
            collection and use of my personal information.
        </label>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="parent_first_name">Parent's First Name</label>
                <input type="text" id="parent_first_name" name="parent_first_name" required>
            </div>
            <div class="form-group">
                <label for="parent_last_name">Parent's Last Name</label>
                <input type="text" id="parent_last_name" name="parent_last_name" required>
            </div>
        </div>

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
        
        <div class="form-group">
            <label for="relation">Relation to Student</label>
            <select id="relation" name="relation" required>
                <option value="" disabled selected>Select One</option>
                <?php 
                    echo '<option value="Guardian">Guardian</option>';
                    echo '<option value="Parent">Parent</option>';
                ?>
            </select>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="grade_level">Grade Level</label>
                <select id="grade_level" name="grade_level" required>
                    <option value="" disabled selected>Select One</option>
                    <?php 
                        // Fallback to original values if no data in DB
                        echo '<option value="Pre Nursery">Pre Nursery</option>';
                        echo '<option value="Kinder">Kinder</option>';
                        for ($i = 1; $i <= 12; $i++) {
                            echo '<option value="Grade ' . $i . '">Grade ' . $i . '</option>';
                        }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="section">Section</label>
                <select id="section" name="section" required>
                    <option value="" disabled selected>Select One</option>
                    <?php foreach (range('A', 'O') as $sec): ?>
                        <option value="<?= $sec ?>"><?= $sec ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
        <label for="email">
            Guardian's Email Address
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            placeholder="Enter your email address"
            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
            style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem;"
        >
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
        <label for="reason">
            Reason of Absence
        </label>
        <input 
            type="text" 
            id="reason" 
            name="reason" 
            required 
            placeholder="Enter your reason for absence"
            style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem;"
        >
        </div>
        
        <div class="form-group">
            <label for="start_date">Start of Absence</label>
            <input type="date" id="start_date" name="start_date" max="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
            <label for="end_date">End of Absence</label>
            <input type="date" id="end_date" name="end_date" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        
        <!-- Medical Certificate Upload Section -->
        <div class="form-group">
            <label for="medical_certificate">Medical Certificate (Optional)</label>
            <div class="file-upload-section">
                <div>
                    <strong>📋 Attach Medical Certificate</strong>
                    <p style="margin: 5px 0; color: #666;">
                        For absences more that 2 days due to medical reasons please upload it here.
                    </p>
                </div>
                
                <div class="file-input-wrapper">
                    <input 
                        type="file" 
                        id="medical_certificate" 
                        name="medical_certificate" 
                        class="file-input"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                        onchange="handleFileSelect(this)"
                    >
                    <button type="button" class="file-upload-btn" onclick="document.getElementById('medical_certificate').click()">
                        Choose File
                    </button>
                </div>
                
                <div id="file-info" class="file-info">
                    No file selected
                </div>
                
                <div class="file-requirements">
                    Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max size: 5MB)
                </div>
            </div>
        </div>
        

        <button type="submit" id="submitBtn">Submit Makeup Slip</button>
    </form>
    
    <!-- Debug info (hidden in production) -->
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
        <h3>Send us Feedback at:</h3>
        <!-- <p>Subjects in database: <?php echo implode(", ", $subjectsInDB); ?></p>
        <p>Grades in database: <?php echo implode(", ", $gradesInDB); ?></p> -->
        <p style="font-family: Arial, sans-serif; color: #333;">edtech@lsgh.edu.ph</p>
    </div>
</div>

<script src="js/script.js"></script>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
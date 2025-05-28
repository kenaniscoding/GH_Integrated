<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSGH Student Absence Request Form</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .file-upload-section {
            margin: 1rem 0;
            padding: 1rem;
            border: 2px dashed #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
        }
        
        .file-upload-section:hover {
            border-color: #007bff;
            background-color: #f0f8ff;
        }
        
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        
        .file-input {
            display: none;
        }
        
        .file-upload-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .file-upload-btn:hover {
            background-color: #0056b3;
        }
        
        .file-info {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .file-selected {
            color: #28a745;
            font-weight: bold;
        }
        
        .file-error {
            color: #dc3545;
            font-weight: bold;
        }
        
        .file-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* Loading Overlay Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-popup {
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 300px;
            width: 90%;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .loading-subtext {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        /* Disable form during submission */
        .form-disabled {
            pointer-events: none;
            opacity: 0.6;
        }
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
    
</div>

<script>
function handleFileSelect(input) {
    const fileInfo = document.getElementById('file-info');
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check file size
        if (file.size > maxSize) {
            fileInfo.innerHTML = '<span class="file-error">File too large! Please select a file smaller than 5MB.</span>';
            input.value = ''; // Reset the input
            return;
        }
        
        // Display file info
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
        fileInfo.innerHTML = `<span class="file-selected">✓ ${file.name} (${fileSize} MB)</span>`;
    } else {
        fileInfo.innerHTML = 'No file selected';
    }
}

// Form submission with loading popup
document.getElementById('absenceForm').addEventListener('submit', function(e) {
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    // Add disabled class to form to prevent user interaction
    document.querySelector('.container').classList.add('form-disabled');
    
    // Change submit button text
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = 'Submitting...';
    submitBtn.disabled = true;
});

// Optional: Add drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadSection = document.querySelector('.file-upload-section');
    const fileInput = document.getElementById('medical_certificate');
    
    uploadSection.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadSection.style.borderColor = '#007bff';
        uploadSection.style.backgroundColor = '#f0f8ff';
    });
    
    uploadSection.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadSection.style.borderColor = '#ccc';
        uploadSection.style.backgroundColor = '#f9f9f9';
    });
    
    uploadSection.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadSection.style.borderColor = '#ccc';
        uploadSection.style.backgroundColor = '#f9f9f9';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(fileInput);
        }
    });
});

// Optional: Hide loading overlay if there's an error or if user navigates back
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        // Page was loaded from cache (user pressed back button)
        document.getElementById('loadingOverlay').style.display = 'none';
        document.querySelector('.container').classList.remove('form-disabled');
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = 'Submit Makeup Slip';
        submitBtn.disabled = false;
    }
});
</script>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
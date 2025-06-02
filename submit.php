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


// Function to send email
function sendEmail($receiver, $subject, $body) {
    $sender = "From: kenanbanal3@gmail.com";
    
    if(mail($receiver, $subject, $body, $sender)) {
        return true;
    } else {
        return false;
    }
}

// Function to get table structure
function getTableColumns($conn, $tableName) {
    $columns = [];
    $query = "SHOW COLUMNS FROM $tableName";
    $result = $conn->query($query);
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    return $columns;
}

// Function to handle file upload
function handleFileUpload($fileInputName) {
    $uploadResult = [
        'success' => false,
        'file_name' => '',
        'file_path' => '',
        'error' => ''
    ];
    
    // Check if file was uploaded
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $uploadResult; // No file uploaded, return with success = false
    }
    
    $file = $_FILES[$fileInputName];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadResult['error'] = 'File upload error: ' . $file['error'];
        return $uploadResult;
    }
    
    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $maxSize) {
        $uploadResult['error'] = 'File size exceeds 5MB limit';
        return $uploadResult;
    }
    
    // Validate file type
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        $uploadResult['error'] = 'Invalid file type. Allowed types: PDF, JPG, PNG, DOC, DOCX';
        return $uploadResult;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = 'uploads/medical_certificates/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $timestamp = date('Y-m-d_H-i-s');
    $uniqueFileName = $timestamp . '_' . uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $uniqueFileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $uploadResult['success'] = true;
        $uploadResult['file_name'] = $uniqueFileName;
        $uploadResult['file_path'] = $uploadPath;
    } else {
        $uploadResult['error'] = 'Failed to save uploaded file';
    }
    
    return $uploadResult;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $parent_first_name = $conn->real_escape_string($_POST["parent_first_name"]);
    $parent_last_name = $conn->real_escape_string($_POST["parent_last_name"]);
    $student_first_name = $conn->real_escape_string($_POST["student_first_name"]);
    $student_last_name = $conn->real_escape_string($_POST["student_last_name"]);
    $grade_level = $conn->real_escape_string($_POST["grade_level"]);
    $section = $conn->real_escape_string($_POST["section"]);
    $start_date = isset($_POST["start_date"]) ? $conn->real_escape_string($_POST["start_date"]) : '';
    $end_date = isset($_POST["end_date"]) ? $conn->real_escape_string($_POST["end_date"]) : '';
    $email = $conn->real_escape_string($_POST["email"]);
    $reason = $conn->real_escape_string($_POST["reason"]);

    // Handle medical certificate upload
    $fileUpload = handleFileUpload('medical_certificate');
    $medical_cert_filename = '';
    $medical_cert_filepath = '';
    
    if ($fileUpload['success']) {
        $medical_cert_filename = $conn->real_escape_string($fileUpload['file_name']);
        $medical_cert_filepath = $conn->real_escape_string($fileUpload['file_path']);
    } elseif (!empty($fileUpload['error'])) {
        // If there was an error with file upload (not just no file), show error
        echo "<div class='error-message'>File Upload Error: " . $fileUpload['error'] . "</div>";
        exit();
    }

    // Determine LC number based on section/grade_level
    $lc_no = ""; // default
    
    // Use grade_level instead of section for more reliable matching
    $grade = strtolower($grade_level);
    
    if ($grade === "pre nursery" || $grade === "kinder" || $grade === "kindergarten") {
        $lc_no = "LCP";
    } else if ($grade === "grade 1" || $grade === "grade 2") {
        $lc_no = "LC1";
    } else if ($grade === "grade 3" || $grade === "grade 4") {
        $lc_no = "LC2";
    } else if ($grade === "grade 5" || $grade === "grade 6") {
        $lc_no = "LC3";
    } else if ($grade === "grade 7" || $grade === "grade 8") {
        $lc_no = "LC4";
    } else if ($grade === "grade 9" || $grade === "grade 10") {
        $lc_no = "LC5";
    } else if ($grade === "grade 11" || $grade === "grade 12") {
        $lc_no = "LC6";
    }
    
    // Insert query with medical certificate fields
    $sql = "INSERT INTO slips (parent_first_name, parent_last_name, student_first_name, student_last_name, grade_level, section, start_date, end_date, email, reason, lc_no, medical_cert_filename, medical_cert_filepath)
            VALUES ('$parent_first_name', '$parent_last_name', '$student_first_name', '$student_last_name', '$grade_level', '$section', '$start_date', '$end_date', '$email', '$reason', '$lc_no', '$medical_cert_filename', '$medical_cert_filepath')";
    
    if ($conn->query($sql) === TRUE) {
        // Send confirmation email to student/parent
        $userSubject = "Absence Slip Submission Confirmation";
        $message = "Dear $parent_first_name $parent_last_name,\n\nThank you for your submission. This is a confirmation that we have received your absence slip request for $student_first_name $student_last_name from $start_date to $end_date.\n\nReason: $reason\n\n";
        
        // Add medical certificate info to email if uploaded
        if (!empty($medical_cert_filename)) {
            $message .= "Medical Certificate: Uploaded ($medical_cert_filename)\n\n";
        }
        
        $message .= "We will contact you if further information is needed.\n\nBest regards,\nAcademic Office";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: kenanbanal3@gmail.com\r\n";
        $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
        
        // Send email to student/parent
        mail($email, $userSubject, $message, $headers);

        // Fetch teacher email - Fixed SQL syntax
        $teacherEmailQuery = "SELECT email, name FROM lc WHERE lc_no = '$lc_no'";
        $teacherEmailResult = $conn->query($teacherEmailQuery);
        
        $teacherEmail = "";
        $teacherName = "";
        
        if ($teacherEmailResult && $teacherEmailResult->num_rows > 0) {
            $teacherRow = $teacherEmailResult->fetch_assoc();
            $teacherEmail = $teacherRow['email'];
            $teacherName = $teacherRow['name'];
            
            $teacherSubject = "Absence Slip Request Notification";
            $teacherBody = "Dear $teacherName,\n\nA $grade_level student at section $section who goes by $student_first_name $student_last_name was absent from $start_date to $end_date due to $reason.\n\n";
            
            // Add medical certificate info to teacher email if uploaded
            if (!empty($medical_cert_filename)) {
                $teacherBody .= "Medical Certificate: Uploaded and available for review ($medical_cert_filename)\n\n";
            }
            
            $teacherBody .= "Likewise, he/she kindly filled up the absence slip and requests to retake any missed quiz. THIS IS THE INITIAL TEST EMAIL MADE BY THE EDTECH OFFICE.\n\nPlease review this request at your earliest convenience.\n\nParent Contact: $parent_first_name $parent_last_name ($email)\n\nBest regards,\nAcademic Office";
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: kenanbanal3@gmail.com\r\n";
            $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
            
            // Send email to teacher
            mail($teacherEmail, $teacherSubject, $teacherBody, $headers);
        }
                
        
        echo ".";
        
        // Show medical certificate upload status
        if (!empty($medical_cert_filename)) {
            echo "<br><br><strong>Medical Certificate:</strong> Successfully uploaded ($medical_cert_filename)";
        }
        
    } else {
        echo "<div class='error-message'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}   

?>

<div class="container">
    <div class="header">
        <h2>LSGH Excuse of Absence Form</h2>
    </div>
    
    <form id="absenceForm" method="POST" action="check1.php" enctype="multipart/form-data">
         <div class='success-message'>
            Your submission has been received successfully. A confirmation email has been sent to you.
        </div>
        <div class="form-group">
             <label for="relation">Do you want to fill up the make-up slip form for missed test/s?</label>
            <select id="relation" name="relation" required>
                <option value="" disabled selected>Select One</option>
                <?php 
                    echo '<option value="yes">Yes</option>';
                    echo '<option value="no">No</option>';
                ?>
            </select>
        </div>

        <button type="submit" id="submitBtn">Submit</button>

        <!-- Debug info (hidden in production) -->
        <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
            <h3>Send us Feedback at:</h3>
            <!-- <p>Subjects in database: <?php echo implode(", ", $subjectsInDB); ?></p>
            <p>Grades in database: <?php echo implode(", ", $gradesInDB); ?></p> -->
            <p style="font-family: Arial, sans-serif; color: #333;">edtech@lsgh.edu.ph</p>
        </div>
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
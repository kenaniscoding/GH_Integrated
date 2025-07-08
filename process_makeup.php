<?php
// Start output buffering to prevent any accidental output before PDF generation
ob_start();

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

// Run only if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if "generate_pdf" is set in the POST data
    $generate_pdf = isset($_POST["generate_pdf"]) && $_POST["generate_pdf"] == "1";

    // Collect and sanitize form inputs
    // $first_name = $conn->real_escape_string($_POST["first_name"]);
    // $last_name = $conn->real_escape_string($_POST["last_name"]);
    // $grade = $conn->real_escape_string($_POST["grade"]);
    // $section = $conn->real_escape_string($_POST["section"]);
    $subject = $conn->real_escape_string($_POST["subject"]);
    $teacher = $conn->real_escape_string($_POST["teacher"]);
    // $start_date = isset($_POST["start_date"]) ? $conn->real_escape_string($_POST["start_date"]) : '';
    // $end_date = isset($_POST["end_date"]) ? $conn->real_escape_string($_POST["end_date"]) : '';
    // $email = $conn->real_escape_string($_POST["email"]);
    // $reason = $conn->real_escape_string($_POST["reason"]);

    $student_first_name = $conn->real_escape_string($_POST['student_first_name']);
    $student_last_name = $conn->real_escape_string($_POST['student_last_name']);

    $sql = "SELECT * FROM slips 
            WHERE student_first_name = '$student_first_name' 
            AND student_last_name = '$student_last_name' 
            ORDER BY created_at DESC 
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $first_name = $row['student_first_name'];
        $last_name = $row['student_last_name'];
        $grade = $row['grade_level'];
        $section = $row['section'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $email =  $row['email'];
        $reason = $row['reason'];
    
    } 

    // Generate PDF regardless of download request (we need it for email attachments)
    require('fpdf/fpdf.php');
    
    $title = 'Makeup Quiz Request Form';
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetTitle($title);

    // if (file_exists('logo.png')) {
    //      $pdf->Image('logo.png', 10, 10, 12.48, 19.26);
    //  }
    // Set up logo and header
    $pdf->SetFont('Arial', 'B', 16);
    // Add a title at the top
    $pdf->Cell(0, 10, 'LA SALLE GREEN HILLS', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'MAKEUP QUIZ REQUEST FORM', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Define table width
    $tableWidth = 180;
    $labelWidth = 50;
    $valueWidth = $tableWidth - $labelWidth;
    
    // Student Information Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Student Information', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    // Draw rectangle around student info
    $pdf->SetDrawColor(0, 0, 0);
    $startY = $pdf->GetY();
    
    // Name
    $pdf->Cell($labelWidth, 8, 'Name:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, $first_name . ' ' . $last_name, 0, 1, 'L');
    
    // Grade & Section
    $pdf->Cell($labelWidth, 8, 'Grade & Section:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, "$grade - $section", 0, 1, 'L');
    
    // Email
    $pdf->Cell($labelWidth, 8, 'Email:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, $email, 0, 1, 'L');
    
    // Draw box around student info
    $endY = $pdf->GetY();
    $pdf->Rect(10, $startY - 2, $tableWidth, $endY - $startY + 4);
    
    $pdf->Ln(5);
    
    // Class Information Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Class Information', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    // Draw rectangle around class info
    $startY = $pdf->GetY();
    
    // Subject
    $pdf->Cell($labelWidth, 8, 'Subject:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, $subject, 0, 1, 'L');
    
    // Teacher
    $pdf->Cell($labelWidth, 8, 'Teacher:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, $teacher, 0, 1, 'L');
    
    // Draw box around class info
    $endY = $pdf->GetY();
    $pdf->Rect(10, $startY - 2, $tableWidth, $endY - $startY + 4);
    
    $pdf->Ln(5);
    
    // Absence Information Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Absence Information', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    // Draw rectangle around absence info
    $startY = $pdf->GetY();
    
    // Absence Date
    $pdf->Cell($labelWidth, 8, 'Start Date:', 0, 0, 'L');
    $pdf->Cell($valueWidth, 8, $start_date, 0, 1, 'L');
    
    // End Date (if applicable)
    if (!empty($end_date)) {
        $pdf->Cell($labelWidth, 8, 'End Date:', 0, 0, 'L');
        $pdf->Cell($valueWidth, 8, $end_date, 0, 1, 'L');
    }
    
    // Reason for Absence
    $pdf->Cell($labelWidth, 8, 'Reason:', 0, 0, 'L');
    
    // Calculate how many lines the reason will take
    $reasonLines = $pdf->GetStringWidth($reason) > $valueWidth ? 
                    ceil($pdf->GetStringWidth($reason) / $valueWidth) : 1;
    
    if ($reasonLines > 1) {
        $pdf->MultiCell($valueWidth, 8, $reason, 0, 'L');
    } else {
        $pdf->Cell($valueWidth, 8, $reason, 0, 1, 'L');
    }
    
    // Draw box around absence info
    $endY = $pdf->GetY();
    $pdf->Rect(10, $startY - 2, $tableWidth, $endY - $startY + 4);
    $pdf->Ln(10);
    
    // Add Parents signature section
    $pdf->SetFont('Arial', '', 10);
    
    $pdf->Cell(90, 10, 'Parent\'s Signature: _________________________', 0, 0, 'L');
    $pdf->Cell(90, 10, 'Date: _______________', 0, 1, 'L');
    
    $pdf->Ln(5);
    
    // Add footer note
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 10, 'This form must be submitted back to the teacher.', 0, 1, 'L');
    $pdf->Cell(0, 10, 'Form generated on ' . date('F j, Y, g:i a'), 0, 1, 'L');
    
    // Save PDF to file before we do anything else with it
    $pdfFileName = 'makeup_request_' . $first_name . '_' . $last_name . '.pdf';
    $pdfFilePath = 'temp_pdfs/' . $pdfFileName;
    
    // Make sure the directory exists
    if (!is_dir('temp_pdfs')) {
        mkdir('temp_pdfs', 0755, true);
    }
    
    // Save the PDF to the server
    $pdf->Output('F', $pdfFilePath);
    
    // If we need to directly download PDF
    if ($generate_pdf || isset($_POST["download_pdf"])) {
        // Clean any previous output
        ob_end_clean();
        // Output PDF file as download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
        readfile($pdfFilePath);
        exit;
    }

    // Continue with the normal form processing
    // Insert query with email
    $sql = "INSERT INTO makeup_slips (first_name, last_name, grade_level, section, subject, teacher_name, start_date, end_date, email, reason)
            VALUES ('$first_name', '$last_name', '$grade', '$section', '$subject', '$teacher', '$start_date', '$end_date', '$email', '$reason')";

    if ($conn->query($sql) === TRUE) {
        // Send email to the student/parent with the PDF attachment
        $userSubject = "[LSGH] Makeup Slip Submission Confirmation";
        $userBody = "Dear $first_name $last_name,\n\nThank you for your submission. This is a confirmation that we have received your makeup slip request for $subject under $teacher.\n\nWe will contact you if further information is needed.\n\nPlease find attached a copy of your makeup quiz request form.\n\nBest regards,\nAcademic Office";

        // Prepare the email with attachment to the student
        // Read the file
        $handle = fopen($pdfFilePath, "r");
        $content = fread($handle, filesize($pdfFilePath));
        fclose($handle);
        
        // Base64 encode the content
        $encoded_content = chunk_split(base64_encode($content));
        
        // Generate a boundary
        $boundary = md5(time());
        
        // Headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: kenanbanal3@gmail.com\r\n";
        $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=$boundary\r\n";
        
        // Email body with attachment
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($userBody)) . "\r\n";
        
        // Attachment
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: application/pdf; name=\"$pdfFileName\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"$pdfFileName\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
        $message .= $encoded_content . "\r\n";
        $message .= "--$boundary--";
        
        // Send email to student
        mail($email, $userSubject, $message, $headers);

        // Now send the same email with attachment to the teacher
        $teacherEmailQuery = "SELECT email FROM teachers WHERE teacher_name = '$teacher' LIMIT 1";
        $teacherEmailResult = $conn->query($teacherEmailQuery);

        if ($teacherEmailResult && $teacherEmailResult->num_rows > 0) {
            $teacherRow = $teacherEmailResult->fetch_assoc();
            $teacherEmail = $teacherRow['email'];
            
            $teacherSubject = "[LSGH] Makeup Slip Request Notification";
            $teacherBody = "Dear $teacher,\n\nA $grade student at section $section who goes by $first_name $last_name was absent from $subject on $start_date to $end_date due to $reason.\n\nLikewise, he/she kindly filled up the makeup slip and requests to retake the missed quiz. THIS IS THE INITIAL TEST EMAIL MADE BY THE EDTECH OFFICE.\n\nPlease review this request at your earliest convenience. The makeup slip request form is attached to this email.\n\nBest regards,\nAcademic Office";
            // Prepare teacher email with attachment
            $teacher_message = "--$boundary\r\n";
            $teacher_message .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
            $teacher_message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $teacher_message .= chunk_split(base64_encode($teacherBody)) . "\r\n";
            
            // Attachment
            $teacher_message .= "--$boundary\r\n";
            $teacher_message .= "Content-Type: application/pdf; name=\"$pdfFileName\"\r\n";
            $teacher_message .= "Content-Disposition: attachment; filename=\"$pdfFileName\"\r\n";
            $teacher_message .= "Content-Transfer-Encoding: base64\r\n";
            $teacher_message .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
            $teacher_message .= $encoded_content . "\r\n";
            $teacher_message .= "--$boundary--";
            
            // Send email to teacher
            mail($teacherEmail, $teacherSubject, $teacher_message, $headers);
        }

        echo "
    <div class='container'>
        <div class='header'>
            <h2>LSGH Excuse of Absence Form</h2>
        </div>
        <link rel='stylesheet' href='css/style.css'>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
        <style>
           
        </style>
        <form id='absenceForm' method='POST' action='another.php' enctype='multipart/form-data'>
            <div class='form-group'>
                <label for='relation'>Do you want to fill up another make-up slip form for other subjects?</label>
                <select id='relation' name='relation' required>
                    <option value='' disabled selected>Select One</option>
                    <option value='yes'>Yes</option>
                    <option value='no'>No</option>
                </select>
            </div>
            <button type='submit' id='submitBtn'>Submit</button>
        </form>
    </div>
    <script>
        document.getElementById('absenceForm').addEventListener('submit', function (e) {
            e.preventDefault(); // stop form from submitting normally
            var selection = document.getElementById('relation').value;
            if (selection === 'yes') {
                window.location.href = 'makeup.php';
            } else if (selection === 'no') {
                window.location.href = 'no.php';
            }
        });
    </script>
    ";
        
        // Clean up - optionally delete the file after sending
        // Comment this out if you want to keep the files on the server
        // unlink($pdfFilePath);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    exit;
}

// Get all tables in the database
$tablesResult = $conn->query("SHOW TABLES");
$tables = [];
if ($tablesResult) {
    while ($row = $tablesResult->fetch_row()) {
        $tables[] = $row[0];
    }
}

// Check if teachers table exists
$teachersTableExists = in_array('teachers', $tables);

$teacherColumns = [];
$teacherData = [];
$message = "";
$results = [];

// Get teacher table columns if it exists
if ($teachersTableExists) {
    $teacherColumns = getTableColumns($conn, 'teachers');
    
    // Check if the table has an email column
    if (in_array('email', $teacherColumns)) {
        // Get all teacher data
        $teacherQuery = "SELECT * FROM teachers";
        $teacherResult = $conn->query($teacherQuery);
        
        if ($teacherResult && $teacherResult->num_rows > 0) {
            while($row = $teacherResult->fetch_assoc()) {
                $teacherData[] = $row;
            }
        }
    } else {
        $message = "The 'teachers' table does not have an 'email' column.";
    }
} else {
    $message = "The 'teachers' table does not exist in the database.";
    
    // Let's try to find a table that might contain teacher information
    foreach ($tables as $table) {
        $columns = getTableColumns($conn, $table);
        if (in_array('email', $columns) || in_array('teacher_email', $columns) || 
            in_array('teacher_name', $columns) || in_array('name', $columns)) {
            $message .= " Found table '$table' that might contain teacher information.";
            break;
        }
    }
}

// Determine name column
$nameColumn = null;
if (!empty($teacherColumns)) {
    $nameOptions = ['teacher_name', 'name', 'full_name', 'first_name', 'lastname', 'last_name'];
    foreach ($nameOptions as $option) {
        if (in_array($option, $teacherColumns)) {
            $nameColumn = $option;
            break;
        }
    }
}

// Get teachers for dropdown
$teachers = [];
if ($teachersTableExists && $nameColumn) {
    $teacherQuery = "SELECT $nameColumn FROM teachers";
    $teacherResult = $conn->query($teacherQuery);
    
    if ($teacherResult && $teacherResult->num_rows > 0) {
        while($row = $teacherResult->fetch_assoc()) {
            $teachers[] = $row[$nameColumn];
        }
    }
}

// Close the database connection
$conn->close();
?>
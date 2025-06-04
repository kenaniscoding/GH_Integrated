<?php
include('session.php');
// DB connection setup
$servername = "localhost";
$username = "root";
$password = "onelasalle"; // Set your password if needed
$dbname = "db3";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//////////////////////////START DELETE///////////////////////////////////////
function sendStatusUpdateEmail($email, $student_name, $parent_name, $slip_id, $new_status, $reason = '') {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: kenanbanal3@gmail.com\r\n";
    $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Customize subject and message based on status
    switch (strtolower($new_status)) {
        case 'accepted':
            $subject = "[LSGH] Absence Slip Approved - " . $student_name;
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;'>
                        <h2 style='color: #28a745; text-align: center;'>Absence Slip Approved ✓</h2>
                        
                        <p>Dear $parent_name,</p>
                        
                        <p>We are pleased to inform you that the absence slip for <strong>$student_name</strong> has been <strong style='color: #28a745;'>APPROVED</strong>.</p>
                        
                        <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>
                            <h3 style='margin-top: 0;'>Slip Details:</h3>
                            <p><strong>Slip ID:</strong> #$slip_id</p>
                            <p><strong>Student:</strong> $student_name</p>
                            <p><strong>Status:</strong> Approved</p>
                            <p><strong>Date Processed:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                        </div>
                        
                        <p>Your child's absence has been officially recorded and approved. Please ensure that any missed assignments or quizzes are coordinated with the respective teachers.</p>
                        
                        <p>If you have any questions, please don't hesitate to contact us.</p>
                        
                        <p>Best regards,<br>
                        <strong>Academic Office</strong><br>
                        La Salle Green Hills</p>
                        
                        <hr style='margin: 30px 0;'>
                        <p style='font-size: 12px; color: #666; text-align: center;'>
                            This is an automated message. Please do not reply to this email.<br>
                            For feedback: edtech@lsgh.edu.ph
                        </p>
                    </div>
                </body>
                </html>
            ";
            break;
            
        case 'rejected':
            $subject = "[LSGH] Absence Slip Requires Attention - " . $student_name;
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;'>
                        <h2 style='color: #dc3545; text-align: center;'>Absence Slip Requires Attention</h2>
                        
                        <p>Dear $parent_name,</p>
                        
                        <p>We need to discuss the absence slip submitted for <strong>$student_name</strong>. The slip status has been marked as <strong style='color: #dc3545;'>REQUIRES ATTENTION</strong>.</p>
                        
                        <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;'>
                            <h3 style='margin-top: 0;'>Slip Details:</h3>
                            <p><strong>Slip ID:</strong> #$slip_id</p>
                            <p><strong>Student:</strong> $student_name</p>
                            <p><strong>Status:</strong> Requires Attention</p>
                            <p><strong>Date Processed:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                        </div>
                        
                        <div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0;'>
                            <h4 style='color: #856404; margin-top: 0;'>Next Steps:</h4>
                            <p style='color: #856404;'>Please contact the Academic Office to discuss this absence slip. Additional documentation or clarification may be required.</p>
                        </div>
                        
                        <p><strong>Contact Information:</strong><br>
                        Academic Office: [Your phone number]<br>
                        Email: edtech@lsgh.edu.ph</p>
                        
                        <p>We appreciate your prompt attention to this matter.</p>
                        
                        <p>Best regards,<br>
                        <strong>Academic Office</strong><br>
                        La Salle Green Hills</p>
                        
                        <hr style='margin: 30px 0;'>
                        <p style='font-size: 12px; color: #666; text-align: center;'>
                            This is an automated message. Please do not reply to this email.<br>
                            For feedback: edtech@lsgh.edu.ph
                        </p>
                    </div>
                </body>
                </html>
            ";
            break;
            
        case 'pending':
            $subject = "[LSGH] Absence Slip Under Review - " . $student_name;
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;'>
                        <h2 style='color: #ffc107; text-align: center;'>Absence Slip Under Review</h2>
                        
                        <p>Dear $parent_name,</p>
                        
                        <p>This is to inform you that the absence slip for <strong>$student_name</strong> is currently <strong style='color: #ffc107;'>UNDER REVIEW</strong>.</p>
                        
                        <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                            <h3 style='margin-top: 0;'>Slip Details:</h3>
                            <p><strong>Slip ID:</strong> #$slip_id</p>
                            <p><strong>Student:</strong> $student_name</p>
                            <p><strong>Status:</strong> Under Review</p>
                            <p><strong>Date Updated:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                        </div>
                        
                        <p>We are currently reviewing the submitted absence slip. You will receive another notification once the review is complete.</p>
                        
                        <p>Thank you for your patience.</p>
                        
                        <p>Best regards,<br>
                        <strong>Academic Office</strong><br>
                        La Salle Green Hills</p>
                        
                        <hr style='margin: 30px 0;'>
                        <p style='font-size: 12px; color: #666; text-align: center;'>
                            This is an automated message. Please do not reply to this email.<br>
                            For feedback: edtech@lsgh.edu.ph
                        </p>
                    </div>
                </body>
                </html>
            ";
            break;
            
        default:
            return false;
    }
    
    // Send email using your existing mail function
    if (mail($email, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}
// Handle form submission for status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'], $_POST['new_status'], $_POST['table_name'])) {
    $update_id = intval($_POST['update_id']);
    $table_name = $conn->real_escape_string($_POST['table_name']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    try {
        // Update the database status
        $update_sql = "UPDATE $table_name SET status = '$new_status' WHERE id = $update_id";
        
        if ($conn->query($update_sql) === TRUE) {
            // Get the updated record details for email
            $select_sql = "SELECT * FROM $table_name WHERE id = $update_id";
            $result = $conn->query($select_sql);
            
            if ($result && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                
                // Prepare email details
                $recipient_email = $record['email'];
                $student_name = $record['student_first_name'] . ' ' . $record['student_last_name'];
                $parent_name = $record['parent_first_name'] . ' ' . $record['parent_last_name'];
                $slip_id = $record['id'];
                $reason = isset($record['reason']) ? $record['reason'] : '';
                
                // Send email notification
                $email_sent = sendStatusUpdateEmail($recipient_email, $student_name, $parent_name, $slip_id, $new_status, $reason);
                
                // Set success message
                if ($email_sent) {
                    $success_message = "Status updated to '$new_status' successfully! Email notification sent to $recipient_email";
                } else {
                    $success_message = "Status updated to '$new_status' successfully, but email notification failed.";
                }
                
                // Optional: Also notify the teacher if status is approved
                if (strtolower($new_status) === 'accepted' && !empty($record['lc_no'])) {
                    $teacher_sql = "SELECT email, name FROM lc WHERE lc_no = '{$record['lc_no']}'";
                    $teacher_result = $conn->query($teacher_sql);
                    
                    if ($teacher_result && $teacher_result->num_rows > 0) {
                        $teacher = $teacher_result->fetch_assoc();
                        $teacher_subject = "Absence Slip Approved - " . $student_name;
                        $teacher_message = "Dear {$teacher['name']},\n\nThe absence slip for $student_name has been approved.\n\nSlip ID: #$slip_id\nGrade: {$record['grade_level']}\nSection: {$record['section']}\nDates: {$record['start_date']} to {$record['end_date']}\nReason: $reason\n\nPlease coordinate with the student for any missed assignments or quizzes.\n\nBest regards,\nAcademic Office";
                        
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "From: kenanbanal3@gmail.com\r\n";
                        $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
                        
                        mail($teacher['email'], $teacher_subject, $teacher_message, $headers);
                    }
                }
            }
            
            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1&status=" . urlencode($new_status));
            exit;
        } else {
            $error_message = "Error updating status: " . $conn->error;
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Display success/error messages
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $status = $_GET['status'] ?? '';
    echo "<div class='success-message'>Status updated to '$status' and email notification sent!</div>";
}

if (isset($error_message)) {
    echo "<div class='error-message'>$error_message</div>";
}
//////////////////////////END DELETE///////////////////////////////////////

// Handle status updates for different tables
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id']) && isset($_POST['new_status']) && isset($_POST['table_name'])) {
    $id = intval($_POST['update_id']);
    $new_status = $_POST['new_status'];
    $table_name = $_POST['table_name'];
    
    // Validate status values and table names
    if (in_array($new_status, ['accepted', 'rejected', 'pending']) && 
        in_array($table_name, ['absence_slip', 'makeup_slips', 'slips'])) {
        
        $stmt = $conn->prepare("UPDATE `$table_name` SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to maintain record position after status update
        $redirect_params = [];
        $redirect_params[] = 'table=' . urlencode($table_name);
        
        if (isset($_POST['table_name']) && $_POST['table_name'] === 'makeup_slips') {
            if (isset($_GET['record'])) {
                $redirect_params[] = 'record=' . intval($_GET['record']);
            }
        } elseif (isset($_POST['table_name']) && $_POST['table_name'] === 'slips') {
            if (isset($_GET['record'])) {
                $redirect_params[] = 'record=' . intval($_GET['record']);
            }
            if (isset($_GET['lc_no'])) {
                $redirect_params[] = 'lc_no=' . urlencode($_GET['lc_no']);
            }
        }
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . implode('&', $redirect_params));
        exit;
    }
}

// Fetch all table names
$tables = [];
$table_result = $conn->query("SHOW TABLES");
while ($row = $table_result->fetch_array()) {
    $tables[] = $row[0];
}

// Get selected table (default to first table if none selected)
$selected_table = isset($_GET['table']) ? $_GET['table'] : (count($tables) > 0 ? $tables[0] : '');

// Handle record navigation for makeup_slips
$current_record_index = 0;
$total_records = 0;
$makeup_slips_records = [];

if ($selected_table === 'makeup_slips') {
    $result = $conn->query("SELECT * FROM makeup_slips ORDER BY id");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $makeup_slips_records[] = $row;
        }
        $total_records = count($makeup_slips_records);
        
        // Get current record index from URL parameter
        if (isset($_GET['record']) && is_numeric($_GET['record'])) {
            $requested_index = intval($_GET['record']);
            if ($requested_index >= 0 && $requested_index < $total_records) {
                $current_record_index = $requested_index;
            }
        }
    }
}

// Handle record navigation for slips table
$slips_current_record_index = 0;
$slips_total_records = 0;
$slips_records = [];
$available_lc_numbers = [];
$selected_lc_no = '';

if ($selected_table === 'slips') {
    // Get all unique LC numbers
    $lc_result = $conn->query("SELECT DISTINCT lc_no FROM slips WHERE lc_no IS NOT NULL AND lc_no != '' ORDER BY lc_no");
    if ($lc_result && $lc_result->num_rows > 0) {
        while ($row = $lc_result->fetch_assoc()) {
            $available_lc_numbers[] = $row['lc_no'];
        }
    }
    
    // Get selected LC number
    $selected_lc_no = isset($_GET['lc_no']) ? $_GET['lc_no'] : (count($available_lc_numbers) > 0 ? $available_lc_numbers[0] : '');
    
    if ($selected_lc_no) {
        $stmt = $conn->prepare("SELECT * FROM slips WHERE lc_no = ? ORDER BY id");
        $stmt->bind_param("s", $selected_lc_no);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $slips_records[] = $row;
            }
            $slips_total_records = count($slips_records);
            
            // Get current record index from URL parameter
            if (isset($_GET['record']) && is_numeric($_GET['record'])) {
                $requested_index = intval($_GET['record']);
                if ($requested_index >= 0 && $requested_index < $slips_total_records) {
                    $slips_current_record_index = $requested_index;
                }
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
            min-width: 70px;
            justify-content: center;
        }
        
        .action-btn.accept {
            background-color: #28a745;
            color: white;
        }
        
        .action-btn.accept:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }
        
        .action-btn.reject {
            background-color: #dc3545;
            color: white;
        }
        
        .action-btn.reject:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        
        .status-badge.status-accepted {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-badge.status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-badge.status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Success Message Styles */
        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #c3e6cb;
            border-left: 6px solid #28a745;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
            position: relative;
            overflow: hidden;
            animation: slideInFromTop 0.5s ease-out;
        }

        .success-message::before {
            content: "✓";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            background: rgba(255, 255, 255, 0.8);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-message {
            padding-left: 70px; /* Make room for the checkmark */
        }

        /* Error Message Styles */
        .error-message {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            color: #721c24;
            border: 2px solid #f1aeb5;
            border-left: 6px solid #dc3545;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
            position: relative;
            overflow: hidden;
            animation: slideInFromTop 0.5s ease-out;
        }

        .error-message::before {
            content: "✕";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            background: rgba(255, 255, 255, 0.8);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-message {
            padding-left: 70px; /* Make room for the X mark */
        }

        /* Warning/Info Message Styles (bonus) */
        .warning-message {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border: 2px solid #ffeaa7;
            border-left: 6px solid #ffc107;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
            position: relative;
            overflow: hidden;
            animation: slideInFromTop 0.5s ease-out;
            padding-left: 70px;
        }

        .warning-message::before {
            content: "⚠";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            font-weight: bold;
            color: #ffc107;
            background: rgba(255, 255, 255, 0.8);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-message {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border: 2px solid #bee5eb;
            border-left: 6px solid #17a2b8;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.15);
            position: relative;
            overflow: hidden;
            animation: slideInFromTop 0.5s ease-out;
            padding-left: 70px;
        }

        .info-message::before {
            content: "ℹ";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            font-weight: bold;
            color: #17a2b8;
            background: rgba(255, 255, 255, 0.8);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animation for messages */
        @keyframes slideInFromTop {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover effects for interactive feel */
        .success-message:hover,
        .error-message:hover,
        .warning-message:hover,
        .info-message:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .success-message,
            .error-message,
            .warning-message,
            .info-message {
                padding: 12px 16px;
                padding-left: 60px;
                font-size: 14px;
                margin: 15px 0;
            }
            
            .success-message::before,
            .error-message::before,
            .warning-message::before,
            .info-message::before {
                width: 28px;
                height: 28px;
                font-size: 16px;
                left: 16px;
            }
        }

        /* Close button for dismissible messages (optional) */
        .message-close {
            position: absolute;
            top: 8px;
            right: 12px;
            background: none;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
            color: inherit;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .message-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.1);
        }

        /* Auto-hide animation (optional) */
        .message-auto-hide {
            animation: slideInFromTop 0.5s ease-out, fadeOut 0.5s ease-in 4.5s forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-30px);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">
                    <i class="fas fa-database"></i>
                    LSGH DATABASE
                </h2>
                <p class="sidebar-subtitle">Admin Dashboard View</p>
            </div>
            
            <nav class="table-nav">
                <div class="nav-section-title">List of Tables</div>
                <?php foreach ($tables as $table): ?>
                    <a href="?table=<?php echo urlencode($table); ?>" 
                       class="table-nav-item <?php echo ($table === $selected_table) ? 'active' : ''; ?>">
                        <i class="fas fa-table"></i>
                        <?php echo htmlspecialchars($table); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- Overlay for mobile -->
        <div class="overlay" id="overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">
                        <i class="fas fa-table"></i>
                        <?php echo htmlspecialchars($selected_table); ?>
                        <?php if ($selected_table && !in_array($selected_table, ['makeup_slips', 'slips'])): ?>
                            <span class="table-indicator">
                                <?php
                                $count_result = $conn->query("SELECT COUNT(*) as count FROM `$selected_table`");
                                $count = $count_result->fetch_assoc()['count'];
                                echo $count . ' record' . ($count != 1 ? 's' : '');
                                ?>
                            </span>
                        <?php elseif ($selected_table === 'makeup_slips' && $total_records > 0): ?>
                            <span class="table-indicator">
                                Record <?php echo ($current_record_index + 1); ?> of <?php echo $total_records; ?>
                            </span>
                        <?php elseif ($selected_table === 'slips' && $slips_total_records > 0): ?>
                            <span class="table-indicator">
                                LC: <?php echo htmlspecialchars($selected_lc_no); ?> - Record <?php echo ($slips_current_record_index + 1); ?> of <?php echo $slips_total_records; ?>
                            </span>
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo substr($login_session, 0, 1); ?>
                        </div>
                        <div class="user-name"><?php echo $login_session; ?></div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sign Out</span>
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="content">
                <?php if ($selected_table && in_array($selected_table, $tables)): ?>
                    <?php if ($selected_table === 'makeup_slips'): ?>
                        <!-- Special single-record view for makeup_slips -->
                        <?php if ($total_records > 0): ?>
                            <!-- Navigation Controls -->
                            <div class="record-navigation">
                                <div class="nav-controls">
                                    <a href="?table=makeup_slips&record=0" 
                                       class="nav-btn <?php echo ($current_record_index == 0) ? 'disabled' : ''; ?>">
                                        <i class="fas fa-angle-double-left"></i> First
                                    </a>
                                    <a href="?table=makeup_slips&record=<?php echo max(0, $current_record_index - 1); ?>" 
                                       class="nav-btn <?php echo ($current_record_index == 0) ? 'disabled' : ''; ?>">
                                        <i class="fas fa-angle-left"></i> Previous
                                    </a>
                                    <span class="record-counter">
                                        <?php echo ($current_record_index + 1); ?> of <?php echo $total_records; ?>
                                    </span>
                                    <a href="?table=makeup_slips&record=<?php echo min($total_records - 1, $current_record_index + 1); ?>" 
                                       class="nav-btn <?php echo ($current_record_index >= $total_records - 1) ? 'disabled' : ''; ?>">
                                        Next <i class="fas fa-angle-right"></i>
                                    </a>
                                    <a href="?table=makeup_slips&record=<?php echo $total_records - 1; ?>" 
                                       class="nav-btn <?php echo ($current_record_index >= $total_records - 1) ? 'disabled' : ''; ?>">
                                        Last <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Single Record Display -->
                            <div class="record-card">
                                <div class="record-header">
                                    <h2 class="record-title">
                                        <i class="fas fa-file-alt"></i> 
                                        Makeup Slip Record #<?php echo htmlspecialchars($makeup_slips_records[$current_record_index]['id']); ?>
                                    </h2>
                                </div>
                                <div class="record-content">
                                    <?php 
                                    $current_record = $makeup_slips_records[$current_record_index];
                                    ?>
                                    <div class="record-fields">
                                        <?php foreach ($current_record as $key => $value): ?>
                                            <div class="field-group">
                                                <label class="field-label"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></label>
                                                <div class="field-value">
                                                    <?php if (strtolower($key) === 'status'): ?>
                                                        <?php
                                                        $status_lower = strtolower($value);
                                                        $status_class = 'status-' . $status_lower;
                                                        $icon = '';
                                                        
                                                        switch($status_lower) {
                                                            case 'accepted':
                                                                $icon = '<i class="fas fa-check-circle"></i>';
                                                                break;
                                                            case 'rejected':
                                                                $icon = '<i class="fas fa-times-circle"></i>';
                                                                break;
                                                            case 'pending':
                                                            default:
                                                                $icon = '<i class="fas fa-clock"></i>';
                                                                $status_class = 'status-pending';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="status-badge <?php echo $status_class; ?>">
                                                            <?php echo $icon . ' ' . htmlspecialchars($value); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($value); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- TODO FIX THE ACCEPT PENDING AND REJECT -->
                                    <!-- Action Buttons -->
                                    <div class="record-actions">
                                        <h3 class="actions-title">Change Status</h3>
                                        <div class="action-buttons-large">
                                            <?php $current_status = strtolower($current_record['status']); ?>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="update_id" value="<?php echo intval($current_record['id']); ?>">
                                                <input type="hidden" name="table_name" value="makeup_slips">
                                                <input type="hidden" name="new_status" value="accepted">
                                                <button type="submit" class="action-btn-large accept <?php echo ($current_status == 'accepted' ? 'active' : ''); ?>"
                                                        <?php echo ($current_status == 'accepted' ? 'disabled' : ''); ?>>
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>

                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="update_id" value="<?php echo intval($current_record['id']); ?>">
                                                <input type="hidden" name="table_name" value="makeup_slips">
                                                <input type="hidden" name="new_status" value="pending">
                                                <button type="submit" class="action-btn-large pending <?php echo ($current_status == 'pending' ? 'active' : ''); ?>"
                                                        <?php echo ($current_status == 'pending' ? 'disabled' : ''); ?>>
                                                    <i class="fas fa-clock"></i> Pending
                                                </button>
                                            </form>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="update_id" value="<?php echo intval($current_record['id']); ?>">
                                                <input type="hidden" name="table_name" value="makeup_slips">
                                                <input type="hidden" name="new_status" value="rejected">
                                                <button type="submit" class="action-btn-large reject <?php echo ($current_status == 'rejected' ? 'active' : ''); ?>"
                                                        <?php echo ($current_status == 'rejected' ? 'disabled' : ''); ?>>
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-file-alt"></i>
                                <p>No makeup slip records found.</p>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($selected_table === 'slips'): ?>
                        <!-- Special single-record view for slips with LC selector -->
                        <?php if (count($available_lc_numbers) > 0): ?>
                            <!-- LC Number Selector -->
                            <div class="lc-selector">
                                <div class="lc-selector-title">
                                    <i class="fas fa-filter"></i> Select LC Number
                                </div>
                                <div class="lc-buttons">
                                    <?php foreach ($available_lc_numbers as $lc_no): ?>
                                        <a href="?table=slips&lc_no=<?php echo urlencode($lc_no); ?>&record=0" 
                                           class="lc-btn <?php echo ($lc_no === $selected_lc_no) ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($lc_no); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($slips_total_records > 0): ?>
                                <!-- Navigation Controls -->
                                <div class="record-navigation">
                                    <div class="nav-controls">
                                        <a href="?table=slips&lc_no=<?php echo urlencode($selected_lc_no); ?>&record=0" 
                                           class="nav-btn <?php echo ($slips_current_record_index == 0) ? 'disabled' : ''; ?>">
                                            <i class="fas fa-angle-double-left"></i> First
                                        </a>
                                        <a href="?table=slips&lc_no=<?php echo urlencode($selected_lc_no); ?>&record=<?php echo max(0, $slips_current_record_index - 1); ?>" 
                                           class="nav-btn <?php echo ($slips_current_record_index == 0) ? 'disabled' : ''; ?>">
                                            <i class="fas fa-angle-left"></i> Previous
                                        </a>
                                        <span class="record-counter">
                                            <?php echo ($slips_current_record_index + 1); ?> of <?php echo $slips_total_records; ?>
                                        </span>
                                        <a href="?table=slips&lc_no=<?php echo urlencode($selected_lc_no); ?>&record=<?php echo min($slips_total_records - 1, $slips_current_record_index + 1); ?>" 
                                           class="nav-btn <?php echo ($slips_current_record_index >= $slips_total_records - 1) ? 'disabled' : ''; ?>">
                                            Next <i class="fas fa-angle-right"></i>
                                        </a>
                                        <a href="?table=slips&lc_no=<?php echo urlencode($selected_lc_no); ?>&record=<?php echo $slips_total_records - 1; ?>" 
                                           class="nav-btn <?php echo ($slips_current_record_index >= $slips_total_records - 1) ? 'disabled' : ''; ?>">
                                            Last <i class="fas fa-angle-double-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Single Record Display -->
                                <div class="record-card">
                                    <div class="record-header">
                                        <h2 class="record-title">
                                            <i class="fas fa-file-alt"></i> 
                                            Slip Record #<?php echo htmlspecialchars($slips_records[$slips_current_record_index]['id']); ?>
                                            <span style="font-size: 0.8em; color: #6b7280;">
                                                (LC: <?php echo htmlspecialchars($selected_lc_no); ?>)
                                            </span>
                                        </h2>
                                    </div>
                                    <div class="record-content">
                                        <?php 
                                        $current_slip_record = $slips_records[$slips_current_record_index];
                                        ?>
                                        <div class="record-fields">
                                            <?php foreach ($current_slip_record as $key => $value): ?>
                                                <div class="field-group">
                                                    <label class="field-label"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></label>
                                                    <div class="field-value">
                                                        <?php if (strtolower($key) === 'status'): ?>
                                                            <?php
                                                            $status_lower = strtolower($value);
                                                            $status_class = 'status-' . $status_lower;
                                                            $icon = '';
                                                            
                                                            switch($status_lower) {
                                                                case 'accepted':
                                                                    $icon = '<i class="fas fa-check-circle"></i>';
                                                                    break;
                                                                case 'rejected':
                                                                    $icon = '<i class="fas fa-times-circle"></i>';
                                                                    break;
                                                                case 'pending':
                                                                default:
                                                                    $icon = '<i class="fas fa-clock"></i>';
                                                                    $status_class = 'status-pending';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="status-badge <?php echo $status_class; ?>">
                                                                <?php echo $icon . ' ' . htmlspecialchars($value); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <?php echo htmlspecialchars($value); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="record-actions">
                                            <h3 class="actions-title">Change Status</h3>
                                            <div class="action-buttons-large">
                                                <?php $current_status = strtolower($current_slip_record['status']); ?>
                                                
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
                                                    <input type="hidden" name="table_name" value="slips">
                                                    <input type="hidden" name="new_status" value="accepted">
                                                    <button type="submit" class="action-btn-large accept <?php echo ($current_status == 'accepted' ? 'active' : ''); ?>"
                                                            <?php echo ($current_status == 'accepted' ? 'disabled' : ''); ?>>
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
                                                    <input type="hidden" name="table_name" value="slips">
                                                    <input type="hidden" name="new_status" value="pending">
                                                    <button type="submit" class="action-btn-large pending <?php echo ($current_status == 'pending' ? 'active' : ''); ?>"
                                                            <?php echo ($current_status == 'pending' ? 'disabled' : ''); ?>>
                                                        <i class="fas fa-clock"></i> Pending
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
                                                    <input type="hidden" name="table_name" value="slips">
                                                    <input type="hidden" name="new_status" value="rejected">
                                                    <button type="submit" class="action-btn-large reject <?php echo ($current_status == 'rejected' ? 'active' : ''); ?>"
                                                            <?php echo ($current_status == 'rejected' ? 'disabled' : ''); ?>>
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <p>No slip records found for LC: <?php echo htmlspecialchars($selected_lc_no); ?>.</p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-file-alt"></i>
                                <p>No LC numbers found in slips table.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Standard table view for other tables with sorting -->
                    <div class="table-card">
                        <div class="table-card-content">
                            <?php
                            $result = $conn->query("SELECT * FROM `$selected_table`");
                            if ($result && $result->num_rows > 0):
                                echo "<table class='data-table sortable-table' id='table-" . htmlspecialchars($selected_table) . "'><thead><tr>";
                                
                                // Print table headers
                                $first_row = $result->fetch_assoc();
                                $column_index = 0;
                                foreach (array_keys($first_row) as $col) {
                                    echo "<th class='sortable' data-column='" . $column_index . "'>" . htmlspecialchars($col) . "</th>";
                                    $column_index++;
                                }
                                
                                // Add Action column for tables with status management
                                if (in_array($selected_table, ['absence_slip', 'makeup_slips', 'slips'])) {
                                    echo "<th>Action</th>";
                                }
                                
                                echo "</tr></thead><tbody>";
                                
                                // Rewind and display all rows
                                $result->data_seek(0);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    foreach ($row as $key => $val) {
                                        // Special handling for status field
                                        if (strtolower($key) === 'status') {
                                            $status_lower = strtolower($val);
                                            $status_class = 'status-' . $status_lower;
                                            $icon = '';
                                            
                                            switch($status_lower) {
                                                case 'accepted':
                                                    $icon = '<i class="fas fa-check-circle"></i>';
                                                    break;
                                                case 'rejected':
                                                    $icon = '<i class="fas fa-times-circle"></i>';
                                                    break;
                                                case 'pending':
                                                default:
                                                    $icon = '<i class="fas fa-clock"></i>';
                                                    $status_class = 'status-pending';
                                                    break;
                                            }
                                            
                                            echo "<td><span class='status-badge " . $status_class . "'>" . $icon . " " . htmlspecialchars($val) . "</span></td>";
                                        } else {
                                            echo "<td>" . htmlspecialchars($val) . "</td>";
                                        }
                                    }
                                    
                                    // Action column for tables with status management
                                    if (in_array($selected_table, ['absence_slip', 'makeup_slips', 'slips'])) {
                                        echo "<td>";
                                        $current_status = strtolower($row['status']);
                                        
                                        // Show action buttons for all statuses
                                        echo '<div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="' . intval($row['id']) . '">
                                                    <input type="hidden" name="table_name" value="' . htmlspecialchars($selected_table) . '">
                                                    <input type="hidden" name="new_status" value="accepted">
                                                    <button type="submit" class="action-btn accept ' . ($current_status == 'accepted' ? 'active' : '') . '"
                                                            ' . ($current_status == 'accepted' ? 'disabled' : '') . '>
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="' . intval($row['id']) . '">
                                                    <input type="hidden" name="table_name" value="' . htmlspecialchars($selected_table) . '">
                                                    <input type="hidden" name="new_status" value="pending">
                                                    <button type="submit" class="action-btn pending ' . ($current_status == 'pending' ? 'active' : '') . '"
                                                            ' . ($current_status == 'pending' ? 'disabled' : '') . '>
                                                        <i class="fas fa-clock"></i> Pending
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="' . intval($row['id']) . '">
                                                    <input type="hidden" name="table_name" value="' . htmlspecialchars($selected_table) . '">
                                                    <input type="hidden" name="new_status" value="rejected">
                                                    <button type="submit" class="action-btn reject ' . ($current_status == 'rejected' ? 'active' : '') . '"
                                                            ' . ($current_status == 'rejected' ? 'disabled' : '') . '>
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                              </div>';
                                        echo "</td>";
                                    }
                                    
                                    echo "</tr>";
                                }
                                
                                echo "</tbody></table>";
                            else:
                                echo "<div class='empty-state'>
                                        <i class='fas fa-database'></i>
                                        <p>No records found in " . htmlspecialchars($selected_table) . ".</p>
                                      </div>";
                            endif;
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-table"></i>
                        <p>Select a table from the sidebar to view its contents.</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all tables
            document.querySelectorAll('.sortable-table th').forEach(headerCell => {
                if (!headerCell.textContent.includes('Action')) {  // Skip the Action column
                    headerCell.addEventListener('click', () => {
                        const table = headerCell.closest('table');
                        const columnIndex = parseInt(headerCell.dataset.column);
                        const currentIsAscending = headerCell.classList.contains('sort-asc');
                        
                        // Remove sort classes from all headers in this table
                        table.querySelectorAll('th').forEach(th => {
                            th.classList.remove('sort-asc', 'sort-desc');
                        });
                        
                        // Add appropriate sort class
                        if (currentIsAscending) {
                            headerCell.classList.add('sort-desc');
                        } else {
                            headerCell.classList.add('sort-asc');
                        }
                        
                        // Sort the table
                        sortTableByColumn(table, columnIndex, !currentIsAscending);
                    });
                }
            });
        });

        function sortTableByColumn(table, columnIndex, ascending = true) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Sort rows based on cell content in the specified column
            const sortedRows = rows.sort((rowA, rowB) => {
                let cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
                let cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();
                
                // Check if the values are numbers
                const numA = parseFloat(cellA);
                const numB = parseFloat(cellB);
                
                if (!isNaN(numA) && !isNaN(numB)) {
                    return ascending ? numA - numB : numB - numA;
                } else {
                    // For text comparison, use localeCompare for proper string comparison
                    return ascending 
                        ? cellA.localeCompare(cellB) 
                        : cellB.localeCompare(cellA);
                }
            });
            
            // Remove existing rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            
            // Add sorted rows back to the table
            sortedRows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
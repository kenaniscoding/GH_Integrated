<?php
// Add this function to your existing PHP file (similar to your sendEmail function)
function sendStatusUpdateEmail($email, $student_name, $parent_name, $slip_id, $new_status, $reason = '') {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: kenanbanal3@gmail.com\r\n";
    $headers .= "Reply-To: kenanbanal3@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Customize subject and message based on status
    switch (strtolower($new_status)) {
        case 'accepted':
            $subject = "Absence Slip Approved - " . $student_name;
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
            $subject = "Absence Slip Requires Attention - " . $student_name;
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
            $subject = "Absence Slip Under Review - " . $student_name;
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
?>

<!-- Your existing HTML buttons remain the same -->
<div class="record-actions">
    <h3 class="actions-title">Change Status</h3>
    <div class="action-buttons-large">
        <?php $current_status = strtolower($current_slip_record['status']); ?>
       
        <form method="POST" style="display: inline;">
            <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
            <input type="hidden" name="table_name" value="absence_slip">
            <input type="hidden" name="new_status" value="accepted">
            <button type="submit" class="action-btn-large accept <?php echo ($current_status == 'accepted' ? 'active' : ''); ?>"
                    <?php echo ($current_status == 'accepted' ? 'disabled' : ''); ?>>
                <i class="fas fa-check"></i> Accept
            </button>
        </form>
       
        <form method="POST" style="display: inline;">
            <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
            <input type="hidden" name="table_name" value="absence_slip">
            <input type="hidden" name="new_status" value="pending">
            <button type="submit" class="action-btn-large pending <?php echo ($current_status == 'pending' ? 'active' : ''); ?>"
                    <?php echo ($current_status == 'pending' ? 'disabled' : ''); ?>>
                <i class="fas fa-clock"></i> Pending
            </button>
        </form>
       
        <form method="POST" style="display: inline;">
            <input type="hidden" name="update_id" value="<?php echo intval($current_slip_record['id']); ?>">
            <input type="hidden" name="table_name" value="absence_slip">
            <input type="hidden" name="new_status" value="rejected">
            <button type="submit" class="action-btn-large reject <?php echo ($current_status == 'rejected' ? 'active' : ''); ?>"
                    <?php echo ($current_status == 'rejected' ? 'disabled' : ''); ?>>
                <i class="fas fa-times"></i> Reject
            </button>
        </form>
    </div>
</div>
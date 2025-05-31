<?php
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is the makeup slip question submission
    if (isset($_POST['relation']) && ($_POST['relation'] === 'yes' || $_POST['relation'] === 'no')) {
        // Get the user's choice
        $makeupChoice = $_POST['relation'];
        
        // Redirect based on choice
        if ($makeupChoice === 'yes') {
            header('Location: makeup.php');
            exit();
        } else {
            header('Location: exit.php');
            exit();
        }
    }
    
    // If we reach here, it means the main absence form was submitted
    // Process the main form data here (your existing logic)
    
    // After processing the main form, show the makeup slip question
    // Don't exit here, let the form display below
}
?>

<script>
// Optional: Add some styling for the success message
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        successMessage.style.cssText = `
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
        `;
    }
});
</script>

</body>
</html>
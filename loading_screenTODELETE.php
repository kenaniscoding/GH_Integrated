<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Loading Screen</title>
    <style>
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .loading-overlay.show {
            display: flex;
        }

        /* Loading Container */
        .loading-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .loading-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #28a745, #17a2b8);
            animation: loadingBar 2s ease-in-out infinite;
        }

        @keyframes loadingBar {
            0% { left: -100%; }
            50% { left: 0%; }
            100% { left: 100%; }
        }

        /* Email Icon Animation */
        .email-icon {
            font-size: 60px;
            color: #007bff;
            margin-bottom: 20px;
            animation: emailBounce 1.5s ease-in-out infinite;
        }

        @keyframes emailBounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        /* Spinner */
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Loading Text */
        .loading-title {
            font-family: 'Arial', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .loading-subtitle {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        /* Progress Steps */
        .progress-steps {
            text-align: left;
            margin-top: 20px;
        }

        .progress-step {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #666;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .progress-step.active {
            opacity: 1;
            color: #007bff;
            font-weight: bold;
        }

        .progress-step.completed {
            opacity: 1;
            color: #28a745;
        }

        .progress-step::before {
            content: '○';
            margin-right: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .progress-step.active::before {
            content: '●';
            color: #007bff;
            animation: pulse 1s ease-in-out infinite;
        }

        .progress-step.completed::before {
            content: '✓';
            color: #28a745;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Dots Animation */
        .dots {
            display: inline-block;
        }

        .dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        /* Success Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #28a745;
            margin: 0 auto 20px;
            position: relative;
            display: none;
        }

        .success-checkmark.show {
            display: block;
            animation: successPop 0.5s ease-out;
        }

        .success-checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 40px;
            font-weight: bold;
        }

        @keyframes successPop {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Button Styles */
        .action-btn-large {
            position: relative;
            overflow: hidden;
        }

        .action-btn-large.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .action-btn-large .btn-text {
            transition: opacity 0.3s ease;
        }

        .action-btn-large.loading .btn-text {
            opacity: 0;
        }

        .btn-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-btn-large.loading .btn-spinner {
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-container">
            <!-- Email Icon -->
            <div class="email-icon">📧</div>
            
            <!-- Loading Title -->
            <div class="loading-title">Sending Email</div>
            <div class="loading-subtitle">Please wait<span class="dots"></span></div>
            
            <!-- Spinner -->
            <div class="spinner"></div>
            
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="progress-step" id="step1">
                    <span>Updating status...</span>
                </div>
                <div class="progress-step" id="step2">
                    <span>Preparing email...</span>
                </div>
                <div class="progress-step" id="step3">
                    <span>Sending notification...</span>
                </div>
                <div class="progress-step" id="step4">
                    <span>Confirming delivery...</span>
                </div>
            </div>
            
            <!-- Success Checkmark (initially hidden) -->
            <div id="successCheckmark" class="success-checkmark"></div>
        </div>
    </div>

    <!-- Example buttons with loading functionality -->
    <div class="record-actions">
        <h3 class="actions-title">Change Status</h3>
        <div class="action-buttons-large">
            <form method="POST" style="display: inline;" onsubmit="showLoading(this, 'accepted')">
                <input type="hidden" name="update_id" value="123">
                <input type="hidden" name="table_name" value="absence_slip">
                <input type="hidden" name="new_status" value="accepted">
                <button type="submit" class="action-btn-large accept">
                    <span class="btn-text"><i class="fas fa-check"></i> Accept</span>
                    <div class="btn-spinner"></div>
                </button>
            </form>
           
            <form method="POST" style="display: inline;" onsubmit="showLoading(this, 'pending')">
                <input type="hidden" name="update_id" value="123">
                <input type="hidden" name="table_name" value="absence_slip">
                <input type="hidden" name="new_status" value="pending">
                <button type="submit" class="action-btn-large pending">
                    <span class="btn-text"><i class="fas fa-clock"></i> Pending</span>
                    <div class="btn-spinner"></div>
                </button>
            </form>
           
            <form method="POST" style="display: inline;" onsubmit="showLoading(this, 'rejected')">
                <input type="hidden" name="update_id" value="123">
                <input type="hidden" name="table_name" value="absence_slip">
                <input type="hidden" name="new_status" value="rejected">
                <button type="submit" class="action-btn-large reject">
                    <span class="btn-text"><i class="fas fa-times"></i> Reject</span>
                    <div class="btn-spinner"></div>
                </button>
            </form>
        </div>
    </div>

    <script>
        let currentStep = 0;
        const steps = ['step1', 'step2', 'step3', 'step4'];

        function showLoading(form, status) {
            // Show loading overlay
            document.getElementById('loadingOverlay').classList.add('show');
            
            // Add loading class to button
            const button = form.querySelector('button');
            button.classList.add('loading');
            
            // Simulate progress steps
            simulateProgress();
            
            // Prevent form submission temporarily for demo
            // Remove this return false in production
            // return false;
        }

        function simulateProgress() {
            currentStep = 0;
            
            const interval = setInterval(() => {
                if (currentStep > 0) {
                    document.getElementById(steps[currentStep - 1]).classList.remove('active');
                    document.getElementById(steps[currentStep - 1]).classList.add('completed');
                }
                
                if (currentStep < steps.length) {
                    document.getElementById(steps[currentStep]).classList.add('active');
                    currentStep++;
                } else {
                    clearInterval(interval);
                    showSuccess();
                }
            }, 800);
        }

        function showSuccess() {
            // Hide spinner
            document.querySelector('.spinner').style.display = 'none';
            document.querySelector('.loading-title').textContent = 'Email Sent Successfully!';
            document.querySelector('.loading-subtitle').textContent = 'Status updated and notification sent.';
            
            // Show success checkmark
            document.getElementById('successCheckmark').classList.add('show');
            
            // Mark last step as completed
            document.getElementById(steps[steps.length - 1]).classList.remove('active');
            document.getElementById(steps[steps.length - 1]).classList.add('completed');
            
            // Hide loading after 2 seconds
            setTimeout(() => {
                hideLoading();
            }, 2000);
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('show');
            
            // Reset loading state
            document.querySelectorAll('.action-btn-large').forEach(btn => {
                btn.classList.remove('loading');
            });
            
            // Reset progress steps
            document.querySelectorAll('.progress-step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            
            // Reset UI elements
            document.querySelector('.spinner').style.display = 'block';
            document.querySelector('.loading-title').textContent = 'Sending Email';
            document.querySelector('.loading-subtitle').textContent = 'Please wait';
            document.getElementById('successCheckmark').classList.remove('show');
            
            currentStep = 0;
        }

        // Handle form submission errors (optional)
        window.addEventListener('beforeunload', function() {
            // Hide loading if page is being refreshed/closed
            hideLoading();
        });
    </script>
</body>
</html>
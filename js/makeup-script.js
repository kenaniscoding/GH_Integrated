// Function to update teachers based on selected subject
function updateTeachers() {
    const subjectSelect = document.getElementById('subject');
    const teacherSelect = document.getElementById('teacher');
    const selectedSubject = subjectSelect.value;
    
    // Clear current teacher options
    teacherSelect.innerHTML = '<option value="">Loading teachers...</option>';
    teacherSelect.disabled = true;
    
    if (!selectedSubject) {
        teacherSelect.innerHTML = '<option value="">Select subject first</option>';
        return;
    }
    
    // Make AJAX request to get teachers for the selected subject
    fetch(`get_teachers.php?subject=${encodeURIComponent(selectedSubject)}`)
        .then(response => response.json())
        .then(data => {
            teacherSelect.innerHTML = '';
            
            if (data.error) {
                teacherSelect.innerHTML = '<option value="">Error loading teachers</option>';
                console.error('Error:', data.error);
                return;
            }
            
            if (data.teachers && data.teachers.length > 0) {
                // Add default option
                teacherSelect.innerHTML = '<option value="" disabled selected>Select a teacher</option>';
                
                // Add teacher options
                data.teachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher;
                    option.textContent = teacher;
                    teacherSelect.appendChild(option);
                });
            } else {
                teacherSelect.innerHTML = '<option value="">No teachers found for this subject</option>';
            }
            
            teacherSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error fetching teachers:', error);
            teacherSelect.innerHTML = '<option value="">Error loading teachers</option>';
            teacherSelect.disabled = false;
        });
}

// Form submission with loading popup
function handleFormSubmission() {
    document.getElementById('makeupForm').addEventListener('submit', function(e) {
        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Add disabled class to form to prevent user interaction
        document.querySelector('.container').classList.add('form-disabled');
        
        // Change submit button text
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = 'Submitting...';
        submitBtn.disabled = true;
    });
}

// File selection handler function
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for subject change
    const subjectSelect = document.getElementById('subject');
    if (subjectSelect) {
        subjectSelect.addEventListener('change', updateTeachers);
    }
    
    // Initialize form submission handler
    handleFormSubmission();
    
    // Drag and drop functionality for file upload
    const uploadSection = document.querySelector('.file-upload-section');
    const fileInput = document.getElementById('medical_certificate');
    
    if (uploadSection && fileInput) {
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
    }
});

// Hide loading overlay if user navigates back
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
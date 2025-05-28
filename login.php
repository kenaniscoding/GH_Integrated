<?php
   $db = mysqli_connect('localhost','root','onelasalle','db3');
   session_start();
   $error='';
   if($_SERVER["REQUEST_METHOD"] == "POST") {
   
      // username and password sent from form
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']);
      $sql = "SELECT * FROM admin WHERE username = '$myusername' and passcode = '$mypassword'";
      $result = mysqli_query($db,$sql);      
      // $row = mysqli_num_rows($result);      
      $count = mysqli_num_rows($result);
      if($count == 1) {
         // session_register("myusername");
         $_SESSION['login_user'] = $myusername;
         header("location: welcome.php");
      } else {
         $error = "Your Login Name or Password is invalid";
      }
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
   <div class="login-container">
      <div class="login-header">
         <h1><strong>LSGH Admin Login</strong></h1>
      </div>
      <div class="login-form">
         <form action="" method="post">
            <div class="form-group">
               <label for="username"><strong>Username</strong></label>
               <div class="input-group">
                  <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required autofocus>
               </div>
            </div>
            <div class="form-group">
               <label for="password"><strong>Password</strong></label>
               <div class="input-group">
                  <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                  <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                     <i class="far fa-eye" id="toggleIcon"></i>
                  </button>
               </div>
            </div>
            <button type="submit" class="btn">Sign In</button>
            
            <?php if($error): ?>
            <div class="error-message">
               <i class="fas fa-exclamation-circle"></i>
               <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-footer">
               <p>Educational Technology Department</p>
            </div>
         </form>
      </div>
   </div>

   <script>
      // Password visibility toggle
      document.querySelector('.toggle-password').addEventListener('click', function() {
         const passwordInput = document.getElementById('password');
         const toggleIcon = document.getElementById('toggleIcon');
         
         if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
         } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
         }
      });
   </script>
</body>
</html>
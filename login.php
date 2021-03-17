<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to login page
                            header("location: login.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $mysqli->close();
}
?>
 
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
  <link
  rel="stylesheet"
  href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
  integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p"
  crossOrigin="anonymous"
/>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
  :root {
    --primary: #08aeea;
    --secondary: #13D2B8;
    --purple: #bd93f9;
    --pink: #ff6bcb;
    --blue: #8be9fd;
    --gray: #333;
    --font: "Poppins", sans-serif;
    --gradient: linear-gradient(40deg, #ff6ec4, #7873f5);
    --shadow: 0 0 15px 0 rgba(0,0,0,0.05);
  }
  *,
  *:before,
  *:after {
    box-sizing: border-box;
  }
  html {
    font-size: 62.5%;
  }

  body {
    font-family: var(--font);
    font-size: 1.4rem;
    overflow-x: hidden;
    font-weight: 300;
  }

  img {
    display: block;
    max-width: 100%;
  }

  a {
    text-decoration: none;
    color: #2772ab;
    font-weight:400;
    font-size: 14px;
  }

  input,
  button,
  textarea,
  select {
    font-family: var(--font);
    font-size: 1.4rem;
    font-weight: 300;
    outline: none;
    border: 0;
    margin: 0;
    padding: 0;
    border-radius: 0;
    -webkit-appearance: none;
  }

  button {
    cursor: pointer;
  }
          .signup-heading {
            text-align: center;
            font-weight: 600;
            color: #363a40;
            font-size: 35px;
            margin-bottom: -5px;
            margin-top: -2px;
          }
          
          .signup-or {
            color: #363a40;
            display: block;
            text-align: center;
            position: relative;
            margin: 10px;
          }
          .signup-or-text {
            display: inline-block;
            padding: 5px 10px;
            background-color: white;
            position: relative;
            font-size: 14px;
          }
          .signup-or:before {
            content: "";
            height: 1px;
            width: 100%;
            position: absolute;
            top: 50%;
            left: 0;
            background-color: #999;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
          }
          .help-block {
            display: block;
            margin-top: 5px;
            margin-bottom: 10px;
            color: #737373;
          }
          .login-form {
            max-width: 35rem;
            margin:  -1.5rem auto;
            padding: 1.5rem;
          }
          .login-form input {
            padding: 1.5rem;
            border: 1px solid #eee;
            color: #333;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            display: block;
            width: 100%;
            transition: border 0.25s linear;
            font-weight: normal;
          }
          .login-form input:focus {
            border-color: var(--primary);
          }
          .login-form .forgot {
            display: inline-block;
            text-transform: uppercase;
            color: #333;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
          }
          .login-form button {
            width: 100%;
            padding: 1.5rem;
            border-radius: 4px;
            color: white;
            background-color: var(--primary);
            text-transform: uppercase;
          }
          .login-form .input-icon {
            position: relative;
          }
          .login-form .input-icon input {
            padding-right: 4rem;
          }
          .login-form .input-icon i {
            position: absolute;
            top: 50%;
            right: 1.5rem;
            transform: translateY(-50%);
            color: #ccc;
            cursor: pointer;
          }
</style>
</head>
<body>
        <h1 class="signup-heading">Log in</h1>
        <div class="signup-or">
        <span class="signup-or-text">Please fill in your credentials to login</span>
        </div>

        <form class="login-form" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="login-form <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Email or Username</label>
         <input type="text" name="username" value="<?php echo $username; ?>" placeholder=""/>
         <span class="help-block"><?php echo $username_err; ?></span>
    </div>
    <div class="login-form <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
          <label>Password</label>
          <div class="input-icon">
            <input type="password" name="password" placeholder=""/>
            <i class="fa fa-eye show-password"></i>
          </div>
    <span class="help-block"><?php echo $password_err; ?></span>
    </div>
          <a href="#" class="forgot">Forgot password?</a>
          <button type="submit" class="button" value="Submit">Log in</button>
        </form>
        <center><p>Don't have an account? <a href="signup.php" class="signup-already-link">Sign up here </a></p></center>
    <script>
        window.addEventListener("load", function () {
const loginForm = document.querySelector(".login-form");
const showPasswordIcon =
loginForm && loginForm.querySelector(".show-password");
const inputPassword =
loginForm && loginForm.querySelector('input[type="password"');
showPasswordIcon.addEventListener("click", function () {
const inputPasswordType = inputPassword.getAttribute("type");
inputPasswordType === "password"
? inputPassword.setAttribute("type", "text")
: inputPassword.setAttribute("type", "password");
});
});
    </script>
</body>
</html>

<?php
include "generalConnection.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'], $_POST['password'], $_POST['name'], $_POST['re_password'])) {
        function validate($data) {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        $name = validate($_POST['name']);
        $email = validate($_POST['email']);
        $pass = validate($_POST['password']);
        $re_pass = validate($_POST['re_password']);
        $register_as_doctor = isset($_POST['register_doctor']) ? 1 : 0;

        $user_data = "email=$email&name=$name";

        if (empty($email) || empty($pass) || empty($re_pass) || empty($name)) {
            header("Location: signup.php?error=All fields are required&$user_data");
            exit();
        }

        if ($pass !== $re_pass) {
            header("Location: signup.php?error=Passwords do not match&$user_data");
            exit();
        }

        // Check if email is already in use
        $sql = "SELECT * FROM userInfo WHERE userEmail=?";
        if ($stmt = mysqli_prepare($data, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                header("Location: signup.php?error=Email is already taken&$user_data");
                exit();
            }
        } else {
            echo "Error: " . mysqli_error($data); // Error handling for DB query
            exit();
        }

        // Hash the password
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        // Handle doctor registration
        if ($register_as_doctor) {
            // Insert into pendingUsers for doctors
            $sql2 = "INSERT INTO pendingUsers (name, userEmail, userPassword, userType) VALUES (?, ?, ?, 'doctor')";
            if ($stmt2 = mysqli_prepare($data, $sql2)) {
                mysqli_stmt_bind_param($stmt2, "sss", $name, $email, $hashed_pass);
                if (mysqli_stmt_execute($stmt2)) {
                    // Success message for doctor registration
                     header("Location: signup.php?success=Your request has been sent to admin for approval.&$user_data");
                    
                } else {
                    echo "Error: " . mysqli_error($data);
                    exit();
                }
            }
        } else {
            // Insert directly into userInfo for regular users
            $sql2 = "INSERT INTO userInfo (name, userEmail, userPassword, userType) VALUES (?, ?, ?, 'user')";
            if ($stmt2 = mysqli_prepare($data, $sql2)) {
                mysqli_stmt_bind_param($stmt2, "sss", $name, $email, $hashed_pass);
                if (mysqli_stmt_execute($stmt2)) {
                    header("Location: signup.php?status=success&message=Your account has been created successfully.");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($data);
                    exit();
                }
            }
        }
    } else {
        header("Location: signup.php");
        exit();
    }
}
?>




<!DOCTYPE html>
<html>
<head>
	<title>SIGN UP</title>
    <style>   
        .form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 350px;
        background-color: #fff;
        padding: 20px;
        border-radius: 20px;
        position: relative;
        padding-left:55px;
        }

        .title {
        font-size: 28px;
        color: royalblue;
        font-weight: 600;
        letter-spacing: -1px;
        position: relative;
        display: flex;
        align-items: center;
        padding-left: 30px;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .error {
        color: red;
        text-align: center;
        margin-top: 10px;
    }

        .title::before,.title::after {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        border-radius: 50%;
        left: 0px;
        background-color: royalblue;
        }

        .title::before {
        width: 18px;
        height: 18px;
        background-color: royalblue;
        }

        .title::after {
        width: 18px;
        height: 18px;
        animation: pulse 1s linear infinite;
        }

        .message, .signin {
        color: rgba(88, 87, 87, 0.822);
        font-size: 14px;
        }

        .signin {
        text-align: center;
        }

        .signin a {
        color: royalblue;
        }

        .signin a:hover {
        text-decoration: underline royalblue;
        }

        .flex {
        display: flex;
        width: 100%;
        gap: 6px;
        }

        .form label {
        position: relative;
        }

        .form label .input {
        width: 100%;
        padding: 10px 10px 20px 10px;
        outline: 0;
        border: 1px solid rgba(105, 105, 105, 0.397);
        border-radius: 10px;
        }

        .form label .input + span {
        position: absolute;
        left: 10px;
        top: 15px;
        color: grey;
        font-size: 0.9em;
        cursor: text;
        transition: 0.3s ease;
        }

        .form label .input:placeholder-shown + span {
        top: 15px;
        font-size: 0.9em;
        }

        .form label .input:focus + span,.form label .input:valid + span {
        top: 30px;
        font-size: 0.7em;
        font-weight: 600;
        }

        .form label .input:valid + span {
        color: green;
        }

        .submit {
        border: none;
        outline: none;
        background-color: royalblue;
        padding: 10px;
        border-radius: 10px;
        color: #fff;
        font-size: 16px;
        transform: .3s ease;
        }

        .submit:hover {
        background-color: rgb(56, 90, 194);
        }
        .card{
        
            width:500px;
            height:70%;
            
        }
        .register_doctor{
            margin-top:40px;
            color:#fff;
        }
        @keyframes pulse {
            from {
                transform: scale(0.9);
                opacity: 1;
            }

            to {
                transform: scale(1.8);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="card" style ="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);">
        <form action="#" method="POST" class="form" id="signup-form">
            <h2 class="title">SIGN UP</h2>
            <?php if (isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            
            <label for="name">
                <input type="text" name="name" id="name" class="input" required placeholder=" ">
                <span>Name</span>
            </label>
            
            <label for="email">
                <input type="email" name="email" id="email" class="input" required placeholder=" ">
                <span>Email</span>
            </label>
            
            <label for="password">
                <input type="password" name="password" id="password" class="input" required placeholder=" ">
                <span>Password</span>
            </label>
            
            <label for="re_password">
                <input type="password" name="re_password" id="re_password" class="input" required placeholder=" ">
                <span>Re-enter your password</span>
            </label>

            <label for="register_doctor"style ="color: grey; padding-top:5px; font-size:12px;">
                <input type="checkbox" name="register_doctor" id="register_doctor" class=""  placeholder=" ">
                <span>Press me if you register as doctor</span>
            </label>
            
            <button type="submit" class="submit">Sign Up</button>
            <p class="signin">Have account? <a href="signin.php">Click here</a></p>
        </form>
    </div>
</body>
<script>
    // Wait until the DOM is fully loaded
window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    // If the status is 'success', display the modal
    if (status === 'success' && message) {
        const modal = document.getElementById('successModal');
        const modalMessage = document.getElementById('modalMessage');
        modalMessage.textContent = message; // Display the success message
        modal.style.display = "block"; // Show the modal

        // Get the close button
        const closeModal = document.getElementsByClassName('close')[0];

        // Close the modal when 'X' is clicked
        closeModal.onclick = function () {
            modal.style.display = "none";
        }

        // Close the modal when clicking anywhere outside the modal content
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    }
}

    </script>
</html>
<?php
include "generalConnection.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($data, $_POST["email"]);
    $password = mysqli_real_escape_string($data, $_POST["password"]);

    // Query to fetch user data based on email
    $sql = "SELECT * FROM userInfo WHERE userEmail = '$email'";
    $result = mysqli_query($data, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verify the entered password against the hashed password
        if (password_verify($password, $row['userPassword'])) {
            $_SESSION['user_id'] = $row['userID']; // Store user ID in session

            // Redirect based on user type
            if ($row["userType"] == "admin") {
                header("Location: adminTheraphistCRUD.php");
            } else {
                header("Location: userhome.php?user_id=" . $row['userID']);
            }
            exit(); // Always use exit after header redirection
        } else {
            $error_message = 'Incorrect email or password!'; // Invalid password
        }
    } else {
        $error_message = 'Incorrect email or password!'; // Invalid email
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form {
            
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 500px;
            background-color: #fff;
            padding: 20px;
            position: relative;
        }
        .form label .input {
            width: 100%;
            padding: 10px 10px 20px 10px;
            outline: 0;
            border: 1px solid rgba(105, 105, 105, 0.397);
            border-radius: 10px;
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

        .title::before,
        .title::after {
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
        }

        .title::after {
            width: 18px;
            height: 18px;
            animation: pulse 1s linear infinite;
        }

        .message,
        .signin {
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

        .form label {
            position: relative;
        }

        .form label .input {
            width: 90%;
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

        .form label .input:focus + span,
        .form label .input:valid + span {
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
            width:90%;
            background-color: royalblue;
            padding: 10px;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transform: .3s ease;
            cursor: pointer;
        }

        .submit:hover {
            background-color: rgb(56, 90, 194);
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
        <form class="form" action="#" method="POST">
                <p class="title">SIGN IN</p>
                <?php if (isset($error_message)) { ?>
                    <p style="color:red;"><?php echo $error_message; ?></p>
                <?php } ?>
                <label>
                    <input required placeholder=" " type="text" name="email" class="input">
                    <span>Email</span>
                </label>
                <label>
                    <input required placeholder=" " type="password" name="password" class="input">
                    <span>Password</span>
                </label>
                <button type="submit" class="submit">Sign In</button>
                <p class="signin">No account? <a href="signup.php">Click here</a></p>
        </form>
    </div>
</body>
</html>
<?php
include 'generalConnection.php';
session_start();
$user_id = $_SESSION['user_id'];
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($user_id)) {
    header('location:signin.php');
    exit; // Ensure no further code is executed after the redirect
}

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:signin.php');
    exit; // Ensure no further code is executed after the redirect
}

function calculate_age($birthdate) {
    $birthDate = new DateTime($birthdate);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;
    return $age;
}

$update = false; // Initialize the update variable
if (isset($_POST['update_profile'])) {
    // Existing profile fields
    $name = mysqli_real_escape_string($data, $_POST['name']);
    $birthdate = mysqli_real_escape_string($data, $_POST['birthdate']);
    $email = mysqli_real_escape_string($data, $_POST['email']);
    $phone = mysqli_real_escape_string($data, $_POST['phone']);
    $notification = mysqli_real_escape_string($data, $_POST['notification']);
    $gender = mysqli_real_escape_string($data, $_POST['gender']);
    $specialization = mysqli_real_escape_string($data, $_POST['specialization']);

    // Calculate age
    $age = calculate_age($birthdate);

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file extension and size
        if (in_array(strtolower($file_ext), $allowed_ext) && $file_size <= 5000000) { // 5MB limit
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = 'uploaded_img/';
            $upload_file = $upload_dir . $new_file_name;

            // Move uploaded file
            if (move_uploaded_file($file_tmp, $upload_file)) {
                // Update profile with the new profile picture
                $profile_pic = $new_file_name;
                $update = mysqli_query($data, "UPDATE `userInfo` SET name = '$name', userAge = '$age', UserBirthdate = '$birthdate', userEmail = '$email', userPhoneNumber = '$phone', userNotificationPreference = '$notification', userGender = '$gender', specialized = '$specialization', userProfilePicture = '$profile_pic' WHERE userID = '$user_id'") or die('Query failed');
            } else {
                echo "Error uploading file!";
            }
        } else {
            echo "Invalid file format or size exceeds 5MB.";
        }
    } else {
        // If no new file is uploaded, update the profile without changing the picture
        $update = mysqli_query($data, "UPDATE `userInfo` SET name = '$name', userAge = '$age', UserBirthdate = '$birthdate', userEmail = '$email', userPhoneNumber = '$phone', userNotificationPreference = '$notification', userGender = '$gender', specialized = '$specialization' WHERE userID = '$user_id'") or die('Query failed');
    }

    // Check if update was successful
    if ($update) {
        echo "Profile updated successfully!";
        header('Location: userhome.php'); // Redirect after success
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($data);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #bcc1b3;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .profile {
            text-align: center;
        }
        .profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile h3 {
            margin: 10px 0;
        }
        .profile p {
            font-size: 16px;
            margin: 5px 0;
        }
        .history-button {
            padding: 10px 20px; /* Adjust padding for size */
            background-color: white; /* Example background color */
            color: black; /* Text color */
            border: 1px solid black; /* Remove default border */
            border-radius: 20px; /* Rounded sides */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 13px; /* Adjust font size */
            transition: background-color 0.3s ease; /* Smooth hover effect */
        }

        .history-button:hover {
            background-color: #bcc1b3; /* Darker color on hover */
        }

        .profile-table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .profile-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }

        .profile-table td:last-child {
            text-align: left;
            color: #333;
        }

        .profile-table tr:last-child td {
            border-bottom: none;
        }

        .btn, .delete-btn {
            display: inline-block;
            margin: 10px 10px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover, .delete-btn:hover {
            background-color: #0056b3;
        }

        .row > .col-6 {
            padding: 10px; /* Ensure spacing between the columns */
        }

        .profile-info {
            text-align: left;
        }

        .profile-info p {
            margin-bottom: 10px; /* Adjust spacing between info items */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile">
        
        <?php
            // Fetch user data from the userInfo table
            $select = mysqli_query($data, "SELECT * FROM `userInfo` WHERE userID = '$user_id'") or die('Query failed');
            if (mysqli_num_rows($select) > 0) {
                $fetch = mysqli_fetch_assoc($select);
            }

            // Display user profile picture
            $profilePic = $fetch['userProfilePicture'];
            if (empty($profilePic) || !file_exists("uploaded_img/$profilePic")) {
                echo '<img src="images/default-avatar.png" alt="Default Avatar">';
            } else {
                echo '<img src="uploaded_img/' . htmlspecialchars($profilePic) . '" alt="Profile Image">';
            }
            if (!empty($fetch['name'])) {
                echo '<p><b>' . htmlspecialchars($fetch['name']) . '</b></p>';
            } 

            // Check if userType is doctor to display specialization
            if ($fetch['userType'] == 'doctor') {
                echo '<p><strong>Specialization:</strong> ' . htmlspecialchars($fetch['specialized']) . '</p>';
            }
        ?>
    </div>

    <br>
    <br>
    <a href="user_assessment.php?user_id=<?php echo $user_id; ?>">here</a>

    <table class="profile-table">
    <tr>
    <td><strong>Age:</strong></td>
    <td><?php echo htmlspecialchars($fetch['userAge'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
</tr>

<tr>
    <td><strong>Email:</strong></td>
    <td><?php echo htmlspecialchars($fetch['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<tr>
    <td><strong>Phone Number:</strong></td>
    <td><?php echo htmlspecialchars($fetch['userPhoneNumber'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<tr>
    <td><strong>Notification Preference:</strong></td>
    <td><?php echo htmlspecialchars($fetch['userNotificationPreference'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<tr>
    <td><strong>Gender:</strong></td>
    <td><?php echo htmlspecialchars($fetch['userGender'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
</tr>

    <tr>
            <td>Up Coming Session</td>
            <td></td>
            
        </tr>
    <tr>
            
            <td>Preliminary Test History</td>
            <td><a href="history.php?user_id=<?php echo $user_id; ?>" class="history-button">View History</a></td>
  
        </tr>
    </table>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4>Edit Profile</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="name">Name:</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($fetch['name']); ?>" required>

                    <label for="birthdate">Birthdate:</label>
                    <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($fetch['UserBirthdate']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($fetch['userEmail']); ?>" required>

                    <label for="phone">Phone Number:</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($fetch['userPhoneNumber']); ?>">

                    <label for="notification">Notification Preference:</label>
                    <input type="text" name="notification" class="form-control" value="<?php echo htmlspecialchars($fetch['userNotificationPreference']); ?>">

                    <label for="gender">Gender:</label>
                    <input type="text" name="gender" class="form-control" value="<?php echo htmlspecialchars($fetch['userGender']); ?>">

                    <?php if ($fetch['userType'] == 'doctor'): ?>
                        <label for="specialization">Specialization:</label>
                        <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($fetch['specialized']); ?>">
                    <?php endif; ?>

                    <label for="profile_picture">Profile Picture:</label>
                    <input type="file" name="profile_picture" class="form-control" accept="image/*">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_profile" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
</div>
<div class="d-flex justify-content-between">
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Update Profile</button>
        <a href="?logout=true" class="delete-btn btn-warning">Logout</a>
    </div>
</body>
</html>

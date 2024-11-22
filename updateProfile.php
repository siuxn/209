<?php
include 'generalConnection.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:signin.php');
    exit; // Ensure no further code is executed after the redirect
}

// Fetch current user data from the database
$select = mysqli_query($data, "SELECT * FROM `userInfo` WHERE userID = '$user_id'") or die('query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}

// Function to calculate age based on birthdate
function calculate_age($birthdate) {
    $birthdate = new DateTime($birthdate); 
    $today = new DateTime();
    $interval = $today->diff($birthdate);
    return $interval->y; // Return age in years
}

// Update profile data and picture
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($data, $_POST['name']);
    $birthdate = mysqli_real_escape_string($data, $_POST['birthdate']);
    $email = mysqli_real_escape_string($data, $_POST['email']);
    $phone = mysqli_real_escape_string($data, $_POST['phone']);
    $notification = mysqli_real_escape_string($data, $_POST['notification']);
    $gender = mysqli_real_escape_string($data, $_POST['gender']);
    $specialization = mysqli_real_escape_string($data, $_POST['specialization']);

    // Calculate age based on birthdate
    $age = calculate_age($birthdate);

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file extension and size (optional)
        if (in_array(strtolower($file_ext), $allowed_ext) && $file_size <= 5000000) { // max 5MB
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = 'uploaded_img/';
            $upload_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_file)) {
                // Update the profile picture in the database
                $profile_pic = $new_file_name;
                $update = mysqli_query($data, "UPDATE `userInfo` SET name = '$name', userAge = '$age', UserBirthdate = '$birthdate', userEmail = '$email', userPhoneNumber = '$phone', userNotificationPreference = '$notification', userGender = '$gender', specialized = '$specialization', userProfilePicture = '$profile_pic' WHERE userID = '$user_id'") or die('query failed');
            } else {
                echo "Error uploading file!";
            }
        } else {
            echo "Invalid file format or size exceeds 5MB.";
        }
    } else {
        // If no new image is uploaded, just update the profile data
        $update = mysqli_query($data, "UPDATE `userInfo` SET name = '$name', userAge = '$age', UserBirthdate = '$birthdate', userEmail = '$email', userPhoneNumber = '$phone', userNotificationPreference = '$notification', userGender = '$gender', specialized = '$specialization' WHERE userID = '$user_id'") or die('query failed');
    }
    
    if ($update) {
        echo "Profile updated successfully!";
        header('Location: userhome.php'); // Redirect to the profile page after successful update
        exit();
    } else {
        echo "Error updating profile!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        
        </style>
    <!-- Add custom CSS link here -->
</head>
<body>
   
<div class="container">
    <div class="profile-edit-form">
        <h3>Edit Profile</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($fetch['name']); ?>" required>

            <!-- Remove Age Input Field, it will be auto-calculated from Birthdate -->

            <label for="birthdate">Birthdate:</label>
            <input type="date" name="birthdate" value="<?php echo htmlspecialchars($fetch['UserBirthdate']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($fetch['userEmail']); ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($fetch['userPhoneNumber']); ?>">

            <label for="notification">Notification Preference:</label>
            <input type="text" name="notification" value="<?php echo htmlspecialchars($fetch['userNotificationPreference']); ?>">

            <label for="gender">Gender:</label>
            <input type="text" name="gender" value="<?php echo htmlspecialchars($fetch['userGender']); ?>">

            <label for="specialization">Specialization:</label>
            <input type="text" name="specialization" value="<?php echo htmlspecialchars($fetch['specialized']); ?>">

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" accept="image/*">
            
            <input type="submit" name="update_profile" value="Update Profile">
        </form>
        <a href="userhome.php" class="btn">Cancel</a>
    </div>
</div>

</body>
</html>

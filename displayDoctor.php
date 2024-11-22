<?php

@include 'generalConnection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Therapist Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    
    <style>
        body{
            background-color:#bcc1b3 ;
        }
        /* Custom styles for card hover effect */
        .card {
            position: relative; /* For positioning the button */
            overflow: hidden; /* To ensure the button is contained within the card */
        }

        .card-img-top {
            transition: opacity 0.3s ease; /* Smooth transition for hover effect */
        }

        .card:hover .card-img-top {
            opacity: 0.5; /* Darken image on hover */
        }

        .view-profile-btn {
            position: absolute; /* Positioning the button */
            top: 50%; /* Center vertically */
            left: 50%; /* Center horizontally */
            transform: translate(-50%, -50%); /* Adjust for perfect centering */
            display: none; /* Hide by default */
            z-index: 1; /* Ensure the button is above the image */
        }

        .card:hover .view-profile-btn {
            display: block; /* Show button on hover */
        }
    </style>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <?php
        // Query to select all necessary fields from userInfo where userType is 'doctor'
        $select = mysqli_query($data, "SELECT userID, name, userEmail, userPhoneNumber, userGender, userProfilePicture, userAge, UserBirthdate,specialized FROM userInfo WHERE userType = 'doctor' ORDER BY userID ASC");

        // Check if any records were returned
        if (mysqli_num_rows($select) > 0) {
            while ($row = mysqli_fetch_assoc($select)) { ?>
                <div class="col-md-3 mb-3">
                    <div class="card" style="width: 18rem;">
                        <img class="card-img-top" style="width:18rem; height:18rem;" src="uploaded_img/<?php echo $row['userProfilePicture']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>'s Profile Picture">
                        <div class="card-body">
                            <h5 class="card-title" style="color:#595e4d;"><b>DR. <?php echo htmlspecialchars($row['name']); ?></b></h5>
                            <p class="card-text" style="color:grey;">Specialist: <?php echo htmlspecialchars($row['specialized']); ?></p>
                        </div>
                        <div class="card-body">
                            <a href="mailto:<?php echo htmlspecialchars($row['userEmail']); ?>" class="card-link">Email User</a>
                        </div>
                        <a href="therapistProfile.php?userID=<?php echo $row['userID']; ?>" class="btn btn-primary view-profile-btn">View Profile</a>
                    </div>
                </div>
            <?php }
        } else {
            // If no doctors are found
            echo '<div class="col-12"><p class="text-center">No doctors found.</p></div>';
        }
        ?>
    </div>
</div>
</body>
</html>

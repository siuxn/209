<?php
@include 'generalConnection.php';

// Check if the userID parameter exists in the URL
if (isset($_GET['userID'])) {
    $userID = intval($_GET['userID']);

    // Query to fetch the therapist's details using the userID
    $query = mysqli_query($data, "SELECT userID, name, userEmail, userPhoneNumber, userGender, userProfilePicture, userAge, UserBirthdate, specialized FROM userInfo WHERE userID = $userID AND userType = 'doctor'");

    // Check if a record was found
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
    } else {
        echo "No therapist found with this ID.";
        exit;
    }
} else {
    echo "No therapist ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body{
            margin-top:20px;
            color: #1a202c;
            text-align: left;
            background-color: #bcc1b3;    
        }
        .main-body {
            padding: 15px;
        }
        .card {
            box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px 0 rgba(0,0,0,.06);
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0,0,0,.125);
            border-radius: .25rem;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1rem;
        }

        .gutters-sm {
            margin-right: -8px;
            margin-left: -8px;
        }

        .gutters-sm>.col, .gutters-sm>[class*=col-] {
            padding-right: 8px;
            padding-left: 8px;
        }
        .mb-3, .my-3 {
            margin-bottom: 1rem!important;
        }

        .bg-gray-300 {
            background-color: #e2e8f0;
        }
        .h-100 {
            height: 100%!important;
        }
        .shadow-none {
            box-shadow: none!important;
        }
        .nav-tabs {
    display: flex;
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    margin-bottom: 1rem;
}

.nav-tabs .nav-link {
    flex: 1;
    text-align: center;
    color: #1a202c;
    padding: 0.75rem 1rem;
    border: 1px solid transparent;
    border-radius: 0;
    transition: background-color 0.3s ease;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #e2e8f0;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    background-color: #e2e8f0;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    background-color: #fff;
}

        </style>
</head>
<body>
<div class="main-body">
    
<div class="container mt-4">
        <div class="card mb-3">
            <div class="row no-gutters">
                <!-- Profile Picture on the left -->
                <div class="col-md-4 text-center">
                    <br>
                <a href="displayDoctor.php" style="color:grey; margin-left:-75%;margin-top:10px;">
                    <i class="fas fa-door-open"></i> <i class="fas fa-arrow-left"></i> 
                </a>
                <br>
                <br>
                <img src="uploaded_img/<?php echo htmlspecialchars($row['userProfilePicture']); ?>" alt="Profile Picture" class="rounded-circle mt-3" style="width: 150px; height: 150px;">
                <br>
                <br>
                <h3 class="card-title"  style ="color:darkgrey; font-size:12px;">Introduction</h3>
                <h3 class="card-title" style ="color:darkgrey; font-size:12px;">DR. <?php echo htmlspecialchars($row['name']); ?></h3>
                <br>
                <br>
            
                </div>
                <!-- Profile details on the right -->
                 <div class ="col-md-1">

                 </div>


                <div class="col-md-7">
                    <div class="card-body">
                        <h3 class="card-title"  style ="color:#595e4d;">DR. <?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="card-text" style ="color:darkgrey;">Specialized in: <?php echo htmlspecialchars($row['specialized']); ?></p>
                        <br><br>
                      
                        <p class="card-text" style ="color:darkgrey;">Ratings <br> <?php echo htmlspecialchars($row['specialized']);?>/10 </p>
                        <a href="#" class="btn btn-secondary">Book Session</a>
                        <br> <br>
                        <div class="nav nav-tabs d-flex justify-content-around" id="therapistTab" role="tablist">
                        <a class="nav-link active" id="rating-tab" data-toggle="tab" href="#rating" role="tab" aria-controls="rating" aria-selected="true">Rating</a>
                        <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="false">About Therapist</a>
                    </div>
                    <div class="tab-content" id="therapistTabContent">
                        <div class="tab-pane fade show active p-3" id="rating" role="tabpanel" aria-labelledby="rating-tab">
                            <h5>Rating</h5>
                            <p><?php echo htmlspecialchars($row['specialized']); ?>/10</p>
                        </div>
                        <div class="tab-pane fade p-3" id="about" role="tabpanel" aria-labelledby="about-tab">
                            <h5>About Therapist</h5>
                            <p class="card-text"><b>Gender:</b> <?php echo htmlspecialchars($row['userGender']); ?></p>
                            <p class="card-text"><b>Age:</b> <?php echo htmlspecialchars($row['userAge']); ?></p>
                            <p class="card-text"><b>Birthdate:</b> <?php echo htmlspecialchars($row['UserBirthdate']); ?></p>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer text-left">
               
                        <a href="mailto:<?php echo htmlspecialchars($row['userEmail']); ?>" class="btn btn-secondary">Email</a>
                    </div>
                </div>
                <div class="card mt-3">
            
        </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

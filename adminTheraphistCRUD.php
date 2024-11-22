<?php

@include 'generalConnection.php';

if (isset($_POST['addUser'])) {
    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $phoneNum = $_POST['phoneNumber'];
    $userEmail = $_POST['userEmail'];
    $gender = $_POST['gender'];
    $userProfilePicture = $_FILES['userProfilePicture']['name'];
    $userProfilePicture_tmp_name = $_FILES['userProfilePicture']['tmp_name'];
    $userProfilePicture_folder = 'uploaded_img/' . $userProfilePicture;
    $specialized = mysqli_real_escape_string($data, implode(", ", $_POST['specialties']));


    // Check required fields
    if (empty($userName) || empty($userEmail) || empty($userProfilePicture)) {
        $message[] = 'Please fill out all required fields';
    } else {
        // Insert query with userType set as "doctor"
        $insert = "INSERT INTO userInfo (name, userType, userEmail, userPassword, userPhoneNumber, userGender, userProfilePicture, specialized) 
                   VALUES ('$userName', 'doctor', '$userEmail', '$password', '$phoneNum', '$gender', '$userProfilePicture', '$specialized')";
        $upload = mysqli_query($data, $insert);
        
        if ($upload) {
            move_uploaded_file($userProfilePicture_tmp_name, $userProfilePicture_folder);
            $message[] = 'New user added successfully';
        } else {
            $message[] = 'Could not add the user: ' . mysqli_error($data);
        }
        
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($data, "DELETE FROM userInfo WHERE userID = $id");
    header('location:adminTheraphistCRUD.php');
    exit();
}
if (isset($_POST['updateUser'])) {
    $id = $_POST['userID'];
    $userName = $_POST['name'];
    $userEmail = $_POST['email'];
    $phoneNum = $_POST['phoneNumber'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $specialized = isset($_POST['specialty']) ? $_POST['specialty'] : NULL;

    // Handle profile picture upload
    if (isset($_FILES['editProfile']['name']) && $_FILES['editProfile']['name'] != '') {
        $newProfilePicture = $_FILES['editProfile']['name'];
        $newProfilePicture_tmp_name = $_FILES['editProfile']['tmp_name'];
        $newProfilePicture_folder = 'uploaded_img/' . $newProfilePicture;

        // Fetch the old picture
        $oldPictureQuery = "SELECT userProfilePicture FROM userInfo WHERE userID = '$id'";
        $result = mysqli_query($data, $oldPictureQuery);
        $row = mysqli_fetch_assoc($result);
        $oldPicture = $row['userProfilePicture'];

        // Upload the new picture and remove the old one
        if (move_uploaded_file($newProfilePicture_tmp_name, $newProfilePicture_folder)) {
            if ($oldPicture && file_exists('uploaded_img/' . $oldPicture)) {
                unlink('uploaded_img/' . $oldPicture);
            }
        }
    } else {
        // Keep the old picture if no new file is uploaded
        $newProfilePicture = $row['userProfilePicture'];
    }

    // Update user data
    $update_data = "UPDATE userInfo SET 
                    name='$userName', 
                    userEmail='$userEmail', 
                    userPhoneNumber='$phoneNum', 
                    userGender='$gender', 
                    userPassword='$password', 
                    specialized='$specialized', 
                    userProfilePicture='$newProfilePicture'
                    WHERE userID = '$id'";
    $upload = mysqli_query($data, $update_data);

    if ($upload) {
        header('Location: adminTheraphistCRUD.php'); // Redirect after success
        exit();
    } else {
        echo "Error: " . mysqli_error($data);
        exit();
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>

    <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- jQuery -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Bootstrap JS -->



</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<span class="message">' . $msg . '</span>';
    }
}
?>

<div class="container">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Admin Page</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link disabled" href="#">Therapist Page</a>
        </li>
        <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Preliminary
  </a>
  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <!-- Ensure the correct file extension for the 'href' attribute -->
    <a class="dropdown-item" href="preliminarydisplay.php">View Preliminary Test</a>
    <a class="dropdown-item " href="displayt.php">View Score Range</a>

  </div>
</li>


        <li class="nav-item">
        <a class="nav-link " href="blog.php">Blog Upload</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="pending.php">Pending Account</a>
        </li>
    </ul>
  </div>
</nav>
<br>
    <div class="product-display">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
    Add New User
</button>
<!-- Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Therapist Name</label>
                        <input type="text" class="form-control" name="userName" id="userName" placeholder="Albert" required>
                    </div>
           
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="userEmail" id="userEmail" placeholder="AlbertEinstein@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="AlbertEinstein123" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="number" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="0123456789" required>
                    </div>
                    <div class="form-group mb-3">
                            <label>Gender:</label>
                            <input type="radio" class="form-check-input" name="gender" id="editMale" value="male" required>
                            <label for="editMale" class="form-input-label">Male</label>
                            <input type="radio" class="form-check-input" name="gender" id="editFemale" value="female" required>
                            <label for="editFemale" class="form-input-label">Female</label>
                        </div>
                        <div class="mb-3">
                            <label for="userProfilePicture" class="form-label">Therapist Profile Picture</label>
                            <input type="file" accept="image/png, image/jpeg, image/jpg" name="userProfilePicture" class="form-control" required>
                        </div>
                        
                        
                        <label for="speciality">Specialties <span style="color: red;">*</span></label>
                            <div id="specialties-container">
                            <input type="text" name="specialties[]" class="form-control" placeholder="Specialty">
                            </div>
                            <br>
                            <button type="button" onclick="addSpecialty()">Add Another Specialty</button>
                            <br><br>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" name="addUser">Save</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> <!-- Bootstrap 5 -->
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="editUserForm" method="post" enctype="multipart/form-data">
    <input type="hidden" name="userID" id="userID">
    <div class="mb-3">
        <label for="editName" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" id="editName" >
    </div>
    <div class="mb-3">
        <label for="editEmail" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" id="editEmail" >
    </div>
    <div class="mb-3">
        <label for="editPhone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="phoneNumber" id="editPhone" >
    </div>
    <div class="mb-3">
        <label for="editPassword" class="form-label">Password (leave empty to keep current)</label>
        <input type="password" class="form-control" name="password" id="editPassword">
    </div>
    <div class="form-group mb-3">
        <label>Gender:</label>
        <input type="radio" class="form-check-input" name="gender" id="editMale" value="male" >
        <label for="editMale" class="form-input-label">Male</label>
        <input type="radio" class="form-check-input" name="gender" id="editFemale" value="female" >
        <label for="editFemale" class="form-input-label">Female</label>
    </div>
    <div class="mb-3">
        <label for="editProfile" class="form-label">Update Profile Picture</label>
        <input type="file" accept="image/png, image/jpeg, image/jpg" name="editProfile" class="form-control">
    </div>
    <div class="mb-3">
        <label for="editSpecialty" class="form-label">Specialty</label>
        <input type="text" class="form-control" name="specialty" id="editSpecialty" placeholder="Enter specialty">
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success" name="updateUser">Update</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>


            </div>
        </div>
        
    </div>


</div>

        
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Profile Picture</th>
                    <th>Specialty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select = mysqli_query($data, "SELECT * FROM userInfo");
                while ($row = mysqli_fetch_assoc($select)) { ?>
                <tr>
                     <td><?php echo $row['name']; ?></td>
                     <td><?php echo $row['userEmail']; ?></td>
                     <td>
    <?php 
        $password = $row['userPassword'];
        if (strlen($password) > 15) {
            $password_display = substr($password, 0, 15) . '...';
            echo $password_display . ' <a href="#" class="see-more" data-password="' . htmlspecialchars($password) . '">See more</a>';
        } else {
            echo htmlspecialchars($password);
        }
    ?>
</td>


                     <td><?php echo $row['userGender']; ?></td>
                     <td><?php echo $row['userPhoneNumber']; ?></td>
                    <td><img src="uploaded_img/<?php echo $row['userProfilePicture']; ?>" width ="100"height="100" alt=""></td>
                    <td><?php echo $row['specialized']; ?></td>
                    <td><button class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editUserModal" 
                        data-id="<?php echo $row['userID']; ?>"  
                        data-name="<?php echo htmlspecialchars($row['name']); ?>"  
                        data-email="<?php echo htmlspecialchars($row['userEmail']); ?>"  
                        data-phone="<?php echo htmlspecialchars($row['userPhoneNumber']); ?>" 
                        data-gender="<?php echo htmlspecialchars($row['userGender']); ?>"
                        data-photo="<?php echo isset($row['userProfilePicture']) ? htmlspecialchars($row['userProfilePicture']) : ''; ?>"
                        data-specialized="<?php echo htmlspecialchars($row['specialized']); ?>"
                        data-password="<?php echo htmlspecialchars($row['userPassword']); ?>"> 
                        Edit
                    </button>
                    <a href="adminTheraphistCRUD.php?delete=<?php echo $row['userID']; ?>" class="btn btn-danger" title="Delete User">
                Delete
             </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href ="logout.php">
            <i class="fa fa-sign-out"  style="font-size:30px; color:red; padding-bottom:20px;"></i>
        </a>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const editUserModal = document.getElementById('editUserModal');
editUserModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const userID = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const email = button.getAttribute('data-email');
    const password = button.getAttribute('data-password');
    const gender = button.getAttribute('data-gender');
    const phoneNumber = button.getAttribute('data-phone');
    const special = button.getAttribute('data-specialized');

    const modalUserID = editUserModal.querySelector('#userID');
    const modalName = editUserModal.querySelector('#editName');
    const modalEmail = editUserModal.querySelector('#editEmail');
    const modalPassword = editUserModal.querySelector('#editPassword');
    const modalGenderMale = editUserModal.querySelector('#editMale');
    const modalGenderFemale = editUserModal.querySelector('#editFemale');
    const photo = button.getAttribute('data-photo');
    const modalPhone = editUserModal.querySelector('#editPhone');
    const modalspecial = editUserModal.querySelector('#editSpecialty');

    modalUserID.value = userID;
    modalName.value = name;
    modalEmail.value = email;
    modalPassword.value = password; // Correct assignment
    modalGenderMale.checked = (gender === 'male');
    modalGenderFemale.checked = (gender === 'female');
    modalPhone.value = phoneNumber;
    
    modalspecial.value = special;
    const profilePreview = document.getElementById('profilePreview');
    if (profilePreview) {
        profilePreview.src = 'uploaded_img/' + (photo || 'default.jpg');
    }
});

        function addSpecialty() {
            const container = document.getElementById("specialties-container");

            // Create a new input element for another specialty
            const input = document.createElement("input");
            input.type = "text";
            input.name = "specialities[]"; // Allows multiple specialties as an array
            input.className = "form-control";
            input.placeholder = `Specialty ${container.children.length + 1}`;
            input.required = true;

            container.appendChild(input);
            }
</script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>

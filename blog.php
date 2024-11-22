<?php

@include 'generalConnection.php';
if (isset($_POST['addBlog'])) {
    // Retrieve form data
    $blogName = $_POST['blogName'];
    $blogCategory = $_POST['blogCategory'];
    $blogContent = $_POST['blogContent'];

    // Handle file upload (image)
    if (isset($_FILES['blogImage']) && $_FILES['blogImage']['error'] === UPLOAD_ERR_OK) {
        $blogImage = $_FILES['blogImage']['name'];
        $blogImageTmpName = $_FILES['blogImage']['tmp_name'];
        $blogImageFolder = 'blog/' . $blogImage;

        // Move the uploaded file to the designated folder
        if (move_uploaded_file($blogImageTmpName, $blogImageFolder)) {
            // If the file is uploaded successfully, insert the blog into the database
            $sql = "INSERT INTO blog (blogName, blogCategory, blogImage, blogContent) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($data, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssss", $blogName, $blogCategory, $blogImage, $blogContent);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: blog.php?msg=Data updated successfully");
                } else {
                    echo "Error executing query: " . mysqli_error($data);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Error preparing query: " . mysqli_error($data);
            }
        } else {
            header("Location: blog.php?msg=Error uploading file");
        }
    } else {
        echo "No image uploaded or error with file upload.";
    }
}

if (isset($_GET['delete'])) {
    $blogID = $_GET['delete'];

    // Sanitize input
    $blogID = mysqli_real_escape_string($data, $blogID);

    // Debug: Print the query before execution
    echo "DELETE FROM blog WHERE blogID = '$blogID'";

    // Delete query
    $deleteQuery = "DELETE FROM blog WHERE blogID = '$blogID'";

    if (mysqli_query($data, $deleteQuery)) {
        header("Location: blog.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($data);
    }
}

if (isset($_FILES['editImage']['name']) && $_FILES['editImage']['name'] != '') {
    // If a new image is uploaded
    $newImage = $_FILES['editImage']['name'];
    $newImage_tmp_name = $_FILES['editImage']['tmp_name'];
    $newImage_folder = 'blog/' . $newImage;

    // Fetch the old image from the database
    $oldImageQuery = "SELECT blogImage FROM blog WHERE blogID = '$id'";
    $result = mysqli_query($data, $oldImageQuery);
    $row = mysqli_fetch_assoc($result);
    $oldImage = $row['blogImage'];

    if (move_uploaded_file($newImage_tmp_name, $newImage_folder)) {
        // Delete the old image if it exists
        if ($oldImage && file_exists('blog/' . $oldImage)) {
            unlink('blog/' . $oldImage); // Delete the old image from the folder
        }
    }
} else {
    // If no new image is uploaded, use the old image
    $oldImageQuery = "SELECT blogImage FROM blog WHERE blogID = '$id'";
    $result = mysqli_query($data, $oldImageQuery);
    $row = mysqli_fetch_assoc($result);
    $newImage = $row['blogImage'];
}

// Now update the blog with the new image (or the existing image if no new one was uploaded)
$update_data = "UPDATE blog SET 
                blogName='$blogTitle', 
                blogCategory='$blogCategory', 
                blogContent='$blogContent', 
                blogImage='$newImage' 
                WHERE blogID = '$id'";

$upload = mysqli_query($data, $update_data);

if (isset($_POST['updateBlog'])) {
    $id = $_POST['blogID'];
    $blogCategory = $_POST['blogCategory'];
    $blogTitle = $_POST['blogTitle'];
    $blogContent = $_POST['blogContent'];

    if (isset($_FILES['editImage']['name']) && $_FILES['editImage']['name'] != '') {
        $newImage = $_FILES['editImage']['name'];
        $newImage_tmp_name = $_FILES['editImage']['tmp_name'];
        $newImage_folder = 'blog/' . $newImage;

        // Fetch the old image
        $oldImageQuery = "SELECT blogImage FROM blog WHERE blogID = '$id'";
        $result = mysqli_query($data, $oldImageQuery);
        $row = mysqli_fetch_assoc($result);
        $oldImage = $row['image'];

        if (move_uploaded_file($newImage_tmp_name, $newImage_folder)) {
            if ($oldImage && file_exists('blog/' . $oldImage)) {
                unlink('blog/' . $oldImage);
            }
        }
    } else {
        $newImage = $row['blogImage'];
    }

    $update_data = "UPDATE blog SET 
                    blogname='$blogTitle', 
                    blogCategory='$blogCategory', 
                    blogContent='$blogContent', 
                    blogImage='$newImage' 
                    WHERE blogID = '$id'";
    $upload = mysqli_query($data, $update_data);

    if ($upload) {
        header('Location: blog.php');
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


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
            <a class="nav-link" href="adminTheraphistCRUD.php">Therapist Page</a>
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
        <a class="nav-link disabled" href="#">Blog Upload</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="pending.php">Pending Account</a>
        </li>
    </ul>
  </div>
</nav>
<br>
    <div class="product-display">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
        Add New Blog
    </button>
<br>
<br>
<!-- Modal -->
<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1" aria-labelledby="addBlogModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBlogModalLabel">Add New Blog</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="blogName">Blog Name</label>
            <input type="text" class="form-control" id="blogName" name="blogName" required>
          </div>
          <div class="form-group">
            <label for="blogCategory">Blog Category</label>
            <input type="text" class="form-control" id="blogCategory" name="blogCategory" required>
          </div>
          <div class="form-group">
            <label for="blogImage">Blog Image</label>
            <input type="file" class="form-control" id="blogImage" name="blogImage">
          </div>
          <div class="form-group">
            <label for="blogContent">Blog Content</label>
            <textarea class="form-control" id="blogContent" name="blogContent" rows="4" required></textarea>
          </div>
          <button type="submit" name="addBlog" class="btn btn-primary mt-3">Add Blog</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Edit Blog Modal --><!-- Edit Blog Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBlogModalLabel">Edit Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBlogForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="blogID" id="blogID">
                    <div class="mb-3">
                        <label for="editBlogTitle" class="form-label">Blog Title</label>
                        <input type="text" class="form-control" name="blogTitle" id="editBlogTitle" >
                    </div>
                    <div class="mb-3">
                        <label for="editBlogCategory" class="form-label">Blog Category</label>
                        <input type="text" class="form-control" name="blogCategory" id="editBlogCategory" >
                    </div>
                    <div class="mb-3">
                        <label for="editBlogContent" class="form-label">Blog Content</label>
                        <textarea class="form-control" name="blogContent" id="editBlogContent" rows="4" ></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editBlogImage" class="form-label">Update Blog Image</label>
                        <input type="file" accept="image/png, image/jpeg, image/jpg" name="editImage" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Blog Image</label><br>
                        <img src="" id="editBlogImagePreview" width="100" alt="Current Image">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="updateBlog">Update</button>
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
            <th>Title</th>
            <th>Category</th>
            <th>Content</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
$select = mysqli_query($data, "SELECT * FROM blog");
while ($row = mysqli_fetch_assoc($select)) {
    // Split the content into an array of words
    $contentWords = explode(' ', $row['blogContent']);
    $isLong = count($contentWords) > 50;
    $shortContent = implode(' ', array_slice($contentWords, 0, 10));
    $fullContent = $row['blogContent'];
?>
    <tr>
        <td><?php echo $row['blogName']; ?></td>
        <td><?php echo $row['blogCategory']; ?></td>
        <td>
            <div style ="max-width:200px;"class="content-preview">
                <span class="short-content">
                    <?php echo htmlspecialchars($shortContent); ?>
                    <?php if ($isLong): ?>
                        ... <a href="#" class="see-more" data-id="<?php echo $row['blogID']; ?>">See more</a>
                    <?php endif; ?>
                </span>
                <span class="full-content" style="display: none;">
                    <?php echo htmlspecialchars($fullContent); ?> 
                    <a href="#" class="see-less" data-id="<?php echo $row['blogID']; ?>">See less</a>
                </span>
            </div>
        </td>
        <td><img src="blog/<?php echo $row['blogImage']; ?>" alt="" width="100"></td>
        <td>
    <a href="#" class="btn btn-primary edit-button" 
       data-id="<?php echo $row['blogID']; ?>"
       data-name="<?php echo $row['blogName']; ?>"
       data-category="<?php echo $row['blogCategory']; ?>"
       data-content="<?php echo $row['blogContent']; ?>"
       data-image="<?php echo $row['blogImage']; ?>" 
       data-bs-toggle="modal" data-bs-target="#editBlogModal">Edit</a>

    <a href="blog.php?delete=<?php echo $row['blogID']; ?>" class="btn btn-danger">Delete</a>
</td>

    </tr>
<?php
}
?>


        </tbody>
    </table>

</div>
<script>
$(document).on('click', '.edit-button', function () {
    var blogID = $(this).data('id');
    var blogName = $(this).data('name');
    var blogCategory = $(this).data('category');
    var blogContent = $(this).data('content');
    var blogImage = $(this).data('image');

    // Populate modal fields
    $('#editBlogModal #blogID').val(blogID);
    $('#editBlogModal #editBlogTitle').val(blogName);
    $('#editBlogModal #editBlogCategory').val(blogCategory);
    $('#editBlogModal #editBlogContent').val(blogContent);
    $('#editBlogModal #editBlogImagePreview').attr('src', 'blog/' + blogImage);

    // Show the modal
    $('#editBlogModal').modal('show');
});



$(document).on('click', '.toggle-content', function() {
    var blogID = $(this).data('id');
    var contentDiv = $('#blogContent_' + blogID);
    var truncated = contentDiv.find('.truncated');
    var fullContent = contentDiv.find('.full-content');
    var link = $(this);

    truncated.toggleClass('d-none');
    fullContent.toggleClass('d-none');

    if (fullContent.hasClass('d-none')) {
        link.text('See more');
    } else {
        link.text('See less');
    }
});

$(document).on('click', '.see-more', function(e) {
        e.preventDefault();
        var parentDiv = $(this).closest('.content-preview');
        parentDiv.find('.short-content').hide();
        parentDiv.find('.full-content').show();
    });

    $(document).on('click', '.see-less', function(e) {
        e.preventDefault();
        var parentDiv = $(this).closest('.content-preview');
        parentDiv.find('.full-content').hide();
        parentDiv.find('.short-content').show();
    });


</script>
<!-- Bootstrap JS and Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
<a href ="logout.php">
            <i class="fa fa-sign-out"  style="font-size:30px; color:red; padding-bottom:20px;"></i>
        </a>
</body>
</html>

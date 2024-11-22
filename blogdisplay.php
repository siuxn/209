<?php
@include 'generalConnection.php';

// Fetch the blog data from the database
$query = "SELECT * FROM blog";
$result = mysqli_query($data, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Blog Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            padding: 40px 0;
            font-size:16px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns per row */
            grid-gap: 20px;
            align-items: stretch;
        }

        .grid > article {
            border: 1px solid #ccc;
            box-shadow: 2px 2px 6px 0px rgba(0, 0, 0, 0.3);
        }

        .grid > article img {
            max-width: 100%;
        }

        .grid .text {
            padding: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px ;
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-block {
            width: 50%;
        }
        .container {
            margin-left: 10%;
            max-height: 690px; /* Set max-height to whatever is appropriate */
            overflow-y: auto; /* Allow scrolling when content overflows */
        }
    </style>
</head>
<body>

<?php
if ($result) {
?>
  <div class="container" style="margin-left:10%;">
    <main class="grid">
      <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $blogName = $row['blogName'];
            $blogCategory = $row['blogCategory'];
            $blogContent = $row['blogContent'];
            $blogImage = $row['blogImage'];

            // Truncate content to 15 words
            $words = explode(' ', $blogContent);
            if (count($words) > 15) {
                $truncatedContent = implode(' ', array_slice($words, 0, 15)) . '...';
            } else {
                $truncatedContent = $blogContent;
            }
      ?>
        <article style="max-width:80%;">
          <!-- Display Image -->
          <img src="blog/<?php echo htmlspecialchars($blogImage); ?>" alt="Blog Image">
          <div class="text">
            <!-- Display Blog Title -->
            <h3 style ="color:black; padding-top:-10px; font-size:20px;"><?php echo htmlspecialchars($blogName); ?></h3>
            <p style ="color:grey; padding-top:-10px; font-size:15px;"><?php echo htmlspecialchars($blogCategory); ?></p>
            <!-- Display truncated Blog Content -->
            <p id="blogContent_<?php echo $row['blogID']; ?>"><?php echo nl2br(htmlspecialchars($truncatedContent)); ?></p>
            <!-- Display See More Link -->
            <?php if (count($words) > 15) { ?>
                <a href="#" class="btn btn-link" data-id="<?php echo $row['blogID']; ?>" data-name="<?php echo htmlspecialchars($blogName); ?>" data-category="<?php echo htmlspecialchars($blogCategory); ?>" data-content="<?php echo htmlspecialchars($blogContent); ?>" data-image="<?php echo $blogImage; ?>" data-bs-toggle="modal" data-bs-target="#blogModal">See More</a>
            <?php } ?>
          </div>
        </article>
      <?php
        }
      ?>
    </main>
  </div>

  <!-- Modal for Viewing Full Blog Content -->
  <div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="blogModalLabel">Blog Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h3 id="modalBlogName"></h3>
          <p><strong>Category:</strong> <span id="modalBlogCategory"></span></p>
          <img id="modalBlogImage" src="" alt="Blog Image" style="width: 100%; max-height: 400px; object-fit: cover;">
          <p id="modalBlogContent"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
      // jQuery to handle the 'See More' link click
      $(document).on('click', '.btn-link', function () {
          var blogID = $(this).data('id');
          var blogName = $(this).data('name');
          var blogCategory = $(this).data('category');
          var blogContent = $(this).data('content');
          var blogImage = $(this).data('image');

          // Populate the modal with full blog details
          $('#modalBlogName').text(blogName);
          $('#modalBlogCategory').text(blogCategory);
          $('#modalBlogContent').text(blogContent);
          $('#modalBlogImage').attr('src', 'blog/' + blogImage);
      });
  </script>

<?php
} else {
    echo "Error fetching blogs: " . mysqli_error($data);
}
?>

</body>
</html>

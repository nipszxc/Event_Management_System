<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include('includes/checklogin.php');
check_login();
if (strlen($_SESSION['odmsaid']==0)) {
    header('location:logout.php');
} else {
    if(isset($_POST['submit'])) {
        // Check if file was uploaded without errors
        if(isset($_FILES['note']) && $_FILES['note']['error'] == 0) {
            $filename = $_FILES['note']['name'];
            $file_tmp = $_FILES['note']['tmp_name'];
            $file_size = $_FILES['note']['size'];
            
            // Validate file extension
            $valid_extensions = array('jpg', 'jpeg', 'png', 'gif');
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($file_ext, $valid_extensions)) {
                // Validate file size (5MB max)
                if($file_size <= 5242880) {
                    // Set upload directory
                    $upload_dir = 'assets/img/companyimages/';
                    
                    // Create directory if it doesn't exist
                    if(!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename to prevent overwrites
                    $new_filename = uniqid() . '.' . $file_ext;
                    $destination = $upload_dir . $new_filename;
                    
                    // Move the uploaded file
                    if(move_uploaded_file($file_tmp, $destination)) {
                        // Update database
                        $sql = "UPDATE tblcompany SET companylogo = :filename";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':filename', $new_filename, PDO::PARAM_STR);
                        
                        if($query->execute()) {
                            echo '<script>alert("Company logo updated successfully")</script>';
                            echo '<script>window.location.href = window.location.href;</script>';
                        } else {
                            echo '<script>alert("Database update failed! Try again later")</script>';
                        }
                    } else {
                        echo '<script>alert("Failed to move uploaded file")</script>';
                    }
                } else {
                    echo '<script>alert("File is too large. Maximum size is 5MB")</script>';
                }
            } else {
                echo '<script>alert("Invalid file type. Only JPG, JPEG, PNG & GIF are allowed")</script>';
            }
        } else {
            echo '<script>alert("Error uploading file. Please try again")</script>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php");?>
<body>
  <div class="container-scroller">
    <?php @include("includes/header.php");?>
    
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Update Company Logo</h4>
                  <form class="form-horizontal" name="insertproduct" method="post" enctype="multipart/form-data">
                    <?php
                    $sql = "SELECT * FROM tblcompany";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    
                    if($query->rowCount() > 0) {
                        foreach($results as $row) {  
                    ?>
                    <div class="form-group">
                      <label>Company Name</label>
                      <input type="text" class="form-control" name="companyname" readonly value="<?php echo htmlentities($row->companyname); ?>">
                    </div>
                    
                    <div class="form-group">
                      <label>Current Logo</label><br>
                      <?php if($row->companylogo == "logo.jpg") { ?>
                        <img src="assets/img/companyimages/logo.jpg" alt="Default Logo" width="200">
                      <?php } else { ?>
                        <img src="assets/img/companyimages/<?php echo htmlentities($row->companylogo); ?>" alt="Company Logo" width="200">
                      <?php } ?>
                    </div>
                    
                    <div class="form-group">
                      <label>New Logo</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="note" id="customFile" required>
                        <label class="custom-file-label" for="customFile">Choose file</label>
                        <small class="form-text text-muted">
                          Allowed formats: JPG, JPEG, PNG, GIF (Max 5MB)
                        </small>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary" name="submit">
                        <i class="mdi mdi-upload"></i> Upload Logo
                      </button>
                    </div>
                    <?php 
                        }
                    } 
                    ?>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php @include("includes/foot.php");?>
    
    <script>
      // Update file input label with selected filename
      document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = document.getElementById("customFile").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
      });
    </script>
  </body>
</html>
<?php } ?>
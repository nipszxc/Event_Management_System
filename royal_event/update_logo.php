<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include('includes/checklogin.php');
check_login();
if (strlen($_SESSION['odmsaid']==0)) 
{
  header('location:logout.php');
} else{
$pid=intval($_GET['id']);// product id
if(isset($_POST['submit']))
{
    $filename = $_FILES['note']['name'];
    
    // Create directory if it doesn't exist
    $upload_dir = 'assets/img/companyimages/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // destination of the file on the server
    $destination = $upload_dir . $filename;
    
    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['note']['tmp_name'];
    
    // move the uploaded (temporary) file to the specified destination
    if (move_uploaded_file($file, $destination)) {
        $sql="update tblcompany set companylogo=:filename where id=:pid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':filename',$filename,PDO::PARAM_STR);
        $query->bindParam(':pid',$pid,PDO::PARAM_INT);
        if ($query->execute()){
            echo '<script>alert("Company logo updated successfully"); window.location.href="companyprofile.php";</script>';
        }else{
            echo '<script>alert("Update failed! Try again later")</script>';
        }
    } else {
        echo '<script>alert("Failed to upload file! Please try again")</script>';
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
                    <br/>
                    <form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">
                      <?php
                      $sql="SELECT * from tblcompany where id=:pid";
                      $query = $dbh->prepare($sql);
                      $query->bindParam(':pid',$pid,PDO::PARAM_INT);
                      $query->execute();
                      $results=$query->fetchAll(PDO::FETCH_OBJ);
                      if($query->rowCount() > 0)
                      {
                        foreach($results as $row)
                        {  
                          ?>
                          <div class="control-group">
                            <label class="control-label" for="basicinput">Company Name</label>
                            <div class="col-6">
                              <input type="text" class="form-control" name="companyname" readonly value="<?php echo $row->companyname;?>">
                            </div>
                          </div>
                          <br>
                          <div class="control-group"> 
                            <label class="control-label" for="basicinput">Current logo</label>
                            <div class="controls">
                              <?php if($row->companylogo=="avatar15.jpg"){ ?>
                                <img class="" src="assets/img/avatars/avatar15.jpg" alt="" width="100" height="100">
                              <?php } else { ?>
                                <img style="height: auto; width: 300px;" src="assets/img/companyimages/<?php echo $row->companylogo;?>" width="180" height="130"> 
                              <?php } ?> 
                            </div>
                          </div>
                          <br>
                          <div class="form-group col-md-6">
                            <label>Upload New Logo</label>
                            <input type="file" name="note" class="form-control" required>
                          </div>
                        <?php }} ?>
                        <br>
                        <div class="form-group row">
                          <div class="col-12">
                            <button type="submit" class="btn btn-primary" name="submit">
                              Update Logo
                            </button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <?php @include("includes/footer.php");?>
            
          </div>
        </div>
      </div>
      
      <?php @include("includes/foot.php");?>
    </body>
    </html>
<?php } ?>
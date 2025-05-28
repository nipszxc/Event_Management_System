<?php
include('includes/dbconnection.php');

// Get company logo from database
$sql = "SELECT companylogo FROM tblcompany LIMIT 1";
$query = $dbh->prepare($sql);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$logo = $result ? $result->companylogo : 'logo.jpg';

// Set the content type header
header('Content-Type: image/x-icon');

// Get the image path
$imagePath = 'assets/img/companyimages/' . $logo;

// If company logo exists, serve it. Otherwise serve default favicon
if (file_exists($imagePath)) {
    readfile($imagePath);
} else {
    readfile('assets/images/favicon.jpg');
}
?> 
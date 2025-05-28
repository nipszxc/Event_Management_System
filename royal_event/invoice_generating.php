<?php
include('includes/checklogin.php');
check_login();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

          <div class="row" id="exampl">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                
                <div class="table-responsive p-3">
                  <?php
                  // Initialize variables
                  $grandtotal = 0;

                  // Get company details
                  $sql_company = "SELECT * FROM tblcompany LIMIT 1";
                  $query_company = $dbh->prepare($sql_company);
                  $query_company->execute();
                  $company = $query_company->fetch(PDO::FETCH_OBJ);

                  if(isset($_GET['invid']) && !empty($_GET['invid'])) {
                      $invid = $_GET['invid'];
                      
                      // Get booking and service details
                      $sql = "SELECT b.*, s.ServiceName, s.ServicePrice 
                             FROM tblbooking b 
                             LEFT JOIN tblservice s ON b.ServiceID = s.ID 
                             WHERE b.ID = :invid";
                      
                      try {
                          $query = $dbh->prepare($sql);
                          $query->bindParam(':invid', $invid, PDO::PARAM_INT);
                          $query->execute();
                          
                          if($query->rowCount() > 0) {
                              $row = $query->fetch(PDO::FETCH_OBJ);
                              
                              // Calculate totals
                              $subtotal = $row->ServicePrice;
                              $tax = $subtotal * 0.18; // 18% GST
                              $grandtotal = $subtotal + $tax;
                              ?>
                              <div class="invoice-container">
                                <!-- Header -->
                                <table class="table table-borderless">
                                  <tr>
                                    <td width="200">
                                      <img src="assets/img/companyimages/<?php echo htmlentities($company->companylogo); ?>" alt="Company Logo" style="max-width: 150px;">
                                    </td>
                                    <td>
                                      <h3><?php echo htmlentities($company->companyname); ?></h3>
                                      <p class="mb-0"><?php echo htmlentities($company->companyaddress); ?></p>
                                      <p class="mb-0">Phone: <?php echo htmlentities($company->companyphone); ?></p>
                                    </td>
                                    <td class="text-right">
                                      <h2 class="text-primary">RECEIPT</h2>
                                      <p class="mb-0"><strong>Date:</strong> <?php echo date('d-m-Y'); ?></p>
                                      <p class="mb-0"><strong>Receipt No:</strong> <?php echo htmlentities($row->BookingID); ?></p>
                                    </td>
                                  </tr>
                                </table>

                                <hr>

                                <!-- Client Details -->
                                <div class="row mb-4">
                                  <div class="col-md-6">
                                    <h5 class="text-primary">Received From:</h5>
                                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlentities($row->Name); ?></p>
                                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlentities($row->MobileNumber); ?></p>
                                  </div>
                                  <div class="col-md-6 text-right">
                                    <h5 class="text-primary">Event Date:</h5>
                                    <p class="mb-1"><?php echo date('d-m-Y', strtotime($row->EventDate)); ?></p>
                                  </div>
                                </div>

                                <!-- Payment Details -->
                                <div class="bg-light p-3 mb-4">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <table class="table table-bordered">
                                        <tr>
                                          <td><strong>Event Type</strong></td>
                                          <td><?php echo htmlentities($row->EventType); ?></td>
                                        </tr>
                                        <tr class="bg-primary text-white">
                                          <td><strong>Total Amount</strong></td>
                                          <td class="text-right"><strong>₱<?php echo number_format(floatval($row->ServicePrice), 2); ?></strong></td>
                                        </tr>
                                      </table>
                                    </div>
                                  </div>
                                </div>

                                <div class="row mt-5">
                                  <div class="col-md-6">
                                    <p><strong>Customer Signature</strong></p>
                                    <div style="border-top: 1px solid #dee2e6; width: 200px; margin-top: 50px;"></div>
                                  </div>
                                  <div class="col-md-6 text-right">
                                    <p><strong>Authorized Signature</strong></p>
                                    <div style="border-top: 1px solid #dee2e6; width: 200px; margin-top: 50px; margin-left: auto;"></div>
                                  </div>
                                </div>

                                <div class="text-center mt-4">
                                  <p class="mb-0">Thank you for choosing <?php echo htmlentities($company->companyname); ?>!</p>
                                  <small>This is a computer generated receipt.</small>
                                </div>
                              </div>
                              <?php
                          } else {
                              echo '<div class="alert alert-danger">No booking found with ID: ' . htmlentities($invid) . '</div>';
                          }
                      } catch(PDOException $e) {
                          echo '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
                      }
                  } else {
                      echo '<div class="alert alert-danger">Invalid invoice ID</div>';
                  }
                  ?>

                  <div class="text-center mt-4 no-print">
                    <button class="btn btn-primary" onclick="window.print();">
                      <i class="mdi mdi-printer"></i> Print Receipt
                    </button>
                  </div>
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

  <script>
    function CallPrint(strid) {
      var prtContent = document.getElementById("exampl");
      var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
      WinPrint.document.write(prtContent.innerHTML);
      WinPrint.document.close();
      WinPrint.focus();
      WinPrint.print();
      WinPrint.close();
    }
  </script>

  <style>
    .invoice-container {
      background: white;
      padding: 30px;
      border: 1px solid #ddd;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      #exampl, #exampl * {
        visibility: visible;
      }
      #exampl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .no-print {
        display: none !important;
      }
      .invoice-container {
        border: none;
        box-shadow: none;
      }
      .table {
        width: 100% !important;
      }
      .bg-primary {
        background-color: #007bff !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
      }
    }

    .table td, .table th {
      padding: 12px;
    }
    
    .text-primary {
      color: #007bff !important;
    }
  </style>
</body>
</html>
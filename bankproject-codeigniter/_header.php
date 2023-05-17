<?php
session_start();

// make sure a user is logged in
if (!isset($_SESSION['EmployeeID'])){
	header("Location: login.php"); 
	exit();
}
include("core/database.php");
include("core/applications-employees-functions.php");
include("core/jobs-functions.php");
include("core/helpers.php");

$res = query("SELECT * FROM EmployeesList WHERE Status = 'active'");
$__employees = [];
while ($row = sqlsrv_fetch_array($res, 2)) {
    $__employees[] = $row;
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>TAB Global</title>
        <link rel="icon" type="image/x-icon" href="img/favicon.ico" />
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="img/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/bootstrap.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/incipit.css" rel="stylesheet">
        <link href="css/fontawesome/css/all.css" rel="stylesheet"/>
                <link rel="stylesheet" href="build/css/intlTelInput.css">
        <link href="css/font.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="css/datatables.css">
        <link rel="stylesheet" type="text/css" href="css/select2.css">
        <link rel="stylesheet" type="text/css" href="css/reviews.css">
        <link rel="stylesheet" type="text/css" href="css/alertify.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		

    </head>
    <body>
        <div class="modal fade" id="ApplicationApproveModal" tabindex="-1" role="dialog" aria-labelledby="ApplicationApproveLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Approve Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="ApplicationApproveModalBody">
                        <form id="approve-app-form" action="core/update-application-status.php" method="POST">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="Interviewers-type" value="default" checked>Default Interviwers&nbsp;&nbsp;
                                <input type="radio" name="Interviewers-type" value="custom">Custom
                            </div>
                            <div id="select-interviewer-box" style="display: none;margin-top: 5px;">
                                <label><b>Select Interviewers</b></label>
                                <select class="select-interviewer" name="employees[]" multiple="multiple" style="width: 100% !important;">
                                    <?php foreach ($__employees as $employee) {?>
                                        <option value="<?=$employee["EmployeeID"]?>"><?=$employee["FirstName"] . " " . $employee["LastName"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" name="ApplicationID" class="form-control">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" id="approve-application-btn">Approve</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="HireModal" tabindex="-1" role="dialog" aria-labelledby="HireLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Hiring</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="hire-form" action="core/set-interview.php" method="POST">
                            <div class="form-group">
                                <label>Date Offer Accepted</label>
                                <input type="date" name="OfferAcceptDate" class="form-control">
                            </div>
                            <input type="hidden" name="ApplicationID" class="form-control">
                        </form>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <a href="core/set-interview.php" data-action="kiv" data-id="" class='btn btn-danger btn-sm hiring'>Reject</a>
                        <button class="btn btn-success btn-sm" id="hire-btn">Hire</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex" id="wrapper">
            <!-- Sidebar-->
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading text-center">
                	<a href="/"><img src="img/logo_smm.png" width="80"></a>
                </div>
                               <div class="list-group list-group-flush" id="sidebar">
                    <!----a class="list-group-item list-group-item-action list-group-item-light p-4" href="index.php"><i class="fas fa-users"></i> Employees</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-4" href="jobs.php"><i class="fas fa-suitcase"></i> Jobs</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-4" href="reviews.php"><i class="fas fa-star"></i> Reviews</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-4" href="job-requirements.php"><i class="fas fa-book"></i> Job Requirements</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-4" href="admin-edit.php"><i class="fas fa-cogs"></i> Admin Edit</a----->
					
					<nav>
  <UL>
   <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-users"></i>
        </div>
        <a href="index.php"><span>Employees</span></a>
      </div>
   </li>
   <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-book"></i>
        </div>
        <a href="applications.php"><span>My Applications</span></a>
      </div>
   </li>
   <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-suitcase"></i> 
        </div>
        <a href="jobs.php"><span>Jobs</span></a>
      </div>
   </li>
   <!-- <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-star"></i> 
        </div>
        <a href="reviews.php"><span>Reviews</span></a>
      </div>
   </li> -->
   <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-book"></i>
        </div>
      <a href="job-requirements.php"><span>Job Requirements</span></a>
      </div>
   </li>
    <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-cogs"></i>
        </div>
      <a href="job-titles.php"><span>Job Titles</span></a>
      </div>
   </li>
    <li class="var_nav">
      <div class="link_bg"></div>
      <div class="link_title">
        <div class=icon> 
        <i class="fas fa-cogs"></i>
        </div>
      <a href="admin-edit.php"><span>Admin Edit</span></a>
      </div>
    </li>
  </UL>
</nav>


                </div>
            </div>
            <!-- Page content wrapper-->
            <div id="page-content-wrapper">
                <!-- Top navigation-->
                <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                    <div class="container-fluid">
                        <!-- <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button> -->
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$_SESSION['FirstName']?> <?=$_SESSION['LastName']?></a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="core/logout.php">Logout</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <!-- Page content-->
                <div class="container-fluid p-4">
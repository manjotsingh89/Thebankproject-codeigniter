<?php
session_start();

include("core/database.php");
include("core/applications-employees-functions.php");
include("core/jobs-functions.php");
include("core/helpers.php");

$application = null;
if(!isset($_GET['id'])) header("location: public-jobs.php");
if(isset($_GET['e']) && $_GET['e'] != null) {

	$employemt_id = explode('-', base64_decode($_GET['e']));
	$employemt_id = end($employemt_id);
	$res = query("SELECT TOP(1) * FROM Applications WHERE EmployeeID = $employemt_id");
	
	if (sqlsrv_has_rows($res)) {
		$application = sqlsrv_fetch_array($res, 2);
	}

	$res   = getApplicationEducationalDetails($conn, $application['ApplicationID']);
	$ed = [];
	while ($row = sqlsrv_fetch_array($res, 2)) {
		$ed[] = $row;
	}
	$ns  = getApplicationNationalServices($conn, $application['ApplicationID']);

	$res  = getApplicationEmploymentHistory($conn, $application['ApplicationID']);
	$employemt_histories = [];
	while ($row = sqlsrv_fetch_array($res, 2)) {
		$employemt_histories[] = $row;
	}
	$res   = getApplicationReferences($conn, $application['ApplicationID']);
	$application_references = [];
	while ($row = sqlsrv_fetch_array($res, 2)) {
		$application_references[] = $row;
	}
}

$job_id = explode('-', base64_decode($_GET['id']));
$job_id = end($job_id);

$edl   = getEducationalDetailsLevel($conn);
$jd   = getJobDetails($conn, $job_id);

if (!$jd) {
	die('Invalid Job ID');
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="ip" content="<?=get_client_ip()?>">
        <title>TAB Global</title>
        <!-- Favicon-->
        <link rel="stylesheet" href="build/css/intlTelInput.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/bootstrap.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/alertify.min.css">
        <link href="css/incipit.css" rel="stylesheet">
        <link href="css/fontawesome/css/all.css" rel="stylesheet"/>
        <link href="css/font.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="css/datatables.css">

    </head>
    <body class="applicationpage">
        <div class="d-flex" id="wrapper">
            <!-- Sidebar-->
                <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading text-center">
                	<a href="/"><img src="img/logo_smm.png" width="80"></a>
                </div>
   
            </div>
            <!-- Page content wrapper-->
            <div id="page-content-wrapper">
                <!-- Page content-->
                <div class="container-fluid p-4">
					<div class="header">
						<!-- hidden employee id -->
						<div class="row">
							<div class="col-4">
								<h2>Application Form</h2>
							</div>
						</div>
						<div class="bg-white p-3 my-4">
								
							<p><strong>Position applied: </strong> <span><?=$jd['JobTitleName']?></span></p>
							<sub>(Please fill this form carefully. Your data will remain confidential.)</sub>
						<!-- <ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#forms-controller" type="button" role="tab" aria-controls="home" aria-selected="true">General Info</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Documents</button>
							</li>
						</ul> -->
							<div class="tab-content mt-4">
								<div class="tab-pane active" id="forms-controller" role="tabpanel" aria-labelledby="home-tab" style="position: relative;">
										<!--personal details section-->
					 					<section id="form-section-1" class="p-4 bg-light" data-position="1" data-table="Applications">
											<form id="form-employee-1">
												<input type="hidden" class="form-control" id="JobID" name="JobID" value="<?=$job_id?>">
												<input type="hidden" class="form-control" id="PositionApplied" name="PositionApplied" required value="<?=$jd['JobTitleName']?>">
												<h5 class="my-4">
													<h3>Personal Particulars</h3>
												</h5>
												<div class="form-group row mb-2">
													<label for="FirstName" class="col-sm-2 col-form-label" >First Name:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="FirstName" name="FirstName" value="<?=$application ? ($application['FirstName'] ?? '') : ''?>" required placeholder="Enter first name">
													</div>
													<label for="LastName" class="col-sm-1 col-form-label">Last Name:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="LastName" name="LastName" value="<?=$application ? ($application['LastName'] ?? '') : ''?>" required placeholder="Enter last name">
													</div>
													<label for="Email" class="col-sm-1 col-form-label">Personal Email:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="Email" name="Email" value="<?=$application ? ($application['Email'] ?? '') : ''?>" required placeholder="you@example.com">
													</div>
												</div>
												<div class="form-group row mb-2">
													<label for="Address" class="col-sm-2 col-form-label">Address:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="address" name="Address" value="<?=$application ? ($application['Address'] ?? '') : ''?>" required placeholder="Enter home address">
													</div>
													<label for="Address" class="col-sm-1 col-form-label">City:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="city" name="City" value="<?=$application ? ($application['City'] ?? '') : ''?>" required placeholder="Enter city">
													</div>
													<label for="Address" class="col-sm-1 col-form-label">State:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="state" name="State" value="<?=$application ? ($application['State'] ?? '') : ''?>" required placeholder="Enter state">
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="TelephoneNumber" class="col-sm-2 col-form-label">Tel:</label>
													<div class="col-sm-3">
														<input type="tel" class="form-control phone" id="TelephoneNumber" required name="TelephoneNumber" value="<?=$application ? ('+' . $application['TelephoneNumber'] ?? '') : ''?>">
													</div>
													<label for="PagerNumber" class="col-sm-1 col-form-label">H/p No/Pager:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="PagerNumber" name="PagerNumber" value="<?=$application ? ($application['PagerNumber'] ?? '') : ''?>" placeholder="Enter H/p No/Pages">
													</div>
													<label for="Email" class="col-sm-1 col-form-label">Company Email:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="CompanyEmail" name="CompanyEmail" value="<?=$application ? ($application['CompanyEmail'] ?? '') : ''?>" required placeholder="you@example.com">
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="PassportNumber" class="col-sm-2 col-form-label">NRIC No (Colour)/Passport No:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="PassportNumber" required name="PassportNumber" value="<?=$application ? ($application['PassportNumber'] ?? '') : ''?>" placeholder="Enter NRIC/passport number">
													</div>
													<label for="Citizenship" class="col-sm-1 col-form-label">Citizenship:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="Citizenship" required name="Citizenship" value="<?=$application ? ($application['Citizenship'] ?? '') : ''?>" placeholder="Enter citizenship">
													</div>
													<label for="Gender" class="col-sm-1 col-form-label">Gender:</label>
													<div class="col-sm-3">
														<select class="form-control" id="Gender" name="Gender">
															<option value="male" <?=$application ? (($application['Gender'] == 'male' ? 'selected' : '') ?? '') : ''?>>Male</option>
															<option value="female" <?=$application ? (($application['Gender'] == 'female' ? 'selected' : '') ?? '') : ''?>>Female</option>
														</select>
													</div>
												</div>
												<div class="form-group row mb-2">
													<label for="SpouseNAddress" class="col-sm-2 col-form-label">Are You Serving Bond With Your Present Employer?</label>
													<div class="col-sm-5">
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="PresentEmployerBond" <?=$application ? (($application['PresentEmployerBond'] == 'yes' ? 'checked' : '') ?? '') : ''?> id="PresentEmployerBondYes" value="yes">
															<label class="form-check-label" for="PresentEmployerBondYes">Yes</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" name="PresentEmployerBond" <?=$application ? (($application['PresentEmployerBond'] == 'no' ? 'checked' : '') ?? '') : 'checked'?> id="PresentEmployerBondNo" value="no">
															<label class="form-check-label" for="PresentEmployerBondNo">No</label>
														</div>
													</div>
												</div>

												<hr>

												<h6 class="mb-4">TAB Global is an equal opportunity employer for people of all background. The following social information is intended only to give us a sense of your origins and general background. </h6>

												<div class="form-group row mb-2">
													<label for="Birthday" class="col-sm-2 col-form-label">Date of Birth:</label>
													<div class="col-sm-3">
														<input type="date" class="form-control" id="Birthday" name="Birthday" value="<?=$application ? ($application['Birthday'] ?? '') : ''?>" required>
													</div>
													<label for="BirthPlace" class="col-sm-1 col-form-label">Birth Place:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="BirthPlace" name="BirthCity" value="<?=$application ? ($application['BirthCity'] ?? '') : ''?>" placeholder="City">
													</div>
													<div class="col-sm-4">
														<input type="text" class="form-control" id="BirthPlace" name="BirthCountry" value="<?=$application ? ($application['BirthCountry'] ?? '') : ''?>" placeholder="Country">
													</div>
													</div>
													<div class="form-group row mb-2">
													<label for="Dialect" class="col-sm-2 col-form-label">Ethnicity/Dialect:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="Dialect" name="Dialect" value="<?=$application ? ($application['Dialect'] ?? '') : ''?>" placeholder="Enter ethnicity/dialect">
													</div>
													<label for="Religion" class="col-sm-1 col-form-label">Religion:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="Religion" name="Religion" value="<?=$application ? ($application['Religion'] ?? '') : ''?>" placeholder="Enter religion">
													</div>
												</div>

												<div class="form-group row mb-2">
													
													<label for="MaritalStatus" class="col-sm-2 col-form-label">Marital Status:</label>
													<div class="col-sm-9">
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusSingle" value="Single" <?=$application ? (($application['MaritalStatus'] == 'Single' ? 'checked' : '') ?? '') : 'checked'?> required>
															<label class="form-check-label" for="MaritalStatusSingle">Single</label>
														</div>
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusMarried" value="Married" <?=$application ? (($application['MaritalStatus'] == 'Married' ? 'checked' : '') ?? '') : ''?> required>
															<label class="form-check-label" for="MaritalStatusMarried">Married</label>
														</div>
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusSeparated" value="Separated" <?=$application ? (($application['MaritalStatus'] == 'Separated' ? 'checked' : '') ?? '') : ''?> required>
															<label class="form-check-label" for="MaritalStatusSeparated">Separated</label>
														</div>
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusDivorced" value="Divorced" <?=$application ? (($application['MaritalStatus'] == 'Divorced' ? 'checked' : '') ?? '') : ''?> required>
															<label class="form-check-label" for="MaritalStatusDivorced">Divorced</label>
														</div>
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusWidowed" value="Widowed" <?=$application ? (($application['MaritalStatus'] == 'Widowed' ? 'checked' : '') ?? '') : ''?> required>
															<label class="form-check-label" for="MaritalStatusWidowed">Widowed</label>
														</div>
													</div>
												</div>

												<hr>
												
												<div class="separator01"></div>

												<div class="form-group row mb-2">
													<label for="SpouseName" class="col-sm-2 col-form-label">If marries, State spouse's Name:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="SpouseName" name="SpouseName" value="<?=$application ? ($application['SpouseName'] ?? '') : ''?>" placeholder="Spouse's name">
													</div>
													<label for="Occupation" class="col-sm-1 col-form-label">Occupation:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="Occupation" name="Occupation" value="<?=$application ? ($application['Occupation'] ?? '') : ''?>" placeholder="Enter occupation">
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="NumberofChildren" class="col-sm-2 col-form-label">No. of Children:</label>
													<div class="col-sm-3">
														<input type="number" min="0" class="form-control" id="NumberofChildren" name="NumberofChildren" value="<?=$application ? ($application['NumberofChildren'] ?? '') : ''?>">
													</div>
													<label for="AgeRange" class="col-sm-1 col-form-label">Age Range:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="AgeRange" name="AgeRange" value="<?=$application ? ($application['AgeRange'] ?? '') : ''?>" placeholder="Enter age range">
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="NextOfKinName" class="col-sm-2 col-form-label">Next-of-kin's:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="NextOfKinName" name="NextOfKinName" value="<?=$application ? ($application['NextOfKinName'] ?? '') : ''?>" placeholder="Enter name">
													</div>
													<label for="Relationship" class="col-sm-1 col-form-label">Relationship:</label>
													<div class="col-sm-2"><input type="text" class="form-control" id="Relationship" name="Relationship" value="<?=$application ? ($application['Relationship'] ?? '') : ''?>" placeholder="Enter relationship">
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="SpouseNAddress" class="col-sm-2 col-form-label">Address:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="SpouseAddress" name="SpouseAddress" value="<?=$application ? ($application['SpouseAddress'] ?? '') : ''?>" placeholder="Enter spouse's address">
													</div>
													<label for="SpouseTelephoneNumber" class="col-sm-1 col-form-label">Tel No:</label>
													<div class="col-sm-2"><input type="tel" class="form-control phone" id="SpouseTelephoneNumber" name="SpouseTelephoneNumber" value="<?=$application ? (isset($application['SpouseTelephoneNumber']) && $application['SpouseTelephoneNumber'] != null ? '+' . $application['SpouseTelephoneNumber'] : '') : ''?>">
													</div>
												</div>

												<hr>

												<!-- <div class="form-group row mb-2">
												</div> -->
												<div class="form-group row mb-2">
													<label for="InterviewPosition" class="col-sm-2 col-form-label">Position for which you are being interviewed:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="InterviewPosition" name="InterviewPosition" value="<?=$jd['JobTitleName']?>" readonly>
													</div>

													<!-- <label for="PositionDesired" class="col-sm-2 col-form-label">Position Desired:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="PositionDesired" name="PositionDesired" placeholder="Write position">
													</div> -->
													<label for="DateAvailable" class="col-sm-2 col-form-label">Date Available:</label>
													<div class="col-sm-2">
														<input type="date" class="form-control" id="DateAvailable" name="DateAvailable" required>
													</div>
												</div>

												<div class="form-group row mb-2">
													<label for="PositionQualified" class="col-sm-2 col-form-label PositionQualified">Other Positions:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="PositionQualified" name="PositionQualified" placeholder="Write positions">
													</div>
													<label for="PreviouslyEmployedToCompany" class="col-sm-2 col-form-label PreviouslyEmployedToCompany">Previously employed by/applied to join Company:</label>
													<div class="col-sm-4 previouslyemployedbox">
														<div class="form-check form-check-inline mt-2">
															<input class="form-check-input" type="radio" name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyYes" value="yes">
															<label class="form-check-label" for="PreviouslyEmployedToCompanyYes">Yes</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" checked name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyNo" value="no">
															<label class="form-check-label" for="PreviouslyEmployedToCompanyNo">No</label>
														</div>
													</div>
												</div>
												<hr/>
												<div class="form-group row mb-2">
													<label for="RelativesInCompany" class="col-sm-2 col-form-label">Do you have any friends/relatives in the company</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="RelativesInCompany" name="RelativesInCompany" placeholder="Leave blank if none">
													</div>
												</div>
											</form>
										</section>  

										<!--educational details section-->
										 <section id="form-section-2" class="p-4 bg-light" data-position="2" data-table="ApplicationEducationalDetails">
											<h5 class="my-4">
												<h3>Educational Details</h3>
											</h5>
											<div><b class="text-warning">NOTE:</b> <small>Please list in chronological order, from primary to university where applicable</small></div>
											<form id="form-employee-2">
												<table width="100%" class="table" id="educational-details-table">
													<tr>
														<td align="center">Name of School</td>
														<td align="center">Address</td>
														<td align="center">Level</td>
														<td align="center">From</td>
														<td align="center">To</td>
														<td align="center">Did You Graduate</td>
														<td colspan="2" align="center">Details</td>
													</tr>
													<?php if (count($ed) > 0) { foreach ($ed as $key => $row) { ?>
													<tr class="educational-details-row">
														<td>
															<input type="text" name="SchoolName[]" value="<?=$row['SchoolName']?>" class="form-control" placeholder="Enter School">
														</td>
														<td>
															<input type="text" name="SchoolCity[]" value="<?=$row['SchoolCity']?>" class="form-control mb-1" placeholder="City">
															<input type="text" name="SchoolCountry[]" value="<?=$row['SchoolCountry']?>" class="form-control" placeholder="Country">
														</td>
														<td>
															<select name="EducationalDetailsLevelID[]" class="form-control">
																<?php while($row2 = sqlsrv_fetch_array($edl, SQLSRV_FETCH_ASSOC) ){ ?>
																<option value="<?=$row2['EducationalDetailsLevelID']?>" <?=$row['EducationalDetailsLevelID'] == $row2['EducationalDetailsLevelID'] ? 'selected' : '' ?>><?=$row2['LevelName']?></option>
																<?php } ?>
															</select>
														</td>
														<td><input type="month" name="AttendedFrom_<?=$key?>" class="form-control AttendedFrom" value="<?=date_format(date_create($row['AttendedFrom']), "Y-m")?>"></td>
														<td><input type="month" name="AttendedTo_<?=$key?>" class="form-control AttendedTo" value="<?=date_format(date_create($row['AttendedTo']), "Y-m")?>"></td>
														<td>
															<div class="form-check form-check-inline mt-2">
																<input class="form-check-input" type="radio" name="IsGraduated_<?=$key?>" value="yes" <?=$row['IsGraduated'] == 'yes' ?'checked' : ''?>>
																<label class="form-check-label" for="IsGraduatedYes">Yes</label>
															</div>
															<div class="form-check form-check-inline">
																<input class="form-check-input" type="radio" name="IsGraduated_<?=$key?>" value="no" <?=$row['IsGraduated'] == 'no' ?'checked' : ''?>>
																<label class="form-check-label" for="IsGraduatedNo">No</label>
															</div>
														</td>
														<td>
															<input type="text" name="SchoolDetails[]" value="<?=$row['SchoolDetails']?>" class="form-control">
														</td>
														<td style="position: relative;"><button style="<?=count($ed) == 1 ? 'display: none;' : '' ?>margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
													</tr>	
													<?php }} else {?>
													<tr class="educational-details-row">
														<td>
															<input type="text" name="SchoolName[]" class="form-control" placeholder="Enter School">
														</td>
														<td>
															<input type="text" name="SchoolCity[]" class="form-control mb-1" placeholder="City">
															<input type="text" name="SchoolCountry[]" class="form-control" placeholder="Country">
														</td>
														<td>
															<select name="EducationalDetailsLevelID[]" class="form-control">
																<?php while($row = sqlsrv_fetch_array($edl, SQLSRV_FETCH_ASSOC) ){ ?>
																<option value="<?=$row['EducationalDetailsLevelID']?>"><?=$row['LevelName']?></option>
																<?php } ?>
															</select>
														</td>
														<td><input type="month" name="AttendedFrom_0" class="form-control AttendedFrom"></td>
														<td><input type="month" name="AttendedTo_0" class="form-control AttendedTo"></td>
														<td>
															<div class="form-check form-check-inline mt-2">
																<input class="form-check-input" type="radio" name="IsGraduated_0" value="yes" checked>
																<label class="form-check-label" for="IsGraduatedYes">Yes</label>
															</div>
															<div class="form-check form-check-inline">
																<input class="form-check-input" type="radio" name="IsGraduated_0" value="no">
																<label class="form-check-label" for="IsGraduatedNo">No</label>
															</div>
														</td>
														<td>
															<input type="text" name="SchoolDetails[]" class="form-control">
														</td>
														<td style="position: relative;"><button style="display: none;margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
													</tr>
													<?php } ?>
												</table>
												<div class="d-flex justify-content-end"><button type="button" class="btn btn-primary" id="add-new-school">+ School</button></div>
											</form>
											<form id="form-employee-2half">
												<div class="form-group my-2">
													<label for="FurtherEducation" class="col-form-label">If you Plan Further Education, Please Explain:</label>
													<textarea type="text" class="form-control" id="FurtherEducation" name="FurtherEducation"><?=$application['FurtherEducation'] ?? '' ?></textarea>
												</div>

												<div class="form-group mb-2">
													<label for="TrainingSkills" class="col-form-label">Other Training Or Skills (eg Technology, skills based, special courses etc):</label>
													<textarea type="text" class="form-control" id="TrainingSkills" name="TrainingSkills"><?=$application['TrainingSkills'] ?? '' ?></textarea>
												</div>

												<div class="form-group mb-2">
													<label for="FullName" class="col-form-label">Hobbies:</label>
													<textarea type="text" class="form-control" id="Hobbies" name="Hobbies"><?=$application['Hobbies'] ?? '' ?></textarea>
												</div>
											</form>
										</section>  

										<!--national services section-->
										<section id="form-section-3" class="p-4 bg-light" data-position="3" data-table="ApplicationNationalServices">
											<div>
												<h3>National Service</h3>
											</div>
											<div class="form-group mb-3">
												<div class="form-check form-check-inline mt-2">
													<input class="form-check-input" type="radio" name="NationalServiceApplicable" value="1" <?=(isset($ns['Applicable']) && $ns['Applicable'] == 1) ? 'checked' : ''?>>
													<label class="form-check-label" for="IsGraduatedYes">Applicable</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="NationalServiceApplicable" value="0" <?=(isset($ns['Applicable']) && $ns['Applicable'] == 1) ? '' : 'checked'?>>
													<label class="form-check-label" for="IsGraduatedNo">Not Applicable</label>
												</div>
											</div>
											<form id="form-employee-3" style="<?=(isset($ns['Applicable']) && $ns['Applicable'] == 1) ? '' : 'display: none;'?>">
												<input type="hidden" name="Applicable" value="0">
												<table width="100%" class="table table-bordered">
													<tr>
														<td align="center">FULL TIME</td>
														<td align="center">From</td>
														<td align="center">To</td>
														<td align="center">Type of Discharge</td>
														<td align="center">Vocation</td>
														<td align="center">Next In-Camp Training</td>
														<td align="center">Last Rank</td>
													</tr>
													<tr>
														<td>
															<input type="text" id="FullTime" name="FullTime" value="<?=$ns != NULL ? $ns['FullTime'] : '' ?>" class="form-control">
														</td>
														<td>
															<input type="date" id="ServedFrom" name="ServedFrom" value="<?=$ns != NULL ? $ns['ServedFrom'] : '' ?>" class="form-control">
														</td>
														<td><input type="date" id="ServedTo" name="ServedTo" value="<?=$ns != NULL ? $ns['ServedTo'] : '' ?>" class="form-control">
														</td>
														<td>
															<input type="text" id="DischargeType" name="DischargeType" value="<?=$ns != NULL ? $ns['DischargeType'] : '' ?>" class="form-control">
														</td>
														<td>
															<input type="text" id="Vocation" name="Vocation" value="<?=$ns != NULL ? $ns['Vocation'] : '' ?>" class="form-control">
														</td>
														<td>
															<input type="text" id="NextInCampTraining" name="NextInCampTraining" value="<?=$ns != NULL ? $ns['NextInCampTraining'] : '' ?>" class="form-control">
														</td>
														<td>
															<input type="text" id="LastRank" name="LastRank" value="<?=$ns != NULL ? $ns['LastRank'] : '' ?>" class="form-control">
														</td>
													</tr>
												</table>
												<div class="form-group row my-2">
													<label for="ServiceSchools" class="col-sm-2 col-form-label">Service Schools Or Special Experience:</label>
													<div class="col-sm-10">
														<input type="text" class="form-control" id="ServiceSchools" name="ServiceSchools" value="<?=$ns != NULL ? $ns['ServiceSchools'] : '' ?>">
													</div>
												</div>
												<div class="form-group row mb-2">
													<label for="SEPartTime" class="col-sm-2 col-form-label">PART TIME:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SEPartTime" name="SEPartTime" value="<?=$ns != NULL ? $ns['SEPartTime'] : '' ?>">
													</div>
													<label for="SEUnitAttached" class="col-sm-2 col-form-label">Unit Attached to:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SEUnitAttached" name="SEUnitAttached" value="<?=$ns != NULL ? $ns['SEUnitAttached'] : '' ?>">
													</div>
													<label for="SEDurLiability" class="col-sm-2 col-form-label">Duration of Liability:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SEDurLiability" name="SEDurLiability" value="<?=$ns != NULL ? $ns['SEDurLiability'] : '' ?>">
													</div>
												</div>
												<div class="form-group row mb-2">
													<label for="SEFreqDuties" class="col-sm-2 col-form-label">Frequency of Duties:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SEFreqDuties" name="SEFreqDuties" value="<?=$ns != NULL ? $ns['SEFreqDuties'] : '' ?>">
													</div>
													<label for="SELastRank" class="col-sm-2 col-form-label">Last Rank:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SELastRank" name="SELastRank" value="<?=$ns != NULL ? $ns['SELastRank'] : '' ?>">
													</div>
													<label for="SEStatus" class="col-sm-2 col-form-label">Exempted/Defered/Awaiting:</label>
													<div class="col-sm-2">
														<input type="text" class="form-control" id="SEStatus" name="SEStatus" value="<?=$ns != NULL ? $ns['SEStatus'] : '' ?>">
													</div>
												</div>
												<div class="form-group row mb-2">
													<label for="SEPeriod" class="col-sm-2 col-form-label">Period/Date of Registration:</label>
													<div class="col-sm-2">
														<input type="date" class="form-control" id="SEPeriod" name="SEPeriod" value="<?=$ns != NULL ? $ns['SEPeriod'] : '' ?>">
													</div>
													<label for="SEStatusReason" class="col-sm-2 col-form-label">Reason(s):</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="SEStatusReason" name="SEStatusReason" value="<?=$ns != NULL ? $ns['SEStatusReason'] : '' ?>">
													</div>
												</div>
											</form>
										</section> 

										<!--employment history section-->
					 					<section id="form-section-4" class="p-4 bg-light" data-position="4" data-table="ApplicationEmploymentHistory">
											<form id="form-employee-4">
												<h5 class="my-4">
													<h3>Employment History</h3>
												</h5>
												<table width="100%" class="table" id="tbl-emphistory">
													<?php if(count($employemt_histories) > 0){ foreach($employemt_histories as $index => $employemt_history) {?>
													<tr class="employee-history-row">
														<td>
															<input type="text" name="EmployerName[]" value="<?=$employemt_history['EmployerName']?>" class="form-control" placeholder="Employer name">
														</td>
														<td>
															<input type="text" name="EmployerCity[]" value="<?=$employemt_history['EmployerCity']?>" class="form-control" placeholder="City">
															<input type="text" name="EmployerCountry[]" value="<?=$employemt_history['EmployerCountry']?>" class="form-control" placeholder="Country">
														</td>
														<td><input type="text" name="Position[]" value="<?=$employemt_history['Position']?>" class="form-control" placeholder="Position"></td>
														<td><input type="month" name="EmploymentFrom_<?=$index?>" value="<?=date_format(date_create($employemt_history['EmploymentFrom']), "Y-m")?>" class="form-control EmploymentFrom"></td>
														<td><input type="month" name="EmploymentTo_<?=$index?>" value="<?=date_format(date_create($employemt_history['EmploymentTo']), "Y-m")?>" class="form-control EmploymentTo"></td>
														<td>
															<input type="text" data-type="currency" name="Salary[]" value="<?=$employemt_history['Salary']?>" class="form-control" placeholder="Salary">
														</td>
														<td>
															<input type="text" name="ReasonForLeaving[]" value="<?=$employemt_history['ReasonForLeaving']?>" class="form-control" placeholder="Enter reasons">
														</td>
														<td style="position: relative;"><button style="<?=count($employemt_histories) == 1 ? 'display: none;' : ''?>margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
													</tr>
													<?php }} else {?>
													<tr class="employee-history-row">
														<td>
															<input type="text" name="EmployerName[]" class="form-control" placeholder="Employer name">
															<input type="hidden" name="ApplicationEmploymentHistoryID[]" class="form-control">
														</td>
														<td>
															<input type="text" name="EmployerCity[]" class="form-control" placeholder="City">
															<input type="text" name="EmployerCountry[]" class="form-control" placeholder="Country">
														</td>
														<td><input type="text" name="Position[]" class="form-control" placeholder="Position"></td>
														<td><input type="month" name="EmploymentFrom_0" class="form-control EmploymentFrom"></td>
														<td><input type="month" name="EmploymentTo_0" class="form-control EmploymentTo"></td>
														<td>
															<div class="input-group">
																<input type="text" name="Salary[]" class="form-control" placeholder="Salary">
																<div class="input-group-append">
																	<span class="input-group-text">$</span>
																</div>
															</div>
														</td>
														<td>
															<input type="text" name="ReasonForLeaving[]" class="form-control" placeholder="Enter reasons">
														</td>
														<td style="position: relative;"><button style="display: none;margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
													</tr>
													<?php } ?>
												</table>
												<a id="add-emphistory" class="btn btn-primary float-end">+ Employment History</a>
											</form>
										</section> 

										<!--languages section-->
					 					<section id="form-section-5" class="p-4 bg-light" data-position="5" data-table="Applications">
											<form id="form-employee-5">
												<h5 class="my-4">
													<h3>Languages</h3>
												</h5>
												<div class="form-group my-2">
													<label for="LanguageSpoken">Language Spoken:</label>
													<input type="text" class="form-control" id="LanguageSpoken" name="LanguageSpoken" value="<?=$application['LanguageSpoken'] ?? ''?>">
												</div>
												<div class="form-group my-2">
													<label for="LanguageWritten">Language Written:</label>
													<input type="text" class="form-control" id="LanguageWritten" name="LanguageWritten" value="<?=$application['LanguageWritten'] ?? ''?>">
												</div>
											</form>
										</section> 

										<!--medical history section-->
					 					<section id="form-section-6" class="p-4 bg-light" data-position="6" data-table="Applications">
											<form id="form-employee-6">
												<h5 class="my-4">
													<h3>Medical History</h3>
												</h5>
												<div class="form-group my-2">
													<label for="PhysicalDisabledDetails">Any Physical Disability:   No / Yes, Please Specify:</label>
													<input type="text" class="form-control" id="PhysicalDisabledDetails" name="PhysicalDisabledDetails" value="<?=$application['PhysicalDisabledDetails'] ?? ''?>">
												</div>
												<div class="form-group my-2">
													<label for="MajorIllnessDetails">Any Major Illiness / Accident in Last Six Months?   No / Yes, Please Specify:</label>
													<input type="text" class="form-control" id="MajorIllnessDetails" name="MajorIllnessDetails" value="<?=$application['MajorIllnessDetails'] ?? ''?>">
												</div>
											</form>
											<p class="text-warning"><b>NOTE: </b>This section is for information only, to help us work with you better.</p>
										</section> 

										<!--references section-->
										<section id="form-section-7" class="p-4 bg-light" data-position="7" data-table="ApplicationReferences">
											<form id="form-employee-7">
												<div class="my-4">
													<h3>References</h3>
													<small>Please provide the contacts of at least two people who can provide us with insights into your suitability for your career with us.</small>
												</div>
												<table width="100%" class="table table-bordered" id="tbl-reference">
													<?php if (count($application_references) > 0) { foreach ($application_references as $index => $application_reference) { ?>
													<tr class="reference-row">
														<td>
															<input type="text" name="ReferenceName[]" value="<?=$application_reference['ReferenceName']?>" class="form-control" placeholder="Enter name">
														</td>
														<td>
															<textarea type="text" name="Association[]" class="form-control" placeholder="Organization where you worked with this person"><?=$application_reference['Association']?></textarea>
														</td>
														<td><input type="email" name="ReferenceEmail[]" value="<?=$application_reference['ReferenceEmail']?>" class="form-control" placeholder="Email"></td>
														<td><input type="tel" name="ReferenceMobile[]" value="<?=$application_reference['ReferenceMobile'] != null ? '+'.$application_reference['ReferenceMobile'] : ''?>" class="form-control phone"></td>
														<td><input type="number" name="YearsKnown[]" value="<?=$application_reference['YearsKnown']?>" class="form-control" placeholder="Years known" min="0"></td>
													</tr>
													<?php }} else {?>
													<tr class="reference-row">
														<td>
															<input type="text" name="ReferenceName[]" class="form-control" placeholder="Enter name">
															<input type="hidden" name="ApplicationReferencesID[]" class="form-control">
														</td>
														<td>
															<textarea type="text" name="Association[]" class="form-control" placeholder="Organization where you worked with this person"></textarea>
														</td>
														<td><input type="email" name="ReferenceEmail[]" class="form-control" placeholder="Email"></td>
														<td><input type="tel" name="ReferenceMobile[]" class="form-control phone"></td>
														<td><input type="number" name="YearsKnown[]" class="form-control" placeholder="Years known" min="0"></td>
													</tr>
													<?php } ?>
												</table>
												<a class="btn btn-primary float-end" id="add-reference">+ New Reference</a>
											</form>
										</section>

										<!--declaration section-->
										<section id="form-section-8" class="p-4 bg-light" data-position="8" data-table="Applications">
											<h5 class="my-4">
												<h3>Declaration</h3>
											</h5>
											<form id="form-employee-8">
												<input type="hidden" name="ApplicationStatus" value="pending approval">
												<div class="form-check">
													<input class="form-check-input declaration" type="checkbox" value="yes" name="Declaration1"  required="true">
													<label class="form-check-label" for="Declaration1" name="Declaration1">I have / have never been convicted on a criminal charge</label>
												</div>
												<div class="form-check">
													<input class="form-check-input declaration" type="checkbox" value="yes" name="Declaration2"  required="true">
													<label class="form-check-label" for="Declaration2">I have / not been taking illicit drugs</label>
												</div>
												<div class="form-check">
													<input class="form-check-input declaration" type="checkbox" value="yes" name="Declaration3"  required="true">
													<label class="form-check-label" for="Declaration3">I hereby certify that the above information as provided by me is true, complete and accurate to the best of my knowledge.</label>
												</div>
												<div class="form-check">
													<input class="form-check-input declaration" type="checkbox" value="yes" name="Declaration4"  required="true">
													<label class="form-check-label" for="Declaration4">I further understand that any wilful act on my part withholding information or making any false statement in this Employment Application is in itself sufficient ground for instant dismissal from the Company.</label>
												</div>
											</form>
										</section>
										<div class="alert alert-danger pt-1 pb-1" style="position: absolute; right: 8%; bottom: 1.5%;display: none;" id="application-error-box"></div>
										<div class="mt-3">
											<div class="d-flex justify-content-between align-items-center">
												<button class="btn btn-warning me-2" id="previous-step" disabled="true">Previous</button> 
												<div id="application-step-icons">
													<span class="application-step active"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
													<span class="application-step"></span>
												</div>
												<button class="btn btn-primary" id="next-step" data-type="new">Next</button>
											</div>
										</div>
								</div>
								<div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">Documents content</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>


        <!-- Instructions popup -->
		<div class="modal fade" id="application-instructions-modal" tabindex="-1" role="dialog" aria-labelledby="application-instructions-modal-lable" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="application-instructions-modal-lable">Application Instructions</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<ol>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
							<li class="mb-2">Instruction</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
        <!-- Core theme JS-->
        <!-- Use as a jQuery plugin -->

		<script src="build/js/intlTelInput.js"></script> 
        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="js/popper.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/alertify.min.js"></script>
        <script type="text/javascript" src="js/datatables.js"></script>
        <script type="text/javascript" src="js/incipit/incipit.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/application.js"></script>
    </body>
</html>
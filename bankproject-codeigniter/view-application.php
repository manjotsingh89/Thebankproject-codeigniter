<?php include("_header.php");

if(!isset($_GET['id'])) header("location: index.php"); 


$e    = getApplicationDetails($conn, $_GET['id']);
$el   = getApplicationEducationalDetails($conn, $_GET['id']);
$ens  = getApplicationNationalServices($conn, $_GET['id']);
$eeh  = getApplicationEmploymentHistory($conn, $_GET['id']);
$er   = getApplicationReferences($conn, $_GET['id']);
$res = query("SELECT * FROM EmployeesList WHERE UserTypeID != 1 AND Status = 'active' ");
$employees = [];
while ($row = sqlsrv_fetch_array($res, 2)) {
	$employees[] = $row;
}
	
if(!$e){
	header("Location:index.php");
}

// echo json_encode($e);

?>

<div class="header">
	<!-- hidden employee id -->
	<input readonly="true" type="hidden" value="<?=$_GET['id']?>" id="EmployeeID">

	<div class="row">
		<div class="col-4">
			<h2>Application Details</h2>
		</div>
		<div class="col-4 text-center">
		</div>
		<?php if(($_SESSION['Interviewer'] == 1 || $_SESSION['UserTypeName'] == 'administrator') && $e['ApplicationStatus'] == 'approved' && $e['InterviewCount'] < 3) { ?>
		<div class="col-4">
			<a href="core/set-interview.php" id="create-interview" data-id="<?=$_GET['id']?>" class="btn btn-md btn-primary btn-rounded float-end">Interview Now</a>
		</div>
		<?php } ?>

		<?php if($_SESSION['UserTypeName'] == 'administrator' && $e['ApplicationStatus'] == 'pending approval') { ?>
		<div class="col-4">
			<button data-status="approve" data-id="<?=$e['ApplicationID']?>" class='btn btn-success btn-sm app-status'>Approve</button>
			<a href="core/update-application-status.php?ApplicationID=<?=$e['ApplicationID']?>" class='btn btn-danger btn-sm'>Reject</a>
		</div>
		<?php } ?>

	</div>
	<div class="bg-white p-3 my-4">
	<!-- <ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#forms-controller" type="button" role="tab" aria-controls="home" aria-selected="true">General Info</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Documents</button>
		</li>
	</ul> -->
		<div class="tab-content mt-4">
			<div class="tab-pane active" id="forms-controller" role="tabpanel" aria-labelledby="home-tab">
				<!--personal details section-->
				<form id="form-employee-1">
					<div class="form-group row mb-2">
						<label for="PositionApplied" class="col-sm-2 col-form-label">Position applied:</label>
						<div class="col-sm-10">
							<input readonly="true" type="text" class="form-control" id="PositionApplied" name="PositionApplied" value="<?=$e['PositionApplied']?>" required>
							<sub>(Please fill up this form correctly and accurately.  All information will be kept in confidence)</sub>
						</div>
					</div>
					<h5 class="my-4">
						<center>Personal Particulars</center>
					</h5>
					<div class="form-group row mb-2">
						<label for="FirstName" class="col-sm-2 col-form-label">First Name:</label>
						<div class="col-sm-3">
							<input readonly="true" type="text" class="form-control" id="FirstName" name="FirstName" value="<?=$e['FirstName']?>">
						</div>
						<label for="LastName" class="col-sm-1 col-form-label">Last Name:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="LastName" name="LastName" value="<?=$e['LastName']?>">
						</div>
						<label for="Email" class="col-sm-1 col-form-label">Personal Email:</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="Email" name="Email" readonly value="<?=$e['Email']?>">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="Address" class="col-sm-2 col-form-label">Address:</label>
						<div class="col-sm-3">
							<input readonly="true" type="text" class="form-control" id="address" name="Address" value="<?=$e['Address']?>">
						</div>
						<label for="Address" class="col-sm-1 col-form-label">City:</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="city" name="City" readonly value="<?=$e['City']?>">
						</div>
						<label for="Address" class="col-sm-1 col-form-label">State:</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="state" name="State" readonly value="<?=$e['State']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="TelephoneNumber" class="col-sm-2 col-form-label">Tel:</label>
						<div class="col-sm-2">
							<input readonly="true" type="tel" class="form-control phone" id="TelephoneNumber" name="TelephoneNumber" value="+<?=$e['TelephoneNumber']?>">
						</div>
						<label for="PagerNumber" class="col-sm-2 col-form-label">H/p No/Pager:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="PagerNumber" name="PagerNumber" value="<?=$e['PagerNumber']?>">
						</div>
						<label for="Email" class="col-sm-1 col-form-label">Company Email:</label>
						<div class="col-sm-3">
							<input type="text" readonly class="form-control" id="CompanyEmail" name="CompanyEmail" readonly value="<?=$e['CompanyEmail']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="PassportNumber" class="col-sm-2 col-form-label">NRIC No (Colour)/Passport No:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="PassportNumber" name="PassportNumber" value="<?=$e['PassportNumber']?>">
						</div>
						<label for="Citizenship" class="col-sm-2 col-form-label">Citizenship:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="Citizenship" name="Citizenship" value="<?=$e['Citizenship']?>">
						</div>
						
					</div>
					
					<div class="form-group row mb-2">
					<label for="Gender" class="col-sm-2 col-form-label">Gender:</label>
						<div class="col-sm-4">
							<select class="form-control" id="Gender" name="Gender">
								<option value="Male" <?=$e['Gender'] == "Male" ? 'selected="selected"' : ""?>>Male</option>
								<option value="Female" <?=$e['Gender'] == "Female" ? 'selected="selected"' : ""?>>Female</option>
							</select>
						</div>
						
						</div>

					<hr>

					<div class="form-group row mb-2">
						<label for="SpouseName" class="col-sm-2 col-form-label">If marries, State spouse's Name:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="SpouseName" name="SpouseName" value="<?=$e['SpouseName']?>">
						</div>
						<label for="Occupation" class="col-sm-2 col-form-label">Occupation:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="Occupation" name="Occupation" value="<?=$e['Occupation']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="NumberofChildren" class="col-sm-2 col-form-label">No. of Children:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="NumberofChildren" name="NumberofChildren" value="<?=$e['NumberofChildren']?>">
						</div>
						<label for="AgeRange" class="col-sm-2 col-form-label">Age Range:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="AgeRange" name="AgeRange" value="<?=$e['AgeRange']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="NextOfKinName" class="col-sm-2 col-form-label">Next-of-kin:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="NextOfKinName" name="NextOfKinName" value="<?=$e['NextOfKinName']?>">
						</div>
						<label for="Relationship" class="col-sm-2 col-form-label">Relationship:</label>
						<div class="col-sm-4"><input readonly="true" type="text" class="form-control" id="Relationship" name="Relationship" value="<?=$e['Relationship']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="SpouseNAddress" class="col-sm-2 col-form-label">Address:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="SpouseAddress" name="SpouseAddress" value="<?=$e['SpouseAddress']?>">
						</div>
						<label for="SpouseTelephoneNumber" class="col-sm-2 col-form-label">Tel No:</label>
						<div class="col-sm-4"><input readonly="true" type="text" class="form-control" id="SpouseTelephoneNumber" name="SpouseTelephoneNumber" value="<?=$e['SpouseTelephoneNumber']?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="SpouseNAddress" class="col-sm-3 col-form-label">Are You Serving Bond With Your Present Employer?</label>
						<div class="col-sm-3">
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="PresentEmployerBond" id="PresentEmployerBondYes" value="yes" <?=$e['PresentEmployerBond'] == "yes" ? 'checked' : ""?>>
								<label class="form-check-label" for="PresentEmployerBondYes">Yes</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="PresentEmployerBond" id="PresentEmployerBondNo" value="no" <?=$e['PresentEmployerBond'] == "no" ? 'checked' : ""?>>
								<label class="form-check-label" for="PresentEmployerBondNo">No</label>
							</div>
						</div>
					</div>

					<hr>

					<h6 class="mb-4">TAB Global is an equal opportunity employer for people of all background. The following social information is intended only to give us a sense of your origins and general background. </h6>

					<div class="form-group row mb-2">
						<label for="Birthday" class="col-sm-2 col-form-label">Date of Birth:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="Birthday" name="Birthday" value="<?=date_format(date_create($e['Birthday']), "d-M-Y")?>">
						</div>
						<label for="BirthPlace" class="col-sm-2 col-form-label">Birth Place:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="BirthCity" name="BirthCity" value="<?=$e['BirthCity']?>">
						</div>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="BirthCountry" name="BirthCountry" value="<?=$e['BirthCountry']?>">
						</div>
						
						
						
					</div>
					
					<div class="form-group row mb-2">
					
					<label for="Dialect" class="col-sm-2 col-form-label">Ethnicity/Dialect:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="Dialect" name="Dialect" value="<?=$e['Dialect']?>">
						</div>
						
					<label for="Religion" class="col-sm-2 col-form-label">Religion:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="Religion" name="Religion" value="<?=$e['Religion']?>">
						</div>
						
						</div>

					<div class="form-group row mb-2">
						
						<label for="MaritalStatus" class="col-sm-2 col-form-label">Marital Status:</label>
						<div class="col-sm-10">
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="MaritalStatus" id="MaritalStatusSingle" value="Single" <?=$e['MaritalStatus'] == "Single" ? 'checked' : ""?>>
								<label class="form-check-label" for="MaritalStatusSingle">Single</label>
							</div>
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="MaritalStatus" id="MaritalStatusMarried" value="Married" <?=$e['MaritalStatus'] == "Married" ? 'checked' : ""?>>
								<label class="form-check-label" for="MaritalStatusMarried">Married</label>
							</div>
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="MaritalStatus" id="MaritalStatusSeparated" value="Separated" <?=$e['MaritalStatus'] == "Separated" ? 'checked' : ""?>>
								<label class="form-check-label" for="MaritalStatusSeparated">Separated</label>
							</div>
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="MaritalStatus" id="MaritalStatusDivorced" value="Divorced" <?=$e['MaritalStatus'] == "Divorced" ? 'checked' : ""?>>
								<label class="form-check-label" for="MaritalStatusDivorced">Divorced</label>
							</div>
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="MaritalStatus" id="MaritalStatusWidowed" value="Widowed" <?=$e['MaritalStatus'] == "Widowed" ? 'checked' : ""?>>
								<label class="form-check-label" for="MaritalStatusWidowed">Widowed</label>
							</div>
						</div>
					</div>

					<hr>

					<!-- <div class="form-group row mb-2">
					</div> -->
					<div class="form-group row mb-2">
						<label for="InterviewPosition" class="col-sm-2 col-form-label">Position for which you are being interviewed:</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="InterviewPosition" name="InterviewPosition" value="<?=$e['InterviewPosition']?>" readonly>
						</div>
						<!-- <label for="PositionDesired" class="col-sm-2 col-form-label">Position Desired:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="PositionDesired" name="PositionDesired" value="<?=$e['PositionDesired']?>">
						</div> -->
						<label for="DateAvailable" class="col-sm-2 col-form-label">Date Available:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="DateAvailable" name="DateAvailable" value="<?=date_format(date_create($e['DateAvailable']), "d-M-Y")?>">
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="PositionQualified" class="col-sm-2 col-form-label">Other Positions:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="PositionQualified" name="PositionQualified" value="<?=$e['PositionQualified']?>">
						</div>
						<label for="PreviouslyEmployedToCompany" class="col-sm-2 col-form-label">Previously employed by/applied to join Company:</label>
						<div class="col-sm-4">
							<div class="form-check form-check-inline mt-2">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyYes" value="yes" <?=$e['PreviouslyEmployedToCompany'] == "yes" ? 'checked' : ""?>>
								<label class="form-check-label" for="PreviouslyEmployedToCompanyYes">Yes</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" disabled="true"readonly="true" name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyNo" value="no" <?=$e['PreviouslyEmployedToCompany'] == "no" ? 'checked' : ""?>>
								<label class="form-check-label" for="PreviouslyEmployedToCompanyNo">No</label>
							</div>
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="RelativesInCompany" class="col-sm-2 col-form-label">Relatives/Friends in Company:</label>
						<div class="col-sm-4">
							<input readonly="true" type="text" class="form-control" id="RelativesInCompany" name="RelativesInCompany" value="<?=$e['RelativesInCompany']?>">
						</div>						
					</div>
				</form>

				<!--educational details section-->

				<h5 class="my-4">
					<center>Educational Details</center>
				</h5>
				<form id="form-employee-2">
					<table width="100%" class="table table-bordered">
						<tr>
							<td align="center">Name of School</td>
							<td align="center">Address</td>
							<td align="center">Level</td>
							<td align="center">From</td>
							<td align="center">To</td>
							<td align="center">Did You Graduate</td>
							<td align="center">Details</td>
						</tr>
						<?php $ctr=0; while($row = sqlsrv_fetch_array($el, SQLSRV_FETCH_ASSOC) ){ ?>
						<tr>
							<td>
								<input readonly="true" type="text" name="SchoolName[]" class="form-control" value="<?=$row['SchoolName']?>">
								<input readonly="true" type="hidden" name="EmployeeID[]" class="form-control" value="<?=$e['EmployeeID']?>">
								<input readonly="true" type="hidden" name="EmployeeEducationalDetailsID[]" class="form-control" value="<?=$row['EmployeeEducationalDetailsID']?>">
							</td>
							<td>
								<input readonly="true" type="text" name="SchoolCity[]" class="form-control" value="<?=$row['SchoolCity']?>">
								<input readonly="true" type="text" name="SchoolCountry[]" class="form-control" value="<?=$row['SchoolCountry']?>">
							</td>
							<td><?=$row['LevelName']?></td>
							<td><input readonly="true" type="text" name="AttendedFrom[]" class="form-control" value="<?=date_format(date_create($row['AttendedFrom']), "M-Y")?>"></td>
							<td><input readonly="true" type="text" name="AttendedTo[]" class="form-control" value="<?=date_format(date_create($row['AttendedTo']), "M-Y")?>"></td>
							<td>
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" disabled="true"readonly="true" name="IsGraduated_<?=$ctr?>" id="IsGraduatedYes_<?=$ctr?>" value="yes" <?=$row['IsGraduated'] == 'yes' ?'checked' : ''?> >
									<label class="form-check-label" for="IsGraduatedYes_<?=$ctr?>" selected>Yes</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" disabled="true"readonly="true" name="IsGraduated_<?=$ctr?>" id="IsGraduatedNo_<?=$ctr?>" value="no" <?=$row['IsGraduated'] == 'no' ?'checked' : ''?>>
									<label class="form-check-label" for="IsGraduatedNo_<?=$ctr?>">No</label>
								</div>
							</td>
							<td>
								<input readonly="true" type="text" name="SchoolDetails[]" class="form-control" value="<?=$row['SchoolDetails']?>">
							</td>
						</tr>
						<?php $ctr++; } ?>
					</table>
				</form>
				<form id="form-employee-2half">
					<input readonly="true" type="hidden" name="eID" class="form-control" value="<?=$e['EmployeeID']?>">
					<div class="form-group my-2">
						<label for="FurtherEducation" class="col-form-label">If you Plan Further Education, Please Explain:</label>
						<textarea type="text" class="form-control" id="FurtherEducation" name="FurtherEducation"><?=$e['FurtherEducation']?></textarea>
					</div>

					<div class="form-group mb-2">
						<label for="TrainingSkills" class="col-form-label">Other Training Or Skills (Factory Or Office Machines Operated, Special Courses etc):</label>
						<textarea type="text" class="form-control" id="TrainingSkills" name="TrainingSkills"><?=$e['TrainingSkills']?></textarea>
					</div>

					<div class="form-group mb-2">
						<label for="FullName" class="col-form-label">Hobbies:</label>
						<textarea type="text" class="form-control" id="Hobbies" name="Hobbies"><?=$e['Hobbies']?></textarea>
					</div>
				</form>

				<!--national services section-->
				<form id="form-employee-3">
					<h5 class="my-4">
						<center>National Services</center>
					</h5>
					<table width="100%" class="table table-bordered">
						<?php if ($ens!=null && $ens['Applicable'] == 1) { ?>
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
								<input readonly="true" type="text" id="FullTime" name="FullTime" class="form-control" value="<?=$ens['FullTime']?>">
							</td>
							<td>
								<input readonly="true" type="date" id="ServedFrom" name="ServedFrom" class="form-control" value="<?=$ens['ServedFrom']?>">
							</td>
							<td><input readonly="true" type="date" id="ServedTo" name="ServedTo" class="form-control" value="<?=$ens['ServedTo']?>">
							</td>
							<td>
								<input readonly="true" type="text" id="DischargeType" name="DischargeType" class="form-control" value="<?=$ens['DischargeType']?>">
							</td>
							<td>
								<input readonly="true" type="text" id="Vocation" name="Vocation" class="form-control" value="<?=$ens['Vocation']?>">
							</td>
							<td>
								<input readonly="true" type="text" id="NextInCampTraining" name="NextInCampTraining" class="form-control" value="<?=$ens['NextInCampTraining']?>">
							</td>
							<td>
								<input readonly="true" type="text" id="LastRank" name="LastRank" class="form-control" value="<?=$ens['LastRank']?>">
							</td>
						</tr>
					</table>
					<div class="form-group row my-2">
						<label for="ServiceSchools" class="col-sm-2 col-form-label">Service Schools Or Special Experience:</label>
						<div class="col-sm-10">
							<input readonly="true" type="text" class="form-control" id="ServiceSchools" name="ServiceSchools" value="<?=$ens['ServiceSchools']?>">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="SEPartTime" class="col-sm-2 col-form-label">PART TIME:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SEPartTime" name="SEPartTime" value="<?=$ens['SEPartTime']?>">
						</div>
						<label for="SEUnitAttached" class="col-sm-2 col-form-label">Unit Attached to:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SEUnitAttached" name="SEUnitAttached" value="<?=$ens['SEUnitAttached']?>">
						</div>
						<label for="SEDurLiability" class="col-sm-2 col-form-label">Duration of Liability:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SEDurLiability" name="SEDurLiability" value="<?=$ens['SEDurLiability']?>">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="SEFreqDuties" class="col-sm-2 col-form-label">Frequency of Duties:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SEFreqDuties" name="SEFreqDuties" value="<?=$ens['SEFreqDuties']?>">
						</div>
						<label for="SELastRank" class="col-sm-2 col-form-label">Last Rank:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SELastRank" name="SELastRank" value="<?=$ens['SELastRank']?>">
						</div>
						<label for="SEStatus" class="col-sm-2 col-form-label">Exempted/Defered/Awaiting:</label>
						<div class="col-sm-2">
							<input readonly="true" type="text" class="form-control" id="SEStatus" name="SEStatus" value="<?=$ens['SEStatus']?>">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="SEPeriod" class="col-sm-2 col-form-label">Period/Date of Registration:</label>
						<div class="col-sm-2">
							<input readonly="true" type="date" class="form-control" id="SEPeriod" name="SEPeriod" value="<?=$ens['SEPeriod']?>">
						</div>
						<label for="SEStatusReason" class="col-sm-2 col-form-label">Reason(s):</label>
						<div class="col-sm-6">
							<input readonly="true" type="text" class="form-control" id="SEStatusReason" name="SEStatusReason" value="<?=$ens['SEStatusReason']?>">
						</div>
					</div>
				</form>
					<?php } else { ?>
						<div>Not Applicable</div>
					<?php } ?>

				<!--employment history section-->
				<form id="form-employee-4">
					<h5 class="my-4">
						<center>Employment History</center>
					</h5>
					<table width="100%" class="table table-bordered">
						<tr>
							<td align="center">Name of Employer</td>
							<td align="center">Address of Employer</td>
							<td align="center">Position</td>
							<td align="center">From</td>
							<td align="center">To</td>
							<td align="center">Salary</td>
							<td align="center">Reason for Leaving</td>
						</tr>
						<?php $ctr = 0; while($row = sqlsrv_fetch_array($eeh, SQLSRV_FETCH_ASSOC) ){ ?>
						<tr>
							<td>
								<input readonly="true" type="text" name="EmployerName[]" class="form-control" value="<?=$row['EmployerName']?>">
								<input readonly="true" type="hidden" name="EmployeeEmploymentHistoryID[]" class="form-control" value="<?=$row['EmployeeEmploymentHistoryID']?>">
							</td>
							<td>
								<input readonly="true" type="text" name="EmployerCity[]" class="form-control" value="<?=$row['EmployerCity']?>">
								<input readonly="true" type="text" name="EmployerCountry[]" class="form-control" value="<?=$row['EmployerCountry']?>">
							</td>
							<td><input readonly="true" type="text" name="Position[]" class="form-control" value="<?=$row['Position']?>"></td>
							<td><input readonly="true" type="text" name="EmploymentFrom[]" class="form-control" value="<?=date_format(date_create($row['EmploymentFrom']), "M-Y")?>"></td>
							<td><input readonly="true" type="text" name="EmploymentTo[]" class="form-control" value="<?=date_format(date_create($row['EmploymentTo']), "M-Y")?>"></td>
							<td>
								<input readonly="true" type="text" name="Salary[]" class="form-control" value="<?=$row['Salary']?>">
							</td>
							<td>
								<input readonly="true" type="text" name="ReasonForLeaving[]" class="form-control" value="<?=$row['ReasonForLeaving']?>">
							</td>
						</tr>
						<?php } ?>
					</table>
				</form>

				<!--languages section-->
				<form id="form-employee-5">
					<h5 class="my-4">
						<center>Languages</center>
					</h5>
					<div class="form-group my-2">
						<label for="LanguageSpoken">Language Spoken:</label>
						<input readonly="true" type="text" class="form-control" id="LanguageSpoken" name="LanguageSpoken" value="<?=$e['LanguageSpoken']?>">
					</div>
					<div class="form-group my-2">
						<label for="LanguageWritten">Language Written:</label>
						<input readonly="true" type="text" class="form-control" id="LanguageWritten" name="LanguageWritten" value="<?=$e['LanguageWritten']?>">
					</div>
				</form>

				<!--medical history section-->
				<form id="form-employee-6">
					<h5 class="my-4">
						<center>Medical History</center>
					</h5>
					<div class="form-group my-2">
						<label for="PhysicalDisabledDetails">Any Physical Disability:   No / Yes, Please Specify:</label>
						<input readonly="true" type="text" class="form-control" id="PhysicalDisabledDetails" name="PhysicalDisabledDetails" value="<?=$e['PhysicalDisabledDetails']?>">
					</div>
					<div class="form-group my-2">
						<label for="MajorIllnessDetails">Any Major Illiness / Accident in Last Six Months?   No / Yes, Please Specify:</label>
						<input readonly="true" type="text" class="form-control" id="MajorIllnessDetails" name="MajorIllnessDetails" value="<?=$e['MajorIllnessDetails']?>">
					</div>
				</form>

				<!--references section-->
				<form id="form-employee-7">
					<h5 class="my-4">
						<center>References</center>
					</h5><table width="100%" class="table table-bordered">
						<tr>
							<td align="center">Name</td>
							<td align="center">Association</td>
							<td align="center">Email</td>
							<td align="center">Mobile</td>
							<td align="center">Years Known</td>
						</tr>
						<?php $ctr = 0; while($row = sqlsrv_fetch_array($er, SQLSRV_FETCH_ASSOC) ){ ?>
						<tr>
							<td>
								<input readonly="true" type="text" name="ReferenceName[]" class="form-control" value="<?=$row['ReferenceName']?>">
								<input readonly="true" type="hidden" name="EmployeeReferencesID[]" class="form-control" value="<?=$row['EmployeeReferencesID']?>">
							</td>
							<td>
								<textarea type="text" name="Association[]" class="form-control" readonly><?=$row['Association']?></textarea>
							</td>
							<td><input readonly="true" type="text" name="ReferenceEmail[]" class="form-control" value="<?=$row['ReferenceEmail']?>"></td>
							<td><input readonly="true" type="text" name="ReferenceMobile[]" class="form-control phone" value="<?=$row['ReferenceMobile']?>"></td>
							<td><input readonly="true" type="number" name="YearsKnown[]" class="form-control" value="<?=$row['YearsKnown']?>"></td>
						</tr>
						<?php } ?>
					</table>
				</form>
			</div>
			<div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">Documents content</div>
		</div>
	</div>
</div>
<!-- Nav tabs -->
<!-- Tab panes -->
<?php include("_footer.php"); ?>
<script src="build/js/intlTelInput.js"></script> 
        <script type="text/javascript" src="js/application.js"></script>
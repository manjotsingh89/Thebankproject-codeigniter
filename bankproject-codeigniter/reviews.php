<?php
	if (!isset($_GET['reviewId']) || $_GET['reviewId'] == '') {
		header('location: index.php');
	}

	include("_header.php");
	include('core/reviews/review-functions.php');
	$reviewDetails = getReviewDetails($conn, $_GET['reviewId']);

	if (!$reviewDetails) {
		die('You\'re not allowed to view this review');
	}

	$review = (object) $reviewDetails['review'];
	$employee = (object) $reviewDetails['employee'];
	$job = (object) $reviewDetails['job'];
	$reviewRequirements = (object) $reviewDetails['reviewRequirements'];
	$submitted = trim(strtolower($review->ReviewStatusName)) != 'draft';
	$reviewee = trim(strtolower($review->RevieweeID)) == $_SESSION['EmployeeID'];
	$kpiCategoriesRes = sqlsrv_query($conn, "SELECT * FROM KPICategories ORDER BY KPICategoryID ASC");
	$frequenciesRes = sqlsrv_query($conn, "SELECT [FreqID], [FreqName] FROM [dbo].[Frequencies] ORDER BY FreqID ASC");
	
	while ($frequency = sqlsrv_fetch_array($frequenciesRes, 2)) {
		$frequencies[] = $frequency;
	}
	
	while ($category = sqlsrv_fetch_array($kpiCategoriesRes, 2)) {
		$kpiCategories[] = $category;
	}

	$confirmation = trim(strtolower($review->ReviewTypeName)) == 'confirmation';
	$toal_ratings = $confirmation ? 5 : 3;

	$completed = false;
	if ($submitted) {
		$completed = trim(strtolower($review->ReviewStatusName)) == 'completed';
		$reviewAnswers = getReviewAnswers($conn, $review->ReviewID, $confirmation);
		$reviewRequirementScores = getReviewRequirementScores($conn, $review->ReviewID);
	}

	if ($completed) {
		$salary_res = query("SELECT TOP(1) * FROM EmployeeSalaries WHERE EmployeeID = " . $employee->EmployeeID . " AND ReviewID = " . $review->ReviewID . ";");
		$salary = sqlsrv_fetch_array($salary_res, 2);
	} else {
		$salary = getEmployeeSalary($employee->EmployeeID);
	}

	if ($confirmation) {
		$periodicQuestions = [
			'Q1' => [
				'title' => 'HOW WOULD YOU RATE YOUR SATISFACTION LEVEL ON YOUR PROBATIONARY PERIOD  1=POOR  5= VERY GOOD',
				'questions' => [
					'a' => 'In general, I enjoy working here<br/><i>(1= I should be working somewhere else 5= I like being here)</i>',
					'b' => 'My specific duties are within the career I want for myself<br/><i>(1= I should be working somewhere else 5=yes, this is my career for the foreseeable future)</i>',
					'c' => 'I enjoyed the projects/job given to me',
					'd' => 'I enjoyed the training given to me to prepare me for my work',
					'e' => 'I enjoyed the help given to me by my colleagues to do my work',
					'f' => 'I am happy with the help given to me by my supervisor / management to do my work',
					'g' => 'This Company is well organized',
					'h' => 'I can see myself being committed to the Company for the foreseeable future',
				],
				'additional_questions' => [
					'Q2' => 'Please provide additional comments or clarifications to the points you rated above.',
				]
			],
			'Q3' => [
				'title' => 'HOW WOULD YOU RATE YOUR FOLLOWING BASIC SKILLS   1=POOR  5= VERY GOOD',
				'questions' => [
					'a' => 'English Language (Written and Verbal)',
					'b' => 'Your Main Working Language (Written and Verbal)',
					'c' => 'Communication Skills (with colleagues)',
					'd' => 'Communication Skills (with Clients)',
					'e' => 'Telephone communication skills',
					'f' => 'Team working skills (as a team)',
					'g' => 'Basic IT skills (Combination of Word/Excel or Access/Powerpoint)',
					'h' => 'Project Management Skills',
					'i' => 'Cost management skills',
					'j' => 'Process skills (following procedures)',
				],
			],
			'Q4' => [
				'title' => 'HOW WOULD YOU RATE YOUR UNDERSTANDING OF THIS COMPANY&#39;S BUSINESS?  1=POOR  5= VERY GOOD',
				'questions' => [
					'a' => 'I do understand the Company&#39;s Business',
					'b' => 'I can explain the Company&#39;s Business to Clients',
				],
			],
			'Q5' => [
				'title' => 'HOW WOULD YOU RATE YOUR PERSONAL ATTRIBUTES  1=POOR  5= VERY GOOD',
				'questions' => [
					'a' => 'I am Achievement Driven',
					'b' => 'I tend to think Analytically (ie. I consider all factors before acting)',
					'c' => 'I tend to think Strategically (ie. Min input, very focused on getting the work done)',
					'd' => 'I am Assertive / Decisive',
					'e' => 'I am Inquisitive about the industry I cover',
					'f' => 'I am Inquisitive about the people I meet',
					'g' => 'I have a “Can Do” Attitude to get my job done',
					'h' => 'I have Concern for Order and Quality',
					'i' => 'I am very focused on making the Customer Happy (External/Internal)',
					'j' => 'I insist on a high level of integrity in my work',
					'k' => 'I have Respect for people who are different from me - "Cross-Cultural Awareness"',
					'l' => 'I am able to relate to clients at all levels',
					'm' => 'I am confident in the job/projects given to me',
					'n' => 'I can tolerate a high level of stress in the job given to me',
					'o' => 'Punctuality and time management',
				],
			],
			'Q6' => [
				'title' => 'SALES AND BUSINESS DEVELOPMENT SKILLS  1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'Potential business development/ Salesmanship - "Impact"',
					'b' => 'Written and proposal writing skills',
					'c' => 'Systematic pursuit of clients',
					'd' => 'Understanding of company&#39;s business',
					'e' => 'Understanding of prospects and clients',
					'f' => 'Closing sales skills',
					'g' => 'Ability to develop marketing collaterals (written or otherwise)',
					'h' => 'Ability to create value for clients',
					'i' => 'Ability to meet assigned budgets/goals',
				],
			],
			'Q7' => [
				'title' => 'RESEARCH SKILLS 1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'Language and communication (Verbal)',
					'b' => 'Written proposal writing skills',
					'c' => 'Resourcefulness in seeking information',
					'd' => 'Understanding of clients business',
					'e' => 'Conceptual skills',
					'f' => 'Interviewing skills',
					'g' => 'Desk research and data gathering',
					'h' => 'Presentation design skills',
					'i' => 'Presentation skills',
				],
			],
			'Q8' => [
				'title' => 'EDITORIAL SKILLS   1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'Language and communication (Verbal)',
					'b' => 'Journalistic writing skills',
					'c' => 'Sub-editing skills',
					'd' => 'Copy-editing / Editor skills',
					'e' => 'Layout and page design skills',
					'f' => 'Ability to identify and pursue news',
					'g' => 'Publisher skills – ability to put news to sales',
				],
			],
			'Q9' => [
				'title' => 'FORUM BUSINESS SKILLS  1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'Ability to identify topics and issues',
					'b' => 'Ability to interact with target audience',
					'c' => 'Collateral development skills',
					'd' => 'Event marketing skills',
					'e' => 'Event management skills',
					'f' => 'Stage management skills',
					'g' => 'Client and speakers management',
					'h' => 'Post event management skills',
				],
			],
			'Q10' => [
				'title' => 'ADMIN AND SUPPORT SKILLS 1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'General administrative skills',
					'b' => 'Secretarial skills',
					'c' => 'Office administration skills',
					'd' => 'Accounting and book keeping skills',
					'e' => 'Web-based support skills',
					'f' => 'Web-based design/communication skills',
					'g' => 'Professional IT and Programming Skills',
					'h' => 'Database and IT-based support skills',
					'i' => 'Systems design skills',
					'j' => 'User friendly conceptual skills',
					'k' => 'Internal customer (staff) support skills',
					'l' => 'External customer service',
				],
			],
			'Q11' => [
				'title' => 'MANAGEMENT/SPECIALIST SKILLS 1=VERY POOR  5=VERY GOOD',
				'questions' => [
					'a' => 'How do you think your peers regard you',
					'b' => 'How do you think your seniors regard you',
					'c' => 'Problem solving skills',
					'd' => 'Multi-tasking skills',
					'e' => 'Time and process management skills',
					'f' => 'People Management - "Providing Delegation &/or Empowerment"',
					'g' => 'Team building skills',
					'h' => 'Judgment skills',
					'i' => 'Communicator skills (listen/u-stand/respond/probe)',
					'j' => 'Influencing/Developing/Motivating Others',
					'k' => 'Flexibility',
					'l' => 'Initiative',
					'm' => 'Innovation',
					'n' => 'Relationship Builder',
					'o' => 'Cross-Functional Awareness',
				],
				'additional_questions' => [
					'Q12' => 'State three areas where you would like to receive greater guidance in your work:',
					'Q13' => 'State three areas where you would like some form of training in the next review period:',
				],
			]
		];
	} else {
		$periodicQuestions = [
			'Q3' => [
				'title' => 'How would you rate your satisfaction level in general matters during the previous period 1=poor 3 = very good',
				'questions' => [
					'a' => 'In general, I enjoy working here<br/><i>1= I should be working somewhere else 2= I like being here 3= I can&#39;t think of a better place</i>',
					'b' => 'My specific duties are within the career I want for myself<br/><i>1= I should be doing something else 3=yes, this is my career for the foreseeable future</i>',
					'c' => 'I enjoyed the projects/job given to me<br/><i>1= I should be doing something else 2= it&#39;s okay 3= I enjoy it</i>',
					'd' => 'I appreciate the training given to me to prepare me for my work<br/><i>1= need improvement 2=sufficient 3= very good</i>',
					'e' => 'I enjoy working with my colleagues<br/><i>1= issues to be sorted 2=yes 3= very good</i>',
					'f' => 'I am happy with my supervisor<br/><i>1= areas for improvement 2=yes 3= very good</i>',
					'g' => 'I am happy with the management of this company<br/><i>1= areas for improvement 2=yes 3= very good</i>',
					'h' => 'I can see myself being committed to the Company for the foreseeable future<br/><i>1= no 2=so far so good 3=yes</i>',
				],
			],
			'Q4' => [
				'title' => 'How would you rate your satisfaction level with your own personal skills in any one or several of the areas listed below (complete only the areas you think are relevant) during the previous period 1=poor 3 = very good',
				'questions' => [
					'a' => 'Project Management Skills (1= I will improve 2=happy 3=very good)',
					'b' => 'People Interaction and Teamwork Skills',
					'c' => 'Content Management Skills (esp Editorial/Research/Forums)',
					'd' => 'Office Finance and/or Administration Skills',
					'e' => 'Technology Management Skills',
					'f' => 'Marketing Collateral Development Skills',
					'g' => 'Managing your bosses ',
					'h' => 'Managing your subordinates',
					'i' => 'I think I understand the Company&#39;s business ',
				],
				'additional_questions' => [
					'Q5' => 'Please provide additional comments or clarifications to any of the points you rated before so as to help the Company improve and/or for the Company to help you better.',
					'Q6' => 'State up to three things you have learnt since the past review that you think makes you more valuable to the Company?',
				],
			],
		];
	}
?>
<div class="header">
	<div class="row">
		<div class="col-4">
			<h2>Employee Review</h2>
		</div>
		<div class="col-8">
			<div class="row">
				<div class="col-6">
					<div class="form-group row mb-2">
						<label class="col-sm-4 col-form-label">Employee Name:</label>
						<div class="col-sm-8 text-right">
							<input type="text" value="<?php echo $employee->FirstName . ' ' . $employee->LastName; ?>" class="form-control" readonly>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-4 col-form-label">Submission Date:</label>
						<div class="col-sm-8 text-right">
							<input type="datetime-local" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?> name="ReviewSubmissionDate" value="<?php echo str_replace(' ', 'T', $review->ReviewSubmissionDate) ?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="col-6">
					<div class="form-group row mb-2">
						<label class="col-sm-4 col-form-label">Current Position:</label>
						<div class="col-sm-8 text-right">
							<input type="text" value="" class="form-control" readonly>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-4 col-form-label">Completion Date:</label>
						<div class="col-sm-8 text-right">
							<input type="datetime-local" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?> name="ReviewCompletionDate" value="<?php echo str_replace(' ', 'T', $review->ReviewCompletionDate) ?>" class="form-control">		
						</div>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>
<div class="mt-3">
	<form id="review-form" action="core/reviews/review-submit.php" method="POST" data-review-type="<?=$confirmation ? 'confirmation':'periodic' ?>" data-type="<?=$submitted ? 'complete' : 'submit' ?>">
		<input type="hidden" name="ReviewID" value="<?=$_GET['reviewId']?>">
		<?php if (!$confirmation) { ?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Current
				<a href="#KPI-instructions" type="button" data-bs-toggle="modal" data-bs-target="#KPI-instructions">KPIs</a>
			</h3>
			<sub>Please rate your satisfaction level with your current KPI's. 1 = least satisfied <?=$toal_ratings?> = most satisfied.</sub>
			<div class="row">
				<?php if (count($reviewRequirements->kpis) > 0) { ?>
				<div class="col-12">
					<div class="row mb-1">
						<div class="col-9"><h6>KPI and associated Description</h6></div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
									<b><?= $i ?></b>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php foreach ($reviewRequirements->kpis as $key => $kpi) { ?>
					<div class="row mb-1">
						<div class="col-9"><?php echo '<b>'. ($key + 1) .': </b>' . $kpi['KPITitle'] ?></div>
						<input type="hidden" name="requirements[]" value="<?=$kpi['ReviewRequirement1ID']?>">
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input type="radio" value="<?=$i?>" <?= $submitted && $reviewRequirementScores[$kpi['ReviewRequirement1ID']]["Score"] == $i ? 'checked' : "" ?> required name="score-<?=$kpi['ReviewRequirement1ID']?>">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } else {?>
				<div class="col-12">
					<p class="text-warning">You do not have any KPIs to rate.</p>
				</div>
				<?php } ?>
				<div class="col-12 mt-4">
					<label>Please state how you would like for your KPIs to be modified for the next review period?</label>
					<textarea rows="5" name="Q1" <?=$submitted ? "readonly" : ""?> required class="form-control border border-secondary" placeholder="Write your answer here"><?= $submitted ? $reviewAnswers["Q1"] : "" ?></textarea>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Current Projects
			</h3>
			<div class="row">
			<?php if (count($reviewRequirements->projects) > 0) { ?>
				<div class="col-12">
					<div class="row mb-1">
						<div class="col-9">
							<h6>Project and any financial or quantifiable achievements</h6>
							<sub>Please rate your satisfaction level with your current Projects. 1 = least satisfied <?=$toal_ratings?> = most satisfied.</sub>
						</div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<b><?= $i ?></b>
						</div>
							<?php } ?>
						</div>
					</div>
					<?php foreach ($reviewRequirements->projects as $key => $project) { ?>
					<div class="row mb-1">
						<div class="col-9"><?php echo '<b>'. ($key + 1) .': </b>' . $project['ProjectName'] ?></div>
						<input type="hidden" name="requirements[]" value="<?=$project['ReviewRequirement1ID']?>">
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input type="radio" value="<?=$i?>" <?= $submitted && $reviewRequirementScores[$project['ReviewRequirement1ID']]["Score"] == $i ? "checked" : "" ?> required name="score-<?php echo $project['ReviewRequirement1ID']?>">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } else {?>
				<div class="col-12">
					<p class="text-warning">You do not have any projects to rate.</p>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Current Duties
			</h3>
			<div class="row">
				<?php if (count($reviewRequirements->duties) > 0) { ?>
				<div class="col-12">
					<div class="row mb-1">
						<div class="col-9"><h6>Duties</h6><sub>Please rate your satisfaction level with your current Duties. 1 = least satisfied <?=$toal_ratings?> = most satisfied.</sub></div>
						<div class="col-3 d-flex justify-content-between align-items-center">
						<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
							<div class="b<?php echo $i; ?>">
						<b><?= $i ?></b>
					</div>
						<?php } ?>
						</div>
					</div>
					<?php foreach ($reviewRequirements->duties as $key => $duty) { ?>
					<div class="row mb-1">
						<div class="col-9"><?php echo '<b>'. ($key + 1) .': </b>' . $duty['DutyName'] ?> </div>
						<input type="hidden" name="requirements[]" value="<?=$duty['ReviewRequirement1ID']?>">
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input type="radio" value="<?=$i?>" <?= $submitted && $reviewRequirementScores[$duty['ReviewRequirement1ID']]["Score"] == $i ? 'checked' : "" ?> required name="score-<?=$duty['ReviewRequirement1ID']?>">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } else {?>
				<div class="col-12">
					<p class="text-warning">You do not have any duties to rate.</p>
				</div>
				<?php } 
				if (!$confirmation) { ?>
				<div class="col-12 mt-4">
					<label>Please provide additional comments or clarifications to any of the points you rated above so as to help the Company improve and/or for the Company to help you better.</label>
					<textarea rows="5" name="Q2" required <?=$submitted ? "readonly" : ""?> class="form-control border border-secondary" placeholder="Write your answer here"><?= $submitted ? $reviewAnswers["Q2"] : "" ?></textarea>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php foreach ($periodicQuestions as $Qno => $Q) { ?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<div class="row">
				<div class="col-12">
					<div class="row mb-1">
						<div class="col-9"><h6><?= $Q['title']; ?></h6></div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
								<b><?= $i ?></b>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php foreach ($Q['questions'] as $qno => $question) { ?>
					<div class="row mb-1 border p-1 bg-white rounded">
						<div class="col-9"><?php echo '<b>'. $qno .': </b>' . $question ?> </div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input required type="radio" value="<?=$i?>" <?= $submitted && $reviewAnswers[$Qno . $qno] == $i ? 'checked' : "" ?> name="<?= $Qno . $qno; ?>">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php if (isset($Q['additional_questions'])) {
					foreach ($Q['additional_questions'] as $q => $question) {
				?>
				<div class="col-12 mt-4">
					<label><?= $question ?></label>
					<textarea rows="5" required name="<?=$q?>" <?=$submitted ? "readonly" : ""?> class="form-control border border-secondary" placeholder="Write your answer here"><?= $submitted ? $reviewAnswers[$q] : "" ?></textarea>
				</div>
				<?php }} ?>
			</div>
		</div>
		<?php } 
		if ($submitted) {
			$reviewRequirements->kpis = getReviewRequirements($conn, $review->ReviewID, "kpi");
			$reviewRequirements->projects = getReviewRequirements($conn, $review->ReviewID, "project");
		}
		?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Projections and Next Phase Negotiation (Step 1)
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-kpis">
					<p>Based on your achievements since your last review, please state up to five quantifiable Key Performance Indicators (KPIs) you would like to be set and be measured on for the next review session. Please take into account KPIs that can give you more responsibilities and make a case for a promotion, if appropriate. </p>
					<div class="d-flex justify-content-between align-items-center mb-1">
						<h3>KPIs</h3>
						<a class="btn btn-sm btn-primary <?= $completed || ($submitted && $reviewee) ? ' d-none' : '' ?>" data-bs-toggle="modal" data-bs-target="#KPIModal">
							<i class="fas fa-plus"></i> Add KPI
						</a>
					</div>
					<?php foreach ($reviewRequirements->kpis as $key => $kpi) { ?>
					<div class="row mb-1" class="review-form-kpi" id="job-req-kpi-<?=$kpi['KPIID']?>">
						<div class="col-12 input-group">
							<input type="hidden" name="KPIID[]" value="<?= $kpi['KPIID'] ?>">
							<input readonly type="text" class="form-control" name="KPITitle[]" value="<?php echo $kpi['KPITitle'] ?>">
							<div class="input-group-append">
								<input type="number" name="KPITargetNum[]" class="form-control rounded-0" required value="<?= $kpi['KPITargetNum'] ?>" placeholder="KPI Target Number" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?>>
							</div>
							<div class="input-group-append">
								<select name="KPITargetFreq[]" class="form-control rounded-0" required <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?>>
									<?php foreach ($frequencies as $frequency) { ?>
									<option value="<?= $frequency['FreqID'] ?>" <?= $frequency['FreqID'] == $kpi['TargetNumFreqID'] ? 'selected' : ($completed || ($submitted && $reviewee) ? 'disabled' : '') ?>><?= $frequency['FreqName'] ?></option>
									<?php } ?>
								</select>
							</div>
							<?php if (!$completed && (($reviewee && !$submitted) || !$reviewee)) { ?>
							<div class="input-group-append">
								<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqKpi(<?= $kpi['KPIID'] ?>)"><i class="fas fa-multiply"></i></span>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Projections and Next Phase Negotiation (Step 2)
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-projects">
					<p>Identify a maximum of eight achievable projects (eg. conferences, reports, projects) that you would like to work on between now and the next review (normally in one year as agreed with your reviewer). Set up to two per quarter. Please also state the financial or qualitifable targets you will set for each of them, if any. The projects/duties you identify must be “SMART”, i.e. Specific, Measurable, Achievable, Realistic and Time-bound. We will ascertain this during the working session.</p>
					<div class="d-flex justify-content-between align-items-center mb-1">
						<h3>Projects</h3>
						<a class="btn btn-primary <?= $completed || ($submitted && $reviewee) ? ' d-none' : '' ?>" data-bs-toggle="modal" data-bs-target="#ProjectModal">
							<i class="fas fa-plus"></i> Add Project
						</a>
					</div>
					<?php foreach ($reviewRequirements->projects as $key => $project) { ?>
					<div class="row mb-2" id="job-req-project-<?=$project['ProjectID']?>" class="review-form-project">
						<div class="form-group">
							<div class="col-12 input-group">
								<input type="hidden" name="ProjectID[]" value="<?= $project['ProjectID'] ?>">
								<input type="text" readonly class="form-control rounded-0" name="ProjectName[]" value="<?=$project['ProjectName'] ?>">
								<div class="input-group-append">
									<input type="number" min="1" max="4" name="ProjectQuarter[]" required class="form-control rounded-0" value="<?=$project['ProjectQuarter'] ?>" placeholder="Quarter" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?>>
								</div>
								<div class="input-group-append">
									<input type="number" name="ProjectYear[]" min="2000" required class="form-control rounded-0" value="<?= $project['ProjectYear'] ?>" placeholder="Year" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?>>
								</div>
								<?php if (!$completed && (($reviewee && !$submitted) || !$reviewee)) { ?>
								<div class="input-group-append">
									<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqProject(<?= $project['ProjectID'] ?>)"><i class="fas fa-multiply"></i></span>
								</div>
								<?php } ?>
							</div>
							<div>
								<textarea name="ProjectTarget[]" data-id="project-target-<?= $project['ProjectID'] ?>" class="form-control rounded-0" <?= $completed || ($submitted && $reviewee) ? 'readonly' : '' ?> placeholder="Write Quantifiable target"><?= $project['ProjectTarget'] ?></textarea>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php if (!$confirmation) { ?>
				<div class="col-12 mt-4">
					<label>State two areas where you would like to receive greater guidance/training in your work.</label>
					<textarea rows="5" name="Q7" required class="form-control border border-secondary" <?= $submitted ? "readonly" : "" ?> placeholder="Write your answer here"><?= $submitted ? $reviewAnswers["Q7"] : "" ?></textarea>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php if ($confirmation) { 
			if ($submitted) {
				$reviewRequirements->duties = getReviewRequirements($conn, $review->ReviewID, "duties");
			}
		?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Projections and Next Phase Negotiation (Step 3)
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-duties">
					<p>Identify a maximum of five achievable projects/duties that you would like to work on between now and the next review (normally in six months or one year as agreed with your reviewer).
						The duties you identify must be "SMART", i.e. Specific, Measurable, Achievable, Realistic and Time-bound. We will ascertain this during the working session.
					</p>
					<div class="d-flex justify-content-between align-items-center mb-1">
						<h3>Duties</h3>
						<a class="btn btn-sm btn-primary <?= $completed || ($submitted && $reviewee) ? ' d-none' : '' ?>" data-bs-toggle="modal" data-bs-target="#DutyModal">
							<i class="fas fa-plus"></i> Add Duty
						</a>
					</div>
					<?php foreach ($reviewRequirements->duties as $key => $duty) { ?>
					<div class="row mb-1" class="review-form-duty" id="job-req-duty-<?=$duty['DutyID']?>">
						<div class="col-12 input-group">
							<input type="hidden" name="DutyID[]" value="<?= $duty['DutyID'] ?>">
							<input readonly type="text" class="form-control" name="DutyName[]" value="<?php echo $duty['DutyName'] ?>">
							<?php if (!$completed && (($reviewee && !$submitted) || !$reviewee)) { ?>
							<div class="input-group-append">
								<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqDuty(<?= $duty['DutyID'] ?>)"><i class="fas fa-multiply"></i></span>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Benchmarks
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-benchmarks">
					<p>STATE WHAT YOU BENCHMARK YOUR SALARY AND BENEFITS WITH AND INDICATE YOUR LEVEL OF SATISFACTION 1=VERY POOR 5=VERY GOOD</p>
					<div class="row mb-1">
						<div class="col-9">
							Benchmark
							<a class="btn btn-sm btn-primary ml-2 <?= $submitted ? ' d-none' : '' ?>" onclick="addBenchmark()">
								<i class="fas fa-plus"></i> Add Benchmark
							</a>
						</div>
						<div class="col-3 d-flex justify-content-between align-items-center">
						<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
							<div class="b<?php echo $i; ?>">
						<b><?= $i ?></b>
					</div>
						<?php } ?>
						</div>
					</div>
					<?php
					if ($submitted) {
					$benchmarks = getReviewBenchmarks($conn, $review->ReviewID);
					foreach ($benchmarks as $benchmark) { ?>
					<div class="row mb-1 bg-white" class="review-form-benchmark" id="benchmark-<?=$benchmark['ReviewBenchmarkID']?>">
						<div class="col-9">
							<div class="b<?php echo $i; ?>">
							<input type="text" class="form-control" readonly name="Benchmark[]" value="<?php echo $benchmark['SalaryBenchmark'] ?>">
						</div>
						</div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input type="radio" value="<?=$i?>" <?= $benchmark["Score"] == $i ? 'checked' : "" ?> required name="score-benchmark-<?=$benchmark['ReviewBenchmarkID']?>">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php }} else { ?>
					<div class="row mb-1 p-1 bg-white" class="review-form-benchmark">
						<div class="col-9">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text btn btn-danger rounded-0" onclick="removeBenchmark(this)"><i class="fas fa-multiply"></i></span>
								</div>
								<input type="text" class="form-control" name="Benchmark[]" placeholder="Salary benchmark" required>
							</div>
						</div>
						<div class="col-3 d-flex justify-content-between align-items-center benchmark-score-div">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input type="radio" value="<?=$i?>" required name="score-benchmark-0">
						</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="col-12 mt-4">
					<div class="row mb-1 border p-2 bg-white rounded p-1 align-middle">
						<div class="col-9">I think my career will move towards a more managerial/specialist direction.</div>
						<div class="col-3 d-flex justify-content-between align-items-center">
							<?php for ($i = 1; $i <= $toal_ratings; $i++) { ?>
								<div class="b<?php echo $i; ?>">
							<input required type="radio" value="<?=$i?>" <?= $submitted && $reviewAnswers["Q20"] == $i ? 'checked' : "" ?> name="Q20">
						</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php }

		if (trim(strtolower($review->ReviewStatusName)) != 'draft') { ?>
		<?php if (!$confirmation) { ?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Reviewers Part <?= $reviewee && !$completed ? '<span class="text-sm text-warning">Your supervisor will update this section.</span>' : '' ?>
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-projects">
					<div class="form-group">
						<label><strong>Training</strong></label>
						<textarea name="Q8" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q8"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Personal attributes / Areas that require counselling</strong></label>
						<textarea name="Q9" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q9"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Long term career plan in the company</strong></label>
						<textarea name="Q11" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q11"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Salary Reviewed</strong></label>
						<section class="p-4 bg-light" data-position="1" data-table="Employees">
							<div class="form-group row mb-2">
								<label for="BasicMonthlySalary" class="col-sm-2 col-form-label">Basic Monthly Salary:</label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="BasicMonthlySalary" name="BasicMonthlySalary" value="<?=$salary['BasicMonthlySalary']??0?>">
								</div>
								<label for="GuaranteedAdditionalWage" class="col-sm-2 col-form-label">Guaranteed Additional Wage:</label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="GuaranteedAdditionalWage" name="GuaranteedAdditionalWage" value="<?=$salary['GuaranteedAdditionalWage']??0 ?>">
								</div>
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="SalesCommission" class="col-sm-2 col-form-label">Sales Commision:</label>
								<div class="col-sm-4">
									<input type="radio" id="SalesCommissionYes" name="SalesCommission" value="1" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 1) ? 'checked' : '' ?> >Yes
									<input type="radio" id="SalesCommissionNo" name="SalesCommission" value="0" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="DiscretionallyBonus" class="col-sm-2 col-form-label">Discretionally Bonus:</label>
								<div class="col-sm-4">
									<input type="radio" id="DiscretionallyBonusYes" name="DiscretionallyBonus" value="1" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="DiscretionallyBonusNo" name="DiscretionallyBonus" value="0" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="ProfitShare" class="col-sm-2 col-form-label">Profit Share:</label>
								<div class="col-sm-4">
									<input type="radio" id="ProfitShareYes" name="ProfitShare" value="1" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="ProfitShareNo" name="ProfitShare" value="0" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="Equity" class="col-sm-2 col-form-label">Equity:</label>
								<div class="col-sm-4">
									<input type="radio" id="EquityYes" name="Equity" value="1" <?=(isset($salary['Equity']) && $salary['Equity'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="EquityNo" name="Equity" value="0" <?=(isset($salary['Equity']) && $salary['Equity'] == 0) ? 'checked' : '' ?>>No
								</div>
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="ReviewDate" class="col-sm-2 col-form-label">Review Date:</label>
								<div class="col-sm-4">
									<input type="datetime" class="form-control" id="ReviewDate" name="ReviewDate" value="<?=$salary['ReviewDate'] ?? ''?>" readonly>
								</div>

								<!-- <label for="NextReviewDate" class="col-sm-2 col-form-label">Next Review Date:</label>
								<div class="col-sm-4">
									<input type="date" class="form-control" name="NextReviewDate" value="<?=$salary['NextReviewDate'] ?? ''?>">
								</div> -->
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="Notes" class="col-12 col-form-label">Notes:</label>
								<div class="col-12">
									<textarea name="Notes" id="Notes" class="form-control"><?=$salary['Notes'] ?? ''?></textarea>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
		<?php } if ($confirmation) { ?>
		<div class="form-group review-tab bg-light rounded shadow-sm p-3">
			<h3 class="mb-3">
				Decision Section <?= $reviewee && !$completed ? '<span class="text-sm text-warning">Your supervisor will update this section.</span>' : '' ?>
			</h3>
			<div class="row">
				<div class="col-12" id="review-form-projects">
					<div class="form-group">
						<label><strong>Job Description / Scope</strong></label>
						<textarea name="Q14" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q14"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Training</strong></label>
						<textarea name="Q15" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q15"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Personal Attributes</strong></label>
						<textarea name="Q17" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q17"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Areas that required counseling</strong></label>
						<textarea name="Q18" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q18"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Long term career plan in the company</strong></label>
						<textarea name="Q19" required <?= $completed || $reviewee ? "readonly" : "" ?> class="form-control"><?= $completed ? $reviewAnswers["Q19"] : "" ?></textarea>
					</div>
					<div class="form-group">
						<label><strong>Salary Reviewed</strong></label>
						<section class="p-4 bg-light" data-position="1" data-table="Employees">
							<div class="form-group row mb-2">
								<label for="BasicMonthlySalary" class="col-sm-2 col-form-label">Basic Monthly Salary:</label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="BasicMonthlySalary" name="BasicMonthlySalary" value="<?=$salary['BasicMonthlySalary']??0?>">
								</div>
								<label for="GuaranteedAdditionalWage" class="col-sm-2 col-form-label">Guaranteed Additional Wage:</label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="GuaranteedAdditionalWage" name="GuaranteedAdditionalWage" value="<?=$salary['GuaranteedAdditionalWage']??0 ?>">
								</div>
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="SalesCommission" class="col-sm-2 col-form-label">Sales Commision:</label>
								<div class="col-sm-4">
									<input type="radio" id="SalesCommissionYes" name="SalesCommission" value="1" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 1) ? 'checked' : '' ?> >Yes
									<input type="radio" id="SalesCommissionNo" name="SalesCommission" value="0" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="DiscretionallyBonus" class="col-sm-2 col-form-label">Discretionally Bonus:</label>
								<div class="col-sm-4">
									<input type="radio" id="DiscretionallyBonusYes" name="DiscretionallyBonus" value="1" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="DiscretionallyBonusNo" name="DiscretionallyBonus" value="0" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="ProfitShare" class="col-sm-2 col-form-label">Profit Share:</label>
								<div class="col-sm-4">
									<input type="radio" id="ProfitShareYes" name="ProfitShare" value="1" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="ProfitShareNo" name="ProfitShare" value="0" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 0) ? 'checked' : '' ?>>No
								</div>
								<label for="Equity" class="col-sm-2 col-form-label">Equity:</label>
								<div class="col-sm-4">
									<input type="radio" id="EquityYes" name="Equity" value="1" <?=(isset($salary['Equity']) && $salary['Equity'] == 1) ? 'checked' : '' ?>>Yes
									<input type="radio" id="EquityNo" name="Equity" value="0" <?=(isset($salary['Equity']) && $salary['Equity'] == 0) ? 'checked' : '' ?>>No
								</div>
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="ReviewDate" class="col-sm-2 col-form-label">Review Date:</label>
								<div class="col-sm-4">
									<input type="datetime" class="form-control" id="ReviewDate" name="ReviewDate" value="<?=$salary['ReviewDate'] ?? ''?>" readonly>
								</div>

								<!-- <label for="NextReviewDate" class="col-sm-2 col-form-label">Next Review Date:</label>
								<div class="col-sm-4">
									<input type="date" class="form-control" name="NextReviewDate" value="<?=$salary['NextReviewDate'] ?? ''?>">
								</div> -->
							</div>
							<div class="form-group row mb-2 d-flex align-items-center">
								<label for="Notes" class="col-12 col-form-label">Notes:</label>
								<div class="col-12">
									<textarea name="Notes" id="Notes" class="form-control"><?=$salary['Notes'] ?? ''?></textarea>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
		<?php } } ?>
		<div class="mt-3">
			<div class="d-flex justify-content-between align-items-center">
				<button type="button" class="btn btn-secondary" id="prevBtn" onclick="nextPrevReviewTab(-1)">Previous</button>
				<div id="review-step-icons"></div>
				<button type="button" class="btn btn-secondary" data-text="<?=$submitted ? ($completed || ($submitted && $reviewee) ? 'd-none' : 'Complete Review') : 'Submit Review'?>" value="" id="nextBtn" onclick="nextPrevReviewTab(1, this)">Next</button>
			</div>
		</div>
	</form>
</div>

<!-- Start of Instructions Modal -->
<div class="modal fade" id="review-instructions-modal" tabindex="-1" role="dialog" aria-labelledby="review-instructions-modal-lable" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="review-instructions-modal-lable">Review Instructions</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<?php if ($confirmation) { ?>
				<ol>
					<li class="mb-2">Complete this sheet to the best of your ability. A negative assessment does not mean that it will impede your development in the company. On the contrary, a false goal or expectation set will affect your assessment seriously. So the best position to take is an honest one all through this process.</li>
					<li class="mb-2">You must go through ALL sections, although you do not need to complete all sections. TICK the box on the right hand side to indicate that the factor listed alongside is relevant to your job. (Eg. You may be in research, but proposal writing under Sales and Marketing may be important to your job, and so you tick it before completing the 1-5 assessment).</li>
					<li class="mb-2">After you have completed your input, pass the sheet IN CONFIDENCE to your immediate head only. The Head will review your submission with your supervisor, and then schedule for a working session with you. That working session will always be at least five working days after you have made your submission and it has been read, so that all parties would have had the chance to think about the review before the session.</li>
					<li class="mb-2">This sheet is not a perfect one. We will improve on it as we go along. So suggestions on areas of assessment are always welcome and will be incorporated.</li>
					<li class="mb-2">The development of this Self Assessment Sheet will be done in conjunction with streamlining job functions and duties, and coming up with a workflow for every line of business. This process will also involve some fine-tuning as we go along. </li>
					<li class="mb-2">The end result will however help the Company understand and benefit from your real skills, set achievable goals for you to work, compensate and train you adequately and map out a career path that makes sense to you.</li>
					<li class="mb-2">This entire process will be conducted respecting your confidentiality.</li>
					<li class="mb-2">
						The rating guideline is as follows:
						<br>
						<ol>
							<li><strong>Very poor:</strong> You do not have the standard for this activity/skill/quality.</li>
							<li><strong>Satisfactory:</strong> Not performing to standard in some areas, but redeemable with some effort. </li>
							<li><strong>Good:</strong> Performance/qualities as required </li>
							<li><strong>Very Good:</strong>  Performance/qualities above required standards in most areas </li>
							<li><strong>Excellent:</strong> Performance/qualities are exceptional by all standards.</li>
						</ol>
					</li>
				</ol>
				<?php } else { ?>
				<ol>
					<li class="mb-2">The purpose of this review is to ensure that the duties of each staff is tightly aligned with the financial and strategic goals of the Company. All staff compensation and career tracking will be based on the outcome from this review.</li>
					<li class="mb-2">Complete this sheet to the best of your ability. A negative but honest assessment will be appreciated by the company. On the contrary, a false goal or expectation set will affect future reviews seriously. So the best position to take is an honest one all through this process.</li>
					<li class="mb-2">You must go through ALL sections, but you do NOT need to complete all sections. If you feel that some sections are best left empty or open-ended, you may do so, although it may be discussed during your review.</li>
					<li class="mb-2">After you have completed your input, pass the sheet IN CONFIDENCE to your immediate supervisor only. Your supervisor must conduct the review with you in person (not skype or phone) with you. That working session must be at least five working days after you have made your submission, so that all parties would have had the chance to think about the review before the session.</li>
					<li class="mb-2">This sheet is not a perfect one. We will improve on it as we go along. So suggestions on areas of assessment are always welcome.</li>
					<li class="mb-2">The end result will however be to help the Company understand and benefit from your real skills, set achievable goals for you to work, compensate and train you adequately and map out a career path that makes sense to you.</li>
					<li class="mb-2">This entire process will be conducted respecting your confidentiality.</li>
					<li class="mb-2">
						The rating guideline is as follows:
						<br>
						<ol>
							<li>
								<strong>Very poor:</strong> You do not have the standard for this activity/skill/quality.
								<li><strong>Satisfactory:</strong> Performance/qualities as required </li>
								<li><strong>Very Good:</strong> Performance/qualities above the required standard.</li>
							</li>
						</ol>
					</li>
				</ol>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<!-- End of Instructions Modal -->

<!-- Start of KPI Guidelines Modal -->
<div class="modal fade" id="KPI-instructions" tabindex="-1" aria-labelledby="KPI-instructionsLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="KPI-instructionsLabel">KPI Guidelines</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>A Key Performance Indicator (KPI) is the actual performance output that you will be measured on. Each item must be quantifiable as far as possible. These can be:</p>
      			<ol>
					<li class="mb-2">-	For sales staff, examples: sales numbers, number of new clients, satisfaction level of clients etc</li>
					<li class="mb-2">-	For marketing staff, examples: No of products to be rated on, Sales value generated Etc</li>
					<li class="mb-2">-	For event staff, examples: Number of events completed independently, Profitability of each event, Quality of event etc. (not quantifiable but can be rated).</li>
					<li class="mb-2">-	For research staff, examples: number of research reports completed in the period, no of award programmes, income generated in research, no of roundtables, etc</li>
					<li class="mb-2">-	For administration staff, examples: closing of accounts on time every month,  number of names in database, staff review completed on time, cost management etc.</li>
      			</ol>
			</div>
		</div>
	</div>
</div>
<!-- End of KPI Guidelines Modal -->


<!-- Start of Add Project Modal -->
<div class="modal fade" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="ProjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form id="create-project-form" method="POST" action="core/projects/create-project.php">
					<div class="form-group mb-2">
						<label for="ProjectName">Project Name:</label>
						<div class="input-group">
							<input type="text" class="form-control" id="ProjectName" name="ProjectName" required="true" placeholder="Enter Project Name">
							<div class="input-group-append rounded-0">
								<span class="input-group-text">
									<label >Lib:</label>
									<input type="checkbox" checked name="InLib" class="m-2">
								</span>
							</div>
						</div>
					</div>
					<div class="form-group mb-2">
						<button type="submit" class='btn btn-primary btn-sm btn-sm w-25' value="new-requirement" name="submit">
							<i class='fa fa-plus'></i> Add Project
						</button>
					</div>
				</form>
				<table class="table table-primary table-hover tr-link mt-4" id="table-projects">
					<thead>
						<tr>
							<th>Project ID</th>
							<th>Project Name</th>
							<th>Action</th>
						</tr>
			   		</thead>
					<tbody id="modal-projects-list"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- End of Add Project Modal -->


<!-- Start of Add KPI Modal -->
<div class="modal fade modal-md" id="KPIModal" tabindex="-1" role="dialog" aria-labelledby="KPIModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new KPI</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form method="POST" action="core/kpis/create-kpi.php" id="create-kpi-form">
					<div class="form-group mb-2">
						<label for="ProjectName">KPI Title:</label>
						<div class="input-group">
							<input type="text" class="form-control" id="KPITitle" name="KPITitle" required="true" placeholder="Enter KPI Title" required>
							<div class="input-group-append rounded-0 selectkpidiv">
								<span class="input-group-text selectkpispan" style="height: 100% !important;">
									<select class="border-0" name="KPICategoryID" required>
										<?php foreach($kpiCategories as $category){ ?>
										<option value="<?=$category['KPICategoryID']?>"><?=$category['CategoryName']?></option>
										<?php } ?>
									</select>
								</span>
							</div>
							<div class="input-group-append rounded-0">
								<span class="input-group-text">
									<label >Lib:</label>
									<input type="checkbox" checked name="InLib" class="m-2">
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class='btn btn-primary btn-sm btn-sm w-25' name="submit">
							<i class='fa fa-plus'></i> Add KPI
						</button>
					</div>
				</form>
				<table class="table table-primary table-hover tr-link mt-4" id="table-kpis">
					<thead>
						<tr>
							<th>KPI ID</th>
							<th>KPI Name</th>
							<th>Category</th>
							<th>Actions</th>
			   		</thead>
					<tbody id="modal-kpis-list"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- End of Add KPI Modal -->

<!-- Start of Add Duty Modal -->
<div class="modal fade modal-md" id="DutyModal" tabindex="-1" role="dialog" aria-labelledby="DutyModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="DutyModalLabel">Add new Duty</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form method="POST" action="core/reviews/create-duty.php" id="create-duty-form">
					<div class="form-group mb-2">
						<label for="ProjectName">Duty Name:</label>
						<div class="input-group">
							<input type="text" class="form-control" id="DutyName" name="DutyName" required="true" placeholder="Enter Duty Name">
							<div class="input-group-append rounded-0">
								<span class="input-group-text">
									<label >Lib:</label>
									<input type="checkbox" checked name="InLib" class="m-2">
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class='btn btn-primary btn-sm btn-sm w-25' name="submit">
							<i class='fa fa-plus'></i> Add Duty
						</button>
					</div>
				</form>
				<table class="table table-primary table-hover tr-link mt-4" id="table-kpis">
					<thead>
						<tr>
							<th>Duty ID</th>
							<th>Duty Name</th>
							<th>Actions</th>
			   		</thead>
					<tbody id="modal-duties-list"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- End of Add Duty Modal -->

<?php include("_footer.php"); ?>

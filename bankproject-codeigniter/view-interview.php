<?php 
if(!isset($_GET['id']) || $_GET['id'] == null) header("location: index.php"); 
include("_header.php");


$id  = getInterviewDetails($conn, $_GET['id']);
if ($id == null) {
	die('Invalid Interview ID');
}

$ad  = getApplicationDetails($conn, $id['ApplicationID']);
$jd  = getJobDetails($conn, $id['JobID']);
$interview_requirements  = getInterviewRequirements($conn, $id['InterviewID']);

$duties   = [];
$projects = [];
$skills   = [];
$kpis 	  = [];

$dIds = [];
$pIds = [];
$sIds = [];
$kIds = [];

//get all requirements to assign to designated type
while($interview_requirement = sqlsrv_fetch_array($interview_requirements, 2)) {
	if($interview_requirement['DutyID'] !== NULL){
		$duties[] = $interview_requirement;
		$dIds[]   = $interview_requirement['DutyID'];
	}
	if($interview_requirement['ProjectID'] !== NULL){
		$projects[] = $interview_requirement;
		$pIds[] = $interview_requirement['ProjectID'];
	}
	if($interview_requirement['SkillID'] !== NULL){
		$skills[] = $interview_requirement;
		$sIds[] = $interview_requirement['SkillID'];
	}
	if($interview_requirement['KPIID'] !== NULL){
		$kpis[] = $interview_requirement;
		$kIds[] = $interview_requirement['KPIID'];
	}
}

?>

<div id="view-interview-page" class="header ">
	<!-- hidden necessary ids -->
	<input type="hidden" value="<?=$ad['ApplicationID']?>" id="ApplicationID" name="ApplicationID">
	<input type="hidden" value="<?=$_GET['id']?>" id="InterviewID" name="InterviewID">
	<div class="row">
		<div class="col-12">
			<center>
				<h3>
					TAB Global (JOB CANDIDATE INTERVIEW NOTES)
				</h3>
			</center>
			<div class="form-group row mb-2">
				<label for="CandidateName" class="col-sm-2 col-form-label">Name of Candidate:</label>
				<div class="col-sm-3">
					<input type="text" readonly class="form-control" id="CandidateName" readonly value="<?=$ad['FirstName']?> <?=$ad['LastName']?>">
				</div>
				<label for="JobTitleName" class="col-sm-3 col-form-label">Position being considered for:</label>
				<div class="col-sm-4">
					<input type="text" readonly class="form-control" id="JobTitleName" readonly value="<?=$jd['JobTitleName']?>">
				</div>
			</div>
			<div class="form-group row mb-2">
				<label for="CandidateName" class="col-sm-2 col-form-label">Interview Status: </label>
				<div class="col-sm-3">
					<div class="input-group mb-3">
						<input type="text" readonly class="form-control" id="InterviewStatusName" readonly value="<?=ucwords($id['InterviewStatusName'])?>">
						<?php if(strtolower($id["InterviewStatusName"]) == 'draft' && $_SESSION['Interviewer'] == 1) {?>
						<div class="input-group-append">
							<span><a href="interview.php?iid=<?=$id['InterviewID']?>" class='btn btn-primary h-100' >Complete</a></span>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<hr class="my-5">
			<hr class="my-5">
			<section id="section-1" data-position="1">
				<p>2. <b>VERY FIRST IMPRESSIONS.</b> What was your very first impression when you first met the candidate.</p>
				<form id="form-int-1">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="5" class="headingquestion">First impressions. Please circle (1) for "not at all" and (5) for "very much so".</td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td width="45%"></td>
							<td align="center">No</td>
							<td align="center">Maybe</td>
							<td align="center">Yes</td>
							<td align="center">Interview's Notes</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>A personality that you liked?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q1" value="no" <?=$id['Q1'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q1" value="maybe" <?=$id['Q1'] == 'maybe' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q1" value="yes" <?=$id['Q1'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q1_note" class="form-control" value="<?=$id['Q1_note']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>Interested in the interview/job?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q2" value="no" <?=$id['Q2'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q2" value="maybe" <?=$id['Q2'] == 'maybe' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q2" value="yes" <?=$id['Q2'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q2_note" class="form-control" value="<?=$id['Q2_note']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>Interested in the interview/job?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q3" value="no" <?=$id['Q3'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q3" value="maybe" <?=$id['Q3'] == 'maybe' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q3" value="yes" <?=$id['Q3'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q3_note" class="form-control" value="<?=$id['Q3_note']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Has decent dress sense?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q4" value="no" <?=$id['Q4'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q4" value="maybe" <?=$id['Q4'] == 'maybe' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q4" value="yes" <?=$id['Q4'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q4_note" class="form-control" value="<?=$id['Q4_note']?>"></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>Did this person strike you as someone we could/should have?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q5" value="no" <?=$id['Q5'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q5" value="maybe" <?=$id['Q5'] == 'maybe' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q5" value="yes" <?=$id['Q5'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q5_note" class="form-control" value="<?=$id['Q5_note']?>"></td>
						</tr>
					</table>
				</form>
			</section>	
			<hr class="my-5">
			<section id="section-2" data-position="2">
				<p>3. <b>FIRST CONVERSATIONS.</b> How prepared or interested was the candidate in the interview?</p>
				<form id="form-int-2">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="5" class="headingquestion">First impressions. Please circle (1) for "not at all" and (5) for "very much so".</td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td width="45%"></td>
							<td align="center">Yes</td>
							<td align="center">No</td>
							<td align="center">Interview's Notes</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>Was the candidate prepared for the interview with knowledge about the role or the organization?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q6" value="yes" <?=$id['Q6'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q6" value="no" <?=$id['Q6'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q6_note" class="form-control" value="<?=$id['Q6_note']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>Was able to describe the Company's 3 core businesses without prompting?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q7" value="yes" <?=$id['Q7'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q7" value="no" <?=$id['Q7'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q7_note" class="form-control" value="<?=$id['Q7_note']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>Has some understanding of business?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q8" value="yes" <?=$id['Q8'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q8" value="no" <?=$id['Q8'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q8_note" class="form-control" value="<?=$id['Q8_note']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Has some understanding of banking?</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q9" value="yes" <?=$id['Q9'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q9" value="no" <?=$id['Q9'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q9_note" class="form-control" value="<?=$id['Q9_note']?>"></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>OTHERS:</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q10" value="yes" <?=$id['Q10'] == 'yes' ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q10" value="no" <?=$id['Q10'] == 'no' ? 'checked' : ''?>></td>
							<td align="center"><input type="text" readonly name="Q10_note" class="form-control" value="<?=$id['Q10_note']?>"></td>
						</tr>
					</table>
				</form>
			</section>	
			<hr class="my-5">
			<section id="section-3" data-position="3">
				<p>4. <b>QUESTIONS ON CANDIDATES ACADEMIC BACKGROUND.</b> Assessing candidates straight out of school requires looking at several indicators that are different from those with work experience. The most important of the indicators we look for is the candidate's high school equivalent (about 16-17 years old) grades, what type of schools they went to and their most memorable activities. The second most important indicator is their pre-university equivalent (17-18 years). Their university grades and activities, although important, are the LEAST indicative of the three for determining if a young person is smart or suitable for us.</p>
				<form id="form-int-3">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="2" class="headingquestion">For candidates fresh out of school. Please state "yes" "excellent" "not relevant" "no" "I detect issues" or any notes to describe your answers.</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>The candidate's education can be said to be good (good school or good overall experiences including both in studies and social activities)?</td>
							<td align="center"><input type="text" readonly name="Q11" class="form-control" value="<?=$id['Q11']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>Candidate's education prepared him/her to discuss current <i>business</i> or banking topics with ease?</td>
							<td align="center"><input type="text" readonly name="Q12" class="form-control" value="<?=$id['Q12']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>The candidate has clearly developed thinking skills and not clueless about his or her career</td>
							<td align="center"><input type="text" readonly name="Q13" class="form-control" value="<?=$id['Q13']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>The candidate is clear about what kind of jobs he is interested in and not looking for just any job?</td>
							<td align="center"><input type="text" readonly name="Q14" class="form-control" value="<?=$id['Q14']?>"></td>
						</tr>
					</table>
					<div class="form-group row mt-2">
						<label for="Q15" class="col-sm-2 col-form-label">ADDITIONAL NOTES:</label>
						<div class="col-sm-10">
							<textarea readonly class="form-control" id="Q15" name="Q15"><?=$id['Q15']?></textarea>
						</div>
					</div>
				</form>
			</section>	
			<hr class="my-5">
			<section id="section-4" data-position="4">
				<p class="my-4">QUANTITATIVE VS QUALITATIVE</p>
				<p>5. <b>CAREER PROGRESSION OF CANDIDATES WITH PAST WORK EXPERIENCE.</b> Ask the candidate to describe his/her career personal career progression - (from job to job, why he/she left each previous jobs and why why apply for this one). From the candidate's answers, describe your assessment of the candidate's mental map - eg. has the candidate been changing jobs because he/she wants to be promoted faster, or is he/she going through different jobs so as the gain more experience in different areas until he/she is really settled. The big question for you to write down your answer here is "Do you think that joining TAB Global is the correct thing for this candidate in this point in his/her career and that he/she will enjoy working with us"?</p>
				<form id="form-int-4">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="2" class="headingquestion">Career Progression. Please circle (1) for "not at all" and (5) for "very much so".</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>The candidate was able to convince me that he/she was not a job-hopper.</td>
							<td align="center"><input type="text" readonly name="Q16" class="form-control" value="<?=$id['Q16']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>I think that this candidate has had a logical career progression that makes him or her suitable for us AT THIS POINT in his/her career.</td>
							<td align="center"><input type="text" readonly name="Q17" class="form-control" value="<?=$id['Q17']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>I think we do need this candidate's skills</td>
							<td align="center"><input type="text" readonly name="Q18" class="form-control" value="<?=$id['Q18']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>OTHERS:</td>
							<td align="center"><input type="text" readonly name="Q19" class="form-control" value="<?=$id['Q19']?>"></td>
						</tr>
					</table>
				</form>
			</section>	            
			<hr class="my-5">
			<section id="section-5" data-position="5">
				<p>6. <b>IDENTIFY KEY PERSONAL SKILLS.</b> Ask the candidate to describe the key skills that best describes the candidate from jobs over the years. Ask questions like "what do you really enjoy doing"?</p>
				<form id="form-int-5">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="2" class="headingquestion">Please list the candidate's answers below as to what he or she thinks are his skill sets. List only 3, even if the candidate tries to list many (eg. sales, sales management, project management, writing, editing, numeric skills etc). After you have listed the candidate's answers, please choose (1) for "this candidate is delusional" to (5) for "yes, I think this candidate has these skills".</td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td>What the candidate said or you deciphered from the conversation</td>
							<td>Your Opinion as the Interviewer</td>
						</tr>
						<tr>
							<td>1.</td>
							<td><input type="text" readonly name="Q20" class="form-control" value="<?=$id['Q20']?>"></td>
							<td align="center"><input type="text" readonly name="Q21" class="form-control" value="<?=$id['Q21']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td><input type="text" readonly name="Q22" class="form-control" value="<?=$id['Q22']?>"></td>
							<td><input type="text" readonly name="Q23" class="form-control" value="<?=$id['Q23']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td><input type="text" readonly name="Q24" class="form-control" value="<?=$id['Q24']?>"></td>
							<td><input type="text" readonly name="Q25" class="form-control" value="<?=$id['Q25']?>"></td>
						</tr>
					</table>
					<div class="form-group row mt-2">
						<label for="Q30" class="col-sm-5 col-form-label">ACHIEVEMENTS IN SCHOOL OR AT WORK THAT DEMONSTRATES THE SKILLS:</label>
						<div class="col-sm-7">
							<textarea readonly class="form-control" id="Q30" name="Q30"><?=$id['Q30']?></textarea>
						</div>
					</div>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-6" data-position="6">
				<form id="form-int-6">
					<p>7. <b>WORKING RELATIONSHIPS WITH BOSSES.</b> "Tell me about the bosses you have worked with." The answers we are looking for include: what kind of bosses you work best family does the candidate comes from (eg. are they entrepreneurs or civil servants or professionals (eg. doctors) or influential (eg. politicians) or just ordinary folk. As the candidate answers the questions look out also for the way he/she answers and if he is truthful and comfortable with his own background or he/she is insecure. Sometimes, family background will tell how the candidate will be motivated (for example, if the family is made up of entrepreneurs, he/she might be likely to be a self-starter).</p>
					<div class="form-group row mt-2">
						<div class="col-12">
							<textarea class="form-control" id="Q27" name="Q27"><?=$id['Q27']?></textarea>
						</div>
					</div>
					<p>8. <b>IMPRESSIONS FROM MEMORABLE CLIENTS/FRIENDS/BUSINESS ASSOCIATES.</b> Ask the candidate to describe his/her best or most memorable clients or suppliers or people in the industry whom he/she has been interacting with. From his/her answers, you will be able to gauge the kind of clients the candidate will be comfortable with (if at all) and the people in our Company that this candidate will most likely feel comfortable with.</p>
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td class="headingquestion">Please circle (1) for "not at all" and (5) for "very much so".</td>
							<td align="center">1</td>
							<td align="center">2</td>
							<td align="center">3</td>
							<td align="center">4</td>
							<td align="center">5</td>
						</tr>
						<tr>
							<td width="5%">1.</td>
							<td width="45%">This candidate has proven experience relating with senior people with ease. </td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q28" value="1" <?=$id['Q28'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q28" value="2" <?=$id['Q28'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q28" value="3" <?=$id['Q28'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q28" value="4" <?=$id['Q28'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q28" value="5" <?=$id['Q28'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>This candidate will be comfortable dealing with the complex organizations that our clients are.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q29" value="1" <?=$id['Q29'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q29" value="2" <?=$id['Q29'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q29" value="3" <?=$id['Q29'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q29" value="4" <?=$id['Q29'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q29" value="5" <?=$id['Q29'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>This candidate likes client/industry facing roles as opposed to back-office roles.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q30" value="1" <?=$id['Q30'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q30" value="2" <?=$id['Q30'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q30" value="3" <?=$id['Q30'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q30" value="4" <?=$id['Q30'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q30" value="5" <?=$id['Q30'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>4.</td>
							<td><input type="text" name="Q31" value="<?=$id["Q31"]?>" class="form-control" placeholder="Other"></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="A31" value="1" <?=$id['A31'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="A31" value="2" <?=$id['A31'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="A31" value="3" <?=$id['A31'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="A31" value="4" <?=$id['A31'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="A31" value="5" <?=$id['A31'] == 5 ? 'checked' : ''?>></td>
						</tr>
					</table>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-7" data-position="7">
				<p>9. <b>FAMILY BACKGROUND.</b> Ask the candidate to share a little bit about his/her family. Ask about parents and what they do, siblings, what they do and significant members of the family who has been an influence in his/her career. The answers we are looking for include: what kind of family does the candidate comes from (eg. are they entrepreneurs or civil servants or professionals (eg. doctors) or influential (eg. politicians) or just ordinary folk. As the candidate answers the questions look out also for the way he/she answers and if he is truthful and comfortable with his own background or he/she is insecure. Sometimes, family background will tell how the candidate will be motivated (for example, if the family is made up of entrepreneurs, he/she might be likely to be a self-starter).</p>
				<form id="form-int-7">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td class="headingquestion">Please circle (1) for "not at all" and (5) for "very much so".</td>
							<td align="center">1</td>
							<td align="center">2</td>
							<td align="center">3</td>
							<td align="center">4</td>
							<td align="center">5</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>I can say that the candidate's family background is useful to this job.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q32" value="1" <?=$id['Q32'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q32" value="2" <?=$id['Q32'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q32" value="3" <?=$id['Q32'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q32" value="4" <?=$id['Q32'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q32" value="5" <?=$id['Q32'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>This candidate's family background DID influence him or her, whether positively or negatively</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q33" value="1" <?=$id['Q33'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q33" value="2" <?=$id['Q33'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q33" value="3" <?=$id['Q33'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q33" value="4" <?=$id['Q33'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q33" value="5" <?=$id['Q33'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>OTHERS:</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q34" value="1" <?=$id['Q34'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q34" value="2" <?=$id['Q34'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q34" value="3" <?=$id['Q34'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q34" value="4" <?=$id['Q34'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q34" value="5" <?=$id['Q34'] == 5 ? 'checked' : ''?>></td>
						</tr>
					</table>
					<div class="form-group row mt-2">
						<label for="Q35" class="col-sm-2 col-form-label">NOTES:</label>
						<div class="col-sm-10">
							<textarea readonly class="form-control" id="Q35" name="Q35"><?=$id['Q35']?></textarea>
						</div>
					</div>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-8" data-position="8">
				<p>10. <b>PERSONALITY TYPE.</b> Ask the candidate to describe his/her own personality traits. Read the following options to the candidate and ask him/her if he/she think that he/she is (circle the correct one) and think about how this candidate will fit into our organisation's other staff:</p>
				<form id="form-int-8">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td class="headingquestion">Please circle (1) for "not at all" and (5) for "very much so".</td>
							<td align="center">1</td>
							<td align="center">2</td>
							<td align="center">3</td>
							<td align="center">4</td>
							<td align="center">5</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>This candidate is a leader rather than a follower. </td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q35" value="1" <?=$id['Q35'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q35" value="2" <?=$id['Q35'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q35" value="3" <?=$id['Q35'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q35" value="4" <?=$id['Q35'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q35" value="5" <?=$id['Q35'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>This candidate is a team player rather than a specialist who works alone.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q36" value="1" <?=$id['Q36'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q36" value="2" <?=$id['Q36'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q36" value="3" <?=$id['Q36'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q36" value="4" <?=$id['Q36'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q36" value="5" <?=$id['Q36'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>This candidate is meticulous and has an eye for detail.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q37" value="1" <?=$id['Q37'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q37" value="2" <?=$id['Q37'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q37" value="3" <?=$id['Q37'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q37" value="4" <?=$id['Q37'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q37" value="5" <?=$id['Q37'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>This candidate is interested in working, will turn up for work and work with minimum disruption.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q38" value="1" <?=$id['Q38'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q38" value="2" <?=$id['Q38'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q38" value="3" <?=$id['Q38'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q38" value="4" <?=$id['Q38'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q38" value="5" <?=$id['Q38'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>This candidate can see the Big Picture as well as has an eye for detail.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q39" value="1" <?=$id['Q39'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q39" value="2" <?=$id['Q39'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q39" value="3" <?=$id['Q39'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q39" value="4" <?=$id['Q39'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q39" value="5" <?=$id['Q39'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>6.</td>
							<td>This candidate is a problem solver as opposed to someone who works best with clear instructions.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q40" value="1" <?=$id['Q40'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q40" value="2" <?=$id['Q40'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q40" value="3" <?=$id['Q40'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q40" value="4" <?=$id['Q40'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q40" value="5" <?=$id['Q40'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>7.</td>
							<td>This candidate can fit into TAB Global culture. </td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q41" value="1" <?=$id['Q41'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q41" value="2" <?=$id['Q41'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q41" value="3" <?=$id['Q41'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q41" value="4" <?=$id['Q41'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q41" value="5" <?=$id['Q41'] == 5 ? 'checked' : ''?>></td>
						</tr>
					</table>
				</form>
			</section> 
			<hr class="my-5">
			<?php if($jd['JobSales'] != 0 || $id['Salary'] == 0){ ?>
			<section id="section-9" data-position="9">
				<form id="form-int-9">
					<?php if($jd['JobSales'] == 1){?>
					<p class="mt-4 fw-bold">QUESTIONS SPECIFIC FOR SALES STAFF</p>
					<p>11. <b>ACTUAL SALES ACHIEVEMENTS. HARD NUMBERS.</b> If the position is for a sales person, ask the candidate to describe his/her own sales achievements. All real sales people exaggerate in some way, so you have to be very firm in making sure that the answers are clear and accurate. Ask for:</p>
					<ol type="i">
						<li>specific sales targets</li>
						<li>monthly or quarterly, </li>
						<li>per ticket prices of products he/she has been selling</li>
						<li>sales lead time for the products he/she has sold before.</li>
					</ol>
					<p>The answers to all these questions above are absolutely important for us to know if this is a genuine and hungry sales person and what kind of products he/she will be most suited to sell for us.</p>
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="2">Please list the candidate's answers below as to what he or she thinks are his skill sets. List only 3, even if the candidate tries to list many (eg. sales, sales management, project management, writing, editing, numeric skills etc). After you have listed the candidate's answers, please choose (1) for "this candidate is delusional" to (5) for "yes, I think this candidate has these skills".</td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td width="45%">Please write into the box.</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>What is this candidate's highest gross sales achievement for his or her products in the previous job?</td>
							<td><input type="text" readonly name="Q42" class="form-control" value="<?=$id['Q42']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>What is the average price of the per ticket item (ie individual products) that this candidate was used to selling in his or her previous job?</td>
							<td><input type="text" readonly name="Q43" class="form-control" value="<?=$id['Q43']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>What kind of product(s) can we put this candidate on for a start? (Remember, it is better to start with something achievable)</td>
							<td><input type="text" readonly name="Q44" class="form-control" value="<?=$id['Q44']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Others</td>
							<td><input type="text" readonly name="Q45" class="form-control" value="<?=$id['Q45']?>"></td>
						</tr>
					</table>
				 	<?php } ?>
					<?php if($id['Salary'] == 1){?>
					<p class="mt-4 fw-bold">THE SALARY QUESTIONS</p>
					<p>Please find out if you are authorized to ask salary questions before proceeding to this section. Please note that not all staff involved in the interviewing process are authorized to ask questions in this area. Only the Chief Operating Officer or a Director of the Company or any of their appointees may ask salary questions. Check with the Chief Operating Officer if you are permitted to ask the salary question.  If you are authorized to ask the salary questions, find out:</p>
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td></td>
							<td colspan="2">Please list the candidate's answers below as to what he or she thinks are his skill sets. List only 3, even if the candidate tries to list many (eg. sales, sales management, project management, writing, editing, numeric skills etc). After you have listed the candidate's answers, please choose (1) for "this candidate is delusional" to (5) for "yes, I think this candidate has these skills".</td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td width="45%" class="headingquestion">Please write into the box.</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>What were this candidate's last 2 salaries?</td>
							<td><input type="text" readonly name="Q46" class="form-control" value="<?=$id['Q46']?>"></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>What kind of related industries or friends did the candidate say he or she benchmarks the salary.</td>
							<td><input type="text" readonly name="Q64" class="form-control" value="<?=$id['Q64']?>"></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>What is the candidate's expected salary?</td>
							<td><input type="text" readonly name="Q65" class="form-control" value="<?=$id['Q65']?>"></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Do you as the interviewer know what the salary range for someone in this position would be.</td>
							<td><input type="text" readonly name="Q66" class="form-control" value="<?=$id['Q66']?>"></td>
						</tr>
					</table>
					<?php } ?>
				</form>

			</section>
			<?php } ?>
			<hr class="my-5">
			<section id="section-10" data-position="10">
				<p class="mt-4 fw-bold">REFLECTIVE INTERVIEW NOTES</p>
				<p><i>The following questions can be completed after the candidate has left the room, as it requires you to reflect on the entire interview.</i></p>
				<p>12. <b>POSSIBLE JOB DESCRIPTION.</b> If we were to draft a job description for this candidate, list at least four duties that we would give this person when he joins us, based on his/her skills as we perceive them to be and where this person will fit:</p>

				<form id="form-int-10">
					<div class="form-group row mb-2">
						<div class="col-sm-1">
							<input type="checkbox" class="form-check-input" id="Q47" name="Q47" <?=$id['Q47'] == 'on' ? 'checked' : ''?>>
						</div>
						<div class="col-sm-11">
							<label for="Q47" class="col-form-label headingquestion">(Tick if true) The same as in Page 2 (if not true just state what the alternatives would be below).
							</label>
						</div>
					</div>
					<table class="table table-bordered table-hover">
						<tr>
							<td width="5%">i.</td>
							<td><input type="text" readonly name="Q48" class="form-control" value="<?=$id['Q48']?>" id="Q48"></td>
						</tr>
						<tr>
							<td>ii.</td>
							<td><input type="text" readonly name="Q49" class="form-control" value="<?=$id['Q49']?>" id="Q49"></td>
						</tr>
						<tr>
							<td>iii.</td>
							<td><input type="text" readonly name="Q50" class="form-control" value="<?=$id['Q50']?>" id="Q50"></td>
						</tr>
						<tr>
							<td>iv.</td>
							<td><input type="text" readonly name="Q51" class="form-control" value="<?=$id['Q51']?>" id="Q51"></td>
						</tr>
					</table>
				</form>
			</section>
			<section id="section-11" data-position="11">
				<p>DO NOT BEGIN THE FIRST INTERVIEW <b>BEFORE</b> YOU COMPLETE THE BLACK BOX IN THIS PAGE</p>
				<p>1.	BEFORE you begin the interview, please complete the following section inside the heavy black box to define the position.
				</p>
				<form id="form-int-11">
					<table class="table table-bordered table-hover mt-4r">
						<tr class="question">
							<td width="5%"></td>
							<td width="50%" class="headingquestion">Duties</td>
							<td align="center" valign="middle">1</td>
							<td align="center" valign="middle">2</td>
							<td align="center" valign="middle">3</td>
							<td align="center" valign="middle">4</td>
							<td align="center" valign="middle">5</td>
						</tr>
						<?php foreach($duties as $i => $duty){ ?>
						<tr>
							<td><?=$i + 1?></td>
							<td class="bl br <?=$i == 0 ? 'bt' : ''?>"><span class="duties"><?=$duty['DutyName']?></span></td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="duty-<?=$duty['InterviewRequirementID']?>" value="1" <?=$duty['Score'] == 1 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="duty-<?=$duty['InterviewRequirementID']?>" value="2" <?=$duty['Score'] == 2 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="duty-<?=$duty['InterviewRequirementID']?>" value="3" <?=$duty['Score'] == 3 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="duty-<?=$duty['InterviewRequirementID']?>" value="4" <?=$duty['Score'] == 4 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="duty-<?=$duty['InterviewRequirementID']?>" value="5" <?=$duty['Score'] == 5 ? 'checked' : ''?>>
							</td>
						</tr>
						<?php } ?>
						<tr class="question">
							<td></td>
							<td class="headingquestion">Projects</td>
							<td align="center" valign="middle">1</td>
							<td align="center" valign="middle">2</td>
							<td align="center" valign="middle">3</td>
							<td align="center" valign="middle">4</td>
							<td align="center" valign="middle">5</td>
						</tr>
						<?php foreach($projects as $i => $project){ ?>
						<tr>
							<td><?=$i + 1?></td>
							<td class="bl br"><?=$project['ProjectName']?></td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="project-<?=$project['InterviewRequirementID']?>" value="1" <?=$project['Score'] == 1 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="project-<?=$project['InterviewRequirementID']?>" value="2" <?=$project['Score'] == 2 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="project-<?=$project['InterviewRequirementID']?>" value="3" <?=$project['Score'] == 3 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="project-<?=$project['InterviewRequirementID']?>" value="4" <?=$project['Score'] == 4 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="project-<?=$project['InterviewRequirementID']?>" value="5" <?=$project['Score'] == 5 ? 'checked' : ''?>>
							</td>
						</tr>
						<?php } ?>
						<tr class="question">
							<td></td>
							<td class="headingquestion">Skills</td>
							<td align="center" valign="middle">1</td>
							<td align="center" valign="middle">2</td>
							<td align="center" valign="middle">3</td>
							<td align="center" valign="middle">4</td>
							<td align="center" valign="middle">5</td>
						</tr>
						<?php foreach($skills as $i => $skill){ ?>
						<tr>
							<td><?=$i + 1?></td>
							<td class="bl br"><?=$skill['SkillName']?></td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="skill-<?=$skill['InterviewRequirementID']?>" value="1" <?=$skill['Score'] == 1 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="skill-<?=$skill['InterviewRequirementID']?>" value="2" <?=$skill['Score'] == 2 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="skill-<?=$skill['InterviewRequirementID']?>" value="3" <?=$skill['Score'] == 3 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="skill-<?=$skill['InterviewRequirementID']?>" value="4" <?=$skill['Score'] == 4 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="skill-<?=$skill['InterviewRequirementID']?>" value="5" <?=$skill['Score'] == 5 ? 'checked' : ''?>>
							</td>
						</tr>
						<?php } ?>
						<tr class="question">
							<td></td>
							<td class="headingquestion">KPIs</td>
							<td align="center" valign="middle">1</td>
							<td align="center" valign="middle">2</td>
							<td align="center" valign="middle">3</td>
							<td align="center" valign="middle">4</td>
							<td align="center" valign="middle">5</td>
						</tr>
						<?php $lastKpi = count($kpis); foreach($kpis as $i => $kpi){ ?>
						<tr>
							<td><?=$i + 1?></td>
							<td class="bl br <?=$i == $lastKpi - 1 ? 'bb' : ''?>"><?=$kpi['KPITitle']?></td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="kpi-<?=$kpi['InterviewRequirementID']?>" value="1" <?=$kpi['Score'] == 1 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="kpi-<?=$kpi['InterviewRequirementID']?>" value="2" <?=$kpi['Score'] == 2 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="kpi-<?=$kpi['InterviewRequirementID']?>" value="3" <?=$kpi['Score'] == 3 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="kpi-<?=$kpi['InterviewRequirementID']?>" value="4" <?=$kpi['Score'] == 4 ? 'checked' : ''?>>
							</td>
							<td align="center">
								  <input disabled class="form-check-input" type="radio" name="kpi-<?=$kpi['InterviewRequirementID']?>" value="5" <?=$kpi['Score'] == 5 ? 'checked' : ''?>>
							</td>
						</tr>
						<?php } ?>
					</table>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-12" data-position="12">
				<p>13. <b>WHY WILL WE NOT HIRE THIS PERSON? (MUST ANSWER THIS QUESTION AT ALL COST).</b> Even if you like the candidate very much, you must answer this question. Think through the entire interview and force yourself to write down that one or two things that could cast a doubt in your mind. You must absolutely write down at least one reason why we would not hire this person. Recall also the things we have learnt from the past based on for example, the age of people, where they come from, and how different people can be expected to perform.</p>
				<form id="form-int-12">
					<table class="table table-bordered table-hover">
						<tr class="question">
							<td width="5%"></td>
							<td class="headingquestion">Please circle (1) for "not at all" to (5) for "very much so"</td>
							<td align="center">1</td>
							<td align="center">2</td>
							<td align="center">3</td>
							<td align="center">4</td>
							<td align="center">5</td>
						</tr>
						<tr>
							<td>1.</td>
							<td>This candidate is a job-hopper</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q52" value="1" <?=$id['Q52'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q52" value="2" <?=$id['Q52'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q52" value="3" <?=$id['Q52'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q52" value="4" <?=$id['Q52'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q52" value="5" <?=$id['Q52'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>This candidate is not likely to stay long</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q53" value="1" <?=$id['Q53'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q53" value="2" <?=$id['Q53'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q53" value="3" <?=$id['Q53'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q53" value="4" <?=$id['Q53'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q53" value="5" <?=$id['Q53'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>This candidate's character is questionable</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q54" value="1" <?=$id['Q54'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q54" value="2" <?=$id['Q54'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q54" value="3" <?=$id['Q54'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q54" value="4" <?=$id['Q54'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q54" value="5" <?=$id['Q54'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Our past experience with candidates with this background is not positive</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q55" value="1" <?=$id['Q55'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q55" value="2" <?=$id['Q55'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q55" value="3" <?=$id['Q55'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q55" value="4" <?=$id['Q55'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q55" value="5" <?=$id['Q55'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>I have questions about the honesty levels of this candidate. </td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q56" value="1" <?=$id['Q56'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q56" value="2" <?=$id['Q56'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q56" value="3" <?=$id['Q56'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q56" value="4" <?=$id['Q56'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q56" value="5" <?=$id['Q56'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>6.</td>
							<td>We do not have any proven past history with candidates of this background, so we will be taking a big chance</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q57" value="1" <?=$id['Q57'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q57" value="2" <?=$id['Q57'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q57" value="3" <?=$id['Q57'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q57" value="4" <?=$id['Q57'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q57" value="5" <?=$id['Q57'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>7.</td>
							<td>We will not have the resources to train this candidate</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q58" value="1" <?=$id['Q58'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q58" value="2" <?=$id['Q58'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q58" value="3" <?=$id['Q58'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q58" value="4" <?=$id['Q58'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q58" value="5" <?=$id['Q58'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>8.</td>
							<td>This candidate cannot work without supervision</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q59" value="1" <?=$id['Q59'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q59" value="2" <?=$id['Q59'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q59" value="3" <?=$id['Q59'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q59" value="4" <?=$id['Q59'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q59" value="5" <?=$id['Q59'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>9.</td>
							<td>This candidate may be lazy or unreliable</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q60" value="1" <?=$id['Q60'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q60" value="2" <?=$id['Q60'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q60" value="3" <?=$id['Q60'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q60" value="4" <?=$id['Q60'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q60" value="5" <?=$id['Q60'] == 5 ? 'checked' : ''?>></td>
						</tr>
						<tr>
							<td>10.</td>
							<td>Too good to be true, something is wrong.</td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q61" value="1" <?=$id['Q61'] == 1 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q61" value="2" <?=$id['Q61'] == 2 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q61" value="3" <?=$id['Q61'] == 3 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q61" value="4" <?=$id['Q61'] == 4 ? 'checked' : ''?>></td>
							<td align="center"><input disabled class="form-check-input" type="radio" name="Q61" value="5" <?=$id['Q61'] == 5 ? 'checked' : ''?>></td>
						</tr>
					</table>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-13" data-position="13">
				<form id="form-int-13">
					<label for="Q62" class="form-label">14.	How did this person do in the written test?</label>
					<textarea readonly class="form-control" id="Q62" name="Q62"><?=$id['Q62']?></textarea>
				</form>
			</section>
			<hr class="my-5">
			<section id="section-14" data-position="14">
				<form id="form-int-14">
					<label for="Q63" class="form-label">15.	Any other observations?</label>
					<textarea readonly class="form-control" id="Q63" name="Q63"><?=$id['Q63']?></textarea>
					<hr>
					<label for="ImpressionToHire" class="form-label">Overall Impression to Hire.</label>
					<select class="form-control" id="ImpressionToHire" name="ImpressionToHire">
						<option value="hire" <?=$id['ImpressionToHire'] == 'hire' ? 'selected' : 'disabled' ?>>Hire</option>
						<option value="kiv" <?=$id['ImpressionToHire'] == 'kiv' ? 'selected' : 'disabled' ?>>Keep In View</option>
					</select>
				</form>
			</section>
		</div>
	</div>
</div>


<!-- Tab panes -->
          
<?php include("_footer.php"); ?>




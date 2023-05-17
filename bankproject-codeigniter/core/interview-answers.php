<?php
session_start();
include("helpers.php");
validatePostRequest(["ApplicationID", "CurrentInterviewID", "Position"], false);
include("database.php");
include("jobs-functions.php");

$application_id = $_POST['ApplicationID'];
$current_interview_id = $_POST['CurrentInterviewID'];
$position = $_POST['Position'];
$sql = "SELECT Interviews.*,Jobs.JobSales, Employees.FirstName, Employees.LastName, Employees.Salary FROM Interviews
		JOIN Employees ON Employees.EmployeeID = Interviews.EmployeeID
		JOIN Jobs ON Jobs.JobID = Interviews.JobID
		WHERE Interviews.ApplicationID = '$application_id' AND InterviewID != '$current_interview_id' AND InterviewStatusID = 2";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$interviews = [];
while ($interview = sqlsrv_fetch_array($res, 2)) {
	$interviews[] = $interview;
}?>
<div class="accordion" id="answers-accordion">
	<?php
	foreach ($interviews as $ctr => $interview) {?>
		<div class="card accordion-item">
			<div class="card-header accordion-header" id="heading_<?=$ctr?>">
				<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?=$ctr?>" aria-expanded="true" aria-controls="collapse_<?=$ctr?>">
					Interviewed By&nbsp;<strong><?=$interview['FirstName'] . ' ' . $interview['LastName'];?></strong>
				</button>
			</div>
			<div id="collapse_<?=$ctr?>" class="collapse" aria-labelledby="heading_<?=$ctr?>" data-bs-parent="#answers-accordion">
				<div class="card-body">
					<?php
					switch ($position) {
						case 1:
							?>
							<table width="100%">
								<tr>
									<td></td>
									<td colspan="5">First impressions. Please circle (1) for "not at all" and (5) for "very much so".</td>
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
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q1" value="no" <?=$interview['Q1'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q1" value="maybe" <?=$interview['Q1'] == 'maybe' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q1" value="yes" <?=$interview['Q1'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q1_note" class="form-control" value="<?=$interview['Q1_note']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>Interested in the interview/job?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q2" value="no" <?=$interview['Q2'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q2" value="maybe" <?=$interview['Q2'] == 'maybe' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q2" value="yes" <?=$interview['Q2'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q2_note" class="form-control" value="<?=$interview['Q2_note']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>Interested in the interview/job?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q3" value="no" <?=$interview['Q3'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q3" value="maybe" <?=$interview['Q3'] == 'maybe' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q3" value="yes" <?=$interview['Q3'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q3_note" class="form-control" value="<?=$interview['Q3_note']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>Has decent dress sense?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q4" value="no" <?=$interview['Q4'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q4" value="maybe" <?=$interview['Q4'] == 'maybe' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q4" value="yes" <?=$interview['Q4'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q4_note" class="form-control" value="<?=$interview['Q4_note']?>"></td>
								</tr>
								<tr>
									<td>5.</td>
									<td>Did this person strike you as someone we could/should have?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q5" value="no" <?=$interview['Q5'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q5" value="maybe" <?=$interview['Q5'] == 'maybe' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q5" value="yes" <?=$interview['Q5'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q5_note" class="form-control" value="<?=$interview['Q5_note']?>"></td>
								</tr>
							</table>
							<?php
							break;
						case 2:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td colspan="5">First impressions. Please circle (1) for "not at all" and (5) for "very much so".</td>
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
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q6" value="yes" <?=$interview['Q6'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q6" value="no" <?=$interview['Q6'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q6_note" class="form-control" value="<?=$interview['Q6_note']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>Was able to describe the Company's 3 core businesses without prompting?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q7" value="yes" <?=$interview['Q7'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q7" value="no" <?=$interview['Q7'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q7_note" class="form-control" value="<?=$interview['Q7_note']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>Has some understanding of business?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q8" value="yes" <?=$interview['Q8'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q8" value="no" <?=$interview['Q8'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q8_note" class="form-control" value="<?=$interview['Q8_note']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>Has some understanding of banking?</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q9" value="yes" <?=$interview['Q9'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q9" value="no" <?=$interview['Q9'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q9_note" class="form-control" value="<?=$interview['Q9_note']?>"></td>
								</tr>
								<tr>
									<td>5.</td>
									<td>OTHERS:</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q10" value="yes" <?=$interview['Q10'] == 'yes' ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q10" value="no" <?=$interview['Q10'] == 'no' ? 'checked' : ''?>></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q10_note" class="form-control" value="<?=$interview['Q10_note']?>"></td>
								</tr>
							</table>
							<?php
							break;
						case 3:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td colspan="2">For candidates fresh out of school. Please state "yes" "excellent" "not relevant" "no" "I detect issues" or any notes to describe your answers.</td>
								</tr>
								<tr>
									<td>1.</td>
									<td>The candidate's education can be said to be good (good school or good overall experiences including both in studies and social activities)?</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q11" class="form-control" value="<?=$interview['Q11']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>Candidate's education prepared him/her to discuss current <i>business</i> or banking topics with ease?</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q12" class="form-control" value="<?=$interview['Q12']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>The candidate has clearly developed thinking skills and not clueless about his or her career</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q13" class="form-control" value="<?=$interview['Q13']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>The candidate is clear about what kind of jobs he is interested in and not looking for just any job?</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q14" class="form-control" value="<?=$interview['Q14']?>"></td>
								</tr>
							</table>
							<div class="form-group row mt-2">
								<label for="Q15" class="col-sm-2 col-form-label">ADDITIONAL NOTES:</label>
								<div class="col-sm-10">
									<textarea readonly class="form-control" id="Q15" name="<?='int_' . $interview['InterviewID'] . '_'?>Q15"><?=$interview['Q15']?></textarea>
								</div>
							</div>
							<?php
							break;
						case 4:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td colspan="2">Career Progression. Please circle (1) for "not at all" and (5) for "very much so".</td>
								</tr>
								<tr>
									<td>1.</td>
									<td>The candidate was able to convince me that he/she was not a job-hopper.</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q16" class="form-control" value="<?=$interview['Q16']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>I think that this candidate has had a logical career progression that makes him or her suitable for us AT THIS POINT in his/her career.</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q17" class="form-control" value="<?=$interview['Q17']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>I think we do need this candidate's skills</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q18" class="form-control" value="<?=$interview['Q18']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>OTHERS:</td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q19" class="form-control" value="<?=$interview['Q19']?>"></td>
								</tr>
							</table>
							<?php
							break;
						case 5:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td colspan="2">Please list the candidate's answers below as to what he or she thinks are his skill sets. List only 3, even if the candidate tries to list many (eg. sales, sales management, project management, writing, editing, numeric skills etc). After you have listed the candidate's answers, please choose (1) for "this candidate is delusional" to (5) for "yes, I think this candidate has these skills".</td>
								</tr>
								<tr>
									<td width="5%"></td>
									<td>What the candidate said or you deciphered from the conversation</td>
									<td>Your Opinion as the Interviewer</td>
								</tr>
								<tr>
									<td>1.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q20" class="form-control" value="<?=$interview['Q20']?>"></td>
									<td align="center"><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q21" class="form-control" value="<?=$interview['Q21']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q22" class="form-control" value="<?=$interview['Q22']?>"></td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q23" class="form-control" value="<?=$interview['Q23']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q24" class="form-control" value="<?=$interview['Q24']?>"></td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q25" class="form-control" value="<?=$interview['Q25']?>"></td>
								</tr>
							</table>
							<div class="form-group row mt-2">
								<label for="Q26" class="col-sm-5 col-form-label">ACHIEVEMENTS IN SCHOOL OR AT WORK THAT DEMONSTRATES THE SKILLS:</label>
								<div class="col-sm-7">
									<textarea readonly class="form-control" id="Q26" name="<?='int_' . $interview['InterviewID'] . '_'?>Q26"><?=$interview['Q26']?></textarea>
								</div>
							</div>
							<?php
							break;
						case 6:
							?>
							<div class="form-group row mt-2">
								<div class="col-12">
									<textarea class="form-control" id="Q27" name="<?='int_' . $interview['InterviewID'] . '_'?>Q27"><?=$interview['Q27']?></textarea>
								</div>
							</div>
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
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q28" value="1" <?=$interview['Q28'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q28" value="2" <?=$interview['Q28'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q28" value="3" <?=$interview['Q28'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q28" value="4" <?=$interview['Q28'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q28" value="5" <?=$interview['Q28'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>This candidate will be comfortable dealing with the complex organizations that our clients are.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q29" value="1" <?=$interview['Q29'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q29" value="2" <?=$interview['Q29'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q29" value="3" <?=$interview['Q29'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q29" value="4" <?=$interview['Q29'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q29" value="5" <?=$interview['Q29'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>This candidate likes client/industry facing roles as opposed to back-office roles.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q30" value="1" <?=$interview['Q30'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q30" value="2" <?=$interview['Q30'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q30" value="3" <?=$interview['Q30'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q30" value="4" <?=$interview['Q30'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q30" value="5" <?=$interview['Q30'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>4.</td>
									<td><input type="text" name="<?='int_' . $interview['InterviewID'] . '_'?>Q31" value="<?=$interview["Q31"]?>" class="form-control" placeholder="Other"></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>A31" value="1" <?=$interview['A31'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>A31" value="2" <?=$interview['A31'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>A31" value="3" <?=$interview['A31'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>A31" value="4" <?=$interview['A31'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>A31" value="5" <?=$interview['A31'] == 5 ? 'checked' : ''?>></td>
								</tr>
							</table>
							<?php
							break;
						case 7:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td>Please circle (1) for "not at all" and (5) for "very much so".</td>
									<td align="center">1</td>
									<td align="center">2</td>
									<td align="center">3</td>
									<td align="center">4</td>
									<td align="center">5</td>
								</tr>
								<tr>
									<td>1.</td>
									<td>I can say that the candidate's family background is useful to this job.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q32" value="1" <?=$interview['Q32'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q32" value="2" <?=$interview['Q32'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q32" value="3" <?=$interview['Q32'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q32" value="4" <?=$interview['Q32'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q32" value="5" <?=$interview['Q32'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>This candidate's family background DID influence him or her, whether positively or negatively</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q33" value="1" <?=$interview['Q33'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q33" value="2" <?=$interview['Q33'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q33" value="3" <?=$interview['Q33'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q33" value="4" <?=$interview['Q33'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q33" value="5" <?=$interview['Q33'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>OTHERS:</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q34" value="1" <?=$interview['Q34'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q34" value="2" <?=$interview['Q34'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q34" value="3" <?=$interview['Q34'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q34" value="4" <?=$interview['Q34'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q34" value="5" <?=$interview['Q34'] == 5 ? 'checked' : ''?>></td>
								</tr>
							</table>
							<div class="form-group row mt-2">
								<label for="Q35" class="col-sm-2 col-form-label">NOTES:</label>
								<div class="col-sm-10">
									<textarea readonly class="form-control" id="Q35" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35"><?=$interview['Q35']?></textarea>
								</div>
							</div>
							<?php
							break;
						case 8:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td>Please circle (1) for "not at all" and (5) for "very much so".</td>
									<td align="center">1</td>
									<td align="center">2</td>
									<td align="center">3</td>
									<td align="center">4</td>
									<td align="center">5</td>
								</tr>
								<tr>
									<td>1.</td>
									<td>This candidate is a leader rather than a follower. </td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35" value="1" <?=$interview['Q35'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35" value="2" <?=$interview['Q35'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35" value="3" <?=$interview['Q35'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35" value="4" <?=$interview['Q35'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q35" value="5" <?=$interview['Q35'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>This candidate is a team player rather than a specialist who works alone.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q36" value="1" <?=$interview['Q36'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q36" value="2" <?=$interview['Q36'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q36" value="3" <?=$interview['Q36'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q36" value="4" <?=$interview['Q36'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q36" value="5" <?=$interview['Q36'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>This candidate is meticulous and has an eye for detail.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q37" value="1" <?=$interview['Q37'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q37" value="2" <?=$interview['Q37'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q37" value="3" <?=$interview['Q37'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q37" value="4" <?=$interview['Q37'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q37" value="5" <?=$interview['Q37'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>This candidate is interested in working, will turn up for work and work with minimum disruption.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q38" value="1" <?=$interview['Q38'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q38" value="2" <?=$interview['Q38'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q38" value="3" <?=$interview['Q38'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q38" value="4" <?=$interview['Q38'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q38" value="5" <?=$interview['Q38'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>5.</td>
									<td>This candidate can see the Big Picture as well as has an eye for detail.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q39" value="1" <?=$interview['Q39'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q39" value="2" <?=$interview['Q39'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q39" value="3" <?=$interview['Q39'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q39" value="4" <?=$interview['Q39'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q39" value="5" <?=$interview['Q39'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>6.</td>
									<td>This candidate is a problem solver as opposed to someone who works best with clear instructions.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q40" value="1" <?=$interview['Q40'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q40" value="2" <?=$interview['Q40'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q40" value="3" <?=$interview['Q40'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q40" value="4" <?=$interview['Q40'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q40" value="5" <?=$interview['Q40'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>7.</td>
									<td>This candidate can fit into TAB Global culture. </td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q41" value="1" <?=$interview['Q41'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q41" value="2" <?=$interview['Q41'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q41" value="3" <?=$interview['Q41'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q41" value="4" <?=$interview['Q41'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q41" value="5" <?=$interview['Q41'] == 5 ? 'checked' : ''?>></td>
								</tr>
							</table>
							<?php
							break;
						case 9:
							if ($interview["JobSales"]) {
							?>
							<!-- Sales -->
							<table class="table table-bordered table-hover">
								<tr>
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
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q42" class="form-control" value="<?=$interview['Q42']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>What is the average price of the per ticket item (ie individual products) that this candidate was used to selling in his or her previous job?</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q43" class="form-control" value="<?=$interview['Q43']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>What kind of product(s) can we put this candidate on for a start? (Remember, it is better to start with something achievable)</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q44" class="form-control" value="<?=$interview['Q44']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>Others</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q45" class="form-control" value="<?=$interview['Q45']?>"></td>
								</tr>
							</table>
							<?php } if ($interview["Salary"]) {?>
							<!-- Salary -->
							<p class="mt-4 fw-bold">THE SALARY QUESTIONS</p>
							<p>Please find out if you are authorized to ask salary questions before proceeding to this section. Please note that not all staff involved in the interviewing process are authorized to ask questions in this area. Only the Chief Operating Officer or a Director of the Company or any of their appointees may ask salary questions. Check with the Chief Operating Officer if you are permitted to ask the salary question.  If you are authorized to ask the salary questions, find out:</p>
							<table class="table table-bordered table-hover">
								<tr>
									<td></td>
									<td colspan="2">Please list the candidate's answers below as to what he or she thinks are his skill sets. List only 3, even if the candidate tries to list many (eg. sales, sales management, project management, writing, editing, numeric skills etc). After you have listed the candidate's answers, please choose (1) for "this candidate is delusional" to (5) for "yes, I think this candidate has these skills".</td>
								</tr>
								<tr>
									<td width="5%"></td>
									<td width="45%">Please write into the box.</td>
								</tr>
								<tr>
									<td>1.</td>
									<td>What were this candidate's last 2 salaries?</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q46" class="form-control" value="<?=$interview['Q46']?>"></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>What kind of related industries or friends did the candidate say he or she benchmarks the salary.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q64" class="form-control" value="<?=$interview['Q64']?>"></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>What is the candidate's expected salary?</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q65" class="form-control" value="<?=$interview['Q65']?>"></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>Do you as the interviewer know what the salary range for someone in this position would be.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q66" class="form-control" value="<?=$interview['Q66']?>"></td>
								</tr>
							</table>
							<?php }
							break;
						case 10:
							?>
							<table class="table table-bordered table-hover">
								<tr>
									<td width="5%">i.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q48" class="form-control" value="<?=$interview['Q48']?>" id="Q48"></td>
								</tr>
								<tr>
									<td>ii.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q49" class="form-control" value="<?=$interview['Q49']?>" id="Q49"></td>
								</tr>
								<tr>
									<td>iii.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q50" class="form-control" value="<?=$interview['Q50']?>" id="Q50"></td>
								</tr>
								<tr>
									<td>iv.</td>
									<td><input type="text" readonly name="<?='int_' . $interview['InterviewID'] . '_'?>Q51" class="form-control" value="<?=$interview['Q51']?>" id="Q51"></td>
								</tr>
							</table>
							<?php
							break;
						case 11:
							$interview_requirements  = getInterviewRequirements($conn, $interview['InterviewID']);

							$duties   = [];
							$projects = [];
							$skills   = [];
							$kpis 	  = [];

							$dIds = [];
							$pIds = [];
							$sIds = [];
							$kIds = [];

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
							<table class="table table-bordered table-hover mt-4r">
								<tr>
									<td width="5%"></td>
									<td width="50%"></td>
									<td align="center" valign="middle">1</td>
									<td align="center" valign="middle">2</td>
									<td align="center" valign="middle">3</td>
									<td align="center" valign="middle">4</td>
									<td align="center" valign="middle">5</td>
								</tr>
								<?php foreach($duties as $i => $duty){ ?>
								<tr>
									<td><?php $i + 1;?></td>
									<td class="bl br <?=$i == 0 ? 'bt' : ''?>"><span class="duties"><?=$duty['DutyName']?></span></td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$duty['Score'] == 1 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$duty['Score'] == 2 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$duty['Score'] == 3 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$duty['Score'] == 4 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$duty['Score'] == 5 ? 'checked' : ''?>>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td></td>
									<td></td>
									<td align="center" valign="middle">1</td>
									<td align="center" valign="middle">2</td>
									<td align="center" valign="middle">3</td>
									<td align="center" valign="middle">4</td>
									<td align="center" valign="middle">5</td>
								</tr>
								<?php foreach($projects as $i => $project){?>
								<tr>
									<td><?=$i + 1?></td>
									<td class="bl br"><?=$project['ProjectName']?></td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$project['Score'] == 1 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$project['Score'] == 2 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$project['Score'] == 3 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$project['Score'] == 4 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$project['Score'] == 5 ? 'checked' : ''?>>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td></td>
									<td></td>
									<td align="center" valign="middle">1</td>
									<td align="center" valign="middle">2</td>
									<td align="center" valign="middle">3</td>
									<td align="center" valign="middle">4</td>
									<td align="center" valign="middle">5</td>
								</tr>
								<?php foreach($skills as $i => $skill){?>
								<tr>
									<td><?=$i + 1?></td>
									<td class="bl br"><?=$skill['SkillName']?></td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$skill['Score'] == 1 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$skill['Score'] == 2 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$skill['Score'] == 3 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$skill['Score'] == 4 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$skill['Score'] == 5 ? 'checked' : ''?>>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td></td>
									<td></td>
									<td align="center" valign="middle">1</td>
									<td align="center" valign="middle">2</td>
									<td align="center" valign="middle">3</td>
									<td align="center" valign="middle">4</td>
									<td align="center" valign="middle">5</td>
								</tr>
								<?php $lastKpi = count($kpis); foreach($kpis as $i => $kpi){?>
								<tr>
									<td><?=$i + 1?></td>
									<td class="bl br <?=$i == $lastKpi - 1 ? 'bb' : ''?>"><?=$kpi['KPITitle']?></td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$kpi['Score'] == 1 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$kpi['Score'] == 2 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$kpi['Score'] == 3 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$kpi['Score'] == 4 ? 'checked' : ''?>>
									</td>
									<td align="center">
										  <input disabled class="form-check-input" type="radio" <?=$kpi['Score'] == 5 ? 'checked' : ''?>>
									</td>
								</tr>
								<?php } ?>
							</table>
							<?php
							break;
						case 12:
							?>
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
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q52" value="1" <?=$interview['Q52'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q52" value="2" <?=$interview['Q52'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q52" value="3" <?=$interview['Q52'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q52" value="4" <?=$interview['Q52'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q52" value="5" <?=$interview['Q52'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>This candidate is not likely to stay long</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q53" value="1" <?=$interview['Q53'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q53" value="2" <?=$interview['Q53'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q53" value="3" <?=$interview['Q53'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q53" value="4" <?=$interview['Q53'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q53" value="5" <?=$interview['Q53'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>3.</td>
									<td>This candidate's character is questionable</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q54" value="1" <?=$interview['Q54'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q54" value="2" <?=$interview['Q54'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q54" value="3" <?=$interview['Q54'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q54" value="4" <?=$interview['Q54'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q54" value="5" <?=$interview['Q54'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>4.</td>
									<td>Our past experience with candidates with this background is not positive</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q55" value="1" <?=$interview['Q55'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q55" value="2" <?=$interview['Q55'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q55" value="3" <?=$interview['Q55'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q55" value="4" <?=$interview['Q55'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q55" value="5" <?=$interview['Q55'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>5.</td>
									<td>I have questions about the honesty levels of this candidate. </td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q56" value="1" <?=$interview['Q56'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q56" value="2" <?=$interview['Q56'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q56" value="3" <?=$interview['Q56'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q56" value="4" <?=$interview['Q56'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q56" value="5" <?=$interview['Q56'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>6.</td>
									<td>We do not have any proven past history with candidates of this background, so we will be taking a big chance</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q57" value="1" <?=$interview['Q57'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q57" value="2" <?=$interview['Q57'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q57" value="3" <?=$interview['Q57'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q57" value="4" <?=$interview['Q57'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q57" value="5" <?=$interview['Q57'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>7.</td>
									<td>We will not have the resources to train this candidate</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q58" value="1" <?=$interview['Q58'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q58" value="2" <?=$interview['Q58'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q58" value="3" <?=$interview['Q58'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q58" value="4" <?=$interview['Q58'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q58" value="5" <?=$interview['Q58'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>8.</td>
									<td>This candidate cannot work without supervision</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q59" value="1" <?=$interview['Q59'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q59" value="2" <?=$interview['Q59'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q59" value="3" <?=$interview['Q59'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q59" value="4" <?=$interview['Q59'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q59" value="5" <?=$interview['Q59'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>9.</td>
									<td>This candidate may be lazy or unreliable</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q60" value="1" <?=$interview['Q60'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q60" value="2" <?=$interview['Q60'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q60" value="3" <?=$interview['Q60'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q60" value="4" <?=$interview['Q60'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q60" value="5" <?=$interview['Q60'] == 5 ? 'checked' : ''?>></td>
								</tr>
								<tr>
									<td>10.</td>
									<td>Too good to be true, something is wrong.</td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q61" value="1" <?=$interview['Q61'] == 1 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q61" value="2" <?=$interview['Q61'] == 2 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q61" value="3" <?=$interview['Q61'] == 3 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q61" value="4" <?=$interview['Q61'] == 4 ? 'checked' : ''?>></td>
									<td align="center"><input disabled class="form-check-input" type="radio" name="<?='int_' . $interview['InterviewID'] . '_'?>Q61" value="5" <?=$interview['Q61'] == 5 ? 'checked' : ''?>></td>
								</tr>
							</table>
							<?php
							break;
						case 13:
							?>
							<label for="Q62" class="form-label">14.	How did this person do in the written test?</label>
							<textarea readonly class="form-control" id="Q62" name="<?='int_' . $interview['InterviewID'] . '_'?>Q62"><?=$interview['Q62']?></textarea>
							<?php
							break;
						case 14:
							?>
							<label for="Q63" class="form-label">15.	Any other observations?</label>
							<textarea readonly class="form-control" id="Q63" name="<?='int_' . $interview['InterviewID'] . '_'?>Q63"><?=$interview['Q63']?></textarea>

							<label for="ImpressionToHire" class="form-label">Overall Impression to Hire.</label>
							<select class="form-control" id="ImpressionToHire" name="<?='int_' . $interview['InterviewID'] . '_'?>ImpressionToHire">
								<option value="hire" <?=$interview['ImpressionToHire'] == 'hire' ? 'selected' : 'disabled' ?>>Hire</option>
								<option value="kiv" <?=$interview['ImpressionToHire'] == 'kiv' ? 'selected' : 'disabled' ?>>Keep in view</option>
							</select>
							<?php
							break;
						default:
							?>
							<div>Invalid Section</div>
							<?php
							break;
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	if (count($interviews) == 0) {
		?>
		<p>This is the first interview. No previous answers available.</p>
		<?php
	}
	?>
</div>
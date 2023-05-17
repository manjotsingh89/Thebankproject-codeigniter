<?php
function getReviewDetails($conn, $id) {
	$sql = "SELECT TOP 1 Reviews.*, ReviewStatuses.ReviewStatusName, ReviewTypes.* FROM [dbo].[Reviews]
	JOIN dbo.ReviewStatuses ON ReviewStatuses.ReviewStatusID = Reviews.ReviewStatusID
	JOIN dbo.ReviewTypes ON ReviewTypes.ReviewTypeID = Reviews.ReviewTypeID
	WHERE ReviewID = '$id' AND (RevieweeID = " . $_SESSION['EmployeeID'] . " OR ReviewerID = " . $_SESSION['EmployeeID'] . " OR '" . $_SESSION['UserTypeName'] . "' = 'administrator')";
	$res = sqlsrv_query($conn, $sql);

	if (!$res || !$review = sqlsrv_fetch_array($res, 2)) {
		return false;
	}
	
	$details['review'] = $review;
	$details['employee'] = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT TOP 1 * FROM dbo.Employees WHERE EmployeeID = " . $details['review']['RevieweeID']), 2);
	$details['job'] = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT TOP 1 * FROM dbo.Jobs WHERE FilledByEmpID = " . $details['employee']['EmployeeID']), 2);
	$sql = "SELECT * FROM dbo.ReviewRequirements1
	LEFT JOIN dbo.Duties ON dbo.Duties.DutyID = dbo.ReviewRequirements1.DutyID
	LEFT JOIN dbo.Projects ON dbo.Projects.ProjectID = dbo.ReviewRequirements1.ProjectID
	LEFT JOIN dbo.Skills ON dbo.Skills.SkillID = dbo.ReviewRequirements1.SkillID
	LEFT JOIN dbo.KPIs ON dbo.KPIs.KPIID = dbo.ReviewRequirements1.KPIID
	WHERE dbo.ReviewRequirements1.ReviewID = " . $review['ReviewID'];
	$res = sqlsrv_query($conn, $sql);

	$details['reviewRequirements']['kpis'] = [];
	$details['reviewRequirements']['projects'] = [];
	$details['reviewRequirements']['duties'] = [];
	$details['reviewRequirements']['skills'] = [];

	if (!$res) return false;
	while ($jobReq = sqlsrv_fetch_array($res, 2))
	{
		if ((int) $jobReq['KPIID'] != 0 && $jobReq["KPIID"] != NULL) {
			$details['reviewRequirements']['kpis'][] = $jobReq;
		}
		elseif ((int) $jobReq['ProjectID'] != 0 && $jobReq["ProjectID"] != NULL) {
			$details['reviewRequirements']['projects'][] = $jobReq;
		}
		elseif ((int) $jobReq['DutyID'] != 0 && $jobReq["DutyID"] != NULL) {
			$details['reviewRequirements']['duties'][] = $jobReq;
		}
		elseif ((int) $jobReq['SkillID'] != 0 && $jobReq["SkillID"] != NULL) {
			$details['reviewRequirements']['skills'][] = $jobReq;
		}
	}

	return $details;
}

function getReviewAnswers($conn, $reviewId, $confirmation = false) {
	$table = $confirmation ? "ReviewAnswers1" : "ReviewAnswers";
	$sql = "SELECT TOP 1 * FROM [dbo].[$table] WHERE ReviewID = " . $reviewId;
	$res = sqlsrv_query($conn, $sql);
	return sqlsrv_fetch_array($res, 2);
}

function getReviewRequirementScores($conn, $reviewId) {
	$sql = "SELECT * FROM [dbo].[ReviewRequirementScores] WHERE ReviewID = " . $reviewId;
	$res = sqlsrv_query($conn, $sql);

	while ($row = sqlsrv_fetch_array($res, 2)) {
		$reviewRequirementScores[$row['ReviewRequirement1ID']] = $row;
	}

	return $reviewRequirementScores ?? [];
}

function getReviewBenchmarks($conn, $reviewId) {
	$sql = "SELECT * FROM [dbo].[ReviewBenchmarks] WHERE ReviewID = " . $reviewId;
	$res = sqlsrv_query($conn, $sql);

	while ($row = sqlsrv_fetch_array($res, 2)) {
		$benchmarks[$row['ReviewBenchmarkID']] = $row;
	}

	return $benchmarks ?? [];
}

function getReviewRequirements($conn, $reviewId, $type) {
	$sql = "SELECT * FROM dbo.ReviewRequirements
	LEFT JOIN dbo.Duties ON dbo.Duties.DutyID = dbo.ReviewRequirements.DutyID
	LEFT JOIN dbo.Projects ON dbo.Projects.ProjectID = dbo.ReviewRequirements.ProjectID
	LEFT JOIN dbo.Skills ON dbo.Skills.SkillID = dbo.ReviewRequirements.SkillID
	LEFT JOIN dbo.KPIs ON dbo.KPIs.KPIID = dbo.ReviewRequirements.KPIID
	WHERE ReviewRequirements.ReviewID = " . $reviewId;

	if ($type == "kpi") {
		$sql .= " AND ReviewRequirements.KPIID IS NOT NULL";
	} elseif ($type == "project") {
		$sql .= " AND ReviewRequirements.ProjectID IS NOT NULL";
	} elseif ($type == "duties") {
		$sql .= " AND ReviewRequirements.DutyID IS NOT NULL";
	}

	$res = sqlsrv_query($conn, $sql);

	while ($row = sqlsrv_fetch_array($res, 2)) {
		$requirements[] = $row;
	}

	return $requirements ?? [];
}

?>
<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/applications-employees-functions.php");

$EmployeeID = $_GET['EmployeeID'] ?? null;
$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';
if ($EmployeeID === null || $EmployeeID == '') {
	die('Employee ID not provided');
}

$r = getEmployeeReviews($EmployeeID, $COLUMN, $ORDER);


$my_reviews = '';
foreach ($r['my_reviews'] ?? [] as $review) { 
	$my_reviews .= '<tr data-link="reviews.php?reviewId=' . $review['ReviewID'] . '">
			<td>' . $review['ReviewID'] . '</td>
			<td>' . ucfirst($review['ReviewTypeName']) . '</td>
			<td>' . ucfirst($review['ReviewStatusName']) . '</td>
			<td><a href="reviews.php?reviewId=' . $review['ReviewID'] . '">Open</a></td>
		</tr>';
}

$reviews = '';
foreach ($r['reviews_submitted_to_me'] ?? [] as $review) { 
	$reviews = '<tr data-link="reviews.php?reviewId=' . $review['ReviewID'] . '">
			<td>' . $review['ReviewID'] . '</td>
			<td>' . ucfirst($review['ReviewTypeName']) . '</td>
			<td>' . ucfirst($review['ReviewStatusName']) . '</td>
			<td>' . $review["FirstName"] . ' ' . $review["LastName"] . '</td>
			<td><a href="reviews.php?reviewId=' . $review['ReviewID'] . '">Open</a></td>
		</tr>';
}

echo json_encode(['reviews' => $reviews, 'my_reviews' => $my_reviews]);
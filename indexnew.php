<?php

include 'MentorClasstest.php';

	$user_id = $_REQUEST['uid'];
	$goal_id = $_REQUEST['gid'];
	
	$objMentor = new MentorClass();
	
	$menteeID = $user_id;
	$goalID   = $goal_id;

	$arrMentorsWithScore = $objMentor->calculateMentorScoreByGoalID($menteeID,$goalID);
	print_r($arrMentorsWithScore);
	
	die;
	
	
	
	

?>
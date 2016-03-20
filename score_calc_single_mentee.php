<?php

	include 'MentorClass.php';

	$user_id = $_REQUEST['uid'];
	$goal_id = $_REQUEST['gid'];
	
	$objMentor = new MentorClass();	
	
	$menteeID = $user_id;
	$goalID   = $goal_id;
	
	//call score rating for single goal id
	$objMentor->calCulateMentorScoreAll($menteeID,$goalID);
	
	$objMentor->get_top10_users($menteeID,$goalID);	
		
?>
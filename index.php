<?php

include 'MentorClass.php';

	$user_id = $_REQUEST['uid'];
	$goal_id = $_REQUEST['gid'];
	
	$objMentor = new MentorClass();
	
	$userDetails = $objMentor->get_user_details($user_id);
	$goalDetails = $objMentor->get_user_goal_details($user_id);
	$userGoalDetails = $objMentor->get_user_goal_by_id($goal_id);
	
	//PrimaryCity Details
	$primaryCityDetailsIDS = $objMentor->get_mentee_primary_city($user_id,$goal_id);
	
	$insert_mentor_score = $objMentor->insertMentorScore($primaryCityDetailsIDS,$user_id,$goal_id);


?>
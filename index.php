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
	
	//State Details
	$stateDetailsIDS = $objMentor->get_mentee_state_track($user_id,$goal_id);
	
	//Country Details
	//$countryDetailsIDS = $objMentor->get_mentee_country_track($user_id,$goal_id);
	
	if($primaryCityDetailsIDS){
		$userWValue = $objMentor->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Primary city"');
		$objMentor->insertMentorScore($primaryCityDetailsIDS,$user_id,$goal_id,@$userWValue);
	}else if(@$secondaryCityDetailsIDS){
		$userWValue = $objMentor->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Secondary city"');
		$objMentor->insertMentorScore($secondaryCityDetailsIDS,$user_id,$goal_id,@$userWValue);
	}else if(@$stateDetailsIDS){
		$userWValue = $objMentor->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="State"');
		$objMentor->insertMentorScore($stateDetailsIDS,$user_id,$goal_id,@$userWValue);
	}else if(@$countryDetailsIDS){
		$userWValue = $objMentor->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Country"');
		$objMentor->insertMentorScore($countryDetailsIDS,$user_id,$goal_id,@$userWValue);
	}
	


?>
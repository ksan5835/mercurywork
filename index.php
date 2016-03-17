<?php

include 'MentorClass.php';

	$user_id = $_REQUEST['uid'];
	$goal_id = $_REQUEST['gid'];
	
	$objMentor = new MentorClass();
	
	$userDetails = $objMentor->get_user_details($user_id);
	$goalDetails = $objMentor->get_user_goal_details($user_id);
	$userGoalDetails = $objMentor->get_user_goal_by_id($goal_id);
	
	//PrimaryCity Details
	//$primaryCityDetailsIDS = $objMentor->get_mentee_primary_city($user_id,$goal_id);
	
	//State Details
	//$stateDetailsIDS = $objMentor->get_mentee_state_track($user_id,$goal_id);
	
	//Country Details
	//$countryDetailsIDS = $objMentor->get_mentee_country_track($user_id,$goal_id);
	
	
	$menteeID = $user_id;
	$goalID   = $goal_id;

	$arrMentorsWithScore = $objMentor->calculateMentorScoreByGoalID($menteeID,$goalID);
	
	print_r($arrMentorsWithScore);
	
	
	
	die;
	/*
	
	$c = array_map(function () {
    return array_sum(func_get_args());
}, $a, $b);

print_r($c);
	
	
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
	}*/
	
/*$getExistsrecord = $this->getone('select id FROM mentor_mentee_score where user_id="'.$uid.'" and mentor_id="'.$UsersPC['user_id'].'" and goal_id="'.$gid.'"');
				
				if(!$getExistsrecord){	

					$userIDs[] = $UsersPC['user_id'];			
				}else
				{						
					$userWValue = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Primary city"');						
					$append_score = $getExistsrecord['mentor_score'] + $userWValue['weightage_value'];			
					$sql = "UPDATE mentor_mentee_score SET mentor_score='".$append_score."' where id=".$getExistsrecord['id']." and user_id=".$uid." and mentor_id=".$UsersPC['user_id']."";
					$this->execute($sql);
				}*/

?>
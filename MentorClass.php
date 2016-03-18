<?php

include 'mySqlAccess.php';

class MentorClass extends db{
	
	public function get_user_details($user_id){
		
		$userWValue = $this->getone('SELECT * FROM user where user_id="'.$user_id .'"');
		return $userWValue;
	}
	
	public function get_user_goal_details($gid){
		
		$userGoal = $this->getAll('SELECT * FROM goal where goal_id="'.$gid .'"');
		return $userGoal;
	}
	
	public function get_user_goals(){
		
		$userGoal = $this->getAll('SELECT * FROM goal where goal_status_id="1"');
		return $userGoal;
	}
	
	public function get_user_goal_by_id($goal_id){
		
		$userGoal = $this->getAll('SELECT * FROM goal where goal_id="'.$goal_id.'"');
		return $userGoal;
	}
	
	public function getCityByStateID($city_id){
		
		$userStateID = $this->getone('SELECT state_id FROM city where city_id="'.$city_id.'"');
		return $userStateID;
	}
	
	public function getCityByCountryID($state_id){
		
		$userStateID = $this->getone('SELECT country_id FROM state where state_id="'.$state_id.'"');
		return $userStateID;
	}
	
	
	public function calCulateMentorScoreAll($menteeID,$goalID)
	{
		
        $menteeStageID = "";
		$goalExpertiseID = "";
		
		
		//get mentee's details
		$menteeDetails = $this->getone('SELECT u.user_id,u.city_id,c.state_id,s.country_id FROM USER u, city c, state s WHERE c.city_id = u.city_id AND c.state_id = s.state_id AND u.user_type = 1 AND u.user_status = 1 and u.user_id = "'.$menteeID .'"');	
		$menteeStageDetails = $this->getone('SELECT company_stage_id FROM mentee_bio where user_id="'.$menteeID .'"');
		if($menteeStageDetails){
			$menteeStageID = $menteeStageDetails['company_stage_id'];
		}
		
		//goal Details
		$goalDetails = $this->getone('SELECT * FROM goal where goal_id="'.$goalID.'"');	
		if($goalDetails)
		{
			$goalExpertiseID = $goalDetails['expertise_id'];									
		}	
		
		
		
		
		//get mentors list
		$mentorsLists = $this->getAll('SELECT u.user_id,u.city_id,c.state_id,s.country_id FROM USER u, city c, state s WHERE c.city_id = u.city_id AND c.state_id = s.state_id AND u.user_type="2" AND u.user_status = 1');	
		
		foreach($mentorsLists as $mentor) {
			
			
			$mentorTotalScore = 0;
			
			$mentorScore = "";
			$mentorStageID = "";
			$mentorExpertiseID = "";
			$mentorUserID = $mentor['user_id'];
			$mentorCityID = $mentor['city_id'];
			$mentorStateID = $mentor['state_id'];
			$mentorCountryID = $mentor['country_id'];
			
			//get mentor Stage id
			$mentorCompStageDetails = $this->getone('SELECT company_stage_id FROM mentor_company_stage where mentor_user_id="'.$mentorUserID .'"');
			if($mentorCompStageDetails){
				$mentorStageID = $mentorCompStageDetails['company_stage_id'];
			}
			
			//get mentor expertise Details			
			$mentorExpertiseDetails = $this->getone('SELECT expertise_id FROM mentor_expertise where mentor_user_id="'.$mentorUserID .'"');
			if($mentorExpertiseDetails){
				$mentorExpertiseID = $mentorExpertiseDetails['expertise_id'];
			}
			
			
			if($menteeDetails){
						
				$menteeUserID = $menteeDetails['user_id'];
				$menteeCityID = $menteeDetails['city_id'];				
				$menteeStateID = $menteeDetails['state_id'];
				$menteeCountryID = $menteeDetails['country_id'];				
				
				//calculate primary city score
				$PrimaryCityScore = $this->calculateScoreByGeneral($menteeCityID,$mentorCityID,"Primary city");
				$mentorTotalScore += $PrimaryCityScore;
				
				//calculate state score
				$stateScore = $this->calculateScoreByGeneral($menteeStateID,$mentorStateID,"State");
				$mentorTotalScore += $stateScore;
				
				//calculate Country score
				$countryScore = $this->calculateScoreByGeneral($menteeCountryID,$mentorCountryID,"Country");
				$mentorTotalScore += $countryScore;
				
				//calculate mentor stage score
				$companyStageScore = $this->calculateScoreByGeneral($menteeStageID,$mentorStageID,"Mentoring stage");
				$mentorTotalScore += $companyStageScore;
				
				//calculate skills score
				$mentorExpertiseScore = $this->calculateScoreByGeneral($goalExpertiseID,$mentorExpertiseID,"Skills");
				$mentorTotalScore += $mentorExpertiseScore;
				
				//calculate rating score
				$mentorRatingScore = $this->calCulateMentorRatingByID($mentorUserID);
				$mentorTotalScore += $mentorRatingScore;

				//calculate pending request score
				$mentorPendingRequestScore = $this->calCulateMentorPendingRequestByID($mentorUserID);
				$mentorTotalScore += $mentorPendingRequestScore;
				
				//calculate reject score
				$mentorRejectRequestScore = $this->calCulateMentorRejectionRequestByID($mentorUserID);
				$mentorTotalScore += $mentorRejectRequestScore;
				
				echo "Mentor ID: ".$mentorUserID;
				echo "<br>";	
				echo "Primary Score - ".$PrimaryCityScore;
				echo "<br>";	
				echo "Stage Score - ".$stateScore;
				echo "<br>";	
				echo "Country Score - ".$countryScore;
				echo "<br>";	
				echo "Stage Score - ".$companyStageScore;
				echo "<br>";					
				echo "expertise Score - ".$mentorExpertiseScore;
				echo "<br>";				
				echo "Rating Score - ".$mentorRatingScore;
				echo "<br>";				
				echo "Pending Request Score - ".$mentorPendingRequestScore;
				echo "<br>";				
				echo "Reject Request Score - ".$mentorRejectRequestScore;
				echo "<br>";			
				echo "<br>";							
				echo "Total Score - ".$mentorTotalScore;
				echo "<br>";	
				
				echo "<br>";	
				echo "<hr>";	
				
				$this->insertMentorScore($menteeUserID ,$mentorUserID,$goalID,$mentorTotalScore);
			   
			}				
			
			
		}
		
		
	}
	
	
	public function calculateScoreByGeneral($cpvalue1,$cpvalue2,$weightageflag)	
	{
		$returnScore = 0;
		
		if($cpvalue1 == $cpvalue2)
		{
			$criteriaWeightage = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="'.$weightageflag.'"');
			if($criteriaWeightage)
			{
				$returnScore = $criteriaWeightage['weightage_value'];
			}
		}
		return $returnScore;
	}
	
	public function calCulateMentorRatingByID($mentorID)
	{
		$returnScore = 0;
		
		$averageScoreDetails = $this->getone('SELECT AVG(rating_givenby_mentee) as mentorRating FROM `goal` WHERE mentor_user_id = '.$mentorID);
		if($averageScoreDetails)
		{
			$criteriaWeightage = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Ratings"');
			$ratingWeightValue = $criteriaWeightage['weightage_value'];
			
			$averageScore = $averageScoreDetails['mentorRating'];
			$returnScore =  round(($averageScore / 5) * $ratingWeightValue);
			
		}
		
		return $returnScore;
	}
	
	public function calCulateMentorPendingRequestByID($mentorID)
	{
		$returnScore = 0;

		$totalRequest = $this->getone('SELECT request_count from mentor_request_count where mentor_user_id="'.$mentorID.'"');		
		
		if($totalRequest['request_count'])
		{
			$criteriaWeightage = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Request accept history"');
			$ratingWeightValue = $criteriaWeightage['weightage_value'];
			
			$rejectionRequest = $this->getone('SELECT rejection_count from mentor_rejection_count where mentor_user_id="'.$mentorID.'"');
			$totalAcceptRequest = $totalRequest['request_count'] - $rejectionRequest['rejection_count'];

			$returnScore =  round(($totalAcceptRequest / $totalRequest['request_count']) * $ratingWeightValue);
		}
		
		return $returnScore;
	}
	
	public function calCulateMentorRejectionRequestByID($mentorID)
	{
		$returnScore = 0;
		
		$totalRequest = $this->getone('SELECT request_count from mentor_request_count where mentor_user_id="'.$mentorID.'"');		

		if($totalRequest['request_count'])
		{
			$criteriaWeightage = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Request reject history"');
			$ratingWeightValue = $criteriaWeightage['weightage_value'];
			
			$rejectionRequest = $this->getone('SELECT rejection_count from mentor_rejection_count where mentor_user_id="'.$mentorID.'"');

			$returnScore =  $ratingWeightValue - round(($rejectionRequest['rejection_count'] / $totalRequest['request_count']) * $ratingWeightValue);
		}

		return $returnScore;
	}
	
	
	public function insertMentorScore($menteeID,$mentorID,$goalID,$mentorScore){
		
		$userMentorID = $mentorID;		
		$userMenteeID = $menteeID;	
		$userGoalID = $goalID;
		$userMentorScore = $mentorScore;		
				
			$getExistsrecord = $this->getone('select id FROM mentor_mentee_score where user_id="'.$userMenteeID.'" and mentor_id="'.$userMentorID.'" and goal_id="'.$userGoalID.'"');
			
			if(!$getExistsrecord){
			
				$sql = "INSERT INTO mentor_mentee_score (user_id, goal_id, mentor_id, mentor_score,create_systemuser_id,create_timestamp,update_systemuser_id,update_timestamp)
						VALUES ('".$userMenteeID."', '".$userGoalID."','".$userMentorID."', '".$userMentorScore."','".$userMenteeID."',now(),'".$userMenteeID."',now())";						
				$this->execute($sql);								
			}else{
				$sql = "UPDATE mentor_mentee_score SET mentor_score='".$userMentorScore."' where id=".$getExistsrecord['id']."";
				$this->execute($sql);
			}
		
		
	}
	
}

	
	
	

?>
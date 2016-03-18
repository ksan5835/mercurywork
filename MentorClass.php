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
				
				//calculdate primary city score
				$PrimaryCityScore = $this->calculateScoreByGeneral($menteeCityID,$mentorCityID,"Primary city");
				$mentorTotalScore += $PrimaryCityScore;
				
				//calculdate primary city score
				$stateScore = $this->calculateScoreByGeneral($menteeStateID,$mentorStateID,"State");
				$mentorTotalScore += $stateScore;
				
				//calculdate Country score
				$countryScore = $this->calculateScoreByGeneral($menteeCountryID,$mentorCountryID,"Country");
				$mentorTotalScore += $countryScore;
				
				//calculdate Country score
				$companyStageScore = $this->calculateScoreByGeneral($menteeStageID,$mentorStageID,"Mentoring stage");
				$mentorTotalScore += $companyStageScore;
				
				//calculdate Country score
				$mentorExpertiseScore = $this->calculateScoreByGeneral($goalExpertiseID,$mentorExpertiseID,"Skills");
				$mentorTotalScore += $mentorExpertiseScore;
				
				//calculdate rating Value
				$mentorRatingScore = $this->calCulateMentorRatingByID($mentorUserID);
				$mentorTotalScore += $mentorRatingScore;			
				
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
				
				echo "<br>";
								
				echo "Total Score - ".$mentorTotalScore;
				echo "<br>";	
				
				echo "<br>";	
				echo "<hr>";	
			   
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
	
	
	public function insertMentorScore($menteeID,$mentorID,$goalID,$mentorScore){
		
		$userMentorIDs = $arrMentorsWithScore;		
		//$userWValue = $userWValue['weightage_value'];
		print_r($userMentorIDs);
		
		
		foreach($userMentorIDs as $key => $value)
		{
			
			$getExistsrecord = $this->getone('select id FROM mentor_mentee_score where user_id="'.$uid.'" and mentor_id="'.$key.'" and goal_id="'.$gid.'"');
			
			if(!$getExistsrecord){
			
				$sql = "INSERT INTO mentor_mentee_score (user_id, goal_id, mentor_id, mentor_score,create_systemuser_id,create_timestamp,update_systemuser_id,update_timestamp)
						VALUES ('".$uid."', '".$gid."','".$key."', '".$value."','".$uid."',now(),'".$uid."',now())";
						
								$this->execute($sql);
								
								echo "New record created successfully<br />";
			}else{
				$sql = "UPDATE mentor_mentee_score SET mentor_score='".$value."' where id=".$getExistsrecord['id']." and user_id=".$uid." and mentor_id=".$key."";
				$this->execute($sql);
			}
		}
		
	}
	
}

	
	
	

?>
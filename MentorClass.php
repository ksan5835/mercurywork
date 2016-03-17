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
		
		$userStateID = $this->getone('SELECT country_id FROM city where city_id="'.$state_id.'"');
		return $userStateID;
	}
	
	
	public function calculateMentorScoreByGoalID($menteeID,$goalID)
	{
		$arrMentorsScores = array();
		unset($arrMentorScore);
		
		//calculate primaary score		
		$menteeDetails = $this->getone('SELECT * FROM user where user_id="'.$menteeID .'"');		
		
		if($menteeDetails){
			$menteeCityID = $menteeDetails['city_id'];
			
			//calculate primary city score
			$arrPrimaryCityMentorScore = $this->get_mentor_primary_city_score($menteeCityID);						
					
						
			//calculate State Score
			$arrStateMentorScore = $this->get_mentor_state_score($menteeCityID);
						
			
			
			
			$arrAllMentorKeys = array_keys($arrPrimaryCityMentorScore + $arrStateMentorScore);
			
					
			foreach ( $arrAllMentorKeys as $key) {
				
				$totKeyValue = 0;
				
				//primary city score
				if (array_key_exists($key,$arrPrimaryCityMentorScore))
				{
					$totKeyValue += $arrPrimaryCityMentorScore[$key]; 
				}
				
				
				//state score
				if (array_key_exists($key,$arrStateMentorScore))
				{
					$totKeyValue += $arrStateMentorScore[$key]; 
				}
				
				$arrMentorsScores[$key] = $totKeyValue;
			}			
			
			
		}
		
		return $arrMentorsScores;
	
	}
	
	//get mentors Primary City score or mentors list by gole id
	public function get_mentor_primary_city_score($menteeCityID,$dataflag = "weight"){
		
		$primaryMentors = array();
		unset($primaryMentors);
		
		$UsersPCs = $this->getAll('SELECT user_id FROM user where user_type="2" and city_id="'.$menteeCityID.'"');				
		
		foreach($UsersPCs as $UsersPC) {	
				
				$userWValue = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Primary city"');				
				
				if($dataflag == "weight"){				
					$primaryMentors[$UsersPC['user_id']] = $userWValue['weightage_value'];
				}
				else{
					$primaryMentors[] = $UsersPC['user_id'];
				}
		}	

		return $primaryMentors;
	}
	
	//get mentors state score
	public function get_mentor_state_score($menteeCityID,$dataflag = "weight"){		
			
		$stateMentors = array();
		unset($stateMentors);
		
		$getStateid  = $this->getCityByStateID($menteeCityID);		
		$UsersPCs = $this->getAll('SELECT user_id FROM user JOIN city ON city.city_id=user.city_id WHERE city.state_id="'.$getStateid['state_id'].'"');	
			
		foreach($UsersPCs as $UsersPC) {	
		
			$userIDs[] = $UsersPC['user_id'];	
			$userWValue = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="State"');						
			$stateScore = $userWValue['weightage_value'];			
			if($dataflag == "weight"){				
				$stateMentors[$UsersPC['user_id']] = $stateScore;
			}
			else{
				$stateMentors[] = $UsersPC['user_id'];
			}
		
		}	

		return $stateMentors;
	}
	
	public function insertMentorScore($mentorids , $uid , $gid , $userWValue){
		
		$userMentorID = $mentorids;		
		$userWValue = $userWValue['weightage_value'];
		
		for($i=0;$i<count($userMentorID);$i++){
			
				$sql = "INSERT INTO mentor_mentee_score (user_id, goal_id, mentor_id, mentor_score,create_systemuser_id,create_timestamp,update_systemuser_id,update_timestamp)
					VALUES ('".$uid."', '".$gid."','".$userMentorID[$i]."', '".$userWValue."','".$uid."',now(),'".$uid."',now())";
					
							$this->execute($sql);
							
							echo "New record".$i." created successfully<br />";

						
			}
		
	}
	
}

	
	
	

?>
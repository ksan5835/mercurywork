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
	
	
	public function calculateMentorScoreByGoalID($menteeID,$goalID)
	{
		$arrMentorsScores = array();
		unset($arrMentorScore);
		
		//calculate primaary score		
		$menteeDetails = $this->getone('SELECT * FROM user where user_id="'.$menteeID .'"');		
		
		if($menteeDetails){
			$menteeCityID = $menteeDetails['city_id'];
			$getmenteeStateID = $this->getone('SELECT state_id FROM city where city_id="'.$menteeCityID.'"');	
			$menteeStateID = $getmenteeStateID['state_id'];
			
			//calculate primary city score
			echo "Primary city";
			$arrPrimaryCityMentorScore = $this->get_mentor_primary_city_score($menteeCityID);						
			print_r($arrPrimaryCityMentorScore);
			echo "<br />";
			
						
			//calculate State Score
			echo "State city";
			$arrStateMentorScore = $this->get_mentor_state_score($menteeCityID);
			print_r($arrStateMentorScore);
			echo "<br />";
			
			//calculate State Score
			//$arrCountryMentorScore = $this->get_mentor_country_score($menteeStateID);
			//print_r($arrCountryMentorScore);
			//echo "<br />";
			
			
			
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
		$UsersPCs = $this->getAll('SELECT user_id FROM user JOIN city ON city.city_id=user.city_id WHERE city.state_id="'.$getStateid['state_id'].'" and user.user_type="2"');	
			
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
	
	//get mentors country score
	public function get_mentor_country_score($menteeStateID,$dataflag = "weight"){		
			
		$countryMentors = array();
		unset($countryMentors);
		
		$getCountryid  = $this->getCityByCountryID($menteeStateID);		
		$UsersPCs = $this->getAll('SELECT user_id FROM user JOIN city ON city.city_id=user.city_id WHERE city.state_id="'.$getCountryid['country_id'].'" and user.user_type="2"');	
			
		foreach($UsersPCs as $UsersPC) {	
		
			$userIDs[] = $UsersPC['user_id'];	
			$userWValue = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Country"');						
			$countryScore = $userWValue['weightage_value'];			
			if($dataflag == "weight"){				
				$countryMentors[$UsersPC['user_id']] = $countryScore;
			}
			else{
				$countryMentors[] = $UsersPC['user_id'];
			}
		
		}	

		return $countryMentors;
	}
	
	public function insertMentorScore($arrMentorsWithScore , $uid , $gid){
		
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
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
	
	public function get_mentee_primary_city($uid,$gid){
		
		$menteeCityID = $this->getone('SELECT city_id FROM user where user_id="'.$uid .'"');
		
		$UsersPCs = $this->getAll('SELECT user_id FROM user where user_type="2" and city_id="'.$menteeCityID['city_id'].'"');
		
		$i = 1;
		
		$userIDs = array();
		
		foreach($UsersPCs as $UsersPC) {			
				
				$getExistsrecord = $this->getone('select id FROM mentor_mentee_score where user_id="'.$uid.'" and mentor_id="'.$UsersPC['user_id'].'" and goal_id="'.$gid.'"');
				
					if(!$getExistsrecord){	

							$userIDs[] = $UsersPC['user_id'];
			
				}

			}	

		return $userIDs;
	}
	
	public function insertMentorScore($mentorids , $uid , $gid){
		
		$userMentorID = $mentorids;
		
		for($i=0;$i<count($userMentorID);$i++){
			
			$userWValue = $this->getone('select weightage_value FROM mentor_mentee_weightage_criteria where weightage_criteria="Primary city"');
			
				$sql = "INSERT INTO mentor_mentee_score (user_id, goal_id, mentor_id, mentor_score,create_systemuser_id,create_timestamp,update_systemuser_id,update_timestamp)
					VALUES ('".$uid."', '".$gid."','".$userMentorID[$i]."', '".$userWValue['weightage_value']."','".$uid."',now(),'".$uid."',now())";
					
							$this->execute($sql);
							
							echo "New record".$i." created successfully<br />";

						
			}
		
	}
	
}

	
	
	

?>
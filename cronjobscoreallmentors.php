<?php

	include 'MentorClass.php';

	//goal loop
	$objLoopMentor = new MentorClass();	
	
	$menteeAllGoals = $objLoopMentor->get_user_goals();
	
	foreach($menteeAllGoals as $menteeAllGoal){
		
		$menteeID = $menteeAllGoal['mentee_user_id'];
		$goalID   = $menteeAllGoal['goal_id'];
		
		//call score rating for single goal id
		$objLoopMentor->calCulateMentorScoreAll($menteeID,$goalID);
	}	

?>
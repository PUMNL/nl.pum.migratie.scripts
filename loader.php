<?php

// Education
$limit = 0;
for($i = 1; $i < 9; $i++){

	if($i < 4) {
		
		$output = shell_exec("php ./education.php create ".$limit);
		echo $output;
		$limit = $limit + 2500;
		
	} else {
		
		if($i == 4) $limit = 0;
		$output = shell_exec("php ./education.php forge ".$limit);
		echo $output;
		$limit = $limit + 2500;
	
	}

}
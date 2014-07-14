<?php

//functions for DDD
	function displaytexttoprocess($articleid){
	
		//display text and D buttons
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$query = "SELECT Article_Text FROM Source_Article WHERE Article_id = $article_id";
		$result = mysqli_query($dbc, $query);
  		while ($row = mysqli_fetch_assoc($result)) {
  			$text = $row['Article_Text'];
  			$text = str_replace('\\', '', $text);
  			echo '<p>' . $text . '</p><br />';
			mysqli_close($dbc);
		}
		//add buttons
		echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
		echo '<button name="d_cat" type="submit" value="1">Deny</button>';
		echo '<button name="d_cat" type="submit" value="2">Disrupt</button>';
		echo '<button name="d_cat" type="submit" value="3">Degrade</button>';
		echo '<button name="d_cat" type="submit" value="4">Deceive</button>' ;
		echo '<input type="hidden" name="article_id" value="' . $article_id .'">';
		echo '</form>';


	}

?>

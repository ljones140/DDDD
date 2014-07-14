<?php

//functions for DDDD

//inserts source text into database
function insertsource_article($source_text){
	global $article_id;
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        /* check connection */
        if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
        }
        $source_text = mysqli_real_escape_string($dbc, $source_text);
        $query = "INSERT INTO Source_Article (Article_Text,Date_Created) VALUES ('$source_text',now())";
        mysqli_query($dbc, $query);
        $article_id = mysqli_insert_id($dbc);
        mysqli_close($dbc);
}


//displays the text to be processed from Article_text and the D buttons
function displaytexttoprocess($article_id){
	
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT Article_Text FROM Source_Article WHERE Article_id = $article_id";
	$result = mysqli_query($dbc, $query);
  	while ($row = mysqli_fetch_assoc($result)) {
  		$text = $row['Article_Text'];
  		$text = str_replace('\\', '', $text);
  		echo '<p>' . $text . '</p><br />';
		mysqli_close($dbc);
	}
	//display buttons
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
	echo '<button name="dcat_id" type="submit" value="1">Deny</button>';
	echo '<button name="dcat_id" type="submit" value="2">Disrupt</button>';
	echo '<button name="dcat_id" type="submit" value="3">Degrade</button>';
	echo '<button name="dcat_id" type="submit" value="4">Deceive</button>' ;
	echo '<input type="hidden" name="article_id" value="' . $article_id .'">';
	echo '</form>';


}

//function to calculate amount of matches in text string
function process_matches($d_cat_id, $article_id) {
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT Data_type_id, Match_Term, Replace_Term FROM Match_Replacement WHERE Data_Type_Id = 1" .
		"AND Cat_id = $d_cat_id";
	$result = mysqli_query($dbc, $query);
}


?>

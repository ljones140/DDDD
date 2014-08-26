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
function fetchoriginaltext($article_id){
	global $text, $article_id;
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT Article_Text FROM Source_Article WHERE Article_id = $article_id";
	$result = mysqli_query($dbc, $query);
  	while ($row = mysqli_fetch_assoc($result)) {
  		$text = $row['Article_Text'];
  //		$text = str_replace('\\', '', $text);
		mysqli_close($dbc);
	}
}




function displaybuttons($article_id) {
	global $article_id;
	//display buttons
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
	echo '<button name="dcat_id" type="submit" value="1">Deny</button>';
	echo '<button name="dcat_id" type="submit" value="2">Disrupt</button>';
	echo '<button name="dcat_id" type="submit" value="3">Degrade</button>';
	echo '<button name="dcat_id" type="submit" value="4">Deceive</button>' ;
	echo '<input type="hidden" name="article_id" value="' . $article_id .'">';
	echo '<input type="hidden" name="text" value="' . $text .'">';
	echo '</form>';


}

//function to calculate amount of matches in text string
function process_matches($dcat_id, $text, $article_id) {
	global $text, $article_id, $dcat_id, $matches ,$processed_id, $dtype;

	//degrade sentence replace
	if ($dcat_id = 3){ 
		$dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        	$query = "SELECT Replace_Term FROM Match_Replacement WHERE Cat_id = 3 AND Data_type_id = 2";
                $result = mysqli_query($dbc, $query);
                while ($row = mysqli_fetch_array($result)){
			$replacesentences[] = $row;
		}
        	mysqli_close($dbc);

		$sentences = explode(". ", $text);

		for ($i = 2; $i < count($sentences); $i += 3) {
			$sentences[$i] =  $replacesentences[mt_rand(0, count($replacesentences) - 1)][0]; 
//			$sentences[$i] = str_replace($sentences[$i], $replacesentences[mt_rand(0, count($replacesentences))][0], $sentences[$i]);
//			array_splice($sentences, $i, 1, $replacesentences[mt_rand(0, count($replacesentences) - 1)][0] );
		}

		$text = implode(". ", $sentences);
//		echo '<h2>' . $replacesentences[0][0]. '</h2>';	
//		echo '<h2>' . $replacesentences[1][0]. '</h2>';	
//		var_dump($replacesentences);
//		var_dump($sentences);
	}
	



	$dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "SELECT Cat_id,  Data_type_id, Match_Term, Replace_Term FROM Match_Replacement WHERE Cat_id in ($dcat_id,  5)" .
			"AND Data_type_id = 1";
                $result = mysqli_query($dbc, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                        $find = $row['Match_Term'];
                //      $pos = strpos($text, $find);
                //      if ($pos !== false) {
                        if (preg_match("/\b$find\b/", $text)) {
                                $matches[] = $row;
                        }
                }
        mysqli_close($dbc);


        //do insert into processed artilce table and the flagfging table
	$dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "INSERT INTO Processed_Article (text, D_Catid, Datecreated) ".
			" VALUES ('$text', $dcat_id , now() )";

	mysqli_query($dbc, $query);
        $processed_id = mysqli_insert_id($dbc);
        mysqli_close($dbc);


        $dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "INSERT INTO Article_Process_Link VALUES ($article_id, $processed_id)";

        mysqli_query($dbc, $query);
        mysqli_close($dbc);

	switch($dcat_id) {
		case 1:
			$dtype = 'Deny';
			break;
		case 2:
			$dtype = 'Disrupt';
			break;
		case 3:
			$dtype = 'Degrade';
			break;
		case 4:
			$dtype = 'Deceive';
			break;

	}
	
}

function fetch_processed_text($processed_id){
	global $processingtext;
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT Text FROM Processed_Article WHERE Pro_Art_id = $processed_id";
		$result = mysqli_query($dbc, $query);
		while ($row = mysqli_fetch_assoc($result)) {
			$processingtext = $row['Text'];

	}
	mysqli_close($dbc);
}

function update_processed_text($processed_id, $processingtext){
	global $processed_id, $processingtext;	
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $processingtext = mysqli_real_escape_string($dbc, $processingtext);
	$query = "UPDATE Processed_Article SET Text = '$processingtext' WHERE Pro_Art_id = $processed_id";
        mysqli_query($dbc, $query);
        mysqli_close($dbc);
}


?>

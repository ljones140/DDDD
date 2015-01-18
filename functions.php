<?php

//functions for DDDD

//Gets text from a given URL using curl and readability

function fetch_url_article($url){
	global $sourcetext, $url, $article_id, $text;
	//initate session
        $cSession = curl_init();
        //set web page
        curl_setopt($cSession,CURLOPT_URL, $url);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true); //will tell curl to return the string instead of print it out 
        curl_setopt($cSession,CURLOPT_HEADER, false); //will tell curl to ignore the header
        //execute session
        $result=curl_exec($cSession);
        //close session
	if(curl_errno($cSession)){
    		echo '<p>Sorry we are unable to use the supplier url. error:' . curl_error($cSession) . '</p>' ;
	}
	else{
        	curl_close($cSession);

        	$html = $result;
		
	        require 'lib/Readability.inc.php';

//	        $Readability     = new Readability($html, $html_input_charset); // default charset is utf-8
        	$Readability     = new Readability($html, 'utf-8'); // default charset is utf-8
        	$ReadabilityData = $Readability->getContent(); // throws an exception when no suitable content is found
		
//        	var_dump($ReadabilityData);
//      	echo "<h1>".$ReadabilityData['title']."</h1>";
//      	echo $ReadabilityData['content'];
		
		$source_text = '<div class="articlehead">' . $ReadabilityData['title'] . '</div>' . $ReadabilityData['content'];
		
		if(strlen($source_text) > 0){
			
			insertsource_article($source_text, $url);
			if ($article_id > 0) {
                		fetchoriginaltext($article_id);
//                		echo '<div class="article"><p>' . $text . '</div></p><br />';
//                		displaybuttons($article_id);
        		} else echo '<h4>Error here</h4>';
		}
		else { 
			echo '<p>Sorry afriad we cannot process this artilce. Do try again</p>';
		}
	}


}





//inserts source text into database
function insertsource_article($source_text, $url){
	global $article_id;
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        /* check connection */
        if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
        }
        $source_text = mysqli_real_escape_string($dbc, $source_text);
        $query = "INSERT INTO Source_Article (Article_Text,Date_Created, URL) VALUES ('$source_text',now(),'$url' )";
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
  		$text = str_replace('\\', '', $text);
		$text = str_replace('\n', '', $text);
		mysqli_close($dbc);
	}
}




function displaybuttons($article_id) {
	global $article_id;
	//display buttons
	echo '<fieldset>';
	echo '<legend>Select Process</legend>';
	echo '<form method="post" class="dddd-button" action="' . $_SERVER['PHP_SELF'] . '">';
	echo '<ul>';
	echo '<li><button name="dcat_id" type="submit" value="1" class="styled-button">Deny</button></li>';
	echo '<li><button name="dcat_id" type="submit" value="2" class="styled-button">Disrupt</button></li>';
	echo '<li><button name="dcat_id" type="submit" value="3" class="styled-button">Degrade</button></li>';
	echo '<li><button name="dcat_id" type="submit" value="4" class="styled-button">Deceive</button></li>' ;
	echo '</ul>';
	echo '<input type="hidden" name="article_id" value="' . $article_id .'">';
	echo '<input type="hidden" name="text" value="' . $text .'">';
	echo '</form>';
	echo '</fieldset>';


}

//function to calculate amount of matches in text string
function process_matches($dcat_id, $text, $article_id) {
	global $text, $article_id, $dcat_id, $matches ,$processed_id, $dtype;

	//degrade sentence replace
	if ($dcat_id == 3){ 
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
		}

		$text = implode(". ", $sentences);
	}
	



	$dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "SELECT Cat_id,  Data_type_id, Match_Term, Replace_Term FROM Match_Replacement WHERE Cat_id in ($dcat_id,  5)" .
			"AND Data_type_id = 1";
                $result = mysqli_query($dbc, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                        $find = $row['Match_Term'];
                        if (preg_match("/\b$find\b/", $text)) {
                                $matches[] = $row;
                        }
                }
        mysqli_close($dbc);


        //do insert into processed artilce table and the flagging table
	$dbc= mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$text = mysqli_real_escape_string($dbc, $text);
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
			$processingtext = str_replace('\\', '', $processingtext);
			$processingtext = str_replace('\n', '', $processingtext);


	}
	mysqli_close($dbc);
}

function update_processed_text($processed_id, $processingtext){
	global $processed_id, $processingtext;	
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//	below line commented ut as was adding unwanted '\' in html
//        $processingtext = mysqli_real_escape_string($dbc, $processingtext);
	$query = "UPDATE Processed_Article SET Text = '$processingtext' WHERE Pro_Art_id = $processed_id";
        mysqli_query($dbc, $query);
        mysqli_close($dbc);
}


?>

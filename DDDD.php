<?php
require('connections.php');
require('functions.php');
//Put refreshing header here

if (isset($_POST['dcat_id'])){
        $dcat_id = $_POST['dcat_id'];
	$article_id = $_POST['article_id'];

	//fetch original article text
	fetchoriginaltext($article_id);

	//build the array of matches	
	process_matches($dcat_id, $text, $article_id);

	//serialize the match array to be sent to next refresh 
	$serializedmatches = base64_encode(serialize($matches));

	//refresh page while sending processid and matches array
	$page = $_SERVER['PHP_SELF'];
 	$sec = "1";
	header("Refresh: $sec; url=$page?processid=$processed_id&matches=$serializedmatches" );


}



//unserialize the array, take off the current match and if still more refresh and pass it on
if (isset($_GET['matches'])){
	$processed_id = $_GET['processid'];
	$serializedmatches = $_GET['matches'];

	$matches = unserialize(base64_decode($serializedmatches));


	if (!empty($matches[0]['Replace_Term'])){
		$currentmatches[] = end($matches);
		unset ($matches[count($matches)-1]);
		$serializedmatches = base64_encode(serialize($matches));
		$page = $_SERVER['PHP_SELF'];
        	$sec = "1";
        	header("Refresh: $sec; url=$page?processid=$processed_id&matches=$serializedmatches" );
	}

}


?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Deny, Disrupt, Degrade and Deceive</title>

 <link rel="stylesheet" type="text/css"  href="dddd.css"> 

</head>
<body>

<?php

echo '<div id="header">';
if (!isset($dtype)){
	echo '<h1>Deny, Disrupt, Degrade and Deceive</h1>';
} else {

	echo '<h1>' . $dtype . '</h1>'; 
}
echo '</div>'; 





?>

<?php


//check to see if 1st time to the page if so diplay text ares to enter text 
if (!isset($_POST['source_text']) && !isset($_POST['url']) && !isset($_POST['dcat_id']) && !isset($_GET['matches']) ) { 
//	echo '<h2>Please Enter Text to be Submitted Below</h2>';
	echo '<fieldset>';
	echo '<legend>Submit Your Article</legend>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" id="textinput">';
	echo '<div class="input">';
//	echo '<textarea rows="20" cols = "50" name="source_text" form="textinput">Enter Text Here.....</textarea>';
	echo '<label for="textinput" class="title">Text:</label>';
	echo '<input type="text" name="source_text">';
	echo '<input type="hidden" name="URL" value=" ">';
//	echo '<br />';
	echo '<input type="submit" class="styled-button">';
	echo '</div>';
	echo '</form>';
	
//	echo '<p>Or better yet enter a URL</p>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF']. '" id="urlinput" >';
	echo '<div class="input">';
	echo '<label for="url" class="title">Url:</label>';
	echo '<input type="url" name="url">';
	echo '<input type="submit" class="styled-button">';
	echo '</div>';
	echo '</form>';
	echo '</fieldset>';

}

//if text added then insert text to db,  display the text and the DDDD buttons
if (isset($_POST['source_text'])){
	$source_text = $_POST['source_text'];
	$url = $_POST['url'];
	insertsource_article($source_text, $url);
	
	if ($article_id > 0) {
		fetchoriginaltext($article_id);
		echo '<div class="article"><p>' . $text . '</p></div><br />';
		displaybuttons($article_id);
	} else echo '<h4>Error</h4>';
}

if (isset($_POST['url'])){
	$url = $_POST['url'];
	fetch_url_article($url);
}



//this does the matching and replacing 
if (isset($_GET['matches'])){ 

//	var_dump($currentmatches);

	fetch_processed_text($processed_id);
	
	if (!empty($currentmatches[0]['Replace_Term'])){
		$find = $currentmatches[0]['Match_Term'];
		$replace = '<strong>' . $currentmatches[0]['Replace_Term'] . '</strong>';
		
		$processingtext = preg_replace("/\b$find\b/", $replace, $processingtext);
	
		update_processed_text($processed_id, $processingtext);	

	}
	
	echo '<p>' . $processingtext . '</p>';

	//puts link to start again if matches empty
	if (empty($currentmatches[0]['Replace_Term'])){
		echo '<br /><a href="' . $_SERVER['PHP_SELF'] .'">Start Again</a>';
	}

}



?>

</body>
</html>

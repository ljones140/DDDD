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
 	$sec = "2";
	header("Refresh: $sec; url=$page?processid=$processed_id&matches=$serializedmatches" );


}

//testing
/*	echo 'full list <br />';
	foreach ($matches as $match) {

	echo $match['Match_Term'] .'<br />' ;
	echo $match['Replace_Term'] .'<br />' ;
	$matchendtest[] = end($matches);

//	var_dump($matchendtest);

	echo $processed_id;

	echo '<br /> leftovers <br />';

	unset ($matches[count($matches)-1]);

//	$leftovers = array_pop($matches);
        foreach ($matches as $match) {

        echo $match['Match_Term'] .'<br />' ;
        echo $match['Replace_Term'] .'<br />' ;

        }

	echo'<br /> the match on this turn <br />';

	foreach ($matchendtest as $match) {

        echo $match['Match_Term'] .'<br />' ;
        echo $match['Replace_Term'] .'<br />' ;

        }

*/


//unserialize the array, take off the current match and if still more refresh and pass it on
if (isset($_GET['matches'])){
	$processed_id = $_GET['processid'];
	$serializedmatches = $_GET['matches'];

	$matches = unserialize(base64_decode($serializedmatches));

	$currentmatches[] = end($matches);

	if (!empty($matches[0]['Replace_Term'])){

		unset ($matches[count($matches)-1]);
		$serializedmatches = base64_encode(serialize($matches));
		$page = $_SERVER['PHP_SELF'];
        	$sec = "2";
        	header("Refresh: $sec; url=$page?processid=$processed_id&matches=$serializedmatches" );
	}

}


?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Deny, Disrupt, Degrade and Deceive</title>

 <!-- <link rel="stylesheet" href="css/styles.css?v=1.0"> -->

</head>
<body>

<?php

if (!isset($dtype)){
	echo '<h1>Deny, Disrupt, Degrade and Deceive</h1>'; 
} else {

	echo '<h1>' . $dtype . '</h1>'; 
}






?>

<?php


//check to see if 1st time to the page if so diplay text ares to enter text 
if (!isset($_POST['source_text']) && !isset($_POST['dcat_id']) && !isset($_GET['matches']) ) { 

	echo '<h2>Please Enter Text to be Submitted Below</h2>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '"id="textinput">';
	echo '<textarea rows="20" cols = "50" name="source_text" form="textinput">Enter Text Here.....</textarea>';
	echo '<br />';
	echo '<input type="submit">';
	echo '</form>';

}

//if text added then insert text to db,  display the text and the DDDD buttons
if (isset($_POST['source_text'])){
	$source_text = $_POST['source_text'];
	insertsource_article($source_text);
	
	if ($article_id > 0) {
		fetchoriginaltext($article_id);
		echo '<p>' . $text . '</p><br />';
		displaybuttons($article_id);
	} else echo '<h4>Error</h4>';
}


 
if (isset($_GET['matches'])){ 

	var_dump($currentmatches);

	if (empty($currentmatches[0]['Replace_Term'])){
		echo '<br /><a href="' . $_SERVER['PHP_SELF'] .'">Start Again</a>';
	}

}



?>

</body>
</html>

<?php
require('connections.php');
require('functions.php');
//Put refreshing header here

if (isset($_POST['dcat_id'])){
        $dcat_id = $_POST['dcat_id'];

	process_matches($dcat_id, $article_id, $text);

	$page = $_SERVER['PHP_SELF'];
 	$sec = "5";
	header("Refresh: $sec; url=$page");
 	echo "Watch the page reload itself in 5 second!";
	echo $matches[0];



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

  <h1>Deny, Disrupt, Degrade and Deceive</h1>

<?php


//check to see if 1st time to the page if so diplay text ares to enter text 
if (!isset($_POST['source_text']) && !isset($_POST['dcat_id'])) { 

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
		displaytexttoprocess($article_id);
	} else echo '<h4>Error</h4>';
}


 
        



?>

</body>
</html>

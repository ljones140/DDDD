<?php
require('connections.php');
require('functions.php');
//Put refreshing header here

if (isset($_POST['dcat_id'])){
        $dcat_id = $_POST['dcat_id'];
	$article_id = $_POST['article_id'];

	fetchoriginaltext($article_id);
	

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "SELECT Cat_id,  Data_type_id, Match_Term, Replace_Term FROM Match_Replacement WHERE Cat_id in ($dcat_id,  5)";
                $result = mysqli_query($dbc, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                	$find = $row['Match_Term'];
		//	$find = 'Deny';
                      	$pos = strpos($text, $find);
                       	if ($pos !== false) {
                         	$matches[] = $row;
                        }                       
                }

	mysqli_close($dbc);

	$serializematches = base64_encode(serialize($matches));
	

	$page = $_SERVER['PHP_SELF'];
 	$sec = "20";
	header("Refresh: $sec; url=$page");


	echo 'full list <br />';
	foreach ($matches as $match) {

	echo $match['Match_Term'] .'<br />' ;
	echo $match['Replace_Term'] .'<br />' ;

	}



	$matchendtest[] = end($matches);

//	var_dump($matchendtest);

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
		fetchoriginaltext($article_id);
		echo '<p>' . $text . '</p><br />';
		displaybuttons($article_id);
	} else echo '<h4>Error</h4>';
}


 
        



?>

</body>
</html>

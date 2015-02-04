

<?php


require('connections.php');
session_start();
  // If the session vars aren't set, try to set them with a cookie
 	if (!isset($_SESSION['user_id'])) {
    		if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
      			$_SESSION['user_id'] = $_COOKIE['user_id'];
      			$_SESSION['username'] = $_COOKIE['username'];
    		}
 	 }


require_once('header.php');


	if (!isset($_SESSION['user_id'])) {
    		echo '<p>Please <a href="login.php">log in</a> to access this page.</p>';
    		exit();
  	}
	else {
    		echo('<p>You are logged in as ' . $_SESSION['username'] . '</p>');
  	}
	

//SQL change Word Match Replacement, user and time. htmlspecialchars for inserted word

	if(isset($_POST['newword'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$newword = mysqli_real_escape_string($dbc, $_POST['newword']);
		if (!empty($newword)){
			$query = "SELECT * FROM word where word = '$newword'";
			$result  = mysqli_query($dbc, $query);
			if(mysqli_num_rows($result) > 0) {
				echo '<h2>Word Already Exists</p>';
			}
			else {
				$query = "INSERT INTO word (word) values ('$newword')";
				mysqli_query($dbc, $query) or die ('Error Querying Database');
			
				echo '<p>New Word Added: ' . $newword . '</p>';
			}
			mysqli_close($dbc);
		}
		else {
			echo '<p>Your word is Empty Dummy!!!</p>';
		}
	}		
?>

	<h2>Enter New Words</h2>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="Match">
	<input type="text" name="Replace">
	<select name="cat_id">
 	<option value="1">Deny</option>
 	<option value="2">Disrupt</option>
 	<option value="3">Degrade</option>
 	<option value="4">Decieve</option>
	</select>
	<select name ="replace_type">
	<option value="1">Match Replace</option>
        <option value="2">Sentence Replace</option>
        </select>
	<input type="submit">
	</form>


	//Build HTML Table

	<h2>Select words to remove</h2>	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="todelete" >

<?php


	//SQL Change for delete/try moving the form above

	if(isset($_POST['todelete'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		foreach ($_POST['todelete'] as $delete_id){
			$query = "DELETE FROM word WHERE wordid = $delete_id";
			mysqli_query($dbc, $query) or die ('Error Querying Database');
		}
		echo '<p>Words removed</p>';
		mysqli_close($dbc);
	}

	//Continue to build table and think about pagination

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$query = "select ".
			"mr.match_term ".
			",mr.replace_term ".
			",dt.description ".
			",mr.date_added ".
			",mr.added_by ".
			"from Match_Replacement mr ".
			"inner join D_Category d ".
			"on d.cat_id = mr.cat_id ".
			"inner join Data_Type dt ".
			"on dt.data_type_id = mr.data_type_id";



        	$result = mysqli_query($dbc, $query);
        	while ($row = mysqli_fetch_assoc($result)){
			echo '<input type="checkbox" value="' . $row['match_term']. '"name="todelete[]"/>';
			echo ' ' . $row['match_term'];
			echo '<br />';
        }
	
	mysqli_close($dbc);
?>

	
		<input type="submit" name="submit" value="remove" />

	//Table and pagination	
		
	</form>

</body>
</html>

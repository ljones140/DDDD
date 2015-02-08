<!DOCTYPE html>
<html>
<head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}
</style>

</head>
<body>

<?php

$catid = intval($_GET['q']);

	switch($catid) {
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

//echo '<div class="message"> <h2>' . $dtype . '</h2></div>';
?>
        <form class="normform" id="newMRenrty" method="post" action="/wordadmin.php">
        <fieldset>
<?php	echo "<legend>Enter New $dtype Entry</legend>"; ?>
	<span id="Match"> 
	<label for="Match">Match</label>
	<input type="text" name="Match"></span>
	<label for="Replace">Replacement</label>
        <input type="text" name="Replace">
	<input type="hidden" name="catid" value="<?php echo $catid; ?>">
<?php

if ($catid == 3) {
	echo '<label for="Find Replace">find replace</label>';
        echo '<input type="radio" name="dtype"  value="1" checked="checked" onclick="showbox(this);"> ';
	echo '<label for="Sentence">Sentence</label>';
        echo '<input type="radio" name="dtype"  value="2" onclick="hidebox(this);">';
        echo '</select>';
}
?>
        <input type="submit">
	</fieldset>
        </form>


<form class = "normform" method="post" action="/wordadmin.php" id="todelete" >
	<fieldset>

<?php
echo "<legend>View and remove $dtype entries</legend>";
require_once('connections.php');



echo '<table>';
echo '<tr>';
echo '<th>Delete</th>';
echo '<th>Match</th>';
echo '<th>Replacement</th>';
echo '<th>Data Type</th>';
echo '<th>Date Added</th>';
echo '<th>Added by</th>';
echo '</tr>';

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                $query = "select ".
                        "mr.match_id ".
			",mr.match_term ".
                        ",mr.replace_term ".
                        ",dt.description ".
                        ",mr.date_added ".
                        ",mr.added_by ".
                        "from Match_Replacement mr ".
                        "inner join D_Category d ".
                        "on d.cat_id = mr.cat_id ".
                        "inner join Data_Type dt ".
                        "on dt.data_type_id = mr.data_type_id ".
			"where mr.cat_id = $catid";

                $result = mysqli_query($dbc, $query);
                while ($row = mysqli_fetch_assoc($result)){
                        echo '<tr>';
			echo '<td><input type="checkbox" value="' . $row['match_id']. '"name="todelete[]"/></td>';	
                        echo '<td>' . $row['match_term']   . '</td>' ;
                        echo '<td>' . $row['replace_term'] . '</td>' ;
                        echo '<td>' . $row['description']  . '</td>' ;
                        echo '<td>' . $row['date_added']   . '</td>' ;
                        echo '<td>' . $row['added_by']     . '</td>' ;
			echo '</tr>';
       		 }

echo '</table>';

        mysqli_close($dbc);

?>


	<input type="hidden" name="catid" value="<?php echo $catid; ?>">
     	<input type="submit" name="submit" value="remove" />
	</fieldset>
</form>

</body>
</html>





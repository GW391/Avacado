<?php

// Set to system page
$system = true;

if (isset($shiftloaded)){
	if ($shiftloaded){
	}else{
		require ("template/asc_shift.php");
	}
}else{
	require ("template/asc_shift.php");
}

//echo "33";
// set security
if(stripos($_SESSION['security'], parameters('SendNewsSecurity')) !== FALSE){
// clear any open SQL
//mysql_free_result($result);
echo "security cleared";
$Select = "Email, RName, Deleted, PVD, fails, data, Test";
$From = "tblnewsletter";
$GROUP = null;
$die = "Sorry there is a problem on this page please, try again later";
$where = "Deleted = 0 AND PVD = 1";
$Limit = null;
$sort = null;
$result = SQL($Select, $From, $die, $where, $Limit, $GROUP, $sort);

//echo $result;

	?>
	<style>
	table {
  text-align: left;
  position: relative;
}

th {
  background: white;
  position: sticky;
  top: 0;
  //z-index: 0;
}

#managerTable {
    max-height: 300px;
    overflow: auto;
}
</style>
<h2>Subscribed users</h2>
<div id="managerTable" >
	<table border="1">
	<tr>
	<th>Name</th>
	<th>Email</th>
	<th>Fails</th>
	<th>data</th>
	<th>Test</th>
	</tr>
	<?php

while ($row = fetch_array($result)){
		$Name = validate(decrypt($row['RName']),'hd');
		$email = validate(decrypt($row['Email']),'hd');
		echo "<tr>";
		echo "<td>" . $Name . "</td>";
		echo "<td>" ;
		$str = $email;
		$pattern = '/...@..../i';
		echo preg_replace($pattern, '***@***', $str);
		echo  "</td>";
		echo "<td>" . validate($row['fails'],'hd')  . "</td>";
		echo "<td>" . validate($row['data'],'hd')  . "</td>";
		echo "<td>" . validate($row['Test'],'hd')  . "</td>";
		echo "</tr>";

/*        $to = validate(decrypt($row['RName']),'hd') . "&lt;" . validate(decrypt($row['Email']),'hd') . "&gt;";
        
	echo $to;
	echo "<br />";*/
}
}
free_results($result);
?>

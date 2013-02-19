<html>
<head><title>Devlogger (WIP)</title></head>
<body style="font-family: Verdana,Helvetica,sans-serif;font-size: 10pt;line-height: 125%;">
<br>
<br>
<center>
<?php
require_once('config.php');

define(db_link, mysql_connect(db_host,db_user,db_pass));
mysql_select_db(db_name);


function get_latest_completed_date( $project_name ) 
{
	// SELECT * FROM todo_tasks WHERE date_completed = (SELECT MAX(date_completed) from todo_tasks);
	// SELECT * FROM todo_tasks WHERE date_completed = (SELECT MAX(date_completed) FROM todo_tasks WHERE project_name='TODO PARSER')
	// fetch the information	
	$query = "";
	if( empty( $project_name ) )
		$query = "SELECT date_completed FROM todo_tasks WHERE date_completed = (SELECT MAX(date_completed) from todo_tasks WHERE task_status = '1')";
	else
		$query = sprintf("SELECT date_completed FROM todo_tasks WHERE date_completed = (SELECT MAX(date_completed) FROM todo_tasks WHERE task_status = '1' AND project_name='%s')", mysql_real_escape_string($project_name) );
	
	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	$dbarray = mysql_fetch_array($result);
	$return_me = stripslashes($dbarray['date_completed']);

	mysql_free_result($result);

	return $return_me;
}

function get_tasks_for_date( $date, $project_name )
{
	// SELECT * FROM todo_tasks WHERE DATE(date_completed) = '2013-01-05';
	$query = "";
	if( empty( $project_name ) )
		$query = sprintf("SELECT * FROM todo_tasks WHERE task_status = '1' AND DATE(date_completed) = '%s'", mysql_real_escape_string($date) );
	else
		$query = sprintf("SELECT * FROM todo_tasks WHERE task_status = '1' AND DATE(date_completed) = '%s' AND project_name='%s'", mysql_real_escape_string($date), mysql_real_escape_string($project_name) );

	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
		$return_value[] = $row; 
	}

	mysql_free_result($result);
	
	return $return_value;
}

// SELECT * FROM todo_tasks WHERE DATE(date_completed) = '2013-01-05';


// echo "Any project: " . get_latest_completed_date( "" ) . "<br>";
// echo "TODO PARSER: " . get_latest_completed_date( "TODO PARSER" ) . "<br>";

// substr( get_latest_completed_date( "TODO PARSER" ), 0, 10 )

$project_name = isset($_GET['project']) ? $_GET['project'] : default_project;

if( isset($_GET['date']) ) $date_string = $_GET['date'];
else $date_string = substr( get_latest_completed_date( $project_name ) , 0, 10 );

$data_array = get_tasks_for_date( $date_string, $project_name );

$image_file = "screenshots/" . str_replace("-", "", $date_string ) . "_shot.png";

// display image only if it exists
if( file_exists( $image_file ) )
{
	echo "<img src=\"$image_file\" width=\"512\" /><br>";
}

echo "<!--";
print_r( $data_array );
echo "-->";

// pretty date
// echo "Devlog #001 - Thuesday, December 29th, 2012"
$pretty_date = date('l, F jS, Y', strtotime($date_string));
echo "<br><div align=\"center\"><b><span style=\"font-size: 14pt; line-height: 1.3em;\"><span style=\"color: maroon;\">";
echo "Devlog #001 - $pretty_date";
echo "</span></span></b></div>";

// list of elements
?>
<table width="512" border="0" cellpadding="0" cellspacing="0"><tr><td>
<ul>
<?php
foreach( $data_array as $entry )
{
	echo "<li style=\"font-size:10pt;\">DONE: " . $entry['task_name'] . "</li>\n";
}

?>
</ul>
</td></tr></table>

</center>
</body>
</html>
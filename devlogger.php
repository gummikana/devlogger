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

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

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
		$query = sprintf("SELECT * FROM todo_tasks WHERE task_status = '1' AND DATE(date_completed) = DATE('%s')", mysql_real_escape_string($date) );
	else
		$query = sprintf("SELECT * FROM todo_tasks WHERE task_status = '1' AND DATE(date_completed) = DATE('%s') AND project_name='%s'", mysql_real_escape_string($date), mysql_real_escape_string($project_name) );

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

function get_latest_devlog()
{
	// SELECT date_created, id FROM todo_dates WHERE date_created = (SELECT MAX( date_created ) FROM todo_dates);
	$query = "SELECT date_created, id FROM todo_dates WHERE date_created = (SELECT MAX( date_created ) FROM todo_dates)";
	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	$dbarray = mysql_fetch_array($result);
	$return_me = $dbarray;

	mysql_free_result($result);

	return $return_me;
}

function get_devlog( $id, $date )
{
	$query = "";
	
	if( empty( $date ) )
		$query = sprintf("SELECT date_created, id FROM todo_dates WHERE id = '%s'", mysql_real_escape_string($id) );
	else
		$query = sprintf("SELECT date_created, id FROM todo_dates WHERE DATE(date_created) = DATE('%s')", mysql_real_escape_string($date) );

	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	$dbarray = mysql_fetch_array($result);
	$return_me = $dbarray;

	mysql_free_result($result);

	return $return_me;
}

// SELECT * FROM todo_tasks WHERE DATE(date_completed) = '2013-01-05';


// echo "Any project: " . get_latest_completed_date( "" ) . "<br>";
// echo "TODO PARSER: " . get_latest_completed_date( "TODO PARSER" ) . "<br>";

// substr( get_latest_completed_date( "TODO PARSER" ), 0, 10 )

$project_name = isset($_GET['project']) ? $_GET['project'] : default_project;
$date_string = "";
$devlog_id = "";
$is_latest = false;

/*if( isset($_GET['date']) ) $date_string = $_GET['date'];
else $date_string = substr( get_latest_completed_date( $project_name ) , 0, 10 );*/

// get_devlog( $id, $date )
if( isset($_GET['date']) || isset($_GET['id'] ) )
{
	$latest_devlog = get_devlog( $_GET['id'], $_GET['date'] );
	$date_string = $latest_devlog['date_created'];
	$devlog_id = $latest_devlog['id'];
}
else 
{
	$latest_devlog = get_latest_devlog();
	$date_string = $latest_devlog['date_created'];
	$devlog_id = $latest_devlog['id'];
	$is_latest = true;
}


if( $date_string == "" ) 
{
	echo "</center>";
	echo "<h1>Devlogger</h1>";
	echo "We have no data for any of your projects, yet :(<br>";
	echo "If you're seeing this it possible means that you've setup your database and config.php, if not go do that now. Check config.php for details.<br>";
	echo "<br>";
	echo "You still need to setup your <a href=\"cronjobs/cronjobs.txt\">cronjobs</a><br>";
	echo "If you've setup your cronjobs, and nothing has appeared yet, you can run the scripts manually by visiting these urls: <a href=\"fetch_todo.php\">fetch_todo.php</a> and <a href=\"fetch_daily_screenshot.php\">fetch_daily_screenshot.php</a><br>";
	echo "(Also make sure you've set the read-write permissions correctly in screenshots and todo folders.)<br>";
	echo "<br>";
	echo "Happy devlogging";
	die();
}

$data_array = get_tasks_for_date( $date_string, $project_name );

$image_file = "screenshots/" . str_replace("-", "", $date_string ) . "_shot.png";

?>
<table border="0" cellpadding="5">
<tr><td valign="top">
<?php if ( $devlog_id > 1 ) { ?>
<a href="<?php echo $_SERVER['PHP_SELF'] . "?id=" . ($devlog_id - 1); ?>">Prev</a>
<?php 
}
?>
</td><td>

<?php

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
echo "Devlog #" . str_pad( $devlog_id, 3, "0", STR_PAD_LEFT ) . " - $pretty_date";
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
</td><td valign="top">
<?php if ( $is_latest == false ) { ?>
<a href="<?php echo $_SERVER['PHP_SELF'] . "?id=" . ($devlog_id + 1); ?>">Next</a>
<?php 
}
?>
</td></tr></table>

</center>
</body>
</html>

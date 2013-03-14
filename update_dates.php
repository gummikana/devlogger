<?php
echo "NOT IN USE... ONLY USED FOR UPDATING PURPOUSES!"
die();
?>
<?php
require_once('config.php');

define(db_link, mysql_connect(db_host,db_user,db_pass));
mysql_select_db(db_name);

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


function insert_new_date( $mysqltime )
{
	$query = sprintf("INSERT INTO todo_dates (date_created) VALUES (DATE('%s'))", 
		mysql_real_escape_string($mysqltime) );

	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}
}

function has_devlog_date( $mysqltime )
{
	// fetch the information
	$query = sprintf("SELECT id FROM todo_dates WHERE date_created=DATE('%s')",
	    mysql_real_escape_string($mysqltime) );

		
	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	$dbarray = mysql_fetch_array($result);
	$r = stripslashes($dbarray['id']);

	mysql_free_result($result);

	return $r;
}

echo mktime(0, 0, 0, 1, 1, 2013) ."<br>";
echo date("Y-m-d H:i:s", mktime(0, 10, 0, 1, 1, 2013) + ( 60 * 60 * 24 ) * $i ) . "<br>";

$time_now = date("Y-m-d H:i:s");
for( $i = 0; $i < 100; $i++ )
{
	$time_i = date("Y-m-d H:i:s", mktime(0, 10, 0, 1, 1, 2013) + ( 60 * 60 * 24 ) * $i );
	if( $time_i < $time_now ) 
	{
		$data_array = get_tasks_for_date( $time_i );
		// print_r( $data_array );
		if( empty( $data_array ) ) 
		{
		}
		else
		{
			insert_new_date( $time_i );
		}
		// echo $time_i . "<br>";
	}
}
/*
if( $task_done ) 
{
	if( has_devlog_date() ) 
	{
		// no need to do anything...
	}
	else 
	{
		insert_new_date();
	}
}*/

?>

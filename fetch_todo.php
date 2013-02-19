<?php
require_once('config.php');

define(db_link, mysql_connect(db_host,db_user,db_pass));
mysql_select_db(db_name);


function write_to_file( $filename, $filecontents )
{
	$fp = fopen( $filename, "w" );
	fwrite($fp, $filecontents );
	fclose($fp);
}

function get_task_status( $task_name, $project_name ) 
{
	// fetch the information
	$query = sprintf("SELECT task_status FROM todo_tasks WHERE task_name='%s' AND project_name='%s'",
	    mysql_real_escape_string($task_name), mysql_real_escape_string($project_name) );

		
	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}

	$dbarray = mysql_fetch_array($result);
	$result = stripslashes($dbarray['task_status']);

	mysql_free_result($result);

	return $result;
}

function insert_into_database( $task_name, $project_name, $task_status, $task_line_priority )
{
	/*insert into todo_tasks (task_name, task_status, task_priority, line_priority, project_name, date_created, date_completed) VALUES 
	-> ('task name, this thing needs to get done', '0', '10', '14', 'Test project', GetDate(), GetDate());*/
	// insert into
	$mysqltime = date("Y-m-d H:i:s");
	
	$query = sprintf("INSERT INTO todo_tasks (task_name, task_status, task_priority, line_priority, project_name, date_created, date_completed) VALUES ('%s','%s','%s','%s','%s','%s','%s')", 
		mysql_real_escape_string($task_name), 
		mysql_real_escape_string($task_status),
		'10',
		mysql_real_escape_string($task_line_priority),
		mysql_real_escape_string($project_name),
		mysql_real_escape_string($mysqltime), 
		mysql_real_escape_string($mysqltime) );

	// echo $query;
	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}
	
	mysql_free_result($result);
}

function update_task_status( $task_name, $project_name, $task_status )
{
	$mysqltime = date("Y-m-d H:i:s");
	$query = sprintf("UPDATE todo_tasks SET task_status = '%s', date_completed = '%s' WHERE task_name='%s' AND project_name='%s'",
	    mysql_real_escape_string($task_status),
	    mysql_real_escape_string($mysqltime), 
	    mysql_real_escape_string($task_name), 
	    mysql_real_escape_string($project_name) );

	$result = mysql_query($query);

	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}
	
	mysql_free_result($result);
}

$url = dropbox_todo_txt;
$str = trim( file_get_contents($url) );

$md5_str = strlen( $str ) . "+" . md5( $str );

echo "TODO MD5 checksum: " . $md5_str . "<br>";
// read the data of the previous 

$prev_md5_str = file_get_contents( "TODO/_latest_todo_md5.txt" );

// if nothing has changed we don't need to continue excecuting this script
if( $prev_md5_str == $md5_str ) die();

echo "Continuing with the script by writing contents to _latest_todo_md5.txt<br> ";

write_to_file( "TODO/_latest_todo_md5.txt", $md5_str );

$backup_filename = "TODO/" . date( "Ymd" ) . "_TODO.txt";
write_to_file( $backup_filename, $str );

$prev_str = file_get_contents( "TODO/_TODO_latest.txt" );

//------ PARSING ----------------------

$project_name = "Misc";
$project_start_line = 0;
$arr = preg_split('/\n|\r\n?/', $str);

$prev_arr = preg_split('/\n|\r\n?/', $prev_str);

foreach ($arr as $line_num => $line_no_trim ) {
    $line = trim( $line_no_trim );
    if( empty( $line ) ) continue;

    // special characters, that we don't parse
    if( $line[0] == '#' ) continue;
    if( $line[0] == '-' ) continue;
    if( $line[0] == '=' ) continue;
    
    // parse project name
    if( $line[0] != '[' ) { $project_name = $line; $project_start_line = $line_num; continue; }
    
    // check if this is the same as before
    if( in_array( $line_no_trim, $prev_arr ) == true ) continue;
    
    $task_status = "0";
    // check the state of the thing
    if( strtolower( substr( $line, 0, 3 ) ) == "[x]" ) $task_status = "1";
    else if( strtolower( substr( $line, 0, 3 ) ) == "[ ]" ) $task_status = "0";
    else { echo "Parsing error - couldn't figure out the task status for line: " . $line . "<br>"; continue; }
    
    // task line priority
    $task_line_priority = $line_num - $project_start_line;
    
    // task name
    $task_name = trim( substr( $line, 3 ) );
   

    $status_in_database = get_task_status( $task_name, $project_name );
    
    echo $status_in_database;
    // insert into database
    if( !($status_in_database == "0" || $status_in_database == "1") ) {
	insert_into_database( $task_name, $project_name, $task_status, $task_line_priority );
	echo "<b>New task ($project_name):</b> $task_name<br>";
    }
    else if( $status_in_database != $task_status ) {
	update_task_status( $task_name, $project_name, $task_status );
    }
}

// ---- WRITE the latest TODO file ----
// to make sure we don't put stuff into the mysql database that are there already 
write_to_file( "TODO/_TODO_latest.txt", $str );

?>
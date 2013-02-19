<?php

require_once('config.php');

function write_to_file( $filename, $filecontents )
{
	$fp = fopen( $filename, "w" );
	fwrite($fp, $filecontents );
	fclose($fp);
}
 
function download_if_changed( $url, $output_file, $md5_file, $extra_output_file )
{
	$str = trim( file_get_contents($url) );
	
	// if we couldn't download the file for what ever reason
	if( strlen( $str ) <= 0 ) return false;
	
	$md5_str = strlen( $str ) . "+" . md5( $str );

	echo "TODO MD5 checksum: " . $md5_str . "<br>";
	// read the data of the previous 

	$prev_md5_str = file_get_contents( $md5_file );

	// if nothing has changed we don't need to continue excecuting this script
	if( $prev_md5_str == $md5_str ) return false;

	echo "Continuing with the script by writing contents to $md5_file<br> ";

	write_to_file( $md5_file, $md5_str );

	$backup_filename = "TODO/" . date( "Ymd" ) . "_TODO.txt";
	write_to_file( $output_file, $str );
	
	if( empty( $extra_output_file ) == false ) write_to_file( $extra_output_file, $str );
	
	return true;
}

$daily_screenshot_name = "screenshots/" . date( "Ymd" ) . "_shot.png";
download_if_changed( dropbox_daily_shot, $daily_screenshot_name, "screenshots/_latest_md5.txt", "screenshots/_latest_shot.png" );

?>
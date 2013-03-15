<html>
<head><title>Devlogger (WIP)</title></head>
<body style="font-family: Verdana,Helvetica,sans-serif;font-size: 10pt;line-height: 125%; margin-left:30px;">
<br>
<br>
<?php
require_once('config.php');

define(db_link, mysql_connect(db_host,db_user,db_pass));
mysql_select_db(db_name);

function get_all_devlogs()
{
	$query = "SELECT * FROM todo_dates";
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

$data = get_all_devlogs();
echo "<!--\n";
print_r( $data );
echo "\n-->\n";

for( $i= count($data) - 1; $i >= 0 ;$i--)
{ 
	$date_string = $data[$i]['date_created'];
	$id = $data[$i]['id'];
	
	$image_file = "screenshots/" . str_replace("-", "", $date_string ) . "_shot.png";


	if( file_exists( $image_file ) )
	{
		echo "<a href=\"devlogger.php?id=" . $id . "\">";
		echo "<img src=\"$image_file\" border=\"0\" style=\"max-width:1280px;\"/></a><br><br><br><br>";
	}
} 






?>




<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-266661-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</center>
</body>
</html>

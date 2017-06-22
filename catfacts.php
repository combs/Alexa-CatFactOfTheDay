<?php

// Utility functions

$cache_file="/tmp/cache-catfacts";

function try_cache($the_file, $max_age) {
        if (file_exists($the_file)) {
                if (time() - filemtime($the_file) < $max_age) {
                        $lines=file($the_file);
                        foreach ($lines as $line_num => $line) {
                                echo $line;
                                usleep(50000);
                        }
                        exit;
                }
                else {
                        // echo "cache too old";
                        unlink($the_file);
                }
        } else {
                // echo "cache doesn't exist";
        }
}
 
function cached_echo($the_string) {
        global $cache_file;
        file_put_contents($cache_file, $the_string, FILE_APPEND | LOCK_EX);
        echo ($the_string);
}

try_cache($cache_file,60*60*24);

$string=file_get_contents("http://catfacts-api.appspot.com/api/facts?amount=1");
$facts=json_decode($string, true);
$fact="";

$datetime = new DateTime("NOW");
$now = $datetime->format(DateTime::W3C);
if (!array_key_exists("facts",$facts)) {

	$string=file_get_contents("https://catfact.ninja/fact");
	$facts=json_decode($string, true);

	if (!array_key_exists("fact",$facts)) {

		try_cache($cache_file,60*60*24*7);
		return;

	} else {

	$fact = $facts["fact"];

	}

} else {

	$fact = $facts["facts"][0];

}

$alexa = [ [ "uid" => time(), "updateDate" => $now, "titleText" => "Fact: " . $fact , "mainText" => $fact, description => $fact ] ];

cached_echo(json_encode($alexa));

?>

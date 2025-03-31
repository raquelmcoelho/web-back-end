<?php
$cookie_name = 'visited';
$filename = "counter.txt";

if (!file_exists($filename)) {
    file_put_contents($filename, "0");
}
$fp = fopen($filename, "r+");

$counter = intval(fread($fp, filesize($filename) ?: 1)); 

function incrementVisitors() {
    global $cookie_name, $fp, $counter, $filename;
    
    if (!isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, "true", time() + 3600, "/"); 
        $counter++;
        ftruncate($fp, 0); 
        rewind($fp);
        fwrite($fp, $counter);
    }
}

incrementVisitors();
fclose($fp);

?>
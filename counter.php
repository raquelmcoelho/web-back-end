<?php
$cookie_name = 'visited';
$filename = "counter.txt";

// Verifica se o arquivo existe e se não está vazio
if (!file_exists($filename)) {
    file_put_contents($filename, "0");
}
$fp = fopen($filename, "r+");

$counter = intval(fread($fp, filesize($filename) ?: 1)); // Evita erro se o arquivo estiver vazio

function incrementVisitors() {
    global $cookie_name, $fp, $counter, $filename;
    
    if (!isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, "true", time() + 10, "/"); // Cookie dura 10 segundos
        $counter++;
        ftruncate($fp, 0); // Limpa o arquivo antes de escrever
        rewind($fp);
        fwrite($fp, $counter);
    }
}

incrementVisitors();
fclose($fp);

?>
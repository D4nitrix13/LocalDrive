<?php

$text = "\n bird";
$filename = "somefile.txt";
$fh = fopen($filename, "a+");
fwrite($fh, $text);
fclose($fh);

//file_put_contents("name.txt", "My SurName Is Perez");
?>
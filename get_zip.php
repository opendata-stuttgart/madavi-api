<?php

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="sensordaten.zip"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();

$handle = popen("zip -r -q - data/*.csv","r");
while (!feof($handle)) {
	print fread($handle, 1024);
	echo $read;
	flush();
}

pclose($handle);

exit;
?>

<?PHP

header('Content-type: text/plain; charset=utf8', true);

$latest_version = trim(shell_exec("./get_version"));
$latest_version_beta = trim(shell_exec("./get_version_beta"));

function check_header($name, $value = false) {
    if(!isset($_SERVER[$name])) {
        return false;
    }
    if($value && $_SERVER[$name] != $value) {
        return false;
    }
    return true;
}

function sendFile($path) {
    header($_SERVER["SERVER_PROTOCOL"].' 200 OK', true, 200);
    header('Content-Type: application/octet-stream', true);
    header('Content-Disposition: attachment; filename='.basename($path));
    header('Content-Length: '.filesize($path), true);
    header('x-MD5: '.md5_file($path), true);
    readfile($path);
}

if(!check_header('HTTP_USER_AGENT', 'ESP8266-http-Update')) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater!\n";
    exit();
}

if(
    !check_header('HTTP_X_ESP8266_STA_MAC') ||
    !check_header('HTTP_X_ESP8266_AP_MAC') ||
    !check_header('HTTP_X_ESP8266_FREE_SPACE') ||
    !check_header('HTTP_X_ESP8266_SKETCH_SIZE') ||
    !check_header('HTTP_X_ESP8266_CHIP_SIZE') ||
    !check_header('HTTP_X_ESP8266_SDK_VERSION') ||
    !check_header('HTTP_X_ESP8266_VERSION')
) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater! (header)\n";
    exit();
}

$version_parts = explode(" ",$_SERVER['HTTP_X_ESP8266_VERSION']);
file_put_contents("headers.txt", gmdate("Y-m-d H:i:s")." - ".$_SERVER['HTTP_X_ESP8266_STA_MAC']." - ".$_SERVER['HTTP_X_ESP8266_AP_MAC']." - ".$_SERVER['HTTP_X_ESP8266_FREE_SPACE']." - ".$_SERVER['HTTP_X_ESP8266_SKETCH_SIZE']." - ".$_SERVER['HTTP_X_ESP8266_CHIP_SIZE']." - ".$_SERVER['HTTP_X_ESP8266_SDK_VERSION']." - ".$_SERVER['HTTP_X_ESP8266_VERSION']."\n", FILE_APPEND | LOCK_EX);

if (($version_parts[0] != $latest_version) &&
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] != "18:FE:34:CF:8C:70") &&
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] != "18:FE:34:CF:7C:4B") &&		// Pragsattel outdoor
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] != "60:01:94:06:9B:DB") &&		// Jule
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] != "60:01:94:0B:54:A1") &&		// Greiz
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] != "18:FE:34:D4:84:16")) {
    sendFile("./data/latest.bin");
} elseif (($version_parts[0] != $latest_version_beta) &&
	(($_SERVER['HTTP_X_ESP8266_STA_MAC'] == "18:FE:34:CF:7C:4B") ||		// Pragsattel outdoor
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] == "60:01:94:06:9B:DB") ||		// Jule
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] == "60:01:94:0B:54:A1") ||		// Greiz
	($_SERVER['HTTP_X_ESP8266_STA_MAC'] == "18:FE:34:CF:8C:70"))) {
    sendFile("./data/latest_beta.bin");
} else {
    header($_SERVER["SERVER_PROTOCOL"].' 304 Not Modified', true, 304);
}

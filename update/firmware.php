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

function sendFile($file,$lang) {
    $path = './data/'.$file.'_'.$lang.'.bin';
    if(!file_exists($path)){
        $path = './data/'.$file.'.bin';
    }
    error_log("Download: ".$path.' -> '.file_exists($path));
    header($_SERVER["SERVER_PROTOCOL"].' 200 OK', true, 200);
    header('Content-Type: application/octet-stream', true);
    header('Content-Disposition: attachment; filename='.basename($path));
    header('Content-Length: '.filesize($path), true);
    header('x-MD5: '.md5_file($path), true);
    readfile($path);
}

function getLang($version_parts,$index){
    $lang = isset($version_parts[$index])?strtolower($version_parts[$index]):'de';
    return in_array($lang, Array('de','en','bg'))?$lang:'de';   
}

function logHeaders(){
    file_put_contents("headers.txt", gmdate("Y-m-d H:i:s")." - ".$_SERVER['HTTP_X_ESP8266_STA_MAC']." - ".$_SERVER['HTTP_X_ESP8266_AP_MAC']." - ".$_SERVER['HTTP_X_ESP8266_FREE_SPACE']." - ".$_SERVER['HTTP_X_ESP8266_SKETCH_SIZE']." - ".$_SERVER['HTTP_X_ESP8266_CHIP_SIZE']." - ".$_SERVER['HTTP_X_ESP8266_SDK_VERSION']." - ".$_SERVER['HTTP_X_ESP8266_VERSION']."\n", FILE_APPEND | LOCK_EX);
}

function mac_filter_latest_version($mac){
    $macs[] = "18:FE:34:CF:8C:70";
    $macs[] = "18:FE:34:CF:7C:4B"; // Pragsattel outdoor
    $macs[] = "60:01:94:06:9B:DB"; // Jule
    $macs[] = "60:01:94:0B:54:A1"; // Greiz
    $macs[] = "18:FE:34:D4:84:16";
    return in_array($mac, $macs);
}

function mac_filter_beta_version($mac){
    $macs[] = "18:FE:34:CF:7C:4B"; // Pragsattel outdoor
    $macs[] = "60:01:94:06:9B:DB"; // Jule
    $macs[] = "60:01:94:0B:54:A1"; // Greiz
    $macs[] = "18:FE:34:CF:8C:70";
    return in_array($mac, $macs);
}

//------------------------------------------------------------
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

logHeaders();

$version_parts = explode(" ",$_SERVER['HTTP_X_ESP8266_VERSION']);
$current_lang  = getLang($version_parts,3);
$installed_lang = getLang($version_parts,4);

//error_log(print_r($version_parts,true));
//error_log('latest_version: '.$latest_version);
//error_log('langs: '.$current_lang.':'.$installed_lang);

if (($version_parts[0] != $latest_version || $current_lang != $installed_lang) && !mac_filter_latest_version($_SERVER['HTTP_X_ESP8266_STA_MAC'])) {
    sendFile("latest",$current_lang);
} elseif (($version_parts[0] != $latest_version_beta) && mac_filter_beta_version($_SERVER['HTTP_X_ESP8266_STA_MAC'])) {
    sendFile("latest_beta",$current_lang);
} else {
    header($_SERVER["SERVER_PROTOCOL"].' 304 Not Modified', true, 304);
}

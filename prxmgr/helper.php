<?php
require_once (__DIR__ . '/include/MysqliDb.php');
$DATABASE_NAME = 'admin_servermgr';
$DATABASE_USER = 'admin_servermgr';
$DATABASE_PASS = 'Ge2DWUH9y';
$password = 'nitro';

$webjsonFilePath = __DIR__ . '/webjson.txt';

                
function initFolder(){
  if(is_dir(__DIR__ . '/prxs/') === false ){
    mkdir($dir);}
}
function readFileContent($file){
  $myfile = fopen($file, 'rb');
  $ret= fgets($myfile);
  fclose($myfile);
  return $ret;
}
function getinput($name,$post){
  if(isset($post[$name])){
    return $post[$name];
  }else{
    return null;
  }
}
function deleteAFile($filename){
  if(file_exists($filename)){
    unlink($filename);
  }
}
function writeFileContent($filename,$data){
  if(file_exists($filename)){
    unlink($filename);
  }
  file_put_contents($filename, json_encode($data));
}
function ChangeSponserIfExist($filename,$sponser){
  if(file_exists($filename)){
    $data = json_decode(readFileContent($filename),true);
    deleteAFile($filename);
    $data['sponser'] = $sponser;
    file_put_contents($filename, json_encode($data));
  }
}
function getListofFilesJson(){
  $files = [];
  if (is_dir(__DIR__ . '/json/')) {
    if ($dh = opendir(__DIR__ . '/json/')) {
        while (($file = readdir($dh)) !== false) {
          if (strpos($file, '.txt') !== false) {
            $files[]= $file;
          }
        }
        closedir($dh);
    }
  }
  return $files;
}
function getListofFiles(){
  $files = [];
  if (is_dir(__DIR__ . '/prxs/')) {
    if ($dh = opendir(__DIR__ . '/prxs/')) {
        while (($file = readdir($dh)) !== false) {
          if (strpos($file, '.txt') !== false) {
            $files[]= $file;
          }
        }
        closedir($dh);
    }
  }
  return $files;
}
function createWebJsonFile($webjsonFilePath,$db){
    $db->where('status',0);
    $db = $db->where('time_created + time_to_live > ' . time());
    $datas = $db->get('sentjson');
    $array = [];
    foreach ($datas as $data){
        $ret = json_decode($data['json_text'], true);
        if($data['json_text']!==null && $ret!==null){
            $array[] = $ret;
        }

    }
    writeFileContent($webjsonFilePath,$array);
}
?>
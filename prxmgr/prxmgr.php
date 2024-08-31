<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '1');
include __dir__ . "/helper.php";
$action = getinput('action',$_POST);
if($action!==null){
  if($password!==getinput('password',$_POST)){
    die(json_encode(['done'=>false,'err'=>'wrong_pass']));
  }
}
initFolder();

switch ($action) {
  case 'add':
    $json = [];
    $json['ip'] = getinput('ip',$_POST);
    $json['prt'] = getinput('prt',$_POST);
    $json['user'] = getinput('user',$_POST);
    $json['pass'] = getinput('pass',$_POST);
    $json['secret'] = getinput('secret',$_POST);
    $sp = getinput('sponser',$_POST);
    if($sp==null){
        $json['sponser'] = false;
    }else{
       if($sp == "true" || $sp == "True"){
            $json['sponser'] = true;
       }else{
            $json['sponser'] = false;
       }
    }
    $filename = md5($json['ip']). ".txt";
    writeFileContent(__DIR__.'/prxs/'. $filename,$json);
    echo json_encode(['done'=>true]);
  break;
  case 'remove':
    $ip = getinput('ip',$_POST);
    $filename = md5($ip). ".txt";
    deleteAFile(__DIR__.'/prxs/'. $filename);
    echo json_encode(['done'=>true]);
    break;
  case 'sponser':
    $ip = getinput('ip',$_POST);
    $sp = getinput('sponser',$_POST);
    if($sp==null){
        $sponser = false;
    }else{
       if($sp == "true" || $sp == "True"){
            $sponser = true;
       }else{
            $sponser = false;
       }
    }
    $filename = md5($ip). ".txt";
    ChangeSponserIfExist(__DIR__.'/prxs/'. $filename,$sponser);
    echo json_encode(['done'=>true]);
    break;
    case 'removeall':
      $list = getListofFiles();
      foreach ($list as $file) {
        deleteAFile(__DIR__.'/prxs/'.$file);
      }
      echo json_encode(['done'=>true]);
      break;
  default:
      $list = getListofFiles();
    $output =[];

    foreach ($list as $file) {
      $output[] = json_decode(readFileContent(__DIR__.'/prxs/'.$file));
    }
    die(json_encode($output));
    break;
}
?>

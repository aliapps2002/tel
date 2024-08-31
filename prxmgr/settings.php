<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include __dir__ . "/helper.php";
$action = getinput('action',$_POST);
if($action!=null){
  if($password!=getinput('password',$_POST)){
    die(json_encode(['done'=>false]));
  }
}
initFolder();

switch ($action) {
  case 'set':
    $json = [];
    $section = getinput('section',$_POST);
    $data = json_decode(base64_decode(getinput('datas',$_POST)),true);
    $filename = md5($section). ".txt";
    writeFileContent(__DIR__.'/json/'. $filename,$data);
    echo json_encode(['done'=>true]);
  break;

  default:
    $list = getListofFilesJson();
    $output =[];
    foreach ($list as $file) {
        $array = json_decode(readFileContent(__DIR__.'/json/'.$file));
        foreach($array as $key=>$value){
            $output[$key]=$value;
        }
      	if(file_exists(__dir__."/apijson.txt")){
          $apis = json_decode(readFileContent(__DIR__.'/apijson.txt'),true);
          if(sizeof($apis)>0){
          	$api = $apis[rand(0,sizeof($apis)-1)];
            $output['SERVER_APP_ID']=strval($api['api_id']);
            $output['SERVER_APP_HASH']=strval($api['api_hash']);
          }

        }
    }
    if($output==[]){
        echo("[{}]");
    }else{
    die(json_encode([$output]));
    }
    break;
}
?>

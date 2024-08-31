<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once(__dir__ . '/helper.php');
$action = getinput('action',$_REQUEST);
$token = getinput('token',$_REQUEST);
$apijsonFilePath = __DIR__ . '/apijson.txt';
if($action===null){
	if (!file_exists($webjsonFilePath) || (time() - filemtime($webjsonFilePath)) > 20 * 60 * 60) {
		$db = new MysqliDb (Array (
			'host' => 'localhost',
			'username' => $DATABASE_USER,
			'password' => $DATABASE_PASS,
			'db'=> $DATABASE_NAME,
			'port' => 3306,
			'prefix' => 'tbl_',
			'charset' => 'utf8'));
		createWebJsonFile($webjsonFilePath,$db);
	}
	die(readFileContent($webjsonFilePath));
} else {
	if ($password !== getinput('password', $_REQUEST)) {
		die(json_encode(['done' => false, 'error' => 'wrong_pass']));
	}
	$db = new MysqliDb (Array (
		'host' => 'localhost',
		'username' => $DATABASE_USER,
		'password' => $DATABASE_PASS,
		'db'=> $DATABASE_NAME,
		'port' => 3306,
		'prefix' => 'tbl_',
		'charset' => 'utf8'));
	if ($action === 'cancel_json') {
		$id = getinput('id', $_REQUEST);
		$db = MysqliDb::getInstance();
		$db->where('id', $id);
		if ($db->update('sentjson', ['status' => 2])) {
			echo(json_encode(['done' => true, 'error' => 'succsess']));
			createWebJsonFile($webjsonFilePath,$db);
		} else {
			echo(json_encode(['done' => false, 'error' => 'errorinsave']));
		}
	} else if ($action === 'add_json') {
		$title = getinput('title', $_REQUEST);
		$json_text = getinput('json_text', $_REQUEST);
		$status = getinput('status', $_REQUEST);
		$topic = getinput('topic', $_REQUEST);
		$phone_name = getinput('phone_name', $_REQUEST);
		$time_to_live = getinput('time_to_live', $_REQUEST);
		if (strlen($title) > 0 && strlen($json_text) > 0) {
			$db = MysqliDb::getInstance();
			$id = $db->insert('sentjson', [
				'title' => $title,
				'json_text' => $json_text,
				'time_created' => time(),
				'phone_name' => $phone_name,
				'topic' => $topic,
				'status' => $status,
				'time_to_live' => $time_to_live,
			]);
			if ($id) {
				createWebJsonFile($webjsonFilePath,$db);
				echo(json_encode(['done' => true, 'error' => 'succsess']));
			} else {
				echo(json_encode(['done' => false, 'error' => 'errorinsave']));
			}
		} else {
			echo(json_encode(['done' => false, 'error' => 'errorindata']));
		}
	} else if ($action === 'list') {
		$db = MysqliDb::getInstance();
		$page = getinput('page', $_REQUEST);
		$page_count = 20;
		$db->orderBy('time_created', 'Desc');
		$data = $db->get('sentjson', [($page) * $page_count, $page_count]);
		echo(json_encode($data));
	}else if ($action === 'api_list') {
      	if(file_exists($apijsonFilePath)){
					echo(readFileContent($apijsonFilePath));
        }else{
          echo("[]");
        }
	}else if ($action === 'api_update') {
      $apibody = getinput('api_body', $_REQUEST);
      if($apibody!=null){
      	writeFileContent($apijsonFilePath,json_decode($apibody,true));
      	echo(json_encode(['done' => true, 'error' => 'succsess']));
			}else {
				echo(json_encode(['done' => false, 'error' => 'errorinsave']));
      }
    }
}
?>

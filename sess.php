<?php
class sess{

private $choosenWebpage;

function __construct(){
	session_start();
}

public function getChoosenWebpage(){
	if(!empty($_SESSION["wep"]))
	return $_SESSION["wep"];//an id (int)
	else return null;
}

public function setChoosenWebpage($id){
	error_log("setChoosenWebpage($id)");
	$_SESSION["wep"] = $id;
}

public function clearChoosenWebpage(){
	error_log("clearChoosenWebpage");
	$_SESSION["wep"] = null;
}


}
?>

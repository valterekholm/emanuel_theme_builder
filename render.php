<?php
require_once("db.php");
require_once("html.php");
include "functions.php";




$db = new db();
$html = new html();





//base node
$sql = "SELECT n.id, n.element_id, n.parent_node_id, e.name FROM nodes n JOIN html_element e ON (element_id = e.id) WHERE ISNULL(parent_node_id)";

$res = $db->select_query($sql);
if($res->rowCount()==0){
	echo "Error: could not find base-node, a node whith parent=null";
}
else{
	$row = $res->fetch();//one row only

	$found = array();

	$base_id = $row["id"];
	//echo $base_id;

	$found[] = $base_id;

	printLevel($base_id);
}
?>
<a href="index.php">Start</a>

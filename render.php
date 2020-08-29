<?php
require_once("db.php");
require_once("html.php");
require_once("sess.php");
include "functions.php";


//TODO: connect to web_page_id


$db = new db();
$html = new html();
$sess = new sess();

$wep = $sess->getChoosenWebpage();
?>
<!DOCTYPE html>
<html>
<head>
<?php

error_log("RENDER");

$sql_elem_css = "select c.*, e.name element_name from element_css c left join html_element e on (c.name = e.id) where web_page_id = $wep";
error_log($sql_elem_css);
$res_elem_css = $db->select_query($sql_elem_css);
$rows_elem_css = $res_elem_css->fetchAll();

$sql_classes = "select * from classes WHERE webpage_id = $wep";
error_log($sql_classes);
$res_classes = $db->select_query($sql_classes);
$rows_classes = $res_classes->fetchAll();

//RENDERING STYLE SECTION
if(count($rows_elem_css)>0 || count($rows_classes)>0){
    echo "<style>";
    foreach($rows_elem_css as $row){
        echo "." . $row["element_name"] . "{ " . $row["css"] . " }\n";
    }
    foreach($rows_classes as $row2){
        echo "." . $row2["name"] . "{ " . $row2["css"] . " }\n";
    }
    echo "</style>";
}//if

?>
</head>


<?php
//get base node
$sql = "SELECT n.id, n.element_id, n.parent_node_id, e.name FROM nodes n JOIN html_element e ON (element_id = e.id) WHERE ISNULL(parent_node_id) AND web_page_id = $wep";

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

	printLevel($base_id, 0, true);
}
?>
<!-- a href="index.php">Start</a-->
</html>

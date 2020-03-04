<?php

function getChildren($parent_id, $exclude=""){

$db = new db();

$sql = "SELECT n.id, n.element_id, n.parent_node_id, n.inner_html, e.name, e.is_empty_tag FROM nodes n JOIN html_element e ON (element_id = e.id) WHERE parent_node_id = $parent_id"; //AND id NOT IN ($exclude)";
error_log($sql);
$res = $db->select_query($sql);
$rows = $res->fetchAll();
return $rows;

}

function printLevel($parent_id, $level = 0){
$children = getChildren($parent_id);

foreach($children as $child){
        //echo $level . " " . $child["id"] . " " . $child["name"] . "<br>";
	echo "<" . $child["name"] . ">";
	if(!empty($child["inner_html"])){
		echo $child["inner_html"];
	}
        printLevel($child["id"], $level+1);
	if($child["is_empty_tag"]==0){
		//is not an empty type of element
		echo "</" . $child["name"] . ">";
	}
}
}


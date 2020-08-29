<?php

function getChildren($parent_id, $exclude=""){

$db = new db();

$sql = "SELECT n.id, n.element_id, n.parent_node_id, n.inner_html, e.name, e.is_empty_tag ".
        "FROM nodes n JOIN html_element e ON (element_id = e.id) ".
        "WHERE parent_node_id = $parent_id"; //AND id NOT IN ($exclude)";

$sql = "SELECT n.*, e.name, e.is_empty_tag, GROUP_CONCAT(c.name SEPARATOR ' ') AS classes ".
        "FROM nodes n JOIN html_element e ON (element_id = e.id) ".
        "LEFT JOIN nodes_classes ON (n.id = id_node) ".
        "LEFT JOIN classes c ON (id_class = c.id) ".
        "WHERE parent_node_id = $parent_id ".
        "GROUP BY id";

error_log($sql);
$res = $db->select_query($sql);
$rows = $res->fetchAll();
return $rows;

}

function printLevel($parent_id, $level, $print_base_node = false){
$db = new db();

$children = getChildren($parent_id);

$base_name = "";

if($print_base_node){

$sql = "SELECT * FROM nodes JOIN html_element e ON (element_id = e.id) WHERE nodes.id = $parent_id";
/*SELECT n.*, e.name, e.is_empty_tag, GROUP_CONCAT(c.name SEPARATOR ' ')
 * FROM nodes n
 * JOIN html_element e
 * ON (element_id = e.id)
 * LEFT JOIN nodes_classes
 * ON (n.id = id_node)
 * LEFT JOIN classes c
 * ON (id_class = c.id)
 * WHERE n.id = 5
 * GROUP BY id*/
$res = $db->select_query($sql);
$row = $res->fetch();

$base_name = $row["name"];

echo "<$base_name>";

}

foreach($children as $child){
        //echo $level . " " . $child["id"] . " " . $child["name"] . "<br>";
	echo "<" . $child["name"] . " class='".$child["classes"]."'>";
	if(!empty($child["inner_html"])){
		echo $child["inner_html"];
	}
        printLevel($child["id"], $level+1);
	if($child["is_empty_tag"]==0){
		//is not an empty type of element
		echo "</" . $child["name"] . ">";
	}
}

if($print_base_node){
echo "</$base_name>";
}
}

function printMenu($menu = array("Start" => "index.php", "Render" => "render.php", "Choose webpage" => "choose_webpage.php", "SQL"=>"sql.php")){

$html = new html();

$script   = $_SERVER['SCRIPT_NAME'];

echo "<div>";
echo "<ul class='horizontal_menu'>";
foreach($menu as $k=>$v){
	$class = "";
	if(strpos($script, $v)){
		$class = " class='highlight'";
	}

	echo "<li$class><a href='$v'>$k</a></li>";
}
echo "</ul>";
echo "</div>";

}

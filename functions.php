<?php

function getChildren($parent_id, $exclude = "")
{

	$db = new db();

	$sql = "SELECT n.id, n.element_id, n.parent_node_id, n.inner_html, e.name, e.is_empty_tag " .
		"FROM nodes n JOIN html_element e ON (element_id = e.id) " .
		"WHERE parent_node_id = $parent_id"; //AND id NOT IN ($exclude)";

	$sql = "SELECT n.*, e.name, e.is_empty_tag, GROUP_CONCAT(c.name ORDER BY c.name ASC SEPARATOR ' ') AS classes, " .
		"COALESCE(rn.times, '1') `times` " .
		"FROM nodes n JOIN html_element e ON (element_id = e.id) " .
		"LEFT JOIN nodes_classes ON (n.id = id_node) " .
		"LEFT JOIN classes c ON (id_class = c.id) " .
		"LEFT JOIN repeating_nodes rn ON (n.id = rn.node_id) " .
		"WHERE parent_node_id = $parent_id " .
		"GROUP BY id";

	error_log($sql);
	$res = $db->select_query($sql);
	$rows = $res->fetchAll();
	return $rows;
}

//starting with base?

function printLevel($parent_id, $level, $print_base_node = false)
{
	$db = new db();

	$children = getChildren($parent_id);

	$base_name = "";

	if ($print_base_node) {

		//$sql = "SELECT * FROM nodes JOIN html_element e ON (element_id = e.id) WHERE nodes.id = $parent_id";
		$sql = "SELECT e.name, COALESCE(rn.times, '1') `times` FROM nodes n JOIN html_element e ON (element_id = e.id) LEFT JOIN repeating_nodes rn ON (n.id = rn.node_id) WHERE n.id = $parent_id";

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

	foreach ($children as $child) {
		//echo $level . " " . $child["id"] . " " . $child["name"] . "<br>";
		$times = intval($child["times"]);

		echo "<" . $child["name"] . " class='" . $child["classes"] . "'>";
		if (!empty($child["inner_html"])) {
			echo $child["inner_html"];
		}
		printLevel($child["id"], $level + 1);
		if ($child["is_empty_tag"] == 0) {
			//is not an empty type of element
			echo "</" . $child["name"] . ">";
		}
	}

	if ($print_base_node) {
		echo "</$base_name>";
	}
}

//made to test str_repeat for a node/tree

function printLevelNoEcho($parent_id, $level, $print_base_node = false)
{
	$db = new db();

	$children = getChildren($parent_id);

	$base_name = "";

	$str = "";

	if ($print_base_node) {
		$sql = "SELECT e.name, COALESCE(rn.times, '1') `times` FROM nodes n JOIN html_element e ON (element_id = e.id) LEFT JOIN repeating_nodes rn ON (n.id = rn.node_id) WHERE n.id = $parent_id";

		$res = $db->select_query($sql);
		$row = $res->fetch();

		$base_name = $row["name"];


		$str .= "<$base_name>";
	}

	foreach ($children as $child) {
		//echo $level . " " . $child["id"] . " " . $child["name"] . "<br>";
		$times = intval($child["times"]);

		$child_text = "<" . $child["name"] . " class='" . $child["classes"] . "'>";
		if (!empty($child["inner_html"])) {
			$child_text .= $child["inner_html"];
		}
		$child_text .= printLevelNoEcho($child["id"], $level + 1);
		if ($child["is_empty_tag"] == 0) {
			//is not an empty type of element
			$child_text .= "</" . $child["name"] . ">";
		}

		$str .= str_repeat($child_text, $times);
	}

	if ($print_base_node) {
		$str .= "</$base_name>";
	}
	return $str;
}

function printMenu($menu = array("Start" => "index.php", "Render" => "render.php", "Choose webpage" => "choose_webpage.php", "SQL" => "sql.php"))
{

	$html = new html();

	$script   = $_SERVER['SCRIPT_NAME'];

	echo "<div>";
	echo "<ul class='horizontal_menu'>";
	foreach ($menu as $k => $v) {
		$class = "";
		if (strpos($script, $v)) {
			$class = " class='highlight'";
		}

		echo "<li$class><a href='$v'>$k</a></li>";
	}
	echo "</ul>";
	echo "</div>";
}

function getAppFolder($name = "settings1")
{
	$ob = getAppSettings($name);
	return $ob->app_folder . "/";
}

function getAppSettings($name = "settings1")
{
	$filename = "app_config.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$json = json_decode($contents);

	$found = array();

	foreach ($json as $ob) {
		//print_r($ob);
		$conf_name = $ob->name;
		//echo "name: $name";
		if ($name == $conf_name) {
			$found = $ob;
		}
	}
	return $found;
}

function getHtmlHeadConfig($name = "head1")
{
	error_log("getHtmlHeadConfig: $name");
	$filename = "html_head_config.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$json = json_decode($contents);

	$found = array();

	foreach ($json as $ob) {
		//print_r($ob);
		$conf_name = $ob->name;
		//echo "name: $name";
		if ($name == $conf_name) {
			error_log("found head conf!");
			$found = $ob;
		}
	}
	return $found;
}

function getCssJsFromObj($ob, $app_dir = "")
{
	$ret = "";
	$css_folder = "";
	$js_folder = "";

	foreach ($ob as $key => $val) {
		switch ($key) {
			case "css-folder":
				$css_folder = $val . "/";
				break;

			case "js-folder":
				$js_folder = $val . "/";
				break;
		}
	}

	foreach ($ob as $key => $val) {

		switch ($key) {
			case "csses":
				foreach ($val as $css) {
					$ret .= "<link rel='stylesheet' href='/$app_dir$css_folder$css'>\n";
				}

				break;

			case "jses":
				foreach ($val as $js) {
					$ret .= "<script src='/$app_dir$js_folder$js'></script>\n";
				}
				break;
		}
	}

	return $ret;
}

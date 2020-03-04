<?php

//add node
if(isset($_GET["add_node"]) && isset($_GET["parent_node_id"]) && isset($_GET["child_element_id"])){

	require_once("db.php");

	$add_node = $_GET["add_node"];
	$parent_node_id = $_GET["parent_node_id"];
	$child_element_id = $_GET["child_element_id"];
	error_log(print_r($_GET, true));

	if(isset($_GET["inner_html"])){
		$inner_html = urldecode($_GET["inner_html"]);
	}
	else{
		$inner_html = "";
	}

	$p = (int) preg_replace('/[^0-9]/', '', $parent_node_id);
	$c = (int) preg_replace('/[^0-9]/', '', $child_element_id);

	$db = new db();

	$row_count = $db->insert_query("INSERT INTO nodes (element_id, parent_node_id, inner_html) VALUES (?,?,?)", array($c, $p, $inner_html));

	if($row_count > 0){
		echo "got 3 args ok from AJAX, row_count: $row_count";
	}
	else{
		http_response_code(500);//Internal Server Error
		echo "Query failed";
	}
}
//add element
//is_empty be 'yes' or *
if(isset($_GET["add_element"]) && isset($_GET["e_name"]) && isset($_GET["is_empty"])){

        require_once("db.php");

        $add_element = $_GET["add_element"];
        $e_name = $_GET["e_name"];
        $is_empty = $_GET["is_empty"];
        error_log(print_r($_GET, true));

	$empty = $is_empty == "yes" ? 1 : 0;
        $db = new db();

        $row_count = $db->insert_query("INSERT INTO html_element (name, is_empty_tag) VALUES (?,?)", array($e_name, $empty));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_GET["add_webpage"]) && isset($_GET["name"])){

        require_once("db.php");

        $name = $_GET["name"];
        error_log(print_r($_GET, true));

        $db = new db();

        $row_count = $db->insert_query("INSERT INTO web_page (name) VALUES (?)", array($name));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}



if(isset($_GET["update_node"]) && isset($_GET["node_id"]) && isset($_GET["element_id"]) && isset($_GET["inner_html"]) && isset($_GET["parent_id"])){
	error_log("update_node");

	require_once("db.php");

	$node_id = $_GET["node_id"];
	$element_id = $_GET["element_id"];
	$parent_id = $_GET["parent_id"];
	$inner_html = $_GET["inner_html"];

	error_log("update node, " . print_r($_GET, true));

	$sql = "UPDATE nodes SET element_id = ?, parent_node_id = ?, inner_html = ?  WHERE id = ?";
	$values = array($element_id, $parent_id, $inner_html, $node_id);
	$db = new db();

	$row_count = $db->insert_query($sql, $values);

        if($row_count > 0){
                echo "got 4 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_GET["delete"]) && isset($_GET["node_id"]) && isset($_GET["move_children"])){
        error_log("delete_node-------------------------------------");

        require_once("db.php");
	$db = new db();

        $node_id = $_GET["node_id"];
        $move_children = $_GET["move_children"];

        //error_log(print_r($_GET, true));

	if($move_children == "yes"){

		//find granparent
		$sql = "SELECT parent_node_id grandparent_id FROM nodes WHERE id = $node_id";
		$stmt = $db->select_query($sql);
		$row = $stmt->fetch();
		if(empty($row)){ echo "error with query"; exit; }
		$gp = $row["grandparent_id"];

		error_log("Found grandparent: $gp");


		$sql = "UPDATE nodes SET parent_node_id = ? WHERE parent_node_id = ?";
		$rowCount = $db->update_query($sql, array($gp, $node_id));
		error_log("Moved up children count: $rowCount, from query $sql");
	}

        $sql = "DELETE FROM nodes WHERE id = ?";
        $values = array($node_id);

        $row_count = $db->update_query($sql, $values);

	if($move_children != "yes"){
		error_log("Delete stray children");
		//delete children
			//DELETE n1 FROM nodes n1 LEFT JOIN nodes n2 on (n1.parent_node_id = n2.id) WHERE n1.parent_node_id IS NOT NULL AND n2.id IS NULL;
		$sql = "DELETE n1 FROM nodes n1 LEFT JOIN nodes n2 on (n1.parent_node_id = n2.id) WHERE n1.parent_node_id IS NOT NULL AND n2.id IS NULL";
		$rowCount = $db->update_query($sql);
		error_log("Deleted stray children: $rowCount");
	}

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

//step up amongst siblings
if(isset($_GET["step_up"]) && isset($_GET["node_id"])){
	error_log("step up");

	require_once("db.php");

        $node_id = $_GET["node_id"];

	if(!is_numeric($node_id)){
		echo "Non numeric id";
		exit;
	}
	$db = new db();

	//get parent node
	$sql = "SELECT parent_node_id FROM nodes WHERE id = $node_id";
	$stmt_ = $db->select_query($sql);

	$row_ = $stmt_->fetch();
	$parent_node_id = $row_["parent_node_id"];

	//see if has siblings
	$sql = "SELECT * FROM nodes WHERE parent_node_id = $parent_node_id";
	$stmt = $db->select_query($sql);

	if($stmt && $stmt->rowCount() > 1){
		$rows = $stmt->fetchAll();
		if($rows[0]["id"] != $node_id){
			$lastId = 0;
			foreach($rows as $row){
				if($row["id"] == $node_id){
					$sql = "update nodes t1 inner join nodes t2 on (t1.id, t2.id) in (($lastId,$node_id),($node_id,$lastId)) set t1.element_id = t2.element_id, t1.inner_html = t2.inner_html";
					error_log($sql);
					$row_count = $db->update_query($sql);
					if($row_count>0){
						echo "Affected_rows: $row_count";
						$sql2 = "UPDATE nodes SET parent_node_id = (CASE WHEN parent_node_id = $node_id THEN $lastId WHEN parent_node_id = $lastId THEN $node_id END) WHERE parent_node_id IN($lastId, $node_id)";
						error_log($sql2);
						$stmt2 = $db->update_query($sql2);
					}
					else{
						echo "Didn't work";
					}
					break;
				}
				$lastId = $row["id"];
			}
		}
		else echo "Element is first";
		exit;
	}
	else{
		echo "Element is alone";
		exit;
	}
}

/*
if(isset($_GET["add_node"]) && isset($_GET["parent_node_id"]) && isset($_GET["child_element_id"])){

        require_once("db.php");

        $add_node = $_GET["add_node"];
        $parent_node_id = $_GET["parent_node_id"];
        $child_element_id = $_GET["child_element_id"];
        error_log(print_r($_GET, true));

        $p = (int) preg_replace('/[^0-9]/', '', $parent_node_id);
        $c = (int) preg_replace('/[^0-9]/', '', $child_element_id);

        $db = new db();

        $row_count = $db->insert_query("INSERT INTO nodes (element_id, parent_node_id, inner_html) VALUES (?,?,?)", array($c, $p, ""));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{   
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}
*/

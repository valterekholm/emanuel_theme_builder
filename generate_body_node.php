<?php

require_once("db.php");
require_once("html.php");
require_once("sess.php");
require_once("functions.php");

$BODY_ELEMENT = "body"; //TODO: make user defined name, if no html elem is saved... or else if no html-elem has parent:null

$db = new db();
$html = new html();
$sess = new sess();

$sq1 = "SELECT * FROM html_element WHERE name = '$BODY_ELEMENT'";

$res = $db->select_query($sq1);

if($res){
$row = $res->fetch();

$found_body_e = $row;
}
else{
$found_body_e = false;
}

if(!$found_body_e){
$html->p("Det saknas rätt html_element ('$BODY_ELEMENT'), därför avbryter jag, gå baks");
exit;
}

$wep = $sess->getChoosenWebpage();

if(!$wep){
	$html->p("Du måste skapa/välja webbsida: <a href='choose_webpage.php'>OK</a>");
	exit;
}
else{
	$html->p("Found choosen webpage: $wep");
}


$sql = "select * from nodes WHERE web_page_id = $wep";

$res = $db->select_query($sql);
error_log(print_r($res, true));
$rows = $res->fetchAll();

$found_body_n = false;

//kolla om redan finns

foreach ($rows as $row) {
	$html->p($row["name"]);
	echo "--------------- <a href='#' id='move_up_" . $row["id"] . "'>^</a>";

       if($row["parent_node_id"] == null && $row["element_id"] == $found_body_e["id"]){
               $found_body_n = $row;

       }
}

$sql2 = "INSERT INTO nodes (element_id, web_page_id) VALUES (" . $found_body_e["id"] . ", $wep)";

if(!$found_body_n){
	echo $sql2;
	$res2 = $db->select_query($sql2);
	if($res2){ $html->p("OK; $BODY_ELEMENT"); }
	else{ $html->p("Query misslyckades?");}
}
else{
	$html->p("Det verkar finnas en body-node (samt element '$BODY_ELEMENT')");
}

?>

<p>
<a href="index.php">Återgå</a>
</p>





<?php

require_once("db.php");
require_once("html.php");
require_once("sess.php");
require_once("functions.php");

$db = new db();
$html = new html();
$sess = new sess();
//todo: flytta in js-filer från vendor-mappen till samma mapp som index
?>
<!DOCTYPE html>
<html>

<?=$html->headOpenConfig("choose webpage", array("charset"=>"utf-8"), "head1");?>
</head>
<body>

<?php

printMenu();

$sql = "SELECT * FROM web_page";

$res = $db->select_query($sql);

?>

<div class="content">

<article>
<?php

$html->p("wep: " . $sess->getChoosenWebpage());

if($res){

$rows = $res->fetchAll();

if(empty($rows)){
	$html->p("No saved webpage");
}
else{
$html->h1("Saved webpages");
echo "<table class='cleartable'>";
echo "<tr><th>Id</th><th>Name</th><th>Choose</th></tr>";
foreach($rows as $row){

$html->tr("<td>".$row["id"]."</td><td>".$row["name"]."</td><td><button onclick='chooseWp(".$row["id"].")'>Choose</button></td>");


}
echo "</table>";



}


}
else{
$html->p("No result from query");
}

?>
</article>
</div>

<form>
<fieldset>
<legend>Create webpage</legend>
<label for="page_name">Name</label>
<input type="text" id="pagename">
<input type="button" value="save" onclick="savepage()">
</fieldset>
</form>

<button onClick="clearWp()">Clear choice</button>


<?php
echo getcwd();//$_SERVER['SCRIPT_FILENAME'];
?>
<script type="text/javascript">


function savepage(){
var wp_name = document.querySelector("#pagename");
getAjax("ajax_operations.php?add_webpage=yes&name=" + encodeURI(wp_name.value), function(result){wp_name.value = ""; location.reload();});
}

function chooseWp(id){
getAjax("ajax_operations.php?choose_webpage=yes&wep_id=" + id, function(result){alert(result);location.reload();});
}

function clearWp(){
getAjax("ajax_operations.php?clear_choosen_webpage=yes", function(result){alert(result);location.reload();});
}

</script>
</body>
</html>

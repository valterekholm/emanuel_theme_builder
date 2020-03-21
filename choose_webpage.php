<?php

require_once("db.php");
require_once("html.php");
require_once("sess.php");

$db = new db();
$html = new html();
$sess = new sess();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
        <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/cupertino/jquery-ui.css">
        <link rel="stylesheet" href="Treant.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.js" integrity="sha256-lUVl8EMDN2PU0T2mPMN9jzyxkyOwFic2Y1HJfT5lq8I=" crossorigin="anonymous"></script>
        <script src="Treant.js"></script>
        <script src="theme_builder.js"></script>
        <script src="dialogesBoxes.js"></script>

</head>
<body>



<?php

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

<a href="index.php">Start</a>

<button onClick="clearWp()">Clear choice</button>

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

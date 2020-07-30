<?php
require_once("db.php");
require_once("html.php");
require_once("sess.php");
require_once("functions.php");
$db = new db();
$html = new html();
$sess = new sess();


if(isset($_POST["fieldname[]"]) && isset($_POST["attributes[]"])){
    $fnames = $_POST["fieldname[]"];
    $attrs = $_POST["attributes[]"];
    $flen = count($fnames);

    for($i=0; $i<$flen; $i++){
        error_log("post arg " . $fnames[$i] . " with attr from post " . $attrs[$i]);
    }
}


?>

<!DOCTYPE html>
<html>
<?=$html->headOpen("Query database", array("charset"=>"utf-8"),
array("https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/cupertino/jquery-ui.css","Treant.css","style.css"),
array("jquery-3.4.1.js", "jquery-ui.js", "raphael.js","Treant.js","theme_builder.js","dialogesBoxes.js")
);?>
</head>
<body>

<?php

printMenu();

?>

<h1>Make query</h1>

<form id="makeQuery" action="sql.php" method="post">
<fieldset>
<legend>Create table</legend>
<label>Name:</label>
<input name="name"></input>
</fieldset>
<fieldset>
<?php
$len=10;

$ph = array("id"=>"INT NOT NULL AUTO_INCREMENT", "name"=>"VARCHAR(20)", "PRIMARY KEY"=>"(id)", "FOREIGN KEY (PersonID)"=>"REFERENCES Persons(PersonID)");
$phl = count($ph);
$phk = array_keys($ph);
$phv = array_values($ph);
for($i=0; $i<$len; $i++){
    if($i<=$phl-1){
        $ph1 = $phk[$i];
        $ph2 = $phv[$i];
    }
    else{
        $ph1 = $ph2 = "";
    }
    ?>
    <label>field-name and definition</label>
<input name="fieldname[]" placeholder="<?=$ph1?>"><input name="attributes[]" placeholder="<?=$ph2?>"><br>
    <?php
}
?>
</fieldset>
<fieldset>
<input type="submit">
</fieldset>
</form>

</body>
</html>

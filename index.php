<?php
require_once("db.php");
require_once("html.php");
require_once("sess.php");

error_reporting(E_ALL);

$BODY_ELEMENT = "body"; //TODO: make user defined
?>

<html>
    <head>
	<link rel="stylesheet" href="../jquery-ui.css">
	<!-- link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/cupertino/jquery-ui.css" -->
	<link rel="stylesheet" href="Treant.css">
	<link rel="stylesheet" href="style.css">
        <script src="../jquery-3.4.1.js"></script>
	<!--script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script-->
        <script src="../jquery-ui.js"></script>
	<!--script src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.js"></script-->
        <script src="../vendor/raphael.js"></script>
	<!--script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.js" integrity="sha256-lUVl8EMDN2PU0T2mPMN9jzyxkyOwFic2Y1HJfT5lq8I=" crossorigin="anonymous"></script-->
        <script src="Treant.js"></script>
	<!-- script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.js" integrity="sha256-znpgHNqR9Hjjydllxj3UCvOia54DxgQcIoacHEXUSLo=" crossorigin="anonymous"></script-->
        <script src="theme_builder.js"></script>
        <script src="dialogesBoxes.js"></script>
        <script>
      //using jquery ui

            $(function () {
                $("#palette p").draggable({helper: "clone"});
            });

            $(function () {
                $(".node").droppable({
                    drop: function (event, ui) {
                        console.log("Dropped at " + this.id);
                        var draggable = ui.draggable[0]; //varför [0]?
                        console.log(draggable);
                        /*save*/

                        var pId = getAfter_(this.id);

                        var chId = getAfter_(draggable.id);

                        var queryArgs = "add_node=yes&parent_node_id=" + pId + "&child_element_id=" + chId;

			var innerHtml = null;

			if(draggable.className.indexOf("empty") > -1){
				console.log("Empty tag");
			}
			else{
				innerHtml = prompt("Enter any inner html:");
				//if(innerHtml.indexOf("'")>-1){	alert("Found quote"); }
			}

                        if (innerHtml != null) {
                            queryArgs = queryArgs + "&inner_html=" + encodeURI(innerHtml);
                        }

                        /*getAjax("ajax_operations.php?" + queryArgs, function (resp) {
                            alert(resp);
                            location.reload();
                        });*/
                        postAjax("ajax_operations.php", queryArgs, function (resp) {
                            alert(resp);
                            location.reload();
                        });



                        $(this)
                                .addClass("ui-state-highlight");
                    }
                });
            });

  $( function() {
    $( "#leftside" ).resizable();
  } );

            function getAfter_(text) {
                var pos_ = text.lastIndexOf("_");
                pos_++;
                return text.substr(pos_);
            }

        </script>
	
    </head>
    <body>

<?php
$db = new db();
$html = new html();
$sess = new sess();

$wep = $sess->getChoosenWebpage();

echo "theme builder (webpage $wep)";

echo "<div style='position:fixed; top:0; right:0; width:40px; height:40px; background:green;font-size:40px' onClick='showGuide()'><a href='#'>?</a></div>";



$sql_wp = "SELECT * FROM web_page";
$res = $db->select_query($sql_wp);
$rows_wp = $res->fetchAll();

if(empty($rows_wp) || empty($sess->getChoosenWebpage())){
	$html->p("You must first create/choose a webpage (session wep is ".$sess->getChoosenWebpage()."), <a href='choose_webpage.php'>OK</a>");
	exit;
}



//insert into html_element (id, name, is_empty_tag) values (1, 'table', 0);
$sql = "select * from html_element";

$res = $db->select_query($sql);
error_log(print_r($res, true));
$rows = $res->fetchAll();

$found_body_e = false;//e element

if(count($rows) == 0){
	$html->p("Du måste generera ett body-element ($BODY_ELEMENT)... <a href=\"generate_body_element.php\">OK</a>");
}
else{
	foreach ($rows as $row) {
		if($row["name"] == $BODY_ELEMENT){
			$found_body_e = $row;
		}
	}
	if(!$found_body_e){
		$html->p("Du måste generera ett body-element ($BODY_ELEMENT)... <a href=\"generate_body_element.php\">OK</a>");
	}
}


?>

        <div id="palette">
	<div id="palette_inner">
        <?php
        $html->h3("Element types:");
        foreach ($rows as $row) {
	    if($row["name"] == $BODY_ELEMENT){
		$html->span("(id: " . $row["id"] . ", " . $row["name"] . ")");//this one should allready be out there, so don't add it again
	    }
	    else{
		$isEmptyTag = $row["is_empty_tag"] == 1;
		$class = $isEmptyTag ? "empty" : "normal";
            	$html->p($row["id"] . " " . $row["name"], array("id" => "e_" . $row["id"], "class" => $class));
	    }
        }
        ?>

        </div>
            <form id="add_element_type">
                <fieldset>
                    <legend>Add element type</legend>
                    <input type="text" id="e_name" name="e_name" placeholder="name">
                    <input type="checkbox" id="is_empty" name="is_empty"><label for="is_empty" title="This is true only for a few elements like hr br input img col area basefont base frame">Is 'empty tag'</label><br>
                    <input type="button" onClick="addElementType()" value="add">

                </fieldset>
            </form>
	    <form id="alter_element_css">
		<fieldset>
			<legend>Edit element css</legend>
			<input type="hidden" id="el_css_web_page_id" value="<?=$wep?>">
			<input type="text" id="el_name" name="el_name" placeholder="element name">
			<input type="text" id="el_css" name="el_css" placeholder="css">
			<!--input type="button" onClick="updateElementCss()" value="update"-->
		</fieldset>
	    </form>

	</div>

<?php
//COALESCE(n.parent_node_id, \"null\")
//Get all nodes
$sql = "select n.id, parent_node_id, e.name, e.is_empty_tag, n.element_id, inner_html from nodes n left join html_element e on (n.element_id = e.id) WHERE web_page_id = $wep";
$res = $db->select_query($sql);
$rows = $res->fetchAll();

if((empty($rows) || count($rows) == 0) && $found_body_e){
		$html->p("Du måste generera en body-node (motsvarande $BODY_ELEMENT)... <a href=\"generate_body_node.php\">OK</a>");
}

//select * from html_element e join nodes n on (element_id = e.id);
?>

<div id="leftside">
<?php
$found_topnode = false;
$html->p("Nodes:");
echo "<table>";
$html->tr("<th>id</th><th>name</th><th>parent</th>");
foreach ($rows as $row) {
    //echo "r";
    $html->tr("<td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["parent_node_id"] . "</td>");
    if($row["parent_node_id"] == null){
	$found_topnode = $row;
	if($row["element_id"] == $found_body_e["id"]){
		//ok
		//$html->p("top-node motsvarar top-element");
	}
	else{
		$html->tr("<td colspan=3>Ditt top-parent-element motsvarar inte första radens html-element</td>");
		//första raden i html_element tabellen ska motsvara top-node i trädet (alltså en node utan parent)
	}
    }
}
echo "</table>";

if(!$found_topnode){
	$html->p("No topnode");
}

?>
</div>


        <!--div class="chart" id="OrganiseChart-simple">
        </div-->

        <div class="chart" id="OrganiseChart-simple2">
        </div>



        <a href="render.php">render</a>
	<a href="choose_webpage.php">choose webpage</a>


        <div id="u-node-dialog-form">

            <form>
                <div>
                    <label>Node id</label>
                    <input id="nodeId" readonly>
                </div>
                <div>
                    <label>Element id</label>
                    <input id="elementId">
                </div>
                <div>
                    <label>Parent id</label>
                    <input id="parentId">
                </div>
                <div>
                    <label>Inner html</label>
                    <input id="innerHtml">
                </div>
                <div>
                    <label>Is empty tag</label>
                    <input id="isEmptyTag" readonly>
                </div>

            </form>
        </div>

<div id="classes">
<h3>Classes</h3>
<?php
$sql3 = "select * from classes";//todo: use web_page_id
$res3 = $db->select_query($sql3);
$rows3 = $res3->fetchAll();
foreach($rows3 as $row){
$html->p($html->cssClass($row["name"], $row["css"], false));
}
?>
</div>
<div id="element_css">
<h3>Element css</h3>
<?php
$sql4 = "select * from element_css c left join html_element e on (c.name = e.id)";//todo: use web_page_id
$res4 = $db->select_query($sql4);
$rows4 = $res4->fetchAll();
foreach($rows4 as $row){
	$html->p($row["name"]."{ ".$row["css"]." } <a href='#' class='editElemCss'>edit</a>",
	array("id"=>"ec_".$row["id"], "data-element"=>$row["name"], "data-css"=>$row["css"], "data-wep"=>$wep));
}
?>
</div>
<script>
var nodes = [];

<?php
$nodes = array();
foreach ($rows as $row) {
	$nodes[] = $row;
}
$nds = json_encode($nodes);
echo "var arr = " . json_encode($nodes, JSON_UNESCAPED_SLASHES) . ";\n";
?>
            console.log(arr);
            var len = arr.length;
            var ids = [];//ids allready handled...
            var newNodes = [];
            for (var i = 0; i < len; i++) {
                ids.push(arr[i].id);//save those who have to be handled

                var node = {};
                node.text = {};
                node.text.name = arr[i].name;
                //node.text.data = arr[i].id;//to be able to add child as a user
                node.HTMLid = "n_" + arr[i].id;
                node.id = arr[i].id;

                node.parentId = arr[i].parent_node_id;

                node.text.data_elementid = arr[i].element_id;
                if (node.parentId !== null) {
		    console.log("node has parent id: " + i);
                    node.text.data_parentid = node.parentId;
                } else {
		    console.log("node has no parent: " + i);
                    node.text.data_parentid = "0";
                    node.HTMLclass = "isBaseNode";
                }
                node.text.data_isemptytag = arr[i].is_empty_tag;
                //console.log(arr[i].inner_html);
                var innerH = arr[i].inner_html; //could be null

                if (null !== innerH && innerH.length > 0) {
		    console.log("hasInnerHtml: " + i + " : " + innerH);
                    node.HTMLclass = "hasInnerHtml"; //just for marking "has text"
                    node.text.data_innerhtml = innerH;
		    node.nodeInnerHTML = innerH;
                } else {
		    console.log("has no innerHtml: " + i);
                    node.text.data_innerhtml = "";
		    node.nodeInnerHTML = innerH;
                }
                var btn = {val: "edit", href: "/", target: "_self"};
                node.text.contact = btn;

                console.log(node);
                newNodes.push(node);//later connect
            }

            var len2 = newNodes.length;

            var topParent = null;

            for (var i = 0; i < len2; i++) {
                if (newNodes[i].parentId == null) {
                    topParent = newNodes[i];
                    //newNodes[i] = null;
                    continue;
                }
                var parent = searchGet(newNodes, newNodes[i].parentId, true);
                if (parent != null) {
                    newNodes[i].parent = newNodes[parent];
                } else {
                    console.log("found not");
                }
            }

            console.log(newNodes);

            var config2 = {
                container: "#OrganiseChart-simple2"
            };
            var simple_chart_config2 = [];

            simple_chart_config2.push(config2);
            simple_chart_config2.push(topParent);

            for (var i = 0; i < len2; i++) {
                /*building chart*/
                if (newNodes[i].id != topParent.id) {
                    simple_chart_config2.push(newNodes[i]);
                }
            }

            chart2 = new Treant(simple_chart_config2, null, $);

            var nodes = document.getElementsByClassName("node");
            console.log("Nodes:");
            console.log(nodes);

            var len = nodes.length;
            for (var i = 0; i < len; i++) {

                //if base node, skip
                if (nodes[i].className.indexOf('isBaseNode') >= 0) {
                    continue;
                }

                var delete_ = document.createElement("a");
                delete_.className = "deleteBtn";
                delete_.innerHTML = "x";
                delete_.href = "#";
                delete_.title = "Delete";

                delete_.addEventListener("click", function (ev) {
                    ev.preventDefault();
                    console.log(ev.target);
                    var par = ev.target.parentElement;
                    console.log(par);
                    var id = getAfter_(par.id);
			//TODO: if no children, don't ask, check
                    var moveChildren = confirm("Move up children (save them)?");
                    var move = moveChildren ? "yes" : "no";
                    getAjax("ajax_operations.php?delete=yes&node_id=" + id + "&move_children=" + move, function (resp) {
                        alert(resp);
                        location.reload();
                    });
                });
                nodes[i].appendChild(delete_);

                var stepUp = document.createElement("a");
                stepUp.className = "stepUpBtn";
                stepUp.innerHTML = "<";
                stepUp.href = "#";
                stepUp.title = "Step up";

                stepUp.addEventListener("click", function (ev) {
                    ev.preventDefault();
                    console.log(ev.target);
                    var par = ev.target.parentElement;
                    console.log(par);
                    var id = getAfter_(par.id);
                    getAjax("ajax_operations.php?step_up=yes&node_id=" + id, function (resp) {
                        alert(resp);
                        location.reload();
                    });
                });
                nodes[i].appendChild(stepUp);
            }


            function searchGet(nodes, id, returnIndex) {
                //console.log("searchGet id " + id);
                var len = nodes.length;
                for (var i = 0; i < len; i++) {
                    if (nodes[i] != null)
                        if (nodes[i].id == id) {
                            //console.log("found");
                            if (returnIndex)
                                return i;
                            return nodes[i];
                        }
                }
                return null;
            }


            function addElementType() {
                var name = document.querySelector("#e_name").value;
                var isEmptyEl = document.querySelector("#is_empty");
                var isChecked = isEmptyEl.checked ? "yes" : "no";


                console.log("addElementType : " + name + ", isChecked : " + isChecked);
                getAjax("ajax_operations.php?add_element=yes&e_name=" + name + "&is_empty=" + isChecked, function (resp) {
                    alert(resp);
                    location.reload();
                });
            }
/*
            function updateElementCss() {
                var name = document.querySelector("#el_name").value;
                var webPageId = document.querySelector("#el_css_web_page_id");
		var css = document.querySelector("#el_css");

                console.log("updateElementCss : " + name + ", css : " + css);
                getAjax("ajax_operations.php?update_element_css=yes&wep="+webPageId+"&e_name=" + name + "&css=" + css, function (resp) {
                    alert(resp);
                    location.reload();
                });
            }
*/
        </script>

    </body>
</html>

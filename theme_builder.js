var choosenNode = null;

function getAjax(url, success) {
	console.log("getAjax med " + url + " och " + success);
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
		else if (xhr.readyState>3 && xhr.status>=500){ alert("Server error (" + xhr.responseText + ")") }
		else if (xhr.readyState>3 && xhr.status>=400){ alert("Client error (" + xhr.responseText + ")") }
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
}


function postAjax(url, data, success) {
	console.log("postAjax till url " + url);
	var params = typeof data == 'string' ? data : Object.keys(data).map(
		function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
	).join('&');

	console.log(params);

	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open('POST', url, true);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) {
			console.log("postAjax succeeded");
			success(xhr.responseText);
		}
		else if (xhr.status>=500) alert("Server error");
		else if (xhr.status>=400) alert("Client error");
	};

	/*xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');*/
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}

function showGuide(){
var infoDiv = document.createElement("div");
infoDiv.style = "width:100%;height:33%;background:rgba(230,250,240,.8); position:fixed;bottom:0;left:0; border: 0 solid blue; border-top-width:5px; text-align: center;";
document.body.appendChild(infoDiv);

var tbl = document.createElement("table");
tbl.className = "infotable";
tbl.style="font-size:40px;text-align:center;width:100%;font-family:sans;line-height:75px;";
tbl.innerHTML = "<tr><td>B</td><td style='width:15%'>A</td></tr>";

infoDiv.appendChild(tbl);

var info = document.createElement("tfoot");
info.style="margin:1%; padding:5%; border: 2px solid gray;";
info.innerHTML = "Drag an element from A to B, drop it in an existing node";

tbl.appendChild(info);

var dismiss = document.createElement("button");
dismiss.innerHTML = "Dismiss";
dismiss.addEventListener("click", function(){
	var div = this.parentElement;
	var gpar = div.parentElement;
	gpar.removeChild(div);
});
infoDiv.appendChild(dismiss);
}

window.onresize = function(){
	setTimeout(function(){
		if(confirm("Window resized, please reload page")){ location.reload(); }
	},200);
}

window.addEventListener("load", function(){
	var nodesTable = document.querySelector("#allNodes");
	var rows = nodesTable.getElementsByTagName("tr");
	var len = rows.length;

	for(var i=0; i<len; i++){
		var r = rows[i];

		r.addEventListener("mouseenter", function(){
			var iH = this.firstElementChild.innerHTML;
			//console.log(iH);
			highlightNodeById("n_" + iH);
			this.className = "higlight";
		});
		r.addEventListener("mouseleave", function(){
			var iH = this.firstElementChild.innerHTML;
			this.className = "";
			highlightOffById("n_" + iH);
		})
	}
});

function highlightNodeById(id){
	console.log("higlight " + id);
	document.getElementById(id).classList.add("highlight");
	setTimeout(function(){
		var id_ = id;
		//console.log(id_);
		//document.getElementById(id_).classList.remove("highlight");
	}, 1000);
}

function highlightOffById(id){
	document.getElementById(id).classList.remove("highlight");
}

//to show buttons to add class
function classMenu(id){
	document.querySelector("#classes").classList.add("menu");
	choosenNode = getAfter_(id);
	console.log("choosen node: " + choosenNode);
}


function addClassToNode(classId){
	console.log("addClassToNode with id " + classId + " to node " + choosenNode);
	var queryArgs = "add_class_to_node=yes&node_id=" + choosenNode + "&class_id=" + classId;
	getAjax("ajax_operations.php?"+queryArgs, function (resp) {
		alert(resp);
		location.reload();
	});
}

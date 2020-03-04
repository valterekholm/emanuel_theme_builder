function getAjax(url, success) {
	console.log("getAjax med " + url + " och " + success);
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
		else if (xhr.status>=500) alert("Server error");
		else if (xhr.status>=400) alert("Client error");
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
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

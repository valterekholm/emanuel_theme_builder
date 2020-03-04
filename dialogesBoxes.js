inner_h = "";
  $( function() {
    var dialog, form,
 
      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      nodeId = $("#nodeId");
      elementId = $("#elementId");
      innerHtml = $("#innerHtml");
      parentId = $("#parentId");

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }


    function updateNode(){
	var valid = true;

	//validate
	if(valid){
		var args = "node_id="+nodeId.val()+"&element_id="+elementId.val()+"&parent_id="+parentId.val()+"&inner_html="+encodeURI(innerHtml.val());
		getAjax("ajax_operations.php?update_node=yes&" + args, function(resp){
			//alert(resp);//replace with non-blocking message (after reload?)
			location.reload();
			});
	}
	return valid;
    }
 
    dialog = $("#u-node-dialog-form").dialog({
      autoOpen: false,
      height: 400,
      width: 350,
      modal: true,
      buttons: {
        "Update node": updateNode,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        //allFields.removeClass( "ui-state-error" );
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });
 
    $( "#create-user" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });

    $( ".node-contact" ).button().on( "click", function(event) {
      event.preventDefault();
      //console.log(event);
      var parent = event.target.parentNode;

      console.log(parent);

      inner_h = parent.getElementsByClassName("node-data_innerhtml")[0].innerHTML;
      console.log(innerHtml);
      var element_id = parent.getElementsByClassName("node-data_elementid")[0].innerHTML;

      var parent_id = parent.getElementsByClassName("node-data_parentid")[0].innerHTML;

      var is_empty_tag = parent.getElementsByClassName("node-data_isemptytag")[0].innerHTML;

      var is_empty = is_empty_tag>0;

      var empty = is_empty ? "yes" : "no";

      var myId = event.target.parentNode.id;

      $("#nodeId").val(getAfter_(myId));
      $("#elementId").val(element_id);
      $("#parentId").val(parent_id);
      $("#innerHtml").val(inner_h);
      $("#isEmptyTag").val(empty);
      dialog.dialog( "open" );
    });

  } );

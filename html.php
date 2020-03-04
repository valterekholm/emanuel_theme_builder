<?php

class html {

    private $opener;//<table>
    private $closer;//</table>
    private $closedTags;

    function __construct() {
        error_log("html construct");
        $this->opener = "p";
        $this->closer = "p";
	$this->closedTags = array('input', 'hr', 'br');
    }

    function setOpener($str){
	$this->opener = $str;
    }

    function setCloser($str){
	$this->closer = $str;
    }

    function makeOpenTag($str = "p"){
	return "<$str>";
    }

    function makeCloseTag($str = "p"){
	return "</$str>";
    }

    function p($innerHTML, $attributes = array()) {
	echo "<p";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){

			echo " $key = '$val'";
		}
	}
	echo ">$innerHTML</p>";
    }

    function span($innerHTML, $attributes = array()) {
	echo "<span";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){

			echo " $key = '$val'";
		}
	}
	echo ">$innerHTML</span>";
    }

    function tr($innerHTML, $attributes = array()) {
	echo "<tr";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){

			echo " $key = '$val'";
		}
	}
	echo ">$innerHTML</tr>";
    }

    function h3($innerHTML, $attributes = array()) {
	echo "<h3";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){

			echo " $key = '$val'";
		}
	}
	echo ">$innerHTML</h3>";
    }
    function h1($innerHTML, $attributes = array()) {
	echo "<h1";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){

			echo " $key = '$val'";
		}
	}
	echo ">$innerHTML</h1>";
    }

}

?>

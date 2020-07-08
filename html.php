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

    function p($innerHTML, $attributes = array(), $echo = true) {
	if($echo) echo "<p";
	else $ret = "<p";
	if(!empty($attributes)){

		foreach($attributes as $key=>$val){
			if($echo)
				echo " $key = '$val'";
			else $ret .= " $key = '$val'";
		}
	}
	if($echo) echo ">$innerHTML</p>";
	else{
		$ret .= ">$innerHTML</p>";
		return $ret;
	}
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

    function cssClass($name, $css, $echo = true){
	if($echo)
	echo ".$name{ $css }";
	else return ".$name{ $css }";
	} 
	
	/**
	 * metas key/value like "charset"=>"utf-8" or "description"=>"a webpage"
	 * csses simple array of strings (files)
	 * jscripts simple array of strings (files)
	*/
	function headOpen($title, $metas = array(), $csses = array(), $jscripts = array()){
		$ret = "<head>";
		$ret .= "<title>$title</title>";

		foreach($metas as $key => $value){
			if($key=="charset"){
				$ret .= "<meta charset='$value'>";
			} else {
				$ret .= "<meta name='$key' content='$value'>";
			}
		}//todo: make function for newlines and good indentations

		foreach($csses as $url){
			$ret .= "<link rel='stylesheet' href='$url'>";
		}

		foreach($jscripts as $script){
			$ret .= "<script src='$script'></script>";			
		}

		return $ret;

	}

	function isAssoc(array $arr)//from https://stackoverflow.com/questions/173400
	{
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

}

?>

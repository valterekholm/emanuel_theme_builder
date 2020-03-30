<?php

class db {

    private $user;
    private $pass;
    private $conn;

    function __construct() {
        error_log("construct");
        $this->user = "builder";
        $this->pass = "12345";
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
        $this->conn = new PDO('mysql:host=localhost;dbname=emanoel_theme_builder', $this->user, $this->pass, $options);

    }

    function select_query($sql, $force_lower_case = true) {
        error_log("select_query, med $sql");
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");

	$stmt = $this->conn->prepare($sql);
        $stmt = $this->conn->query($sql);

        return $stmt;
    }

    //sql - a query with positional placeholders
    //values an indexed array
    function insert_query($sql, $values = array(), $force_lower_case = true){
	error_log("insert_query($sql)");
	error_log("values: " . print_r($values, true));
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");


	$stmt = $this->conn->prepare($sql);
	$res = $stmt->execute($values);
	error_log("stmt: " . print_r($stmt, true));
	error_log("res: " . print_r($res, true));
	$error = $stmt->errorInfo()[0];
	$detail = $stmt->errorInfo()[1];
	if($error == 23000 && $detail == 1062){
		//error_log("Place is taken");
		throw new DomainException('Dublicate error');
	}

	//error_log("errorInfo: " . print_r($stmt->errorInfo(), true));
	return $stmt->rowCount();
    }

    function update_query($sql, $values = array(), $force_lower_case = true){
	error_log("update_query($sql)");
	error_log("values: " . print_r($values, true));
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");

        $stmt = $this->conn->prepare($sql);
        $res = $stmt->execute($values);
        error_log("stmt: " . print_r($stmt, true));
        error_log("res: " . print_r($res, true));
        return $stmt->rowCount();
    }


function array_to_pdo_params($array) {
  $temp = array();
  foreach (array_keys($array) as $name) {
    $temp[] = "`$name` = ?";
  }
  return implode(', ', $temp);
}

}
?>

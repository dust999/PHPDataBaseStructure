<?php

    // VALUES
	$server = "127.0.0.1";
	$user = "root";
	$pass = "";

	$database = "name";

	function MakeConnect($server, $user, $pass, $database){
        $conn = mysqli_connect($server, $user, $pass, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;

    }

    function MakeQuery($conn, $sql){
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
	}

    function SimpleQuery($sql){

        global $server, $user, $pass, $database;

        $conn = MakeConnect($server, $user, $pass, $database);

        MakeQuery($conn, $sql);

        CloseConnect($conn);
    }

	function SimpleRequest($sql){

        global $server, $user, $pass, $database;

        $conn = MakeConnect($server, $user, $pass, $database);

        $response=[];

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_row()) {
            //while($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        } else {
            echo "No results";
        }

        CloseConnect($conn);

        return $response;
    }

    function InsertRowArray($table, $rowArray){

        $sql ="INSERT INTO ".$table;

        foreach($rowArray as $key=> $value){
            $values[] = $value;
            $keys[] = $key;
        }

        $sql .= " (";

        foreach ($keys as $key){
            $sql .= $key.", ";
        }

        $sql = substr($sql, 0, strlen($sql)-2);
        $sql .= ") VALUES (";

        foreach ($values as $value){
            $sql .= $value.", ";
        }

        $sql = substr($sql, 0, strlen($sql)-2);
        $sql .= ")";

        SimpleQuery  ($sql);
}

    function CloseConnect($conn){
        $conn->close();
    }

?>
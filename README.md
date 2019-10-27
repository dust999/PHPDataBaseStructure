# PHP DataBase Structure

Simple PHP script that create simple API for CREATE, UPDATE, SELECT and DELETE data from the database

Just two files:
- mysql_connect.php
- create_db_structure.php

Script compatible with mysql database but you can easily convert to any other database

You create in PhpMyAdmin your database. And use this script to create simple API for it.
Script generate file for each table with 4 functions inside. You can easyly change it to produce just one file

Generated file example for table `comments`:

<code>
//// TABLE: comments AUTO GENERATED API

	function InsertComments( $user_id, $comment_text ){

		$table = "comments";

		$row ["comment_id"] = "NULL";
		$row ["user_id"] = $user_id; //int(11);
		$row ["comment_text"] = $comment_text; //text;

		InsertRowArray($table, $row);
	}

	function SelectComments( $columns = "*", $conditions = "" ){

		$table = "comments";

		$sql = "SELECT ".$columns." FROM ".$table;

		if ( strlen($conditions) > 0 ) $sql .= " ".$conditions;

		return SimpleRequest($table, $sql);
	}

	function UpdateComments( $comment_id, $user_id = false,  $comment_text = false ){

		$table = "comments";

		if ( $user_id === false && $comment_text === false ) return false;

		$sql = "UPDATE `comments` SET";

		if ($user_id !== false) $sql .=  "`user_id` = $user_id,";
		if ($comment_text !== false) $sql .=  "`comment_text` = $comment_text,";

		$sql = substr( $sql, 0, strlen($sql)-2 );

		$sql .= " WHERE `comments`.`comment_id` = $comment_id";

		SimpleQuery($table, $sql);
	}

	function DeleteComments( $comment_id ){

		$table = "comments";

		$sql = "DELETE FROM `comments` WHERE `comments`.`comment_id` = $comment_id";

		SimpleQuery($table, $sql);
}
</code>

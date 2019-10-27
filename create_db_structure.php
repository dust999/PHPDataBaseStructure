<?php
    // DATA BASE SIMPLE API
    require_once ("mysql_connect.php");

    $sql = "SHOW TABLES";

    $tables = SimpleRequest($sql);

    foreach( $tables as $key => $value){

        $table = $value[0];

        CreateDataBaseFunctions($table);

    }

    // GENERATOR INSERT, SELECT, UPDATE, DELETE  FOR EACH TABLE IN BASE
    function CreateDataBaseFunctions($table){

        $file ="<?php";
        $file .= "\n\n//// TABLE: ".$table." AUTO GENERATED API\n\n";

        $out = "\tfunction Insert".ucfirst($table)."( {params} ){\n\n";
        $out .= "\t\t".'$table = "'.$table.'";'."\n\n";
        $params = "";

        $sql = "DESCRIBE ".$table;

        $columns = SimpleRequest($sql);

        $fields = [];

        foreach ($columns as $c=>$val){
            if(strpos($val[5],"auto_increment") !== false){
                $val[1] = "NULL";
                $id = $val[0];
                $out .= "\t\t".'$row ["'.$val[0] .'"] = "NULL";'."\n";
            }
            else{
                $params .= "$".$val[0].", ";
                $fields[] = "$".$val[0];
                $out .= "\t\t".'$row ["'.$val[0] .'"] = $'.$val[0].'; //'.$val[1].";\n";
            }


        }

        $out .= "\n\t\t".'InsertRowArray($table, $row);'."\n";
        $out .= "\t".'}'."\n";

        $params = substr($params, 0 , strlen($params)-2);

        $out= str_replace("{params}", $params, $out);

        $file .= $out;

        // SELECT FUNCTION
        $out = "\n\tfunction Select".ucfirst($table).'( $columns = "*", $conditions = "" ){'."\n\n";
        $out .= "\t\t".'$table = "'.$table.'";'."\n\n";
        $out .= "\t\t".'$sql = "SELECT ".$columns." FROM ".$table;'."\n\n";
        $out .= "\t\t".'if ( strlen($conditions) > 0 ) $sql .= " ".$conditions;'."\n\n";
        $out .= "\t\t".'return SimpleRequest($table, $sql);'."\n";
        $out .= "\t".'}'."\n";

        $file .= $out;

        // UPDATE FUNCTION
        $paramsUpdate = str_replace(","," = false, ", $params). " = false";
        $out = "\n\tfunction Update".ucfirst($table)."( $".$id.", ".$paramsUpdate." ){\n\n";
        $out .= "\t\t".'$table = "'.$table.'";'."\n\n";
        $out .= "\t\t".'if ( '.str_replace(', ',' === false && ', $params).' === false ) return false;'."\n\n";
        $out .= "\t\t". '$sql = "UPDATE `'.$table.'` SET";'."\n\n";
        foreach($fields as $field){
            $out .= "\t\t".'if ('.$field.' !== false) $sql .=  "`'.str_replace("$", "", $field).'` = '.$field.',";'."\n";
        }
        $out .= "\n\t\t".'$sql = substr( $sql, 0, strlen($sql)-2 );'."\n\n";
        $out .= "\t\t".'$sql .= " WHERE `'.$table.'`.`'.$id.'` = $'.$id.'";'."\n\n";
        $out .= "\t\t".'SimpleQuery($table, $sql);'."\n";
        $out .= "\t".'}'."\n";

        $file .= $out;

        // DELETE FUNCTION
        $out = "\n\tfunction Delete".ucfirst($table)."( $".$id." ){\n\n";
        $out .= "\t\t".'$table = "'.$table.'";'."\n\n";
        $out .= "\t\t".'$sql = "DELETE FROM `'.$table.'` WHERE `'.$table.'`.`'.$id.'` = $'.$id.'";'."\n\n";
        $out .= "\t\t".'SimpleQuery($table, $sql);'."\n";
        $out .= '}'."\n";

        $file .= $out;

        $file .= "?>";

        SaveFile($table, $file);
    }

    function SaveFile($name, $content){
        // MAKE FOLDER
        if(!is_dir("database"))
            mkdir("database");

        // SAVE FILES
        $fh = fopen("database/".$name.".php","w+");
        fputs($fh, $content);
        fclose($fh);
    }
?>
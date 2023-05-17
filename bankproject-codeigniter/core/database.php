<?php
$serverName = "demo2.database.windows.net"; 
$connectionInfo = array( "Database"=>"TABHR_TEST", "UID"=>"PublicUser", "PWD"=>"Shoretec!Azure!qwerty", 'ReturnDatesAsStrings'=> true, "CharacterSet" => 'utf-8');

$conn = sqlsrv_connect($serverName, $connectionInfo);

ini_set('default_charset', 'ISO-8859-1');                                 

if( !$conn ) {
     echo "Connection could not be established.<br>";
     die( print_r( sqlsrv_errors(), true));
}

$version = "1.0";


function query($sql, $values = []) {
    $conn = $GLOBALS["conn"];
    $res = sqlsrv_query($conn, $sql, $values);
    if (!$res) {
        die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . "<br>" . $sql]));
    }
    return $res;
}

function insertQuery($sql, $values) {
    $res = query($sql . "; SELECT SCOPE_IDENTITY()", $values);
    return getLastInsertID($res);
}

function existsQuery($table, $column, $value) {
    $sql = "SELECT * FROM $table WHERE $column = '$value'";
    $res = query($sql);
    if (sqlsrv_has_rows($res)){
        return sqlsrv_fetch_array($res, 1)[0];
    } else {
        return false;
    }
}

function getQueries($table, $columns, $PK) {
    $insert_sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . substr(str_repeat('?, ', count($columns)), 0, -2) . ");";
    $update_sql = "UPDATE $table SET " . implode(' = ?, ', $columns) . " = ? WHERE $PK = ?;";
    return [$insert_sql, $update_sql];
}
?>
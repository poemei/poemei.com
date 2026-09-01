<?php
/**
 * path: /app/core/database.php
 */
$config_path = __DIR__ . '/../config.php';

if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("critical error: config.php not found at " . $config_path);
}

// check if constant exists before use to prevent fatal error
if (!defined('db_host')) {
    die("critical error: db_host constant not defined in config.php");
}

$db = mysqli_connect(db_host, db_user, db_pass, db_name);

if (!$db) {
    die("connection failed: " . mysqli_connect_error());
}

// procedural helpers

/**
 * query()
 * Usage: query("select * FROM table WHERE something = 1);
*/
function query($sql) {
    global $db;
    return mysqli_query($db, $sql);
}

/**
 * escape()
 * Escapes strings
 * Usage: ecape($value);
*/
function escape($value) {
    global $db;
    return mysqli_real_escape_string($db, $value);
}

/**
 * fetch_all()
 * executes a query and returns all rows as an associative array
 * Usage: fetch_all($query);
 */
function fetch_all($sql) {
    global $db;
    $res = mysqli_query($db, $sql);
    
    if (!$res) {
        return [];
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    
    return $data;
}

function fetch_one($sql) {
    $result = query($sql);
    return mysqli_fetch_assoc($result);
}

<?php
require_once "functions.php";

try {
    $db = openDb();
}
catch(PDOException $pdoex) {
    returnError($pdoex);
}
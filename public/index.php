<?php

session_start();

// We need bootstrap
require_once dirname(__DIR__) . '/app/bootstrap.php';


$app = new router();
$app->run();

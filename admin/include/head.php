<?php
ini_set('session.gc_maxlifetime', 30*60);

// overrides the default PHP memory limit.
ini_set('memory_limit', '-1');

// set time limit for prevent time out
set_time_limit(0);

define('MY_ROOT_DIR', __DIR__);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

date_default_timezone_set("Asia/Hong_Kong");

include('PM/PM.php');

?>
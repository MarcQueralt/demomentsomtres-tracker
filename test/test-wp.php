<?php
echo "<pre>Inici\n";
include_once '../lib-client/dms3-tracking-wp.php';

global $demomentsomtres_tracking;
echo print_r($demomentsomtres_tracking,true);
echo $demomentsomtres_tracking->tracking();
//echo print_r($sessio,true);
echo "Fi</pre>";
?>
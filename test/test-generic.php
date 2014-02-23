<?php
echo "<pre>Inici\n";
include_once '../lib-client/dms3-tracking-generic.php';

global $demomentsomtres_tracking;
$demomentsomtres_tracking->tracking($data);
echo "Fi</pre>";
?>
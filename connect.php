<?php
$servername = "us-cdbr-east-06.cleardb.net";
$username = "b6ada911f41140";
$password = "55195bbd";
$db="heroku_4f545d4371d334f";
$conn = mysqli_connect("$servername", "$username", "$password","$db");
mysqli_set_charset($conn,"utf8");
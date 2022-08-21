<?php
require_once 'connect.php';
$chat_id=1701;
function no_apostrof(string $satr):string{
    for ($i=0;$i<strlen($satr);$i++){
        if($satr[$i]=="'"){
            $satr[$i]="`";
        }
    }
    return $satr;
}
echo no_apostrof("Diyorbek");
$firstname="diyorbek";
$lastname="boltayev";
$username="zzzzzv";

$sql = "select * from users where chat_id='$chat_id'";
$result = mysqli_query($conn, $sql);
var_dump($result);
if ($result->num_rows == 0) {

    $sql = "insert into users (chat_id,firstname,lastname,username,page) values ('$chat_id','$firstname','$lastname','$username','')";
    $result = mysqli_query($conn, $sql);
    var_dump($result);
}
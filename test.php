<?php
require_once 'connect.php';
$chat_id=232434;
function no_apostrof(string $satr):string{
    for ($i=0;$i<strlen($satr);$i++){
        if($satr[$i]=="'"){
            $satr[$i]="`";
        }
    }
    return $satr;
}


//$sql="select language from users where chat_id='$chat_id'";
//$result=mysqli_query($conn,$sql);
//$result=$result->fetch_assoc();
//$lang=$result['language'];
//echo $lang;
var_dump(getPage($chat_id));
function getPage($chat_id)
{
    global $conn;
    $sql = "select page from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    $result = $result->fetch_assoc();

    return $result['page'];
}
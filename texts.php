<?php
require_once "user.php";

function getTexts($keyword,$chat_id){
    global $conn;
    $lang=getLang($chat_id);
    $sql="select * from texts where keyword='{$keyword}' limit 1";
    $result=mysqli_query($conn,$sql);
    $result=$result->fetch_assoc();
    return $result[$lang];
}


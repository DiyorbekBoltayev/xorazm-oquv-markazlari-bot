<?php
require_once "connect.php";
require_once "index.php";

function setLang($chat_id, $lang)
{
    global $conn;
    $sql = "update users set language='$lang' where chat_id='$chat_id'";
    mysqli_query($conn, $sql);
}

function getLang($chat_id)
{
    global $conn;
    $sql = "select language from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    $result = $result->fetch_assoc();
    return $result['language'];
}

function changeLang($chat_id){
    if(getLang($chat_id)=="uz"){
        setLang($chat_id,'ru');
    }elseif (getLang($chat_id)=='ru'){
        setLang($chat_id,'uz');
    }
}

function setPage($chat_id, $page)
{
    global $conn;
    $sql = "update users set page='$page' where chat_id='$chat_id'";
    mysqli_query($conn, $sql);
}

function getPage($chat_id)
{
    global $conn;
    $sql = "select page from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    $result = $result->fetch_assoc();
    $s = $result['page'];
    if (is_null($s)) {
        $s = "";
    };
    return $s;
}


function getDistricts($chat_id){
    global $conn;
    $lang=getLang($chat_id);
    $sql="select $lang from districts ";
    $result=mysqli_query($conn,$sql);
    $d=[];
    while ($row=$result->fetch_assoc()){
        $d[]=$row[$lang];
    }
    return $d;
}

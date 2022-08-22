
<?php
require_once 'connect.php';
    $text="Urganch tumani";
    $sql="select id from districts where uz='$text' or ru='$text'";
    $result=mysqli_query($conn,$sql);
    if($result->num_rows==0){
        var_dump(0);
    }else{

        $result=$result->fetch_assoc();
        var_dump($result['id']);
    }



<?php
    $servername="localhost";
    $username="root";
    $password="";
    $dbname="primebank";
    $conn=mysqli_connect($servername,$username,$password,$dbname);
    if($conn){
        
    }
    else{
        echo "Connection failed.";
    }
?>
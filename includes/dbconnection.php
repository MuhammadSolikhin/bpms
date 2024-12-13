<?php

$host = 'localhost';
$dbname = 'bpmsdb';
$username = 'root'; 
$password = ''; 


$con = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($con->connect_error) {
    die("Koneksi ke database gagal: " . $con->connect_error);
}
?>

<?php
header("Content-Type:image/png");

$image = './desk.png'; 

if(is_file($image))
    readfile($image);
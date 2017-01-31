<?php
$str1 = 'abcdef';
$substr1 = substr($str1, 0, 1);
$substr2 = substr($str1, 2, 3);

print($str1.' の0バイトから1バイト取り出すと '.$substr1.' です<br>');
print($str1.' の2バイトから3バイト取り出すと '.$substr2.' です<br><br>');


    // am 9:00 ~ pm 22:45
    //$target_hh = array("9","10","11","12","13","14","15","16","17","18","19","20","21","22");
    $target_hh = array("h_9", "h_10", "h_11", "h_12", "h_13", "h_14", "h_15", "h_16", "h_17", "h_18", "h_19", "h_20", "h_21", "h_22", "h_23");

$target_mm = array("00","15","30","45");
    $columnArray = array();
    echo "target_hh count:" .count($target_hh) ."\n";
    foreach ($target_hh as $k => $v) {
        $actionArray = array();
        echo "throw key:" .$k ."<br>";
        echo "edit number" .substr($v,2) ."\n";
        if ((($k + 1) % 3 == 0) || (($k + 1) == (count($target_hh)))) {
            echo "key:" .$k ."\n";
        }
    }
    echo "end\n";

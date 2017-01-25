<?php


    // am 9:00 ~ pm 22:45
    $target_hh = array("9","10","11","12","13","14","15","16","17","18","19","20","21","22");
    $target_mm = array("00","15","30","45");
    $columnArray = array();
    echo "target_hh count:" .count($target_hh) ."\n";
    foreach ($target_hh as $k => $v) {
        $actionArray = array();
        echo "throw key:" .$k ."\n";
        if ((($k + 1) % 3 == 0) || (($k + 1) == (count($target_hh)))) {
            echo "key:" .$k ."\n";
        }
    }
    echo "end\n";

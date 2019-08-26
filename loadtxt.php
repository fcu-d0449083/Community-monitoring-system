<?php    //監控功能中讀取文字檔以顯示監測到的人名//
        $file = "show/monitor.txt";
        $f = fopen($file, "r");
        while ( $line = fgets($f, 1000) ) {
            print $line;
        }
    ?>
<?php


ini_set('display_errors', 'stderr'); //Erross on stderr 

// if ($argc > 1)
// if ($argv[2] == 'neco ')

    echo("kek");

    while($line = fgets(STDIN)) {

        // oddelene mista mezerami 
        // split rozdeli podle mezer 
        // trim da pryc newline  
        $splitted = explode(' ', trim($line, "\n"));
        foreach ($splitted as $terminal){
            echo($terminal . "\n");
        }
    }

    switch($splitted[0]){
        case 'MOVE':
            echo("dd");
    }

    exit(0);







?>
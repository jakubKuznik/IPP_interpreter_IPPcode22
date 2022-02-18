<?php

//fwrite(STDERR, "something");

ini_set('display_errors', 'stderr'); //Erross on stderr 

    define("HELP", " \nProgram read IPP_code22 from stdin and represent it in xml
    --help        // Display help cannot be combinated with anything 
    --stats=file  // File where statisic will be writen.
    --loc         // statistic - number of lines   
    --coments     // statistic - number of lines that has a comment 
    --labels      // statistic - number of labels in code     
    --jumps       // statistic - number or returns and jumps instruction\n");


    $loc     = 0;  // Number of lines in code 
    $coments = 0;  // Number of lines that has a comment 
    $labels  = 0;  // Number of labels in code 
    $jumps   = 0;  // Number of retuns and jumps in code 

    parse_args($argc, $argv);


    /**
     * Function parse arguments.
     * 
     * --help        // Display help cannot be combinated with anything 
     * --stats=file  // File where statisic will be writen.
     * --loc         // statistic - number of lines   
     * --coments     // statistic - number of lines that has a comment 
     * --labels      // statistic - number of labels in code     
     * --jumps       // statistic - number or returns and jumps instruction
     * 
     * 
     *  example 
     * ./parse.php --stats=file1 --loc --comments --stats=file2 --coments -labels --jumps 
     * 
     * 
     * -- unknown parameter - error 10
     * -- 
     * 
     *  */    
    function parse_args(int $argc, array $argv){
        echo($argc);
        foreach ($argv as $ar){
            if($ar == '--help'){
                if($argc == 2 )
                    echo HELP;
                else{
                    fwrite(STDERR, "Cannot combine --help with other parameters.");
                    exit(10);
                }
            }
            echo($ar);
        }
    }

    /**
     *   
     *
     */

    

/*
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

    echo(sum(2,5));


    //strto upper 
    switch($splitted[0]){
        case 'MOVE':
            echo("dd");
    }
    
    //strto upper 
    switch($splitted[1]){
        case 'MOVE':
            echo("dd");
    }



    exit(0);

    function sum(int $a, int $b){
        return $a + $b;
    }
*/
?>
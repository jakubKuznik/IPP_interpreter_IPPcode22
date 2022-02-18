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


    $loc     = 0;   // Number of lines in code 
    $coments = 0;   // Number of lines that has a comment 
    $labels  = 0;   // Number of labels in code 
    $jumps   = 0;   // Number of retuns and jumps in code 
    $stats_files;   // All files for statistic 
    $stats_params;  // all params for all files

    $h = parse_args($argc, $argv);
    $stats_files  = $h[0];
    $stats_params = $h[1];
    print_r($stats_files);
    print_r($stats_params);


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
     *  example: 
     * ./parse.php --stats=file1 --loc --comments --stats=file2 --coments -labels --jumps 
     * 
     *  stats_file[0] = file1
     *  stats_file[1] = file2 
     * 
     *  stats_param[0][0] = loc,     stats_param[0][1] = comments
     *  stats_param[1][0] = coments, stats_param[1][1] = lables    stats_param[1][2] = jumps 
     * 
     * 
     * -- unknown parameter - error 10
     * -- 
     * 
     * 
     * return array($stats_filse, $stats_param)
     * 
     *  */    
    function parse_args(int $argc, array $argv){
        $stats          = false;   // Indicator if stats arg is there 
        $stats_arg_sum  = 0;       // How many stats args are there 
        $stats_sum      = 0;       // how manz stats files are there.
        $s_f;                      // stats_file File where statisic will be writen 
        $s_p;                      //  statas_param Parameters for eas statas 
        $patern = "/^--stats=/";   // regex for --stats=filename

        foreach ($argv as $ar){
            if ($argv[0] == $ar) //Skip script name  
                continue;
            else if ($ar == '--help') {
                if ($argc == 2){
                    echo("chuj");
                    echo HELP;
                }
                else {
                    fwrite(STDERR, "Cannot combine --help with other parameters.");
                    exit(10);
                }
            }
            // todo if --stats void then error 
            else if (preg_match($patern, $ar)){ // if == --stats
                // explode return string after --stats= in array   
                // imlode convert that array to single string 
                $s_f[$stats_sum] = implode( " ", explode("--stats=", $ar)); 
                $stats_sum++;
                $stats = true;
            }

            else if ($ar == '--loc'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'loc';
                $stats_arg_sum++;
            }
            else if ($ar == '--comments'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'com';
                $stats_arg_sum++;
            }
            else if ($ar == '--lables'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'lab';
                $stats_arg_sum++;

            }
            else if ($ar == '--jumps'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'jum';
                $stats_arg_sum++;
            }
            else {
                fwrite(STDERR, "UNKNOWN parameter\n");
                exit(10);
            }
        }
        return array($s_f, $s_p);
    }

    /**
     * Check if stats file exist. 
     */
    function stats_file_check(){
        //todo
        exit(0);
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
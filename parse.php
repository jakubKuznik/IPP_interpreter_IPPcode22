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


    $loc     = 0;   // Number of lines, that has instruction in code 
    $coments = 0;   // Number of lines that has a comment 
    $labels  = 0;   // Number of labels in code 
    $jumps   = 0;   // Number of retuns and jumps in code 
    $stats_files;   // All files for statistic 
    $stats_params;  // all params for all files

    // return type of parse_args is array($stats_filse, $stats_params)
    $h = parse_args($argc, $argv); 
    $stats_files  = $h[0];
    $stats_params = $h[1];
    if ($stats_files != NULL)
        file_open_check($stats_files);
    
    parse();

    
    /*      INSTRUCTION 
        MOVE      CREATEFRAME   PUSHFRAME   POPFRAME   DEFVAR   CALL 
        RETURN    PUSHS         POPS        ADD        SUB      MUL
        IDIV      LT            GT          EQ         AND      OR
        NOT       INT2CHAR      STRI2INT    READ       WRITE    CONCAT
        STRLEN    GETCHAR       SETCHAR     TYPE       LABEL    JUMP
        JUMPIFEQ  JUMPIFNEQ     EXIT        DPRINT     BREAK
    */
    /**
     * Parse input program in ipp_code22 to array
     * 
     */
    function parse(){
        

        while($line = fgets(STDIN)){
            // todo wait for stdin input
            // Remove whitespace 
            echo($line);
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            foreach ($splitted as $terminal){
                echo($terminal . "\n");
            }
        }
    }

    /**
     * control if each instruction is on new line;
     * 
     * return 1 if line has instruction 
     * return 0 if line has no instruction 
     * 
     */
    
    function syntax_line_con(){

    }

    /*

    while($line = fgets(STDIN)) {

        // oddelene mista mezerami 
        // split rozdeli podle mezer 
        // trim da pryc newline  
        $splitted = explode(' ', trim($line, "\n"));
        foreach ($splitted as $terminal){
            echo($terminal . "\n");
        }
    
    }

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

*/



    /**
     * Function parse arguments.
     * 
     * --help        // Display help cannot be combinated with anything 
     * --stats=file  // File where statisic will be writen.
     * --loc         // statistic - number of lines   
     * --coments     // statistic - number of lines that has a comment 
     * --labels      // statistic - number of labels in code     
     * --jumps       // statistic - number or returns and jumps instruction
     * --fwjumps     // statistic - forward jumps 
     * --backjumps   // statistic - back jumps 
     * --badjumps    // statistic - jumps on nonexistig label
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
     * -- unknown parameter           - error 10
     * -- multiple write to same file - error 12
     * 
     * todo Pokus o zápis více skupin statistik
     * do stejného souboru během jednoho spuštění skriptu vede na chybu 12. [1,5 b]
     * 
     * 
     * return array($stats_files, $stats_param)
     * 
     *  */    
    function parse_args(int $argc, array $argv){
        $stats          = false;   // Indicator if stats arg is there 
        $stats_arg_sum  = 0;       // How many stats args are there 
        $stats_sum      = 0;       // how manz stats files are there.
        $s_f = NULL;               // stats_file File where statisic will be writen 
        $s_p = NULL;               //  statas_param Parameters for eas statas 
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
                $s_f[$stats_sum] = trim(implode( " ", explode("--stats=", $ar))); 
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
            else if ($ar == '--fwjumps'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'fwj';
                $stats_arg_sum++;
            }
            else if ($ar == '--backjumps'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'bac';
                $stats_arg_sum++;
            }
            else if ($ar == '--badjumps'){
                if ($stats == false){
                    fwrite(STDERR, "--stats=fileName has to be defined before\n");
                    exit(10);
                }
                $s_p[$stats_sum-1][$stats_arg_sum] = 'bad';
                $stats_arg_sum++;
            }
            else {
                fwrite(STDERR, "UNKNOWN parameter\n");
                exit(10);
            }
        }

        if (array_duplicity($s_f) == TRUE){
            fwrite(STDERR, "Error One file for multiple statistic\n");
            exit(12);
        }
        return array($s_f, $s_p);
    }

    /**
     * return True if array has duplicit node
     * return False if not
     * 
     * for error 12  
     */
    function array_duplicity($arr){
        for ($i = 0; $i < sizeof($arr); $i++){
            for ($j = 0; $j < sizeof($arr); $j++){
                if ($j == $i)
                    continue;
                if ($arr[$i] == $arr[$j])
                    return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * Check if stats file exist. 
     * 
     * error -12 --ouptu files cannot be open 
     * //todo filepath can contain unicode in UTF-8 
     */
    function file_open_check($files){
        foreach($files as $f){
            if (!file_exists($f)){
                echo ($f);
                fwrite(STDERR, "File does not exist\n");
                exit(12);
            }
            $o_f = fopen($f, "w");
            if ($o_f == false){
                fwrite(STDERR, "Cannot open or Cannot write to output file. \n");
                exit(12);
            }
            fclose($o_f);
        }
        exit(0);
    }


    

?>
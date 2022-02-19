<?php

    ini_set('display_errors', 'stderr'); //Erross on stderr 
    
    /** ERRORS
     * 10 - BAD params. combination
     * 11 - input files errors
     * 12 - output files errors 
     * 21 - missing headear  
     */


    define("HELP", " \nProgram read IPP_code22 from stdin and represent it in xml
    --help        // Display help cannot be combinated with anything 
    --stats=file  // File where statisic will be writen.
    --loc         // statistic - number of lines   
    --coments     // statistic - number of lines that has a comment 
    --labels      // statistic - number of labels in code     
    --jumps       // statistic - number or returns and jumps instruction\n");


    $loc      = 0;   // Number of lines, that has instruction in code, Same as instruction order 
    $coments  = 0;   // Number of lines that has a comment 
    $labels   = 0;   // Number of labels in code 
    $jump     = 0;   // Number of retuns and jumps in code
    $jump_fw  = 0;   // Number of forward jumps 
    $jump_bac = 0;   // Back jumps
    $jump_bad = 0;   // Bad jumps 
    
    $stats_files;   // All files for statistic 
    $stats_params;  // all params for all files



    // return type of parse_args is array($stats_filse, $stats_params)
    $h = parse_args($argc, $argv); 
    $stats_files  = $h[0];
    $stats_params = $h[1];
    if ($stats_files != NULL)
        file_open_check($stats_files);
    
    if (check_header() == FALSE){
        fwrite(STDERR, "ERROR missing header .IPPcode22");
        exit(21);
    }
    parse();

    
    /*      INSTRUCTION 
        MOVE      CREATEFRAME   PUSHFRAME   POPFRAME   DEFVAR   CALL 
        RETURN    PUSHS         POPS        ADD        SUB      MUL
        IDIV      LT            GT          EQ         AND      OR
        NOT       INT2CHAR      STRI2INT    READ       WRITE    CONCAT
        STRLEN    GETCHAR       SETCHAR     TYPE       LABEL    JUMP
        JUMPIFEQ  JUMPIFNEQ     EXIT        DPRINT     BREAK

        #comentary
    */
    /**
     * Parse input program in ipp_code22 to array
     * 
     */
    function parse(){
        $line_num = 0 ;   
        while($line = fgets(STDIN)){
            
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            $splitted = array_values(array_filter($splitted)); //remove empty
            if(empty($splitted) == TRUE)
                continue;
            $splitted = remove_comm($splitted);
            print_r($splitted);
            syntax_validation($splitted);
        }
    }
    /**
     * Store given instruction to xml
     * 
     * calculate loc and use that value as instruction_order  
     * 
     */
    function store_to_xml($instruction){
        return 0;
    }

    /** 
     * 
     * Remove commentary from line 
     * 
     * calculate coments 
     * 
     * Return new array without comments 
     * 
    */
    function remove_comm($one_line){
        global $coments;
        $r_come = "/^\#/";  // comentary 
        $is_coment = FALSE;
        $new_array = [];
        //Match all the commands 
        foreach($one_line as $tok){
            if (preg_match($r_come, $tok)){
                $coments++;
                return $new_array;
            }
            array_push($new_array, $tok);
        }
        return $new_array;
    }

    /**
     * Get splited array of strings that represent one line of code from parse();
     * 
     * calculate:
     *  - lables
     *  - 
     * 
     * If succesfull send line to store_to_xml 
     * 
     * return TRUE 
     * Exit program if there is error  
     * 
     */
    function syntax_validation($one_line){
        // echo($splitted[0] . "\n");
        return TRUE;
    }

    /**
     * Check if there is a headear ". IPPcode22"
     * return TRUE  if yes 
     * return FALSE if not 
     */
    function check_header(){
        global $coments;
        $r_come = "/^\#/";  // comentary 
        $r_ipp  = "/\.IPPCODE22\b/";
        $r_ipp_com = "/\.IPPCODE22\b#/";
        
        while($line = fgets(STDIN)){
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            $splitted = array_values(array_filter($splitted)); //remove empty
            $splitted = array_change_key_case($splitted, CASE_UPPER);
            if (empty($splitted) == TRUE)
                continue; 
            foreach ($splitted as $key=>$sp){
                //if it is commentary
                if (preg_match($r_come, $sp)){
                    $coments++;
                    break; // whole line is commentary
                }
                else if (preg_match($r_ipp_com, $sp)){  //.IPPcode22#coment
                    $coments++;
                    return TRUE;
                }
                else if (preg_match($r_ipp, $sp)){ //.Ippcode22
                    if (array_key_exists($key+1, $splitted) == FALSE)
                        return TRUE;
                    if (preg_match($r_come, $splitted[$key+1]) == TRUE){ //comentary
                        $coments++;
                        return TRUE;
                    }
                    else
                        return FALSE;
                }
                else
                    return FALSE;
            }
        }
    }


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
                if ($argc == 2)
                    echo HELP;
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
        if($arr == NULL)
            return FALSE;
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
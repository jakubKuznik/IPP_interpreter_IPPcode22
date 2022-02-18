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

    /** REGEX */
    //Match all the commands 
    $r_comm = "/\bMOVE\b|\bCREATEFRAME\b|\bPUSHFRAME\b|\bPOPFRAME\b|\bDEFVAR\b|\bCALL\b|\bRETURN\b|\bPUSHS\b|\bPOPS\b|\bADD\b|\bSUB\b|\bMUL\b|\bIDIV\b|\bLT\b|\bGT\b|\bEQ\b|\bAND\b|\bOR\b|\bNOT\b|\bINT2CHAR\b|\bSTRI2INT\b|\bREAD\b|\bWRITE\b|\bCONCAT\b|\bSTRLEN\b|\bGETCHAR\b|\bSETCHAR\b|\bTYPE\b|\bLABEL\b|\bJUMP\b|\bJUMPIFEQ\b|\bJUMPIFNEQ\b|\bEXIT\b|\bEXIT\b|\bDPRINT\b|\bBREAK\b /";

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
    
    if (check_header() == FALSE){
        fwrite(STDERR, "ERROR missing header .IPPcode22");
        exit(21);
    }
    #parse();

    
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
            // Check for 
            // todo wait for stdin input
            // Remove whitespace
            //syntax_line_con($line); 
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            foreach ($splitted as $terminal){
                echo($terminal . "\n");
            }
        }
    }
    
    /**
     * Check if there is a headear ". IPPcode22"
     * return TRUE  if yes 
     * return FALSE if not 
     */
    function check_header(){
        $r_come = "/^\#/";  // comentary 
        $r_ipp  = "/\bIPPCODE22\b/";
        $r_ipp_com = "/\bIPPCODE22\b#/";
        
        while($line = fgets(STDIN)){
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            if(sizeof($splitted) == 1) //empty line 
                continue;
            for ($i = 0; $i < sizeof($splitted); $i++){
                //to upper
                $splitted[$i] = strtoupper($splitted[$i]);
                //if it is commentary
                if (preg_match($r_come, $splitted[$i])){
                    $coments++;
                    break; // whole line is commentary
                }
                // todo check . IPPcode22#comment 
                else if ($splitted[$i] == '.'){
                    if ($i +1 < sizeof($splitted)){
                        if (preg_match($r_ipp_com, $splitted[$i+1])){ //IPPcode22#comm
                            $coments++;
                            return TRUE;
                        }
                        else if(preg_match($r_ipp, $splitted[$i+1])) //IPPcode22{
                            // check for comments after 
                            for ($i = $i; $i < sizeof($splitted); $i++){
                                if (preg_match($r_come, $splitted[$i]))
                                    $coments++;
                                else
                                    return FALSE; //IPPcode add 
                            }
                            return TRUE;
                        } 
                        else
                            return FALSE;
                    }
                else
                    return FALSE;
                echo($splitted[$i] . "\n");
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
    function syntax_line_con($line){

        echo($line);
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
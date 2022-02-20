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
    
    // FOR STATISTIC 
    define("JUMP", 0);          
    define("DECLARATION",1);
    
    // INSTRUCTION FORMATS 
    define("VARI", 2);          define("LABEL", 3);
    define("SYMB", 4);          define("VARSYMB", 5);
    define("VARTYPE", 6);       define("VARSYMBSYMB", 7);
    define("LABELSYMBSYMB", 8); define("NONE", 9);
 
    $loc            = 0;       // Number of lines, that has instruction in code, Same as instruction order 
    $coments        = 0;       // Number of lines that has a comment 
    
    $jumps[0]       = 0;       // jump on lables     
    $labels[0]      = 0;       // labels declarations   
    $labels_line[0] = 0;
    $jumps_line[0]  = 0;
    $retunrs        = 0;

    $stats_files;              // All files for statistic 
    $stats_params;             // all params for all files

    // return type of parse_args is array($stats_filse, $stats_params)
    $h = parse_args($argc, $argv); // return array so i use help variable
    $stats_files  = $h[0];  $stats_params = $h[1];
    if ($stats_files != NULL)
        file_open_check($stats_files);
    
    if (check_header() == FALSE){
        fwrite(STDERR, "ERROR missing header .IPPcode22");
        exit(21);
    }

    // Xml setup 
    $h = xml_init(); // return array so i use help variable 
    $dom     = $h[0]; $program = $h[1];
    
    parse();
    print_r($dom->saveXML());
    
    # dont forget to sum up jumps with returns. Statistic 
    jump_stats_counter();
    exit(0);

    
    /**
     * Parse input program in ipp_code22a and store to xml 
     */
    function parse(){
        $line_num = 0 ;   
        while($line = fgets(STDIN)){
            // parse 
            $splitted = preg_split('/\s+/', trim($line, "\n"));
            $splitted = array_values(array_filter($splitted)); //remove empty
            if(empty($splitted) == TRUE)
                continue;
            // remove commentary  
            $splitted = remove_comm($splitted); 
            // syntax chceck 
            $type = syntax_validation($splitted); 
            if (empty($splitted))
                continue;
            // store to xml 
            store_to_xml($splitted, $type);
        }
    }


    /* 
        <?xml version="1.0" encoding="UTF-8"?>

        <program language="IPPcode22">
        <instruction order="1" opcode="CREATEFRAME">
        </instruction>
        <instruction order="2" opcode="MOVE">
                <arg1 type="var">TF@a</arg1>
                <arg2 type="nil">nil</arg2>
        </instruction>
        </program>
        February 17, 2022    */
    /*
     * Store given instruction to xml
     *
     * $instruction - one program line 
     * $order       - instruction order 
     *   
     * 
     * $typ0e =   VARI || SYMB || LABEL || VARTYPE || VARSYMB || VARSYMBSYMB
     *         || LABELSYMBSYMB || NONE  
     * 
     */
    function store_to_xml($one_line, $type){
        global $loc, $dom, $program;
        $instr;

        switch($type){
            case NONE:
                $instr = $dom->createElement('instruction');
                print_r($one_line);
                break;
            case VARI:
                return;    
                break;
            case SYMB:
                return;    
                break;
            case LABEL:
                return;    
                break;
            case VARTYPE:
                return;    
                break;
            case VARSYMB:
                return;    
                break;
            case VARSYMBSYMB:
                return;    
                break;
            case LABELSYMBSYMB:
                return;    
                break;
            default:
                return;    
                break;
        }
        $program->appendChild($instr);


        return 0;
    }

    /**
     *  initialize xml.
     */
    function xml_init(){
        global $program;
        $d = new DOMDocument('1.0', 'utf-8');
        $d->preserveWhiteSpace = false;
        $d->formatOutput = true;
        $program = $d->createElement('program');
        $program->setAttribute("language", "IPPcode22");
        $d->appendChild($program);
        return [$d,$program];
    }

    /**
     * Get splited array of strings that represent one line of code from parse();
     * 
     * ERORRS: 
     *  22 - unknown or bad opcode of instruction 
     *  23 - other lexical or syntax error 
     * 
     * calculate:
     *  - loc
     * 
     * return TRUE 
     * Exit program if there is error  
     * 
     * <var>   -- variable
     * <symb>  -- constant or variable 
     * <label> -- label
     * <type>  -- {int, string, bool}
     * 
     */
    function syntax_validation($one_line){
        // todo multiple @
        global $loc, $retunrs; 
        if (empty($one_line) == TRUE) return; //if empty line skip.

        $loc++; //One line of instruction 
    
        // echo($splitted[0] . "\n");
        switch(strtoupper($one_line[0])){
            /******* <var> <symb> **************/
            case "MOVE":      // <var> <symb> 
                expe_size($one_line, 3); expe_var($one_line[1]); expe_sym($one_line[2]);
                return(VARSYMB);
            case "INT2CHAR":  // <var> <symb>
                expe_size($one_line, 3); expe_var($one_line[1]); expe_sym($one_line[2]);
                return(VARSYMB);
            case "STRLEN":    // <var> <symb>
                expe_size($one_line, 3); expe_var($one_line[1]); expe_sym($one_line[2]);
                return(VARSYMB);
            case "TYPE":      // <var> <symb>
                expe_size($one_line, 3); expe_var($one_line[1]); expe_sym($one_line[2]);
                return(VARSYMB);
            /******* <var> *********************/
            case "DEFVAR":    // <var>
                expe_size($one_line, 2); expe_var($one_line[1]);
                return(VARI);
            case "POPS":      // <var>
                expe_size($one_line, 2); expe_var($one_line[1]);
                return(VARI);
            /******* <label> *********************/
            case "CALL":      // <label>
                expe_size($one_line, 2); expe_lable($one_line[1], JUMP);
                return(LABEL);
            case "LABEL":     // <label>
                expe_size($one_line, 2); expe_lable($one_line[1], DECLARATION);
                return(LABEL);
            case "JUMP":      // <label>
                expe_size($one_line, 2); expe_lable($one_line[1], JUMP);
                return(LABEL);
            /******* <symb> ********************/
            case "PUSHS":     // <symb>
                expe_size($one_line, 2); expe_sym($one_line[1]);
                return(SYMB);
            case "WRITE":     // <symb>
                expe_size($one_line, 2); expe_sym($one_line[1]);
                return(SYMB);
            case "EXIT":      // <symb>
                expe_size($one_line, 2); expe_sym($one_line[1]);
                return(SYMB);
            case "DPRINT":    // <symb>
                expe_size($one_line, 2);
                return(SYMB);
            /******* <var> <symb1> <symb2> ****/
            case "ADD":       // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "SUB":       // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "MUL":       // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "IDIV":      // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "LT":        // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "GT":        // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "EQ":        // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "\AND":      // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "OR":        // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "NOT":       // <var> <symb1> <sybm2> 
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "STRI2INT":  // <var> <symb1> <symb2>
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "CONCAT":    // <var> <symb1> <symb2>
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "GETCHAR":   // <var> <symb1> <symb2>
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            case "SETCHAR":   // <var> <symb1> <symb2>
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            /****** <label> <symb1> <symb2> ***/
            case "JUMPIFEQ":  // <label> <symb1> <symb2>
                expe_size($one_line, 4); expe_lable($one_line[1], JUMP); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(LABELSYMBSYMB);
            case "JUMPIFNEQ": // <label> <symb1> <symb2>
                expe_size($one_line, 4); expe_lable($one_line[1], JUMP); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(LABELSYMBSYMB);
            /******* <var> <type> *************/
            case "READ":      // <var> <type>
                expe_size($one_line, 3); expe_var($one_line[1]); expe_typ($one_line[2]);
                return(VARTYPE);
            /******* NONE *********************/
            case "CREATEFRAME":
                expe_size($one_line, 1); 
                return(NONE);
            case "PUSHFRAME": 
                expe_size($one_line, 1); 
                return(NONE);
            case "POPFRAME": 
                expe_size($one_line, 1); 
                return(NONE);
            case "RETURN":         // Variable  
                expe_size($one_line, 1);
                $retunrs++;
                return(NONE);
            case "BREAK":
                expe_size($one_line, 1); 
                return(NONE);
            /******************************** */
            default:
                fwrite(STDERR, "Unknown or bad instruction.\n");
                exit(22);      
        }
    }

    /**
     * Expect that array is $size big
     */
    function expe_size($one_line, $size){
        if(sizeof($one_line) != $size){
            fwrite(STDERR, "Instruction doesn`t have operator.\n");
            exit(22);
        }
        return;
    }
 
    /**
     * Expect variable in $token
     */
    function expe_var($token){
        $frame_definiton = "/@/";  // comentary 
        // GF@label format 
        if (preg_match($frame_definiton, $token)){
            $parts = explode('@', $token);
            if (($parts[0] != "GF") and ($parts[0] != "LF") and ($parts[0] != "TF")){
                fwrite(STDERR, "Unknown variable frame.\n");
                exit(22);
            }
        }
        else{
            fwrite(STDERR, "Missing variable frame.\n");
            exit(22);
        }
        return;
    }
    
    /**
     * Expect label in $token  
     * $type - JUMP || DECLARATION 
     */
    function expe_lable($token, $type){
        global $labels, $jumps, $loc, $jumps_line, $labels_line;
        $frame_definiton = "/@/";  // comentary 

        // Variable  
        if (preg_match($frame_definiton, $token)){
            fwrite(STDERR, "Expect label not variable\n");
            exit(22);
        }
            
        // store for statistic 
        if ($type == JUMP){
            array_push($jumps, $token);
            array_push($jumps_line, $loc);
        }
        elseif($type == DECLARATION){
            array_push($labels, $token);
            array_push($labels_line, $loc);
        }

        return;
    }

    /**
     * Expect constant or variable 
     * Zápis každé konstanty v IPPcode22 se skládá ze dvou částí oddělených zavináčem (znak @; bez
     * bílých znaků), označení typu konstanty (int, bool, string, nil) a samotné konstanty (číslo, literál, nil).
     * Např. bool@true, nil@nil nebo int@-5.
     */
    function expe_sym($token){
        $frame_definiton = "/@/";  // comentary 
        // Variable  
        if (preg_match($frame_definiton, $token) == FALSE){
            fwrite(STDERR, "Expect constant or variable\n");
            exit(22);
        }

        //todo WRITE string@Proměnná \032 GF@counter \032 obsahuje \032

        $parts = explode('@', $token);
        if (($parts[0] == "GF") or ($parts[0] == "LF") or ($parts[0] == "TF")){
            return; //variable 
        }
        // todo kompability of data types 
        else if (($parts[0] == "int") or ($parts[0] == "bool") or ($parts[0] == "string") or ($parts[0] == "nil")){
            return; // constant 
        }

        exit (22);
    }
    
    
    /**
     * type E {int, string, bool}
     */
    function expe_typ($token){
        // todo kompability of data types 
        if (($parts[0] == "int") or ($parts[0] == "bool") or ($parts[0] == "string")){
            return; // constant 
        }
        fwrite(STDERR, "Expect type\n");
        exit(22);
    }


   
    
    /** 
     * 
     * Remove commentary from line 
     * 
     * calculate coments for statistic 
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
     * Count jumps in program based on $jumps $labels $jumps_line $labels_line 
     * 
     * return $jumps, $fwjumps, $backjumps $badjumps
     * 
     */
    function jump_stats_counter(){
        global $jumps, $labels, $jumps_line, $labels_line;
        
        $s_j = 0; $s_jf = 0; $s_jbac = 0; $s_jbad = 0;
     
        foreach($jumps as $key=>$j){
            $s_j++;
            if (in_array($j, $labels) == FALSE){
                $s_jbad; // label doesnt exits
                continue;
            }
            $i = array_search($j, $labels);
            if ($labels_line[$i] > $jumps_line[$key]){
                $s_jf++;
            }
            elseif($labels_line[$i] < $jumps_line[$key]){
                $s_jbac;
            }
        }
        
        /*
        print_r($jumps);
        print_r($jumps_line);
        print_r($labels_line);
        print_r($labels);
        */

        return [$s_j, $s_jf, $s_jbac, $s_jbad];
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
                    fwrite(STDERR, "Cannot combine --help with other parameters.\n");
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
     * todo filepath can contain unicode in UTF-8 
     * 
     * todo -  Pozor na rozdíl mezi relativní cestou vůči zpracovávanému PHP skriptu
     *  (rel/to/script/path) a relativní cestou vůči aktuálnímu adresáři
     *  (./rel/to/actual/path). V případě, že bude skript spuštěn z jiného adresáře,
     *  tak se liší a potom bude rozdílně fungovat např. funkce require_once(). Nepředpokládejte,
     *  že bude váš skript při spuštění obsažen v aktuálním adresáři!
     * 
     */
    function file_open_check($files){
        $relative = substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        
        foreach($files as $f){ 
            if (substr($f, 0, 1 ) != '/')
                $f = $relative . '/' . $f;
        
            if (!file_exists($f)){
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
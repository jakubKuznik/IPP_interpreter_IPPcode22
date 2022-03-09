<?php

    ini_set('display_errors', 'stderr'); //Erross on stderr 
    
    /** ERRORS
     * 10 - BAD params. combination
     * 11 - input files errors
     * 12 - output files errors 
     * 21 - missing headear 
     * 22 - BAD OPCODE
     * 23 - other syntax or lexical error 
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
    $returns        = 0;

    $stats_files;              // All files for statistic 
    $stats_params;             // all params for all files

    // return type of parse_args is array($stats_filse, $stats_params)
    $h = parse_args($argc, $argv); // return array so i use help variable
    $stats_files  = $h[0];  $stats_params = $h[1];
    if ($stats_files != NULL)
        file_open_check($stats_files);
    
    if (check_header() == FALSE){
        fwrite(STDERR, "ERROR missing header .IPPcode22\n");
        exit(21);
    }

    // Xml setup 
    $h = xml_init(); // return array so i use help variable 
    $dom     = $h[0]; $program = $h[1];
    
    # Parse input program 
    parse();

    ## program to output 
    print_r($dom->saveXML());
    ## STATP - statistic to output files 
    Write_stats($stats_files, $stats_params, $labels);

    exit(0);
    
    /**
     * Parse input program in IPP_code22 and store to xml
     * 
     * @return void  
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

    /**
     *  replace & < > with &amp &gt &lt
     * 
     * @var vars is array of strings  
     * @return modified array
     */
    function xml_replace_special($vars){
        foreach ($vars as $key=>$v){
            $v = str_replace("&", "&amp;", $v);         
            $v = str_replace(">", "&gt;", $v);         
            $v = str_replace("<", "&lt;", $v);         
            $vars[$key] = $v; 
        }
        return $vars;
    }

    /*
     * Store given instruction to xml
     *
     * $instruction - one program line 
     * $order       - instruction order 
     * 
     * @var one_line = one line from input file  
     * @var type =   VARI || SYMB || LABEL || VARTYPE || VARSYMB || VARSYMBSYMB
     *         || LABELSYMBSYMB || NONE  
     * @return void  
     */
    function store_to_xml($one_line, $type){
        global $loc, $dom, $program;
        //$instr, $arg1, $arg2, $arg3;

        $instr = $dom->createElement('instruction');
        $instr->setAttribute("order", $loc);
        $instr->setAttribute("opcode", strtoupper($one_line[0]));

        // replace & < > with xml escapes 
        $one_line = xml_replace_special($one_line);
            
        switch($type){
            case NONE:
                break;
            case VARI:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "var");
                $instr->appendChild($arg1);
                break;
            case SYMB:
                $arg1 = $dom->createElement("arg1", get_sym_name($one_line[1]));
                $arg1->setAttribute("type", get_literal_type($one_line[1]));
                $instr->appendChild($arg1);
                break;
            case LABEL:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "label");
                $instr->appendChild($arg1);
                break;
            case VARTYPE:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "var");
                $instr->appendChild($arg1);

                $arg2 = $dom->createElement("arg2", $one_line[2]);
                $arg2->setAttribute("type", "type");
                $instr->appendChild($arg2);
                break;
            case VARSYMB:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "var");
                $instr->appendChild($arg1);

                $arg2 = $dom->createElement("arg2", get_sym_name($one_line[2]));
                $arg2->setAttribute("type", get_literal_type($one_line[2]));
                $instr->appendChild($arg2);
                break;
            case VARSYMBSYMB:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "var");
                $instr->appendChild($arg1);

                $arg2 = $dom->createElement("arg2", get_sym_name($one_line[2]));
                $arg2->setAttribute("type", get_literal_type($one_line[2]));
                $instr->appendChild($arg2);
                
                $arg3 = $dom->createElement("arg3", get_sym_name($one_line[3]));
                $arg3->setAttribute("type", get_literal_type($one_line[3]));
                $instr->appendChild($arg3);
                break;
            case LABELSYMBSYMB:
                $arg1 = $dom->createElement("arg1", $one_line[1]);
                $arg1->setAttribute("type", "label");
                $instr->appendChild($arg1);
                
                $arg2 = $dom->createElement("arg2", get_sym_name($one_line[2]));
                $arg2->setAttribute("type", get_literal_type($one_line[2]));
                $instr->appendChild($arg2);
                
                $arg3 = $dom->createElement("arg3", get_sym_name($one_line[3]));
                $arg3->setAttribute("type", get_literal_type($one_line[3]));
                $instr->appendChild($arg3);
                break;
            
            default:
                break;
        }
        $program->appendChild($instr);
        return 0;
    }

    /**
     *  initialize xml.
     *  
     *  @return XML created in dom 
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
     * @return literal name.
     * @var token - one program token 
     */
    function get_sym_name($token){
        $parts = explode('@', $token);
        $res = "";

        if (($parts[0] == "GF") or ($parts[0] == "LF") or ($parts[0] == "TF")){
            return $token;
        }
        foreach ($parts as $key=>$p)
            if($key > 0)
                $res = $res . $p;
        return $res;
    }

    /**
     * returns:
     *  literal data type or var. 
     *  
     * Use on alredy checked symbols with exp func!
     * 
     * exit(23) - if int is not an num 
     *          - nil is not nil 
     *          - bool is not true or false  
     * 
     * @return "var" || "int" || "bool" || "string" || "nil"
     * @var token - one token 
     */
    function get_literal_type($token){
        $r_hexa = "/[G-Z]|[g-z]/";  
        $r_dec  = "/[A-Z]|[a-z]/";
        $r_oct  = "/[8-9]|[A-Z]|[a-z]/";

        $parts = explode('@', $token);
        /****** VARIABLE  *****/
        if (($parts[0] == "GF") or ($parts[0] == "LF") or ($parts[0] == "TF")){
            return "var"; //variable 
        } /**** INT *****/ 
        else if ($parts[0] == "int"){ // could be hexa deca or octal.
            ///0
            if ((!preg_match($r_oct, $parts[1])) and (intval($parts[1], 8)))    // octal 
                return "int";
            else if (!preg_match($r_dec, $parts[1]) and intval($parts[1], 10))  // decimal 
                return "int";
            else if (!preg_match($r_hexa, $parts[1]) and intval($parts[1], 16)) // hex
                return "int";
            else if ($parts[1] == 0){
                return "int";
            }
            fwrite(STDERR, "Not an int literal\n");
            exit(23);
        } /***** BOOL  ****/
        else if ($parts[0] == "bool"){ // false or true 
            if ($parts[1] == "true" or $parts[1] == "false")
                return "bool";
            fwrite(STDERR, "not a bool literal\n");
            exit(23);
        }
        else if ($parts[0] == "string"){
            return "string";
        }
        else if ($parts[0] == "nil"){
            if ($parts[1] == "nil")
                return "nil";
            fwrite(STDERR, "not a bool literal\n");
            exit(23);
        }
    }

    /**
     * Get splited array of strings that represent one line of code from parse();
     * 
     * ERORRS: 
     *  Exit program if there is error  
     *  22 - unknown or bad opcode of instruction 
     *  23 - other lexical or syntax error 
     * 
     * calculate:
     *  - loc
     * 
     * @return VARSYMB || VARI || LABEL || SYMB || LABELSYMBSYMB || VARSYMBSYMB 
     *          VARTYPE || NONE 
     * @var one_line - one input program line 
     *  
     * <var>   -- variable
     * <symb>  -- constant or variable 
     * <label> -- label
     * <type>  -- {int, string, bool}
     */
    function syntax_validation($one_line){
        global $loc, $returns; 
        if (empty($one_line) == TRUE) return; //if empty line skip.

        $loc++; // One line of instruction 
    
        switch(strtoupper($one_line[0])){
            /******* <var> <symb> **************/
            case "MOVE":      // <var> <symb> 
            case "INT2CHAR":  // <var> <symb>
            case "STRLEN":    // <var> <symb>
            case "TYPE":      // <var> <symb>
            case "NOT":       // <var> <symb1>   
                expe_size($one_line, 3); expe_var($one_line[1]); expe_sym($one_line[2]);
                return(VARSYMB);
            /******* <var> *********************/
            case "DEFVAR":    // <var>
            case "POPS":      // <var>
                expe_size($one_line, 2); expe_var($one_line[1]);
                return(VARI);
            /******* <label> *********************/
            case "CALL":      // <label>
            case "JUMP":      // <label>
                expe_size($one_line, 2); expe_lable($one_line[1], JUMP);
                return(LABEL);
            case "LABEL":     // <label> // separated because of stats calculation
                expe_size($one_line, 2); expe_lable($one_line[1], DECLARATION);
                return(LABEL);
            /******* <symb> ********************/
            case "PUSHS":     // <symb>
            case "WRITE":     // <symb>
            case "EXIT":      // <symb>
            case "DPRINT":    // <symb>
                expe_size($one_line, 2); expe_sym($one_line[1]);
                return(SYMB);
            /******* <var> <symb1> <symb2> ****/
            case "ADD":       // <var> <symb1> <sybm2>
            case "SUB":       // <var> <symb1> <sybm2> 
            case "MUL":       // <var> <symb1> <sybm2> 
            case "IDIV":      // <var> <symb1> <sybm2> 
            case "LT":        // <var> <symb1> <sybm2> 
            case "GT":        // <var> <symb1> <sybm2> 
            case "EQ":        // <var> <symb1> <sybm2> 
            case "AND":      // <var> <symb1> <sybm2> 
            case "OR":        // <var> <symb1> <sybm2> 
            case "STRI2INT":  // <var> <symb1> <symb2>
            case "CONCAT":   
            case "GETCHAR":   // <var> <symb1> <symb2>
            case "SETCHAR":   // <var> <symb1> <symb2>
                expe_size($one_line, 4); expe_var($one_line[1]); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(VARSYMBSYMB);
            /****** <label> <symb1> <symb2> ***/
            case "JUMPIFEQ":  // <label> <symb1> <symb2>
            case "JUMPIFNEQ": // <label> <symb1> <symb2>
                expe_size($one_line, 4); expe_lable($one_line[1], JUMP); expe_sym($one_line[2]); expe_sym($one_line[3]);
                return(LABELSYMBSYMB);
            /******* <var> <type> *************/
            case "READ":      // <var> <type>
                expe_size($one_line, 3); expe_var($one_line[1]); expe_typ($one_line[2]);
                return(VARTYPE);
            /******* NONE *********************/
            case "CREATEFRAME":
            case "BREAK":
            case "PUSHFRAME": 
            case "POPFRAME": 
                expe_size($one_line, 1); 
                return(NONE);
            case "RETURN":         // Variable  
                expe_size($one_line, 1);
                $returns++;
                echo "tu";
                return(NONE);
            /******************************** */
            default:
                fwrite(STDERR, "Unknown or bad instruction.\n");
                exit(22);      
        }
    }

    /**
     * Expect that array is $size big
     * @var one_line - one input program line 
     * @var size - size we expect
     * @return void 
     */
    function expe_size($one_line, $size){
        if(sizeof($one_line) != $size){
            fwrite(STDERR, "Instruction doesn't have operator.\n");
            exit(23);
        }
        return;
    }
 
    /**
     * Expect variable in $token
     * @var token - one token 
     * @return void 
     */
    function expe_var($token){
        $frame_definiton = "/@/";  // comentary 
        $beg_num = "/^[0-9]/";
        $ilegal_char = "/#|\s/";
        $ilegal_char2 = "/\\\|\//";

        // GF@label format 
        if (preg_match($frame_definiton, $token)){
            $parts = explode('@', $token);
            if (($parts[0] != "GF") and ($parts[0] != "LF") and ($parts[0] != "TF")){
                fwrite(STDERR, "Unknown variable frame.\n");
                exit(23);
            }
            if (sizeof($parts) > 2){ // no multiple @
                exit(23);
            }
            if (preg_match($beg_num, $parts[1])){ //cannot start with number 
                fwrite(STDERR, "Var name cannot start with number\n");
                exit(23);
            }
            if (preg_match($ilegal_char, $parts[1]) or preg_match($ilegal_char2, $parts[1])){
                fwrite(STDERR, "bad label \n");
                exit(23);
            }
        }
        else{
            fwrite(STDERR, "Missing variable frame.\n");
            exit(23);
        }
        return;
    }
   
    /**
     * Expect label in $token  
     * @var type - JUMP || DECLARATION 
     * @var token - one token 
     * @return void 
     */
    function expe_lable($token, $type){
        global $labels, $jumps, $loc, $jumps_line, $labels_line;
        $frame_definiton = "/@/";  // comentary 
        $ilegal_char = "/#|\s/";
        $beg_num = "/^[0-9]/";
        $ilegal_char2 = "/\\\|\//";

        // Variable  
        if (preg_match($frame_definiton, $token)){
            fwrite(STDERR, "Expect label not variable\n");
            exit(23);
        }

        if (preg_match($ilegal_char, $token) || preg_match($ilegal_char2, $token)){
            fwrite(STDERR, "bad label \n");
            exit(23);
        }
        if (preg_match($beg_num, $token)){ //cannot start with number 
            fwrite(STDERR, "lab name cannot start with number\n");
            exit(23);
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
     * @var token - one token
     * @return void 
     * 
     */
    function expe_sym($token){
        $frame_definiton = "/@/";  // comentary 
        $ilegal_char = "/#|\s/";
        $ilegal_char2 = "/\\\|\//";
        $beg_num = "/^[0-9]/";
        $nv_escape = "/\\\D..|\\\.\D.|\\\..\D/";
        $escape = "/\\\(0[0-3][0-2]|092|035)/";
        // Variable  
        if (preg_match($frame_definiton, $token) == FALSE){
            fwrite(STDERR, "Expect constant or variable\n");
            exit(23);
        }

        $parts = explode('@', $token);
        if (($parts[0] == "GF") or ($parts[0] == "LF") or ($parts[0] == "TF")){
            if(sizeof($parts) > 2){
                fwrite(STDERR, "bad variable symbol \n");
                exit(23);
            }
            if (preg_match($beg_num, $parts[1])){ //cannot start with number 
                fwrite(STDERR, "Var name cannot start with number\n");
                exit(23);
            }
            if (preg_match($ilegal_char, $parts[1]) or preg_match($ilegal_char2, $parts[1])){
                fwrite(STDERR, "bad variable symbol \n");
                exit(23);
            }
            return; //variable 
        }
        // todo kompability of data types 
        else if (($parts[0] == "int") or ($parts[0] == "bool") or ($parts[0] == "nil")){
            return; // constant 
        } 
        # string cannot have # \ or white space 
        else if ($parts[0] == "string"){
            $lit = "";
            foreach ($parts as $key => $s)
                if ($key > 0)
                    $lit = $lit.$s;
            if (preg_match($ilegal_char, $lit)){
                fwrite(STDERR, "Expect constant or variable\n");
                exit(23);
            }
            if (preg_match($ilegal_char2, $lit)){
                if(preg_match($nv_escape, $lit)){
                    fwrite(STDERR, "Expect constant or variable\n");
                    exit(23);
                }
                if (preg_match($escape, $lit)){
                    return;
                }    
                fwrite(STDERR, "Expect constant or variable\n");
                exit(23);
            }
            return;
        }

        fwrite(STDERR, "Expect constant or variable\n");
        exit (23);
    }
    
    /**
     * type E {int, string, bool}
     * @var token - one token 
     * @return void  
     */
    function expe_typ($token){
        if (($token == "int") or ($token == "bool") or ($token == "string")){
            return; // constant 
        }
        fwrite(STDERR, "Expect type\n");
        exit(23);
    }

    /** 
     * Remove commentary from line 
     * 
     * calculate coments for statistic 
     * @var one_line - one program one 
     * @return new array without comments 
    */
    function remove_comm($one_line){
        global $coments;
        $r_come = "/\#/";  // comentary 
        $is_coment = FALSE;
        $new_array = [];
        //Match all the commands 
        foreach($one_line as $tok){
    
            if (preg_match($r_come, $tok)){
                $before_coment = preg_split('/#/', $tok);
                $coments++;
                array_push($new_array, $before_coment[0]);
                return array_filter($new_array);
            }
            array_push($new_array, $tok);
        }
        return $new_array;
    }

    /**
     * Check if there is a headear ". IPPcode22"
     * @return TRUE  if yes
     * @return FALSE if not 
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
            $splitted = array_filter($splitted);
    
            if (empty($splitted) == TRUE)
                continue; 
            foreach ($splitted as $key=>$sp){
                $sp = strtoupper($sp);
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
     * @var argv
     * @var argc
     * @return array($stats_files, $stats_param)
     * 
     *  */    
    function parse_args($argc, array $argv){
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
            else if ($ar == '--labels'){
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
                fwrite(STDERR, "UNKNOWN parameter: " . $ar . "\n");
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
     * @var arr - array where we are looking for duplicit
     * @return True if array has duplicit node
     * @return False if not
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
     * 
     * todo -  Pozor na rozdíl mezi relativní cestou vůči zpracovávanému PHP skriptu
     *  (rel/to/script/path) a relativní cestou vůči aktuálnímu adresáři
     *  (./rel/to/actual/path). V případě, že bude skript spuštěn z jiného adresáře,
     *  tak se liší a potom bude rozdílně fungovat např. funkce require_once(). Nepředpokládejte,
     *  že bude váš skript při spuštění obsažen v aktuálním adresáři!
     * @var files - array of files name 
     * @return void  
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
            if ($f == false){
                fwrite(STDERR, "Cannot open or Cannot write to output file. \n");
                exit(12);
            }
            //fclose($f);
        }
    }
    
    /**
     * Count jumps in program based on $jumps $labels $jumps_line $labels_line 
     * 
     * @return $jumps, $fwjumps, $backjumps $badjumps
     */
    function jump_stats_counter(){
        global $jumps, $labels, $jumps_line, $labels_line;
        global $returns;
        $s_j = 0; $s_jf = 0; $s_jbac = 0; $s_jbad = 0;
    
        $i = 0;
        $s_j = $returns;
        array_shift($jumps);
        foreach($jumps as $key=>$j){
            $s_j++;
            $i = array_search($j, $labels);
            if (in_array($j, $labels) == FALSE){
                $s_jbad++; // label doesnt exits
                continue;
            }
            if ($labels_line[$i] >= $jumps_line[$key]){
                $s_jf++;
            }
            elseif($labels_line[$i] < $jumps_line[$key]){
                $s_jbac++;
            }

        }
       
        return [$s_j, $s_jf, $s_jbac, $s_jbad];
    }

    /**
     * @return number of unicate string in array 
     * @var labels array of string
     */
    function label_count($labels){

        $labels = array_unique($labels);
        array_shift($labels);
        return count($labels);
    }

    /**
     * Write statistic to output files. 
     *
     * @var $files  - where statistic will be writen
     * @var $params - two dimensional array. (What statistic will be writen to each file.)
     * @return void 
     */
    function Write_stats($files, $params, $labels){
        # dont forget to sum up jumps with returns. Statistic 
        # -loc, com, lab, jump, fwj, bckj, badj,
        global $loc;
        global $coments;

        $j;         // jump
        $jf;        // jforw
        $jbac;      // j back
        $jbad;      // j bad 
        $lab_uniq;  // number of unicate labels 

        $h = jump_stats_counter();
        $j = $h[0]; $jf = $h[1]; $jbac = $h[2]; $jbad = $h[3];
        $lab_uniq = label_count($labels);
       
        if ($files == NULL)
            return;
       
        foreach ($files as $key=>$f){
            $openf = fopen($f, "w");
            foreach ($params[$key] as $p){
                switch($p){
                    case "loc": 
                        fwrite($openf, $loc);      fwrite($openf, "\n"); break;
                    case "com": 
                        fwrite($openf, $coments);  fwrite($openf, "\n"); break;
                    case "lab": 
                        fwrite($openf, $lab_uniq); fwrite($openf, "\n"); break;
                    case "jum": 
                        fwrite($openf, $j);        fwrite($openf, "\n"); break;
                    case "fwj": 
                        fwrite($openf, $jf);       fwrite($openf, "\n"); break;
                    case "bac": 
                        fwrite($openf, $jbac);     fwrite($openf, "\n"); break;
                    case "bad": 
                        fwrite($openf, $jbad);     fwrite($openf, "\n"); break;
                    default:
                        break;
                }
            }
            fclose($openf);
        }
    }
?>
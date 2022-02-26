<?php
    ini_set('display_errors', 'stderr'); //Erross on stderr 

    /************** MACROS **************************/
    define("HELP_MESS", " Test script for parser.php and interpret.py
        --help               // Display help cannot be combinated with anything 
        -parse-only         // olnly parser.php test. can't combine with --int-only, --int-script 
        --int-only           // only interpret.py
        --recursive          // Not only given folder but recursive folder to 
        --noclean            // doesnt remove temp. files
        --directory=path     // Folder where we are looking for tests 
        --parse-script=file  // parse.php      - if not given actual folder  
        --int-script=file    // interpret.py   - if not given actual folder 
        --jexampath=path      // path to jexamxml.jar (A7Soft)
        --match=regexp       // Only files that match regex of PCRE sytax 
        --testlist=file      // explicit folders definition or files\n"); 

    define ("ERR_HELP", "Cannot combine with other arguments\n");
    define ("ERR_COM", "Cannot combine --parse-only and --int-only\n");
    define ("ERR_NON_EXI_FILE", " File does not exists.\n");

    /**************** GLOBAL VARS ******************/
    $tests_files;                                   // array of files that we will test 
    $parser_on   = true;                           // parser tests 
    $inter_on    = true;                           // interpreter 
    $parse_path  = "parse.php";                     // path to parser script 
    $int_path    = "interpret.py";                  // Path to interpreter script 
    $jexam_path  = "/pub/courses/ipp/jexamxml/jexamxml.jar";
    
    /***************** PROGRAM  *******************/
    parse_args($argc, $argv);
    
    // check if $parse_path, $int_path, $jexam_path exist.
    file_exist($parser_on, $inter_on);




    /**
     * Check if $parse_path, $int_path $jexam_path exist 
     * exit(41) if not 
     *
     * @return void 
     * @global $int_path, $parse_path, $jexam_path
     * @var $par -- if true check $parse_path and $jexam_path
     * @var @inte -- if true check $int_path 
     */
    function file_exist($par, $inte){
        global $parse_path, $int_path, $jexam_path;

        if ($par == true){
            if (file_exists($parse_path) == false){
                fwrite(STDERR, $parse_path . ERR_NON_EXI_FILE); exit(41);
            }
            if (file_exists($jexam_path) == false){
                fwrite(STDERR, $jexam_path . ERR_NON_EXI_FILE); exit(41);
            }
        }
        if ($inte == true){
            if (file_exists($int_path) == false){
                fwrite(STDERR, $int_path . ERR_NON_EXI_FILE); exit(41);
            }
        }
    }

    function testing($tests_files){
        // if file rc != 0
        // if file rc == 0
        return 0;
    }

    // get output from .out file 
    function test_file_output($test_case){
        return 0;
    }
    
    // get output from 
    function get_output(){
        return 0;
    }

    function test_file_rc(){
        return 0;
    }

    /**
     * Function parse arguments.
     * 
     * --help               // Display help cannot be combinated with anything 
     * --parse-only         // olnly parser.php test. can't combine with --int-only, --int-script 
     * --int-only           // only interpret.py
     * --recursive          // Not only given folder but recursive folder to 
     * --noclean            // doesnt remove temp. files
     * --directory=path     // Folder where we are looking for tests 
     * --parse-script=file  // parse.php      - if not given actual folder  
     * --int-script=file    // interpret.py   - if not given actual folder 
     * --jexampath=path      // path to jexamxml.jar (A7Soft)
     * --match=regexp       // Only files that match regex of PCRE sytax 
     * --testlist=file      // explicit folders definition or files 
     *                      // -can't combine with --directory
     * 
     * //globals that are modified in function 
     * @global $parser_on, $inter_on, $parse_path, $int_path, $jexam_path;
     * 
     * 
     * @var argc
     * @var argv
     * @return void 
     * 
     *  */    
    function parse_args(int $argc, array $argv){ 
        global $parser_on, $inter_on, $parse_path, $int_path; $jexam_path;
        
        $parse_only = false; 
        $int_only   = false;
        $directory  = false;
        $testlist   = false;
        
        $help_file_name;
        // two switch one for arguments with '='
        foreach ($argv as $arg){
            
            /********** FOR ARGUMENTS WITH '=' SYMBOL ***********/
            if (str_contains($arg, "=")){
                $arg = explode("=", $arg);
                $help_file_name = concat_str($arg, 1); // store file name
                
                switch ($arg[0]){
                    case "--directory";
                        if ($testlist == true){
                            fwrite(STDERR, ERR_HELP); exit(10);
                        }
                        $directory = true;
                        break;
                    case "--parse-script":
                        if ($int_only == true){ //cannot combine with --int-only
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $parse_path = $help_file_name;
                        break;
                    case "--int-script":
                        if ($parse_only == true){
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $int_path = $help_file_name;
                        break;
                    case "--jexampath":      
                        if ($int_only == true){ //cannot combine with --int-only
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $jexam_path = $help_file_name; 
                        break;
                    case "--match":
                        break;
                    case "--testlist":
                        if ($directory == true){
                            fwrite(STDERR, ERR_HELP); exit(10);
                        }
                        $testlist = true;
                        break;
                    default:
                        break;
                }
            }
            /********** WITHOUT '=' *******************/
            switch ($arg){
                case "--help":
                    if ($argc != 2){ // cannot combine help with other params  
                        fwrite(STDERR, ERR_HELP); exit(10);
                    }
                    echo (HELP_MESS); break;
                case "--parse-only":
                    if ($int_only == true){
                        fwrite(STDERR, ERR_COM); exit(10);
                    } 
                    $parse_only = true;
                    $inter_on   = false;  // disable interpreter test 
                    break;
                case "--int-only":
                    if ($parse_only == true){
                        fwrite(STDERR, ERR_COM); exit(10);
                    }
                    $int_only   = true;   
                    $parser_on  = false;  // disable parser test 
                    break;
                case "--recursive":
                    break;
                case "--noclean":
                    break;
                default:
                    break;
            }
        }
        return 0;
    }

    /**
     * @var arr - input array  
     * @var i_f - index from we are concatenate 
     * 
     * @return - concatenate string and add '=' between then
     */
    function concat_str($arr, $i_f){
        $out = "";
        foreach ($arr as $key=>$n){
            if ($key < $i_f)
                continue;
            if ($key == sizeof($arr)-1){
                $out = $out . $n; 
                continue;
            }
            $out = $out . $n . "=";
        }
        return $out;
    }



?>
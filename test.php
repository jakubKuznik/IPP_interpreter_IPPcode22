<?php
    ini_set('display_errors', 'stderr'); //Erross on stderr 

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

    parse_args($argc, $argv);

    $tests_files; // Files that we will test 


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
     * @var 
     * @return 
     * 
     *  */    
    function parse_args(int $argc, array $argv){
        
        $equal_sym = false;
        $file_name;
        // two switch one for arguments with '='
        foreach ($argv as $arg){
            
            if (str_contains($arg, "=")){
                $arg = explode("=", $arg);
                $file_name = concat_str($arg, 1); // store file name
                switch ($arg[0]){
                    case "--directory";
                        break;
                    case "--parse-script":
                        break;
                    case "--int-script":
                        break;
                    case "--jexampath":
                        break;
                    case "--match":
                        break;
                    case "--testlist":
                        break;
                    default:
                        break;
                }
            }

            switch ($arg){
                case "--help":
                    if ($argc != 2){
                        fwrite(STDERR, ERR_HELP); exit(10);
                    }
                    echo (HELP_MESS); break;
                case "--parse-only":
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
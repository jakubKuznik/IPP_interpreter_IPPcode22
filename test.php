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

    /***************** PROGRAM  *******************/
    $set = new Settings();
    
    parse_args($argc, $argv, $set);
    
    // check if $parse_path, $int_path, $jexam_path exist.

    $set->file_exist();


    /**
     * Script settings. 
     */
    class Settings {
        public $parser_on   = true;              // parser tests 
        public $inter_on    = true;              // interpreter 
        public $recursive   = false;             // recursive folder                  
        public $noclean     = false;             // dont delete temp files 
        public $parse_path  = "parse.php";       // path to parser script 
        public $int_path    = "interpret.py";    // Path to interpreter script 
        public $jexam_path  = "/pub/courses/ipp/jexamxml/jexamxml.jar";
        public $match_regex = "";                // match regex files 
        public $tests_files;                     // array of files and folders that we will test 

        
        /**
         * Check if $parse_path, $int_path $jexam_path exist 
         * exit(41) if not 
         *
         * @return void 
         */
        function file_exist(){

            if ($this->parser_on == true){
                if (file_exists($this->parse_path) == false){
                    fwrite(STDERR, $this->parse_path . ERR_NON_EXI_FILE); exit(41);
                }
                if (file_exists($this->jexam_path) == false){
                    fwrite(STDERR, $this->jexam_path . ERR_NON_EXI_FILE); exit(41);
                }
            }
            if ($this->inter_on == true){
                if (file_exists($this->int_path) == false){
                    fwrite(STDERR, $this->int_path . ERR_NON_EXI_FILE); exit(41);
                }
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
     * 
     * @var argc
     * @var argv
     * @return void 
     * 
     *  */    
    function parse_args(int $argc, array $argv, $set){ 
        
        $int_only = false;
        print_r($set);
        
        // two switch one for arguments with '='
        foreach ($argv as $arg){
            
            /********** FOR ARGUMENTS WITH '=' SYMBOL ***********/
            if (str_contains($arg, "=")){
                $arg = explode("=", $arg);
                $help_file_name = concat_str($arg, 1); // store file name
                
                switch ($arg[0]){
                    case "--directory";
                        if ($testlist == true || $directory == true){
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
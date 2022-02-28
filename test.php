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
    define ("ERR_NON_EXI_DIR", " Directory does not exists.\n");
    define ("ERR_BAD_REG", " Not an PCRE regex.\n");
    define ("ERR_FIL_CRE", " Cannot create file\n");

    /***************** PROGRAM  *******************/
    $set = new Settings();
    
    print_r($set);
    parse_args($argc, $argv, $set);
    print_r($set);

    // check if given files exists and regex is valid 
    $set->file_exist();
    $set->dir_exist();
    $set->regex_valid();

    // creating test file class to find and store test files 
    $tf = new Test_files;

    // FIND TEST FILES // could be --match=regex or --recursive 
    // loking for .src files 
    if ($set->test_list == true) // testlist-file=
        $tf->find_test_from_list($set->directory, $set->recursive, $set->match_regex);
    else                         // just from directory
        $tf->find_test_inputs($set->directory, $set->recursive, $set->match_regex);

    foreach ($tf->tests_files as $t){
        $tf->gene_missing($t);
    }



    exit(0);

    /**
     * Script settings. 
     */
    class Settings {
        public $parser_on   = true;              // parser tests 
        public $inter_on    = true;              // interpreter 
        public $recursive   = false;             // recursive folder                  
        public $noclean     = false;             // dont delete temp files 
        public $test_list   = false;
        public $parse_path  = "parse.php";       // path to parser script 
        public $int_path    = "interpret.py";    // Path to interpreter script 
        public $jexam_path  = "/pub/courses/ipp/jexamxml/jexamxml.jar";
        public $match_regex = "/.*/";            // match regex files 
        public $directory = ".";                 // for --directory= or --testlist=
        
     
        /**
         * Check if $match_regex is valid PCRE regex
         */
        function regex_valid(){
            preg_match($this->match_regex, 'foobar foobar foobar');
            if (preg_last_error() !== PREG_NO_ERROR) {
                fwrite(STDERR, $this->match_regex . ERR_BAD_REG); exit(11);
            }
        }

        /**
         * Check if $directory exist 
         * 
         * if $test_list = true => check if $directory is file 
         * if $test_list = false => check if $directory is folder 
         */
        function dir_exist(){
            if ($this->test_list == true){
                if (file_exists($this->directory) == false){
                    fwrite(STDERR, $this->directory . ERR_NON_EXI_FILE); exit(41);
                }
            }
            else{
                if (is_dir($this->directory) == false){
                    fwrite(STDERR, $this->directory . ERR_NON_EXI_DIR); exit(41);
                }
            }
        }

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

    /**
     * Finds out all test files and store them  
     */ 
    class Test_files {
        public $tests_files = array();     // array of files and folders that we will test 
        private $src = '/.src/';           // src files 
        private $hiden_f = "/\/\./";       // hidden file         


        /**
         * Generate missing .out .rc .in files
         * 
         * @var file_path
         * 
         * if cannot create file exit(41);
         *  
         */
        function gene_missing($file_path){
            $dirname   = dirname($file_path); 
            $base_name = basename($file_path);
            $base_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $base_name);
            //echo $dirname . $base_name . "\n";

            // .out file 
            $h = $dirname . "/" . $base_name . ".out";
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fclose($a);
            }
            // .in 
            $h = $dirname . "/" . $base_name . ".in";
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fclose($a);
            }
            // .rc
            $h = $dirname . "/" . $base_name . ".rc";
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fwrite($a, "0");
                fclose($a);
            }


            //echo  . "\n";
            //echo $file_path . "\n";
            
        }

        /**
         * Function finds test inputs and store it to $test_files array
         * looking for .src 
         *
         * open $file and line by line go throw given paths 
         *  
         * @var $file 
         * @var $recursive true or fals \
         * @var $match regex - regex for filename 
         */
        function find_test_from_list($file, $recursive, $match_regex){
            // todo regex 
            // todo recursive 
            $f = fopen($file, "r");
            while ($path = fgets($f)){
                $path = rtrim($path, "\r\n");
                if (is_dir(realpath($path))){
                    $this->find_test_inputs($path, $recursive, $match_regex);
                }
                else{
                    if (preg_match($this->src ,$path) == false) // does't ends with .src 
                        continue;
                    if (preg_match($match_regex, basename($path)) == false) // does't match regex
                        continue;
                    array_push($this->tests_files, realpath($path));
                }
            }
            $this->tests_files = array_values(array_unique($this->tests_files));
            fclose($f);
        }


        /**
         * Function finds test inputs and store it to $test_files array
         * looking for .src 
         * 
         * @var $directory 
         * @var $recursive true or fals \
         * @var $match regex - regex for filename 
         * 
         */
        function find_test_inputs($directory, $recursive, $match_regex){
            
            $files = scandir($directory);
            //print_r($files);
            foreach ($files as $f){
                $f = $directory . "/" . $f;  //todo check if i have rights to read dir
                if (is_dir(realpath($f))){ //directory
                    if (preg_match($this->hiden_f, $f))// hiden folders  
                        continue;
                    if ($recursive == true){ // recursive call 
                        $this->find_test_inputs($f, $recursive, $match_regex);
                        continue;
                    }
                }
                else{ // normal file 
                    if (preg_match($this->src ,$f) == false) // does't ends with .src 
                        continue;
                    if (preg_match($match_regex, basename($f)) == false) // does't match regex
                        continue;
                    array_push($this->tests_files, realpath($f));
                }
            }
            $this->tests_files = array_values(array_unique($this->tests_files));
        }
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
        
        $int_only   = false; // cannot combine --int-only wiht --parse-only
        $parse_only = false;
        $directory  = false; // cannot 
        $testlist   = false;

     
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
                        $set->directory = $help_file_name;
                        break;
                    case "--parse-script":
                        if ($int_only == true){ //cannot combine with --int-only
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $parse_only = true;
                        $set->parse_path = $help_file_name;
                        break;
                    case "--int-script":
                        if ($parse_only == true){
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $set->int_path = $help_file_name;
                        $int_only = true;
                        break;
                    case "--jexampath":      
                        if ($int_only == true){ //cannot combine with --int-only
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $parse_only = true;
                        $set->jexam_path = $help_file_name; 
                        break;
                    case "--match":
                        $set->match_regex = $help_file_name;
                        break;
                    case "--testlist":
                        if ($directory == true){
                            fwrite(STDERR, ERR_HELP); exit(10);
                        }
                        $set->directory = $help_file_name;
                        $set->test_list = true;
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
                    $set->inter_on   = false;  // disable interpreter test 
                    break;
                case "--int-only":
                    if ($parse_only == true){
                        fwrite(STDERR, ERR_COM); exit(10);
                    }
                    $int_only   = true;   
                    $set->parser_on  = false;  // disable parser test
                    break;
                case "--recursive":
                    $set->recursive = true;
                    break;
                case "--noclean":
                    $set->noclean = true;
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
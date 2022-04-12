<?php
    ini_set('display_errors', 'stderr'); //Erross on stderr 

    /************** MACROS **************************/
    define("HELP_MESS", " Test script for parser.php and interpret.py
                --help               // Display help cannot be combinated with anything 
                --parse-only         // olnly parser.php test. can't combine with --int-only, --int-script 
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

    define ("OUT", 0);
    define ("IN", 1);
    define ("RC", 2);
    define ("SRC", 3);

    define ("FAIL", 0);
    define ("SUCCESS", 1);
    define ("INVALID", 2);

    $temp_file_name = "";

    /***************** PROGRAM  *******************/
    $set = new Settings();
    
    //print_r($set);
    parse_args($argc, $argv, $set);
    //print_r($set);

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

    $test_c = new Test($set);
    $html_generator = new Html_generation();
    $html_generator->init($set);
    $out_file = create_temp_file(); 
    // Check if .out .rc .in are there if not create them 
    foreach ($tf->tests_files as $test_file){
        if ($set->parser_only == true){
            $test_c->parse_only_test($test_file, $temp_file_name);
        }
        else if ($set->inter_only == true){
            $test_c->int_only_test($test_file, $temp_file_name);
        }
        else{
            $test_c->test_both($test_file, $temp_file_name);
        }
    }
    $html_generator->create_summ($test_c->succ_sum, $test_c->fail_sum, $test_c->test_num);
    $html_generator->create_end($set);
   
    // deleting temp file 
    fclose($out_file);
    if ($set->noclean == false)
        unlink($temp_file_name);
        
    
    exit(0);

    /**
     * Class for parse.php test 
     */
    class Test {
        
        // constructor will set it from Settings class 
        private $parse_path  = "";       // path to parser script 
        private $jexam_path  = "";
        private $int_path    = "";       // Path to interpreter script 
        public $test_num    = 0;        // Every test has unique index
        public $fail_sum    = 0;
        public $succ_sum    = 0;
        
        /**
         * @var $set -  set is type class Settings  
         *              gets $parse_path and $jesam_path from there.
         */
        function __construct(Settings $set){
            $this->parse_path = $set->parse_path;
            $this->jexam_path = $set->jexam_path;
            $this->int_path   = $set->int_path;
        }
        
        /**
         * Call this method if test failed  
         * @var @file_path file that failed 
         */
        function test_fail($file_path, $ref_rc, $rc){
            global $html_generator;
            $this->fail_sum++;
            $html_generator->store_test(FAIL, $this->test_num, $file_path, $ref_rc, $rc); 
        }

        /**
         * Call this method if test succ 
         * @var @file_path file that succ 
         */
        function test_succ($file_path){
            global $html_generator;
            $this->succ_sum++;
            $html_generator->store_test(SUCCESS, $this->test_num, $file_path, 0, 0); 
        }

        /**
         * @var $command that will be aplied 
         */
        function zero_rc_testcase($command, $ref_rc, $file_path){
            exec($command, $result_out, $result_rc);
            if ($result_rc == $ref_rc){
                $this->test_succ($file_path);
            }
            else{
                $this->test_fail($file_path, $ref_rc, $result_rc);
            }
        }

        /**
         *  -test for both parser and interpreter. parser will create a xml and interpret will execute it 
         * @var $file_path -- path to file that ll be tested 
         * @var $temp_file -- temporarily file for program outputs 
         */
        function test_both($file_path, $temp_file){
            $this->test_num++;
            
            $result_rc      = "";
            $diff_ret       = "";
            $out            = "";
            $file_src       = get_file_name($file_path, SRC);
            $file_in        = get_file_name($file_path, IN);
            $ref_file_out   = get_file_name($file_path, OUT);
            $ref_rc         = file_get_contents(get_file_name($file_path, RC), true);     
            $parse_comm     = "php8.1 ".$this->parse_path ." < " . $file_path . " > " . $temp_file; 
            $inter_comm     = "python3 " . $this->int_path . " --source=" . "9999999999.out "  . " --input=" . $file_in . " >" . $temp_file . " 2>/dev/null";           

            // Non valid return code 
            if (is_numeric($ref_rc) == false){
                fwrite(STDERR,"UNVALID Test: " . $this->test_num . " " . $file_path . "\n");
                return;
            }
            
            /********* ref RETURN CODE NOT 0 ****************/
            if ($ref_rc != 0){    
                exec($parse_comm, $result_out, $result_rc);
                if ($result_rc == 0){ // parser have 0 return code 

                    exec("cat " . $temp_file . " >9999999999.out");
                    exec($inter_comm, $result_out, $result_rc); //execute interpret.py on xml 
                    exec("diff -q " . $ref_file_out . " " . $temp_file, $out ,$diff_ret);
                    exec("rm " . "9999999999.out");
                    
                    if ($result_rc == $ref_rc){
                        $this->test_succ($file_path);
                        return;
                    }
                    else{
                        $this->test_fail($file_path, $ref_rc, $result_rc);
                        return;
                    }
                }
                else{ // parser have negative return code
                    if($result_rc == $ref_rc){
                        $this->test_succ($file_path);
                        return;
                    }
                    else{
                        $this->test_fail($file_path, $ref_rc, $result_rc);
                        return;
                    }
                }
            }
            /********* ref RETURN CODE 0  ****************/
            else{
                exec($parse_comm, $result_out, $result_rc);
                if ($result_rc != 0){
                    $this->test_fail($file_path, $ref_rc, $result_rc); //parser failed 
                    return;
                }
                
                exec("cat " . $temp_file . " >9999999999.out");
                exec($inter_comm, $result_out, $result_rc); //execute interpret.py on xml 
                exec("diff -q " . $ref_file_out . " " . $temp_file, $out ,$diff_ret);
                exec("rm " . "9999999999.out");
                
                if (($diff_ret != 0) || ($result_rc != $ref_rc)){
                    $this->test_fail($file_path, $ref_rc, $result_rc);
                    return;
                }
                else{
                    $this->test_succ($file_path);
                    return;
                }
            }
        }

        /**
         * --int-only tests 
         * 
         * @var $file_path -- path to file that ll be tested 
         * @var $temp_file -- temporarily file for program outputs 
         */ 
        function int_only_test($file_path, $temp_file){
            $this->test_num++;
            
            $result_rc       = "";
            $diff_ret        = "";
            $out             = "";
            $ref_file_out    = get_file_name($file_path, OUT);
            $ref_rc          = file_get_contents(get_file_name($file_path, RC), true);
            $file_in         = get_file_name($file_path, IN);
            $file_src        = get_file_name($file_path, SRC);
            $inter_comm      = "python3 " . $this->int_path . " --source=" . $file_src . " --input=" . $file_in . ">" . $temp_file . " 2>/dev/null";           
            
            // Non valid return code 
            if (is_numeric($ref_rc) == false){
                fwrite (STDERR,"UNVALID Test: " . $this->test_num . " " . $file_path . "\n");
                return;
            }
            /********* ref RETURN CODE NOT 0 ****************/
            if ($ref_rc != 0){
                $this->zero_rc_testcase($inter_comm, $ref_rc, $file_path);
            }
            /********* ref RETURN CODE 0  ****************/
            else{
                // store parser output to temp variable 
                exec($inter_comm, $result_out, $result_rc);
                exec("diff -q " . $ref_file_out . " " . $temp_file, $out ,$diff_ret);
                
                if (($diff_ret != 0) || ($result_rc != $ref_rc)){
                    $this->test_fail($file_path, $ref_rc, $result_rc);
                }
                else{
                    $this->test_succ($file_path);
                }
            }
        }
        
        /**
         * --parse-only tests 
         * 
         * @var $file_path -- path to file that ll be tested 
         * @var $temp_file -- temporarily file for program outputs 
         *  
         */
        function parse_only_test($file_path, $temp_file){
            $this->test_num++;

            $result_rc      = "";
            $out            = "";
            $ref_file_out   = get_file_name($file_path, OUT);
            $ref_rc         = file_get_contents(get_file_name($file_path, RC), true);
            $script_stderr  = "";
            $jar_com        = "java -jar " . $this->jexam_path . " " . $ref_file_out  . " " . $temp_file;
            $parse_com      = "php8.1 ".$this->parse_path ." < " . $file_path . " > " . $temp_file; 
            
            // Non valid return code 
            if (is_numeric($ref_rc) == false){
                fwrite (STDERR, "UNVALID Test: " . $this->test_num . " " . $file_path . "\n");
                return;
            }

            /********* ref RETURN CODE NOT 0 ****************/
            if ($ref_rc != 0){
                $this->zero_rc_testcase($parse_com, $ref_rc, $file_path);
            }
            /********* ref RETURN CODE 0  ****************/
            else{
                // store parser output to temp variable 
                exec($parse_com, $result_out, $result_rc);
                // compare ref output vs parser output using JeXAM
                exec($jar_com, $non, $jar_rc);

                if (($jar_rc != 0) or ($result_rc != $ref_rc)){
                    $this->test_fail($file_path, $ref_rc, $result_rc);
                }
                else{
                    $this->test_succ($file_path);
                }
            }
        }
    }

    /**
     * Generate html output. 
     */
    class Html_generation { 
        public $header = '<!DOCTYPE html><html lang="en"><head><title>CSS Template</title><meta charset="utf-8"><style> .up { width: 100%; display: flex; justify-content: center; align-items: center; padding: 50; } .left_up { margin: 10px; border: 2px outset lightgray; background-color: rgb(250, 248, 248); text-align: center; padding: 50; } .right_up { margin: 10px; border: 2px outset lightgray; background-color: rgb(250, 248, 248); text-align: center; padding: 50; } .center { justify-content: center; text-align: center; align-items: center; position: relative; padding: 50; } .test { display: inline-block; left:50%; top:50%; width: 80%; margin: 10px; border: 2px outset lightgray; background-color: rgb(250, 248, 248); text-align: left; padding: 10pt; } .header { width: 100%; display: flex; justify-content: center; align-items: center; } ul { 	 display: inline-block; 	 text-align: left; 	}</style></head><body><div class="header"> <header> <h2>IPP 2022</h2> </header>';
        public $config_begin = '</div><div class="up"> <div class="left_up"> <h2>Konfigurace</h2> <!-- VLOZIT TABULKU KONFIGURACE --> <ul>';
        public $end = "</div></div></body>";
        public $all_test = "";

        function init(Settings $set){
            $this->create_header();
            $this->create_configuration($set);
        }

        function create_header(){
            echo $this->header;
        }

        function create_configuration(Settings $set){
            echo $this->config_begin;
            echo "<li>Adresář: .....";
            echo $set->directory;
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "</li>";

            if ($set->parser_only == true){
                echo "<li>Parser only ON";
                echo "</li>";
            }
            else if($set->inter_only == true){
                echo "<li>Interpret only ON";
                echo "</li>";
            }
            else{
                echo "<li>Testing both ON";
                echo "</li>";
            }

            if($set->recursive == true){
                echo "<li>Recursive ON";
                echo "</li>";
            }
            if($set->noclean == true){
                echo "<li>noclean ON";
                echo "</li>";
            }
            if($set->test_list == true){
                echo "<li>Test list ON";
                echo "</li>";
            }

            echo "<li>Regex match: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'";
            echo $set->match_regex;
            echo "'</li>";
            echo "</ul></div>";
        }

        function create_summ(int $succ, int $fail, int $sum){
            echo '<div class="right_up"> <h2>Výsledky</h2> <table> <tbody> <tr> <td style="font-size: x-large;color: green; ">Úspěšných: .......................................</td> <td style="font-size: x-large;color: green;"> ';    
            echo $succ;
            echo '</td> </tr> <tr> <td style="font-size: x-large; color: red;">Neúspěšných: ....................................</td> <td style="font-size: x-large; color: red;">';
            echo $fail;
            echo '</td> </tr> <tr> <td style="font-size: x-large;">Celkem: ............................................</td> <td style="font-size: x-large;" >';
            echo $sum;
            echo '</td> </tr> </tbody></table> </div></div>';
        }

        function store_test($state, $index, $name, $ref_rc, $rc){
            if($state == FAIL){
                //difrent return code 
                if ($ref_rc != $rc){
                    $this->all_test = $this->all_test . '<div class="test"> <div style="display: flex; justify-content: space-between;"> <p>' . $index  . '.</p> <p style="font-size: medium ;color: red;">FAIL</p>  <p>Test:' .  $name  . '</p></div> <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ROZDÍLNÁ NÁVRATOVÁ HODNOTA:&nbsp;&nbsp;&nbsp;refereční:&nbsp;' . $rc  . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; skutečný: &nbsp;' . $ref_rc  .'</p> </div>';
                    return;
                }
                $this->all_test = $this->all_test . '<div class="test"> <div style="display: flex; justify-content: space-between;"> <p>' . $index  . '.</p> <p style="font-size: medium ;color: red;">FAIL</p>  <p>Test:' .  $name  . '</p></div> <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ROZDÍLNÝ VÝSTUP</p> </div>';
            }
            else if($state == SUCCESS){
                $this->all_test = $this->all_test . '<div class="test"> <div style="display: flex; justify-content: space-between;"> <p>' . $index . '.</p> <p style="font-size: medium ;color: green;">SUCCESS</p> <p>Test:' . $name . '</p></div> </div>';
            }
        }
        
        function create_end(){
            echo '<div class="center"><h1>Výsledky jednotlivých testů:</h1>';
            echo $this->all_test;
            echo $this->end;
        }
    }

    /**
     * Script settings. 
     */
    class Settings {
        public $parser_only = false;              // parser tests 
        public $inter_only  = false;              // interpreter 
        public $recursive   = false;             // recursive folder                  
        public $noclean     = false;             // dont delete temp files 
        public $test_list   = false;
        public $parse_path  = "parse.php";       // path to parser script 
        public $int_path    = "interpret.py";    // Path to interpreter script 
        public $jexam_path  = "/pub/courses/ipp/jexamxml/jexamxml.jar";
        public $match_regex = "/.*/";            // match regex files 
        public $directory   = ".";                 // for --directory= or --testlist=
     
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
            if ($this->parser_only == true){
                if (file_exists($this->parse_path) == false){
                    fwrite(STDERR, $this->parse_path . ERR_NON_EXI_FILE); exit(41);
                }
                if (file_exists($this->jexam_path) == false){
                    fwrite(STDERR, $this->jexam_path . ERR_NON_EXI_FILE); exit(41);
                }
            }
            else if ($this->inter_only == true){
                if (file_exists($this->int_path) == false){
                    fwrite(STDERR, $this->int_path . ERR_NON_EXI_FILE); exit(41);
                }
            }
            else{
                if (file_exists($this->parse_path) == false){
                    fwrite(STDERR, $this->parse_path . ERR_NON_EXI_FILE); exit(41);
                }
                if (file_exists($this->jexam_path) == false){
                    fwrite(STDERR, $this->jexam_path . ERR_NON_EXI_FILE); exit(41);
                }
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
        public $tests_files = array();      // array of files and folders that we will test 
        private $src        = '/.src/';     // src files 
        private $hiden_f    = "/\/\./";     // hidden file         

        /**
         * Generate missing .out .rc .in files
         * @var file_path
         * 
         * if cannot create file exit(41);
         */
        function gene_missing($file_path){
            // todo valid 
            // .out file 
            $h = get_file_name($file_path, OUT);
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fclose($a);
            }
            // .in 
            $h = get_file_name($file_path, IN);
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fclose($a);
            }
            // .rc
            $h = get_file_name($file_path, RC);
            if (file_exists($h) == false){
                if (($a = fopen($h, "w")) == false){
                    fwrite(STDERR, $m . ERR_FIL_CRE); exit(41);
                }
                fwrite($a, "0");
                fclose($a);
            }
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
        function find_test_from_list($file, bool $recursive, $match_regex){
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
        function find_test_inputs($directory, bool $recursive, $match_regex){
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
     * @var argc
     * @var argv
     * @return void 
     *  */    
    function parse_args(int $argc, array $argv, Settings $set){ 
        
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
                        $set->parse_path = $help_file_name;
                        break;
                    case "--int-script":
                        if ($parse_only == true){
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        $set->int_path = $help_file_name;
                        break;
                    case "--jexampath":      
                        if ($int_only == true){ //cannot combine with --int-only
                            fwrite(STDERR, ERR_COM); exit(10);
                        }
                        fwrite(STDERR, ERR_COM); exit(10);
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
                    if ($argc != 2){            // cannot combine help with other params  
                        fwrite(STDERR, ERR_HELP); exit(10);
                    }
                    echo (HELP_MESS); break;
                case "--parse-only":
                    if ($int_only == true){
                        fwrite(STDERR, ERR_COM); exit(10);
                    } 
                    $parse_only = true;
                    $set->parser_only   = true;  // disable interpreter test 
                    break;
                case "--int-only":
                    if ($parse_only == true){
                        fwrite(STDERR, ERR_COM); exit(10);
                    }
                    $int_only   = true;   
                    $set->inter_only  = true;  // disable parser test
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
    function concat_str(array $arr, int $i_f){
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

    /**
     * @return string with file name but with diferent suffix 
     * 
     * @var @file_path 
     * @var $suff - OUT IN RC 
     */ 
    function get_file_name($file_path, int $suff){
        $dirname   = dirname($file_path); 
        $base_name = basename($file_path);
        $base_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $base_name);
        
        if ($suff == OUT)
            return ($dirname . "/" . $base_name . ".out");
        else if ($suff == IN)
            return ($dirname . "/" . $base_name . ".in");
        else if ($suff == RC)
            return ($dirname . "/" . $base_name . ".rc");
        else if ($suff == SRC)
            return ($dirname . "/" . $base_name . ".src");
    }
    
    /**
     * Create temp file for outputs
     */
    function create_temp_file(){
        global $temp_file_name;
        $temp_file_name = "xkuzni04_" . rand(0, 1000000000) . ".out";
        $myfile = fopen($temp_file_name, "w");
        return $myfile;
    }
?>
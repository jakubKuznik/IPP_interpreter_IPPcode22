<?php
    ini_set('display_errors', 'stderr'); //Erross on stderr 



    function parse_args($argc, $argv);





    /**
     * Function parse arguments.
     * 
     * --help               // Display help cannot be combinated with anything 
     * --directory=path     // Folder where we are looking for tests 
     * --recursive          // Not only given folder but recursive folder to 
     * --parse-script=file  // parse.php      - if not given actual folder  
     * --int-script=file    // interpret.py   - if not given actual folder 
     * --parse-only         // olnly parser.php test. can't combine with --int-only, --int-script 
     * --int-only           // only interpret.py
     * --jexapath=path      // path to jexamxml.jar (A7Soft)
     * --noclean            // doesnt remove temp. files
     * --testlist=file      // explicit folders definition or files 
     *                      // -can't combine with --directory
     * --match=regexp       // Only files that match regex of PCRE sytax 
     * 
     *  */    
    function parse_args(int $argc, array $argv){
        return 0;
    }







?>
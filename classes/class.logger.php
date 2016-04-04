<?php
    /**
    *  There is Logger class. Singleton.
    */
    class Logger {
        private static $_instance; // singletone instance
        private $_oFile; // file pointer

        /**
         * We need only one instance, which will lock the file
         * from further editing.
         */
        private function __construct()
        {
            // form log file path
            $sPath = APP_ROOT . '/logs/log_' . date('Y-m-d-H-i-s') . '.log';
            $this->_oFile = fopen($sPath,'wb+');
        }

        public function __destruct()
        {
            fclose($this->_oFile);
        }

        // log info to file
        public function log($sText)
        {
            echo $sText . "\n";
            fwrite($this->_oFile, $sText . "\n");
        }


        //This method is the standard way to create a Singleton.
        public static function getInstance()
        {
            if (self::$_instance === null) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
    }

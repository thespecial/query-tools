<?php
    /**
      * There is AgentApp class - application for testing User-Agent requests.
      * This class is Singleton class, as it's best practice for apps/frameworks.
      * So we guarantee that only one app instance run in a time.
    */
    class AgentApp {
        /**
          * Constant variable APP_NAME used for logger, as Logger class shared between apps.
          * Specifies folder where app is placed to help logger find app logs diretory
        */
        const APP_NAME = 'user_agent';

        // Singleton variable
        private static $_instance;

        /**
          * Default curl options which are used in the app. For now is private,
          * but may be some kind of public to make possibility modify variables
          * from where app called.
        */
        private $_aDefaultOptions = array(
            CURLOPT_RETURNTRANSFER => 1,        // to return response ro variable
            CURLOPT_FOLLOWLOCATION => 1,        // to follow redirects
            CURLOPT_VERBOSE        => 1,        // to display verbose info
            CURLOPT_HEADER         => 1,        // to include headers to output
            CURLOPT_COOKIESESSION  => 1,        // to ignore previously set cookies
            CURLOPT_CONNECTTIMEOUT => 10,       // seconds to wait during connection
            CURLOPT_USERAGENT      => 'hacker'  // user agent value
        );

        // Request url
        private $_sUrl = 'http://localhost/index.html';

        // Main function
        public function main()
        {
            // Set default timezone to work with date() function
            date_default_timezone_set('Europe/Moscow');

            /**
              * Wrap execution in exception catching wrapper (to get exceptions if any).
              *
              * Steps:
              * 1. Initialize Logger
              * 2. Initialize cUrl request with specified URL and options
              * 3. Log message: [PROGRAM STARTED]
              * 4. Execute cUrl request
              * 5. Parse response for body, http code, header
              * 6. Verify if body is empty or http code 403 (acceptance criterias)
              * 7. Log result message
              * 8. Log message: [PROGRAM FINISHED]
              * 9. If any exceptions, they will be caught!
              *    Stacktrace will be printed to logs/ folder.
              * 10. If exceptions, log message: '[ABORTED]'
            */
            try {
                $logger = Logger::getInstance();                  // 1
                $oCurl = new CurlRequest($this->_sUrl);           // 2

                $oCurl->setOptionsArray($this->_aDefaultOptions); // 2

                $logger->log('[PROGRAM STARTED]');                // 3
                $oCurl->exec();                                   // 4
                $aResult = $oCurl->getResponseData();             // 5

                if (empty($aResult['body']) || ($aResult['http_code'] == 403)) { // 6
                    $logger->log("Page didn't return any content. SUCCESS!");    // 7
                }
                else {
                    $logger->log("Page returned content. FAIL!");                // 7
                }

                $logger->log("[PROGRAM FINISHED]"); //8
            }
            catch (Exception $e){   // 9
                $logger->log($e->getMessage());
                $logger->log($e->getTraceAsString());
                $logger->log('[ABORTED]');  // 10
                exit();
            }

        }


        // This method is the standard way to create a Singleton
        public static function getInstance()
        {
            if (self::$_instance === null) {
                self::$_instance = new self(APP_NAME);
            }
            return self::$_instance;
        }

        private function __construct() {}
    }

?>

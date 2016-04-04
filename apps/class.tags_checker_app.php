<?php
    /**
      * There is TagsCheckerApp class - application for testing tags for correctness.
      * This class is Singleton class, as it's best practice for apps/frameworks.
      * So we guarantee that only one app instance run in a time.
    */
    class TagsCheckerApp {
        /**
          * Constant variable APP_NAME used for logger, as Logger class shared between apps.
          * Specifies folder where app is placed to help logger find app logs diretory
        */
        const APP_NAME = 'tags_checker';

        // Singletone variable
        private static $_instance;

        // Tags we need to catch
        private $_aTagsWeNeed = array(
          array(
            'name' => 'title',
            'meta' => false // it's true, but as we have meta_ prefix in .csv, then false
          ),
          array(
            'name' => 'description',
            'meta' => true
          )
        );

        /**
          * Default curl options which are used in the app. For now is private,
          * but may be some kind of public to make possibility modify variables
          * from where app called.
        */
        private $_aDefaultOptions = array(
            CURLOPT_RETURNTRANSFER => 1,        // to return response ro variable
            CURLOPT_FOLLOWLOCATION => 1,        // to follow redirects
            CURLOPT_VERBOSE        => 0,        // to display verbose info
            CURLOPT_HEADER         => 1,        // to include headers to output
            CURLOPT_COOKIESESSION  => 1,        // to ignore previously set cookies
            CURLOPT_CONNECTTIMEOUT => 10,       // seconds to wait during connection
            CURLOPT_USERAGENT      => 'hacker',  // user agent value
            CURLOPT_HTTPHEADER     => array('Cookie: test=1') // set cookies
        );

        // Main function
        public function main()
        {
            // Set default timezone to work with date() function
            date_default_timezone_set('Europe/Moscow');

            // path to fixtures (test sample.csv file)
            $sFixtures = 'fixtures/sample.csv';

            /**
              * Wrap execution in exception catching wrapper (to get exceptions if any).
              *
              * Steps:
              * 1. Initialize Logger
              * 2. Initialize cUrl request with specified options
              *    (without URL, as it will be read from file)
              * 3. Log message: [PROGRAM STARTED]
              * 4. Log message: 'Reading .csv file...'
              * 5. Open fixture file gor reading in binary mode (for best portability)
              * 6. Initialize TagsChecker class.
              * 7. Start reading file by getting each row as csv string with
              *    fgetcsv($oFile, 1024, ','), where:
              *    1) $oFile - file pointer
              *    2) 1024 - max csv string length in chars
              *    3) ','  - delimeter
              *
              * 8. Parse each csv string to associative array of:
              *      array(
              *        'meta_title' => '',
              *        'meta_description' => '',
              *        'url' => ''
              *      )
              * 9. Pass URL param to cUrl object and execute cUrl request
              * 10. Then geetTagsFromResponse() - more than 1000 words :)
              * 11. Assert method of TagsChecker - compare expected(from file)
              *     and actual(from response) tags, og result message
              * 12. Log message: [PROGRAM FINISHED]
              * 13. If any exceptions, they will be caught!
              *     Stacktrace will be printed to logs/ folder.
              * 14. If exceptions, log message: '[ABORTED]'
            */

            try {
                $logger = Logger::getInstance(); // 1
                $oCurl = new CurlRequest(); // 2
                $oCurl->setOptionsArray($this->_aDefaultOptions); // 2

                $logger->log('[PROGRAM STARTED]'); // 3
                $logger->log('Reading .csv file...'); // 4
                $oFile = fopen($sFixtures, 'rb'); // 5

                $aHeader = array();

                if ($oFile !== false)
                {
                    $logger->log('Initializing tags checker...');
                    $oChecker = new TagsChecker(); // 6

                    $logger->log('Setting curl variables...');

                    while (($aRow = fgetcsv($oFile, 1024, ',')) !== false) // 7
                     {
                         if (empty($aHeader)) // 8
                             $aHeader = $aRow; // 8 also fetch file headers to make pretty associative arrays;
                         else {
                             $aExpected = array_combine($aHeader, $aRow); // 8 combine headers with array
                             $logger->log('Verifying tags for ' . $aExpected['url']);
                             $oCurl->setOption(CURLOPT_URL, $aExpected['url']); // 9
                             $sResponse = $oCurl->exec(); // 9
                             $aActual = $oChecker->getTagsFromResponse($sResponse, $this->_aTagsWeNeed); // 10
                             $oChecker->assert($aExpected, $aActual); // 11
                         }
                     }
                 }

                 $logger->log("[PROGRAM FINISHED]"); // 12
            }
            catch (Exception $e){ // 13
                $logger->log($e->getMessage());
                $logger->log($e->getTraceAsString());
                $logger->log('[ABORTED]');
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

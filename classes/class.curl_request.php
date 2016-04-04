<?php
    /**
    * There is CurlRequest class. It just wraps up standard cUrl stuff.
    */
    class CurlRequest {
        private $_oCurl = null; // curl variable
        private $_sResponse = null; // response varible for additional processing

        function __construct($sUrl = '') {
            // init curl object
            $this->_oCurl = curl_init($sUrl);
        }

        public function exec() {
            $this->_sResponse = curl_exec($this->_oCurl);

            // throwing exception if error occured during request
            if (!$this->_sResponse) {
                throw new Exception('Error: "' . curl_error($this->_oCurl) . '" - Code: ' . curl_errno($this->_oCurl));
            }

            return $this->_sResponse;
        }

        // converts response string to array
        public function getResponseData() {
            $iHeaderSize = curl_getinfo($this->_oCurl, CURLINFO_HEADER_SIZE);

            return $aResponse = array(
                'http_code' => curl_getinfo($this->_oCurl,CURLINFO_HTTP_CODE),
                'header'    => substr($this->_sResponse, 0, $iHeaderSize),
                'body'      => substr($this->_sResponse, $iHeaderSize)
            );
        }

        // set one curl option
        public function setOption($oOption, $value) {
            curl_setopt($this->_oCurl, $oOption, $value);
        }

        // set array of cUrl options
        public function setOptionsArray($aOptions) {
            curl_setopt_array($this->_oCurl, $aOptions);
        }

        public function __destruct()
        {
            unset($this->_sResponse);
            // closing curl connection
            curl_close($this->_oCurl);
        }
    }

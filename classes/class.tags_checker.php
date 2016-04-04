<?php
    /**
    * There is TagsChecker class. Has empty constructor.
    */
    class TagsChecker {
        /*
        * Recieve cUrl response as param ($sHtml) and tags we need to look for ($aTags)
        * Returns associative array. Example:
        * array(
        *   'meta_title' => 'some title',
        *   'meta_description' => 'some description'
        * )
        */
        public function getTagsFromResponse($sHtml, $aTags) {
          // new DOMDocument
          $oDoc = new DOMDocument();
          // Load cUrl response
          @$oDoc->loadHTML($sHtml);
          $aResult = array();

          /**
          * Go through $aTags and verify, if it's tag meta, forms proper key for result erray.
          * If it's tag not meta, $key will be such a simple tag name (except title).
          * Title will have $key 'meta_title'. As we have so in .csv file.
          * And of course it can be improved. But I decided not to bring another logic.
          */
          foreach($aTags as $aTag) {
              if ($aTag['meta']) {
                  $key = 'meta_' . $aTag['name'];

                  // get meta tags
                  $oMetas = $oDoc->getElementsByTagName('meta');

                  // go through meta tags
                  for ($i = 0; $i < $oMetas->length; $i++) {
                      $oMeta = $oMetas->item($i);
                      // in our case here we're looking for description
                      if($oMeta->getAttribute('name') == $aTag['name'])
                          $aResult[$key] = $oMeta->getAttribute('content');
                  }
              }
              else {
                  // and get title
                  $key = ($aTag['name'] == 'title') ? 'meta_title' : $aTag['name'];
                  $oNodes = $oDoc->getElementsByTagName('title');
                  $aResult[$key] = $oNodes->item(0)->nodeValue;
              }
          }
          return $aResult;
        }


        // this function calls value comparison function
        // also does some output stuff 
        public function assert($aExpected, $aActual) {
            $sMsg = "******* [Results]: " . $aExpected['url'] . " *******\n";


            foreach ($aActual as $key => $value) {
                $sMsg .= $key . "\n";
                $sMsg .= $this->_compareTags($aExpected[$key], $aActual[$key]);
            }

            $sMsg .= "********************[Results End]***********************\n\n";
            echo $sMsg;
        }

        // this function compares expected tags
        private function _compareTags($sExpected, $sActual) {
            $sMsg = '';

            if ($sExpected != $sActual) {
                $sMsg .= "\tExpected: " . $sExpected . "\n";
                $sMsg .= "\tActual: " . $sActual . "\n";
            }
            else
              $sMsg .= "\tOK\n";

            return $sMsg;
        }
    }
?>

<?php

// Version 1.14 - 2016-07-27

class srFax {
  
  // class member variables
  protected $access_id  = "";
  protected $access_pwd = "";
  protected $serverUrl  = "https://www.srfax.com/SRF_SecWebSvc.php";
  
  protected $lastStatus = "";
  protected $lastResult = "";
  
  // constructor requires user access id and password
  function __construct($accessId, $pwd) {
    $this -> access_id  = $accessId;
    $this -> access_pwd = $pwd;
    
    return;
  
  }
 
  /*
   * Queue_Fax - Queue a fax to be sent
   * USAGE
   *  $srFax -> Queue_Fax(array(
   *    'sCallerID'        =>  '', //REQUIRED - Sender's Fax Number (10 digits)
   *    'sSenderEmail'     =>  '', //REQUIRED - Sender's email address
   *    'sFaxType'         =>  '', //REQUIRED - SINGLE/BROADCAST
   *    'sToFaxNumber'     =>  '', //REQUIRED - 11 digit number, up to 50 11 digit numbers separated by "|" (pipe)
   *
   *    'sResponseFormat'  =>  '', //OPTIONAL - XML or JSON(default)
   *    'sAccountCode'     =>  '', //OPTIONAL - if you want billing to be detailed by an account code
   *    'sRetries'         =>  '', //OPTIONAL - numer of retries (0-6)
   *    'sCoverPage'       =>  '', //OPTIONAL - If you want to use one of your cover pages (Basic/Standard/Company/Default)
   *    'sCPFromName'      =>  '', //OPTIONAL - Sender's name on the cover page 
   *    'sCPToName'        =>  '', //OPTIONAL - Recipient's name on the cover page
   *    'sCPOrganization'  =>  '', //OPTIONAL - Organization on the cover page
   *    'sCPSubject'       =>  '', //OPTIONAL - Subject line on the cover page
   *    'sCPComments'      =>  '', //OPTIONAL - Comments place in the bodyof the cover page
   *    'sFileName_*'      =>  '', //At least 1 required - Valid file name, replace * with a number starting at 1
   *    'sFileContent_*'   =>  '', //At least 1 required - Base64 encoding of file contents. Replacce * with corresponding number from file name
   *    'sNotifyURL'       =>  '', //OPTIONAL - Provide an absolute URL (starting with http:// or https://) and receive a post to that address when the fax completes
   *    'sFaxFromHeader'   =>  '', //OPTIONAL
   *    'sQueueFaxDate'    =>  '', //OPTIONAL - A future date you want to queue a fax for.  Must be YYYY-MM-DD
   *    'sQueueFaxTime'    =>  '', //OPTIONAL - A future time you want to queue a fax for. Must be HH:MM (00:00 - 23:59)
   *
   *  ));
   */
  public function Queue_Fax($parameters = array()) {
     
     $postVariables  = array();
     $requiredFields = array('sCallerID', 'sSenderEmail', 'sFaxType', 'sToFaxNumber');
     $optionalFields = array('sResponseFormat', 'sAccountCode', 'sRetries', 'sCoverPage', 'sCPFromName', 'sCPToName', 'sCPOrganization',
                             'sCPSubject', 'sCPComments', 'sFileName_*', 'sFileContent_*', 'sNotifyURL', 'sFaxFromHeader', 'sQueueFaxDate', 'sQueueFaxTime');
     
     $this ->_validateRequiredFields($requiredFields, $parameters);
     
     $postVariables = $this->_preparePostVariables($requiredFields, $optionalFields, $parameters);
     
     $postVariables['action']     = "Queue_Fax";
     $postVariables['access_id']  = $this->access_id;
     $postVariables['access_pwd'] = $this->access_pwd;

      $result = $this->_processRequest($postVariables);
      $this->_processResponse($result, $parameters);
      return $this->lastStatus;

  }
  
  // 
  /*
   * Get_FaxStatus - retrieves the current status of a fax queued for delivery
   * USAGE
   * $srFax -> Get_FaxStatus(array(
   *  'sFaxDetailsID'   => '', //REQUIRED FaxDetailsID retured from Queue_Fax
   *  'sResponseFormat' => '', //OPTIONAL - XML or JSON(default)
   * ));
  */
  public function Get_FaxStatus($parameters) {
    
    $requiredFields = array('sFaxDetailsID');
    $optionalFields = array('sResponseFormat');
    
    $this -> _validateRequiredFields($requiredFields, $parameters);
    
    $postVariables = $this -> _preparePostVariables($requiredFields, $optionalFields, $parameters);
    
    $postVariables['action']     = "Get_FaxStatus";
    $postVariables['access_id']  = $this -> access_id;
    $postVariables['access_pwd'] = $this -> access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
  
  }
  
  
  /*
   * Get_MultiFaxStatus - retrieves the status of multiple faxes that have been queued for delivery
   * USAGE
   * $srFax -> Get_MultiFaxStatus(array(
   *  'sFaxDetailsID'   => '', // REQUIRED - Multiple FaxDetailsIDs can be requested by separating each FaxDetailsID with a pipe character
   *  'sResponseFormat' => '', // OPTIONAL - XML or JSON(default)
   * ));
  */
  public function Get_MultiFaxStatus($parameters) {
    
    $requiredFields = array('sFaxDetailsID');
    $optionalFields = array('sResponseFormat');
    
    $this -> _validateRequiredFields($requiredFields, $parameters);
    
    $postVariables = $this -> _preparePostVariables($requiredFields, $optionalFields, $parameters);
    
    $postVariables['action']     = "Get_MultiFaxStatus";
    $postVariables['access_id']  = $this -> access_id;
    $postVariables['access_pwd'] = $this -> access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this ->_processResponse($result, $parameters);
    
    return $this->lastStatus;
  
    
  }
  
 
  /*
   * Get_Fax_Inbox - retrieves a list of all faxes received for a given time period
   * USAGE
   * $srFax -> Get_Fax_Inbox(array(
   *   'sResponseFormat'   =>  '', //OPTIONAL - XML or JSON(default)
   *   'sPeriod'           =>  '', //OPTIONAL - RANGE or ALL (default)
   *   'sStartDate'        =>  '', //OPTIONAL - Only required when using RANGE - must be YYYYMMDD
   *   'sEndDate'          =>  '', //OPTIONAL - Only required when usiing RANGE - must be YYYYMMDD
   *   'sViewedStatus'     =>  '', //OPTIONAL - UNREAD, READ, or ALL (default)
   *   'sIncludeSubUsers'  =>  '', //OPTIONAL - Set to Y to include all faxes received by sub users of the account
   * ));
  */
  public function Get_Fax_Inbox($parameters = array()) {
    
    $optionalFields = array('sResponseFormat', 'sPeriod', 'sStartDate', 'sEndDate', 'sViewedStatus', 'sIncludeSubUsers', 'sFaxDetailsID');
    
    $postVariables = $this->_preparePostVariables(array(), $optionalFields, $parameters);
    
    $postVariables['action']     = "Get_Fax_Inbox";
    $postVariables['access_id']  = $this -> access_id;
    $postVariables['access_pwd'] = $this -> access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);

    return $this->lastStatus;

  }
  
  
  /*
   * Get_Fax_Outbox - retrieves a list of all faxes sent for a given time period
   * USAGE
   * $srFax -> Get_Fax_Outbox(array(
   *   'sResponseFormat'   =>  '', //OPTIONAL - XML or JSON(default)
   *   'sPeriod'           =>  '', //OPTIONAL - RANGE or ALL (default)
   *   'sStartDate'        =>  '', //OPTIONAL - Only required when using RANGE - must be YYYYMMDD
   *   'sEndDate'          =>  '', //OPTIONAL - Only required when usiing RANGE - must be YYYYMMDD
   *   'sIncludeSubUsers'  =>  '', //OPTIONAL - Set to Y to include all faxes received by sub users of the account
   * ));
  */
  public function Get_Fax_Outbox($parameters = array()){
    
    $optionalFields = array('sResponseFormat', 'sPeriod', 'sStartDate', 'sEndDate', 'sIncludeSubUsers');
    
    $postVariables = $this->_preparePostVariables(array(), $optionalFields, $parameters);
    $postVariables['action']     = "Get_Fax_Outbox";
    $postVariables['access_id']  = $this -> access_id;
    $postVariables['access_pwd'] = $this -> access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
    
  }
  
  
  
  /*
   * Retrieve_Fax - Returns a specified sent or received fax file in PDF or TIFF format
   * USAGE
   * $srFax -> Retrieve_Fax(array(
   *  'sFaxFileName'     =>  '', //REQUIRED - sFaxFileName returned by Get_Fax_Inbox or Get_Fax_Outbox
   *  'sFaxDetailsID'    =>  '', //REQUIRED - sFaxDetailsID returned byy Get_Fax_Inbox, Get_Fax_Outbox, or Queue_Fax (Use either this or sFaxFileName)
   *  'sDirection'       =>  '', //REQUIRED - IN or OUT (inbound or outbound)
   *  'sFaxFormat'       =>  '', //OPTIONAL - PDF or TIFF - uses account settings if not specified
   *  'sMarkasViewed'    =>  '', //OPTIONAL - Y (mark as viewed after retrieving) or N (leave status as is)(default)
   *  'sSubUserID'       =>  '', //OPTIONAL - The account number of your sub account, allowing a head office to retrieve a sub account's fax
   *  'sResponseFormat'  =>  '', //OPTIONAL - XML or JSON(default)
   * ));
  */
  public function Retrieve_Fax($parameters) {
    $requiredFields = array('sFaxFileName|sFaxDetailsID', 'sDirection');
    $optionalFields = array('sFaxFormat', 'sMarkasViewed', 'sResponseFormat', 'sSubUserID');
    
    $this->_validateRequiredFields($requiredFields, $parameters);
    $postVariables = $this->_preparePostVariables($requiredFields, $optionalFields, $parameters);
    $postVariables['action']     = "Retrieve_Fax";
    $postVariables['access_id']  = $this->access_id;
    $postVariables['access_pwd'] = $this->access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
    
    
  }
  
  /*
   * Update_Viewed_Status - mark a fax as either read or unread
   * 
   * USAGE
   * $srFax -> Update_Viewed_Status(array(
   *  'sFaxFileName'    =>  '', //REQUIRED - sFaxFileName returned by Get_Fax_Inbox or Get_Fax_Outbox
   *  'sDirection'      =>  '', //REQUIRED - IN or OUT (inbound or outbound)
   *  'sMarkasViewed'   =>  '', //REQUIRED - Y (mark as read) or N (mark as unread)
   *  'sResponseFormat' =>  '', //OPTIONAL - XML or JSON(default)
   * ));
  */
  public function Update_Viewed_Status($parameters) {
      $requiredFields = array('sFaxFileName|sFaxDetailsID', 'sDirection', 'sMarkasViewed');
      $optionalFields = array('sResponseFormat');
      
      $this->_validateRequiredFields($requiredFields, $parameters);
      $postVariables = $this->_preparePostVariables($requiredFields, $optionalFields, $parameters);
      $postVariables['action']     = "Update_Viewed_Status";
      $postVariables['access_id']  = $this->access_id;
      $postVariables['access_pwd'] = $this->access_pwd;
      
      $result = $this->_processRequest($postVariables);
      $this->_processResponse($result, $parameters);
      
      return $this->lastStatus;
      
  }
  
  /*
   * Delete_Fax - delete a fax or faxes from the server
   *
   * USAGE
   * $srFax -> Delete_Fax(array(
   *   'sDirection'       =>  '', //REQUIRED - IN or OUT for inbound or outbound fax
   *   'sFaxFileName_*'   =>  '', //REQUIRED - sFaxFileName returned by Get_Fax_Inbox or Get_Fax_Outbox.  Multiple files can be deleted by replacing * with a sequential number
   *   'sResponseFormat'  =>  '', //OPTIONAL - XML or JSON(default)
   *   'sSubUserID        =>  '', //OPTIONAL - The Account Number of a valid sub user if using the master account to delete a sub users's fax
   * ));
   * 
   */
  public function Delete_Fax($parameters) {
    
    $requiredFields = array('sDirection', 'sFaxFileName_*|sFaxDetailsID_*');
    $optionalFields = array('sResponseFormat', 'sSubUserID');
    
    $this->_validateRequiredFields($requiredFields, $parameters);
    $postVariables = $this->_preparePostVariables($requiredFields, $optionalFields, $parameters);
    $postVariables['action']     = "Delete_Fax";
    $postVariables['access_id']  = $this->access_id;
    $postVariables['access_pwd'] = $this->access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
  }
  
  /*
   * Stop_Fax - attempts to stop a fax that has been queued
   *
   * USAGE
   * $srFax -> Stop_Fax(array(
   *  'sFaxDetailsID'    =>  '', //REQUIRED - sFaxDetailsID returned by Queue_Fax
   *  'sResponseFormat'  =>  '', //OPTIONAL - XML or JSON(default)
   * ));
  */
  
  public function Stop_Fax($parameters) {
    $requiredFields = array('sFaxDetailsID');
    $optionalFields = array('sResponseFormat');
    
    $this->_validateRequiredFields($requiredFields, $parameters);
    $postVariables = $this->_preparePostVariables($requiredFields, $optionalFields, $parameters);
    $postVariables['action'] = "Stop_Fax";
    $postVariables['access_id'] = $this->access_id;
    $postVariables['access_pwd'] = $this->access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
    
  }
  
  /*
   * Get_Fax_Usage - Usage report for a specified user and period
   *
   * USAGE
   * $srFax -> Get_Fax_Usage(array(
   *  'sResponseFormat'   =>  '', //OPTIONAL - XML or JSON(default)
   *  'sPeriod'           =>  '', //OPTIONAL - RANGE or ALL(default)
   *  'sStartDate'        =>  '', //OPTIONAL - Only required when using RANGE. Must be YYYYMMDD
   *  'sEndDate'          =>  '', //OPTIONAL - Only required when using RANGE.  Must be YYYYMMDD
   *  'sIncludeSubUsers'  =>  '', //OPTIONAL - Set to Y to include all usage by sub users of the account
   * ));
   * 
   */
  
  public function Get_Fax_Usage($parameters = array()){
    
    $optionalFields = array('sResponseFormat', 'sPeriod', 'sStartDate', 'sEndDate', 'sIncludeSubUsers');
    
    $postVariables = $this->_preparePostVariables(array(), $optionalFields, $parameters);
    $postVariables['action']     = "Get_Fax_Usage";
    $postVariables['access_id']  = $this->access_id;
    $postVariables['access_pwd'] = $this->access_pwd;
    
    $result = $this->_processRequest($postVariables);
    $this->_processResponse($result, $parameters);
    
    return $this->lastStatus;
    
  }
  
  // public method returns a boolean retrieved from the last request processed
  public function getRequestStatus () {
    return $this->lastStatus;  
  }
  
  // public method returns the parsed response of the last request processed
  public function getRequestResponse() {
    return $this->lastResult;
  }
  
  // public function saves the last API response to a file
  public function saveLastResponseAsFile($destinationFileName, $destinationFolder = "/", $base64decode = true) {
    $path = $destinationFolder.$destinationFileName;

    $fileContents = $this->lastResult;
    
    if ($base64decode) {
        $fileContents = base64_decode($fileContents);
    }
    
    return file_put_contents($path, $fileContents);
  
  }
  
  /************************************** INTERNAL FUNCTIONS ************************************************/
  
  
  // utility function throws exception when required fields are missing
  protected function _validateRequiredFields($requiredFields, $parameters) {
    
    foreach ($requiredFields as $field) {
      
      if(substr($field, -1) == "*" && strpos($field, "|") === false) { // non piped wildcard variable.  check for first instance
        $fieldPrefix   = substr($field, 0, strlen($field) - 1 );
        $wildCardField = $fieldPrefix . "1";
        if (!array_key_exists($wildCardField, $parameters)) {
          throw new Exception("Required Field missing.  No values for $fieldPrefix");
        }
        elseif (!$parameters[$wildCardField]) {
          throw new Exception("Required Field missing. No values for $fieldPrefix");
        }
      }
      else {
          
        if (strpos($field, "|") !== false) { // 1 of the pipe separated fields must be present.
            $checkSuccessful = false;
            $tmpFields       = explode("|", $field);
            
            foreach ($tmpFields as $tmpField) {
                $tmpField = trim($tmpField);
                
                if (substr($tmpField, -1) == "*") { // piped wildcard field
                    $fieldPrefix = substr($tmpField, 0, strlen($tmpField) - 1);
                    $wildCardField = $fieldPrefix . "1";
                    
                    if (array_key_exists($wildCardField, $parameters)) {
                        if (!empty($parameters[$wildCardField])) {
                            $checkSuccessful = true;
                        }
                    }
                }
                else {
                    if (array_key_exists($tmpField, $parameters)) {
                        if(!empty($parameters[$tmpField])) {
                            $checkSuccessful = true;
                        }
                    }
                }
            }
            
            if (!$checkSuccessful) { // none of the piped fields are present.
                throw new Exception ("Required field missing.  You must provide at lease 1 of the following: " . implode(",", $tmpFields));
            }
            
        }
        else {
          if (!array_key_exists($field, $parameters)) {
            throw new Exception("Required field $field is missing!");
          }
          elseif (!$parameters[$field]) { // parameter is present but empty
            throw new Exception("Required field $field is missing!");
          }
        }
        
      }
    }
      
   
     return;
    
  }

  // returns array of post variables 
  protected function _preparePostVariables($requiredField, $optionalFields, $parameters) {
    
    $inputVariables = array_merge($requiredField, $optionalFields);
    $variables      = array();
    
    foreach ($inputVariables as $field) {
      
      if (substr($field, -1) == "*" && strpos($field, "|") === false) { // non-piped field uses a wild card
        $fieldPrefix = substr($field, 0, strlen($field) - 1);
        $variables   = array_merge($variables, $this->_getWildcardVariables($fieldPrefix, $parameters));
      }
      elseif(strpos($field, "|") !== false) { // collection of piped fields
          foreach (explode("|", $field) as $pipedField) {
              $pipedField = trim($pipedField);
              if (substr($pipedField, -1) == "*") { // piped wildcard field
                  $fieldPrefix = substr($pipedField, 0, strlen($pipedField) -1 );
                  $variables   = array_merge($variables, $this->_getWildcardVariables($fieldPrefix, $parameters));
              }
              else {
              
                  if (array_key_exists($pipedField, $parameters)) {
                      if ($parameters[$pipedField]) {
                          $variables[$pipedField] = $parameters[$pipedField];
                      }
                  }
              }
          }
      }
      elseif(array_key_exists($field, $parameters)) {
        if ($parameters[$field]) {
          $variables[$field] = $parameters[$field];
        }
      }
    }
   
    return $variables;
  }
  
  // utility function prepares wildcard variables and returns the array
  protected function _getWildcardVariables($fieldPrefix, $parameters) {
    $wildCardVariables = array();
    $suffix            = 1;
    
    $done = false;
    
    while (!$done) {
      
      if (!array_key_exists($fieldPrefix.$suffix, $parameters)) { $done = true; } // field doesn't exist, so stop
      elseif (!$parameters[$fieldPrefix.$suffix])               { $done = true; } // field is empty, so stop
      else {
        $wildCardVariables[$fieldPrefix.$suffix] = $parameters[$fieldPrefix.$suffix];
      }
      
      $suffix++;
      
      // fail safe to prevent infinite loop.  Will only allow for a maximum of 1000 instances per wildcard
      if ($suffix > 1000) { $done = true; }
      
    }
    
    return $wildCardVariables;
    
  }
  
  // method executes cURL call from prepared post variables
  protected function _processRequest($postVariables) {
    
    $result  = "";
    
    $postFields = http_build_query($postVariables);
    
    $curlDefaults = array(
       CURLOPT_POST           => 1,
       CURLOPT_HEADER         => 0,
       CURLOPT_URL            => $this->serverUrl,
       CURLOPT_FRESH_CONNECT  => 1,
       CURLOPT_RETURNTRANSFER => 1,
       CURLOPT_FORBID_REUSE   => 1,
       CURLOPT_TIMEOUT        => 90,
       CURLOPT_SSL_VERIFYPEER => 1,
       CURLOPT_SSL_VERIFYHOST => 2,
       CURLOPT_POSTFIELDS     => $postFields,
     );
  
    $ch = curl_init();
    curl_setopt_array($ch, $curlDefaults);
    
    $result = curl_exec($ch);
    
    if (curl_errno($ch)) {
        
     $curlError  = curl_error($ch);
      
      if (strpos($postFields, "=XML") !== false) { // format error as XML
         $result = '<?xml version="1.0" encoding="UTF-8"?> <Response> <Status>Failed</Status> <Result>' . $curlError . '</Result> </Response>'; 
      }
      else { // format error as JSON
         $result = json_encode( array('Status' => 'Failed', 'Result' => $curlError) );
      }
      
    }
    
    curl_close($ch);
    return $result;
  
  }
  
  // utility function to process results returned from the API
  protected function _processResponse($response, $parameters) {
  
    if (array_key_exists('sResponseFormat', $parameters)) {
      if ($parameters['sResponseFormat'] == "JSON" || $parameters['sResponseFormat'] == "" ) {
        $jsonObj = json_decode($response);
        $this -> lastResult = $jsonObj->Result;
        $this -> lastStatus = $jsonObj->Status == "Success" ? true : false;
        
      }
      else {
        list($status, $result) = $this->_parseXml($response);
        $this-> lastResult = $result;
        $this-> lastStatus = $status == "Success" ? true : false;
        
      }
    }
    else { // JSON encoding is default
      
      $jsonObj = json_decode($response);
      $this -> lastResult = $jsonObj->Result;
      $this -> lastStatus = $jsonObj->Status == "Success" ? true : false;
    }
    
    return; 
  }
  
  // utility function parses out "Status" and "Result" from returned XML
  protected function _parseXml($rawXML) {
    $status = $this->_getSubStringBetween($rawXML, "<Status>", "</Status>");
    $result = $this->_getSubStringBetween($rawXML, "</Status>", "</Response>");
    
    
    return array($status, $result);
    
  }
  
  // utility function returns a substring found between the first start/end pair
  protected function _getSubStringBetween($string, $start, $end) {
    
    $stringArray = explode($start, $string);
    if (isset($stringArray[1])) {
      $stringArray = explode($end, $stringArray[1]);
      return trim($stringArray[0]);
    }
    
    return "";
    
  }
  
} // class ends


?>
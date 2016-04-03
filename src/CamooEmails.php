<?php namespace Camoo;
/**
 *
 * CAMOO SARL: http://www.camoo.cm
 * @copyright (c) camoo.cm
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: CamooEmails.php
 * updated: Apr 2015
 * Created by: Epiphane Tchabom (e.tchabom@camoo.cm)
 * Description: CAMOO Emailing LIB
 *
 * @link http://www.camoo.cm
 */

/**
 * Class Camoo\Emails handles the methods and properties of sending an SMS message.
 *
 * Usage: $oEmail = new \Camoo\Emails ( $account_key, $account_password );
 * Methods:
 *     Send ( $hData )
 *     displayOverview( $camoo_response=null )
 *     responseRequest()
 *
 *
 */
use stdClass;

class Emails {

        // Camoo account credentials
        private $cm_key     = NULL;
        private $cm_secret  = NULL;

        /**
         * @var string Camoo server URI
         *
         * We're sticking with the JSON interface here since json
         * parsing is built into PHP and requires no extensions.
         * This will also keep any debugging to a minimum due to
         * not worrying about which parser is being used.
         */
        private $camoo_email_uri = 'https://api.camoo.cm/v1/email.json';


        /**
         * @var array The most recent parsed Camoo response.
         */
        private $camoo_response = '';

        // Current message
        public $message_id = NULL;
        protected $_ResponseRequest = NULL;

        // A few options
        public $ssl_verify = false; // Verify Camoo SSL before sending any message


        public function __construct ($api_key, $api_secret) {
                $this->cm_key = $api_key;
                $this->cm_secret = $api_secret;
        }

        /**
         * Prepare new text message.
         *
         *@param Array $hData, contains the needed data
         *@return object(stdClass)
         */
        public function Send($hData) {
                if ( !array_key_exists('type', $hData) ) {
                        $hData['type'] = 'text';
                }
                return $this->sendRequest($hData);
        }

        /**
         * Return the orginal camoo response
         *
         *@return object(stdClass)
         */
        public function responseRequest() {
                return $this->_ResponseRequest;
        }

        /**
         * Prepare and send a new message.
         */
        private function sendRequest ( $hPost ) {
                // Build the post data
                $hData = array_merge($hPost, ['api_key' => $this->cm_key, 'api_secret' => $this->cm_secret]);
                $sData = http_build_query($hData);


                // If available, use CURL
                if (function_exists('curl_version')) {

                        $to_camoo = curl_init( $this->camoo_email_uri );
                        curl_setopt( $to_camoo, CURLOPT_POST, true );
                        curl_setopt( $to_camoo, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $to_camoo, CURLOPT_POSTFIELDS, $sData );

                        if (!$this->ssl_verify) {
                                curl_setopt( $to_camoo, CURLOPT_SSL_VERIFYPEER, false);
                        }

                        $from_camoo = curl_exec( $to_camoo );
                        curl_close ( $to_camoo );

                } elseif (ini_get('allow_url_fopen')) {
                        // No CURL available so try the awesome file_get_contents

                        $opts = array('http' =>
                                        array(
                                                'method'  => 'POST',
                                                'header'  => 'Content-type: application/x-www-form-urlencoded',
                                                'content' => $sData
                                             )
                                     );
                        $context = stream_context_create($opts);
                        $from_camoo = file_get_contents($this->camoo_email_uri, false, $context);

                } else {
                        // No way of sending a HTTP post
                        return false;
                }
                $this->_ResponseRequest = json_decode($from_camoo);
                return $this->camooParse();
        }


        /**
         * Recursively normalise any key names in an object, removing unwanted characters
         */
        private function normaliseKeys ($obj) {

                // Determine is working with a class or araay
                if ( is_object($obj) ) {
                        $new_obj = new stdClass();
                        $is_obj = true;
                } else {
                        $new_obj = array();
                        $is_obj = false;
                }

                foreach($obj as $key => $val){
                        // If we come across another class/array, normalise it
                        if ($val instanceof stdClass || is_array($val)) {
                                $val = $this->normaliseKeys($val);
                        }

                        // Replace any unwanted characters in they key name
                        if ($is_obj) {
                                $new_obj->{str_replace('-', '', $key)} = $val;
                        } else {
                                $new_obj[str_replace('-', '', $key)] = $val;
                        }
                }

                return $new_obj;
        }


        /**
         * Parse server response.
         */
        private function camooParse() {
                $response = $this->_ResponseRequest;

                // Copy the response data into an object, removing any '-' characters from the key
                $response_obj = $this->normaliseKeys($response);
                $response_obj = $response_obj->email;

                if ($response_obj) {
                        $this->camoo_response = $response_obj;
                        return $response_obj;

                } else {
                        // A malformed response
                        $this->camoo_response = [];
                        return false;
                }
        }


        /**
         * @Brief Display a brief overview of a sent message.
         * Useful for debugging and quick-start purposes.
         */
        public function displayOverview( $oResponse=NULL ){
                $orInfo = ( $oResponse !== null ) ? $this->camoo_response : $oResponse;
                if (!$oResponse || $oResponse->code != 200 ) return 'Cannot display an overview of this response';
                $oInfo = $oResponse->message;
                // How many messages were sent?
                if ( $oInfo->messagecount > 1 ) {

                        $status = 'Your message was sent to ' . $oInfo->messagecount . ' recipients';

                } elseif ( $oInfo->messagecount == 1) {

                        $status = 'Your message was sent';

                } else {

                        return 'There was an error sending your message';
                }

                // Build an array of each message status and ID
                $message_status = [];
                $tmp = array('id'=>'', 'status'=>0);

                if ( $oInfo->status != 0) {
                        $tmp['status'] = $oInfo->errortext;
                } else {
                        $tmp['status'] = 'OK';
                        $tmp['id'] = $oInfo->messageid;
                }

                $message_status[] = $tmp;

                // Build the output
                if (isset($_SERVER['HTTP_HOST'])) {
                        // HTML output
                        $ret = '<table><tr><td colspan="2">'.$status.'</td></tr>';
                        $ret .= '<tr><th>Status</th><th>Message ID</th></tr>';
                        foreach ($message_status as $mstat) {
                                $ret .= '<tr><td>'.$mstat['status'].'</td><td>'.$mstat['id'].'</td></tr>';
                        }
                        $ret .= '</table>';

                } else {

                        // CLI output
                        $ret = "$status:\n";

                        // Get the sizes for the table
                        $out_sizes = array('id'=>strlen('Message ID'), 'status'=>strlen('Status'));
                        foreach ($message_status as $mstat) {
                                if ($out_sizes['id'] < strlen($mstat['id'])) {
                                        $out_sizes['id'] = strlen($mstat['id']);
                                }
                                if ($out_sizes['status'] < strlen($mstat['status'])) {
                                        $out_sizes['status'] = strlen($mstat['status']);
                                }
                        }

                        $ret .= '  '.str_pad('Status', $out_sizes['status'], ' ').'   ';
                        $ret .= str_pad('Message ID', $out_sizes['id'], ' ')."\n";
                        foreach ($message_status as $mstat) {
                                $ret .= '  '.str_pad($mstat['status'], $out_sizes['status'], ' ').'   ';
                                $ret .= str_pad($mstat['id'], $out_sizes['id'], ' ')."\n";
                        }
                }

                return $ret;
        }
}

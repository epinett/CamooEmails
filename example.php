<?php
include_once( "src/CamooEmails.php" );
/**
 * @Brief Send an Email
 *
 */
// Step 1: Declare new Emails.
$oEmail = new \Camoo\Emails('api_key', 'api_secret');
// Step 2: Use Send($hData) method to send a message.
$orEmail = $oEmail->Send(
                [
                'to'         => 'foo@yahoo.fr',
                'fromEmail'   => 'your.email@your-domain.tld',
                'fromName'    => 'Your Company',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer mailing system!']
                );

//var_dump($oEmail->responseRequest());
// Optional
// Step 3: Display an overview of the message
echo $oEmail->displayOverview($orEmail);
// Done!
?>

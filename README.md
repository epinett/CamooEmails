# CamooEmails
Send emails from your application with CamooEmails API

---

Important: This library requires PHP 5.3 or higher.


Quick Examples

  default all messages are sending with type "text"

1) Sending an email

    $oEmail = new \Camoo\Emails('account_key', 'account_secret');
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );


2) Display an overview of a successfully sent message

    echo $oEmail->displayOverview($orEmail);


Add fromName
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'fromName'   => 'your Name',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );


Add replyTo
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'replyTo'     => 'my-account@yahoo.fr',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );
      
Define Message type as text
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'type'     => 'text',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );

Define Message type as html
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'type'     => 'html',
                'subject'     => 'Test subject',
                'message'     => 'Hello <b>kmer</b> world!'
                ]
            );

Add cc
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'cc'   => 'partner@foo.com',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );
            
            
Add bcc
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'bcc'   => 'manager@bar.com',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );
            
            
Extended expample
-------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'cc'          => 'partner@foo.com',
                'bcc'         => 'my-boss@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'fromName'    => 'Your Company',
                'replyTo'     => 'my-account@yahoo.fr',
                'subject'     => 'Test subject',
                'type'        => 'html',
                'message'     => 'Hello <b>kmer</b> world!']
                );

Add multiple recipients
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => array('foo@bar.com', 'bar@foo.com', 'foo2@bar.com'),
                'fromEmail'   => 'your.email@your-domain.tld',
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );

Add multiple Ccs
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'cc'   => array('partner@foo.com', 'friend@foo.com'),
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );
            
            
Add multiple Bccs
------------
    $orEmail = $oEmail->Send(
                [
                'to'         => 'foo@bar.com',
                'fromEmail'   => 'your.email@your-domain.tld',
                'bcc'   => array('manager@bar.com', 'boss@bar.com'),
                'subject'     => 'Test subject',
                'message'     => 'Hello kmer world!'
                ]
            );
            

Most Frequent Issues
--------------------

Sending a message returns false.

    This is usually due to your webserver unable to send a request to CAMOO. Make sure the following are met:

  1) Either CURL is enabled for your PHP installation or the PHP option 'allow_url_fopen' is set to 1 (default).

  2) You have no firewalls blocking access to https://api.camoo.cm/v1/email.json on port 443.


Your message appears to have been sent but you do not recieve it.

    Run the example.php file included. This will show any errors that are returned from CAMOO.

NOTE: The total message size is limited to 20,480,000 bytes, or approximately 19.0MB

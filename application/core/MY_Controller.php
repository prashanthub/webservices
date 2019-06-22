<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{



     public function __construct()
	{
		parent::__construct();
        date_default_timezone_set('Asia/Kolkata');

    
    }

    
    public function sendmail($email, $subject, $message, $file = NULL, $from = NULL) {


        require_once(APPPATH.'libraries/PHPMailer/PHPMailerAutoload.php');
        
        $mail = new PHPMailer; 
        $mail->From = "pamphlet@jagsal.com"; 
        $mail->FromName = "Pamphlet"; 
        $mail->addAddress("$email", "Recipient Name"); //Provide file path and name of the attachments 
        //$mail->addAttachment("file.txt", "File.txt");    
        //$mail->addAttachment("images/profile.png"); //Filename is optional 
        $mail->isHTML(true); 
        $mail->Subject = $subject; 
        $mail->Body = "<i>Mail body in HTML</i>"; 
        $mail->AltBody = "This is the plain text version of the email content"; 
        if(!$mail->send()) 
        { 
        echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        else 
        { 
        echo "Message has been sent successfully"; 
        }
    }

}

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @param $to = array(
 *                  array('email@domain.tld', 'Full Name'),
 *                  array('email2@domain2.tld, 'Another Name')
 *                  );
 */
function send_email($to = array(), $subject = '', $message = '', $from = '', $from_name = '', $cc = array(), $bcc = array(), $attachments = array(), $html = FALSE)
{
    require_once("phpmailer/class.phpmailer.php");

    $mail = new PHPMailer();

    $mail->FromName = $from_name;
    $mail->From = $from;
    $mail->Subject = $subject;
    $mail->Sender = 'admin@enoahisolution.com';
    
    if ($html)
    {
        $mail->IsHTML(true);
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
    }
    else
    {
        $mail->Body = $message;
    }
    
    $mail->IsSMTP();
    $mail->Host = '192.168.0.227';
    
    if (is_array($to))
    {
        foreach ($to as $address)
        {
            if (is_array($address))
            {
                $mail->AddAddress($address[0], $address[1]);
            }
            else
            {
                $mail->AddAddress($address);
            }
        }
    }
    
    if (is_array($cc))
    {
        foreach ($cc as $address)
        {
            if (is_array($address))
            {
                $mail->AddCC($address[0], $address[1]);
            }
            else
            {
                $mail->AddCC($address);
            }
        }
    }
    
    if (is_array($bcc))
    {
        foreach ($bcc as $address)
        {
            if (is_array($address))
            {
                $mail->AddBCC($address[0], $address[1]);
            }
            else
            {
                $mail->AddBCC($address);
            }
        }
    }
    
    foreach ($attachments as $attachment)
    {
        if (count($attachment) == 2 && file_exists($attachment[0]))
        {
            $mail->AddAttachment($attachment[0], $attachment[1]);
        }
    }
    
    
    if ($mail->Send())
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

/* End of file phpmailer_pi.php */
/* Location: ./system/plugins/phpmailer_pi.php */

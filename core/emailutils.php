<?php
	require_once 'core/vendor/PHPMailer/PHPMailerAutoload.php';

	function GenerateUrlKey()
	{
		return md5(microtime().rand());
	}

	function SendEmail(
		$toEmail,
		$toFirstName,
		$toLastName,
		$from,
		$subject,
		$body,
		$attachments,
		&$resultString
		)
	{
		DBG_ENTER(DBGZ_CORE, __FUNCTION__, "toEmail=$toEmail, toFirstName=$toFirstName, toLastName=$toLastName, from=$from, subject=$subject");

		$mail = new PHPMailer();

		//Tell PHPMailer to use SMTP
		$mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;

		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		//$mail->Host = 'email-smtp.us-west-2.amazonaws.com';
		$mail->Host = 'smtp.gmail.com';

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;

		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		//$mail->Username = "AKIAIT3NWIPZHOETK5MQ";
			$mail->Username = "abcp0449@gmail.com";
		//Password to use for SMTP authentication
		//$mail->Password = "AszxeKjrNzRAZIkyCs2EX9x9BYSybPZ3khC9p73TWemp";
			$mail->Password = "vensai@123";
		//Set who the message is to be sent from
		$mail->setFrom('abcp0449@gmail.com', 'IDAX');

		//Set who the message is to be sent to
		$mail->addAddress($toEmail, "{$toFirstName} {$toLastName}");

		//Set the subject line
		$mail->Subject = $subject;

		// create message body HTML string
		$HTMLString = $body;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($HTMLString,  dirname(__FILE__));

		if ($attachments != NULL)
		{
			foreach ($attachments as &$attachment)
			{
				if ($attachment["type"] == "string")
				{
					$mail->AddStringAttachment($attachment["string"], $attachment["name"]);
				}
				else
				{
					$mail->AddAttachment($attachment["string"], $attachment["name"]);
				}
			}
		}

		//send the message
		$result = $mail->send();

		if (!$result)
		{
			DBG_ERR(DBGZ_CORE, __FUNCTION__, "Error sending email - ErrorInfo = '$mail->ErrorInfo'");
		}

		DBG_RETURN_BOOL(DBGZ_CORE, __FUNCTION__, $result);
		return $result;
	}
?>

<?php
	require 'phpmailer/PHPMailerAutoload.php';
	header('Access-Control-Allow-Origin: *');

	// Read user input (sent from runtime.js as JSON object)
	$json = file_get_contents('php://input');
	$obj = json_decode($json);

	// Initialize new mail object
	$mail = new PHPMailer;

	// Enable verbose debug output
	$mail->SMTPDebug = 3;

	// Inject user specified configurations
	$mail->isSMTP();
	$mail->Host = $obj->smtp_server;
	$mail->SMTPAuth = true;
	$mail->Username = $obj->from_mail;
	$mail->Password = $obj->password;
	$mail->SMTPSecure = $obj->security;
	$mail->Port = $obj->port;

	// Set sender and recipient
	$mail->setFrom($obj->from_mail, $obj->name_from);
	$mail->addAddress($obj->recipient, $obj->recipient);
	//$mail->addAddress('ellen@example.com');
	//$mail->addReplyTo('info@example.com', 'Information');
	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');

	// Build attachment
	$image=$obj->file;
	$filename=$obj->attachment_name;

	$data = substr($image, strpos($image, ","));
	$filedata = base64_decode($data);
	$f = finfo_open();
	$mime_type = finfo_buffer($f, $filedata, FILEINFO_MIME_TYPE);
	$encoding = "base64";
	$type = $mime_type;

	// Attach if attachment exists
	if ($filedata)
		$mail->AddStringAttachment($filedata, $filename, $encoding, $type);

	// Set email format to HTML
	$mail->isHTML(true);
	$mail->Subject = $obj->subject;
	$mail->Body    = $obj->body;
	$mail->AltBody = $obj->body;

	// Send email and report back the result
	if (!$mail->send()) {
		if ($obj->debug == true)
		{
			echo 'Message could not be sent.';
			echo 'Error: ' . $mail->ErrorInfo;
		}
	} else {
	    echo 'OK';
	}
?>

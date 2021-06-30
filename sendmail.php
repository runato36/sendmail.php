<?php
error_reporting( E_ALL );

// send mail function
function sendmail($data, $addressee) : bool
{
	$subject = "Mail with JSON attachment";
	// message
	$body = "JSON File in attachment";

	// get json content
	$content = $data;

	// split content
	$content = chunk_split(base64_encode($content));

	$uid = md5(time());

	$name = "jsonFormat.json";

	$eol = PHP_EOL;
	// build headers
	$header = "From: no-replay@domain.com ".$eol;
	$header .= "MIME-Version: 1.0\r\n";
	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"";

	// build message
	$message = "--".$uid.$eol;
	$message .= "Content-Type: text/plain; charset=utf-8".$eol;
	$message .= "Content-Transfer-Encoding: 8bit".$eol;
	// NEW : Add X-Message-ID to add message content to send
	$message .= "X-Message-ID: ".rand(1000,99999).$eol.$eol;
	$message .= $body.$eol;

	// attachment
	$message .= "--".$uid.$eol;
	$message .= "Content-Type: application/json; name=\"".$name."\"".$eol;
	$message .= "Content-Disposition: attachment; filename=\"".$name."\"".$eol;
	$message .="Content-Transfer-Encoding: base64\r\n";
  $message .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
	$message .= $content.$eol;
	$message .= "--".$uid."--";

	return mail($addressee, $subject, $message, $header);
}

// data POST
$data = $_POST['myData'];

// NEW : Check that data is empty or not
if ($data === NULL)
{
	$responseCode = 501;
	$returnValue = array
	(
		"status" => 501,
		"redirectURL" => "error501.php?",
	);

	goto error;
}

// Json string format
// NEW : add json_decode($data) to convert string into urly Json form
$json_string = json_encode(json_decode($data, JSON_UNESCAPED_UNICODE), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// get encrypted URL
$url = $_POST['addressee'];


if (sendmail($json_string, $addressee) === false)
{
	// set return Object to JavaScript
	$return = array
	(
		"status" => 501,
		"redirectURL" => "error501.php?",
	);

	$responseCode = 501;
  goto error;
}
else
{

	// set return Object to JavaScript
	$return = array
	(
		"status" => 200,
		"redirectURL" => "danke.php?".$url,
	);

	$responseCode = 200;
	echo json_encode($return);
}

	header("Content-Type: application/json");
	http_response_code($responseCode);
	echo json_encode($returnValue);
	exit;

error:

	header("Content-Type: application/json");
	http_response_code($responseCode);
	echo json_encode($returnValue);
	exit;

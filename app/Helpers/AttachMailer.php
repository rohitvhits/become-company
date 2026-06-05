<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Config;

class AttachMailer
{

	/*
	 * @params url du document ajout�
	 */
	public static function attachFile($url, $name = "")
	{
		$documents = array();
		$attachment = chunk_split(base64_encode(file_get_contents($url)));
		//$attachment = chunk_split(base64_encode(get_data($url)));

		$handle = fopen($url, "r");  // set the file handle only for reading the file 
		$content = fread($handle, $size); // reading the file 
		fclose($handle);                  // close upon completion 

		$docName    = $name == "" ? basename($url) : $name;
		$randomHash = md5(date('r', time()));;
		$docOutput = "--PHP-alt-$randomHash--\r\n\r\n"
			. "--PHP-mixed-$randomHash\r\n"
			. "Content-Type: application/pdf; name=\"$docName\" \r\n"
			. "Content-Transfer-Encoding: base64 \r\n"
			. "Content-Disposition: attachment \r\n\r\n"
			. $content . "\r\n";
		return $documents = $docOutput;
	}
	public static function addCC($CCEmail)
	{
		return $cc = $CCEmail;
	}
	public static function sendEmail($from, $to, $subject, $html, $url = "", $cc = "")
	{
		// MAIL_DRIVER=sendmail
		// MAIL_HOST=smtp.sendgrid.net
		// MAIL_PORT=587 
		// MAIL_USERNAME=apikey
		// MAIL_PASSWORD=
		// MAIL_ENCRYPTION=ssl
		// MAIL_FROM_ADDRESS=no-reply@nybestmedicals.com
		try {

			$mail = Mail::mailer('second')->send([], [], function ($message) use ($to, $subject, $html) {
				// echo $html;exit;
				$message->to($to, "User")
					->subject($subject)->html($html);
				//     $message->bcc('hiten@virtualheight.com',"hiten");

			});
		} catch (\Throwable $th) {
			//throw $th;
			//echo  $html;
		}

		return true;
		// Mail::raw('emails.reminder', ['user' => $user], function ($m) use ($user) {
		//            $m->from('hello@app.com', 'Your Application');

		//            $m->to($user->email, $user->name)->subject('Your Reminder!');
		//        });
	}

	public static function get_data($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	private static function makeMessage($message)
	{
		$documents = array();
		$randomHash = md5(date('r', time()));;
		$messageOutput = "--PHP-mixed-$randomHash\r\n"
			. "Content-Type: multipart/alternative; boundary=PHP-alt-$randomHash\r\n\r\n"
			. "--PHP-alt-$randomHash\r\n"
			. "Content-Type: text/plain; charset='iso-8859-1'\r\n"
			. "Content-Transfer-Encoding: 7bit\r\n\r\n"
			. $message . "\r\n\r\n"
			. "--PHP-alt-$randomHash\r\n"
			. "Content-Type: text/html; charset='iso-8859-1'\r\n"
			. "Content-Transfer-Encoding: 7bit\r\n\r\n"
			. $message . "\r\n";

		foreach ($documents as $document) {
			$messageOutput .= $document;
		}
		$messageOutput .= "--PHP-mixed-$randomHash;--";
		return $messageOutput;
	}

	public static function send12($from, $to, $subject, $message, $url = "", $cc = "")
	{
		$output = SELF::makeMessage($message);
		//$output="sdfsfs";
		$randomHash = md5(date('r', time()));;
		$headers = "From: $from\r\nReply-To: $from" . "\r\n";
		if ($cc != "") {
			$headers .= 'Cc: ' . $cc . '';
		}
		$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-$randomHash\"";
		if ($url != "") {
			$headers .= SELF::attachFile($url);
		}
		echo $to;
		//echo $mail_sent=	mail("hiten@virtualheight.com","test","test");
		echo 	$mail_sent = @mail($to, $subject, $output, $headers);
		return $mail_sent ? 1 : 0;
	}
	public static function send($from, $to, $subject, $message, $url = "", $cc = "")
	{



		//attachment file path


		//header for sender info
		$headers = "From: $from" . " <" . $from . ">";
		//boundary 
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		//headers for attachment 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
		//multipart boundary 
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		//preparing attachment

		$file = $url;
		if ($url  != "") {
			$message .= "--{$mime_boundary}\n";
			$fp =    @fopen($file, "rb");
			$data =  @fread($fp, filesize($file));

			@fclose($fp);
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" .
				"Content-Description: " . basename($file) . "\n" .
				"Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" .
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

			$message .= "--{$mime_boundary}--";
			$returnpath = "-f" . $from;
		}

		//send email
		$mail = @mail($to, $subject, $message, $headers, $returnpath);

		return $mail;
	}
	public static function sendHaPatient($to, $subject, $message, $headers)
	{
		$mail = @mail($to, $subject, $message, $headers);

		return $mail;
	}

	public static function sendReferal($to, $subject, $message, $headers)
	{
		$mail = @mail($to, $subject, $message, $headers);

		return $mail;
	}

	public static function sendDocusing($from, $to, $subject, $message, $url = "", $cc = "")
	{

		//attachment file path
		//header for sender info
		$headers = "From: $from" . " <" . $from . ">";
		//boundary 
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		//headers for attachment 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
		//multipart boundary 
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		//preparing attachment

		$file = $url;
		if ($url != "") {
			$message .= "--{$mime_boundary}\n";
			$fp = @fopen($file, "rb");
			$data = @fread($fp, filesize($file));

			@fclose($fp);
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" .
				"Content-Description: " . basename($file) . "\n" .
				"Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" .
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

			$message .= "--{$mime_boundary}--";
			$returnpath = "-f" . $from;
		}

		//send email
		$mail = @mail($to, $subject, $message, $headers, $returnpath);
		return $mail;
	}

	public static function sendMultipleattachemnt($from, $to, $subject, $message, $url = array())
	{

		$headers = "From: $from" . " <" . $from . ">";
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		//$file = $url;
		foreach ($url as $file) {

			$file_name = basename($file);
			$file_size = filesize($file);

			$message .= "--{$mime_boundary}\n";
			$fp =    @fopen($file, "rb");
			$data =  @fread($fp, $file_size);
			@fclose($fp);
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"\n" .
				"Content-Description: " . $file_name . "\n" .
				"Content-Disposition: attachment;\n" . " filename=\"" . $file_name . "\"; size=" . $file_size . ";\n" .
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		}
		$message .= "--{$mime_boundary}--";
		$returnpath = "-f" . $from;


		$mail = @mail($to, $subject, $message, $headers, $returnpath);

		$mail = @mail('vishal@virtualheight.com', $subject, $message, $headers, $returnpath);
		
		return $mail;
	}

	public static function sendMultipleattachemntnew($from, $to, $subject, $message, $url = array())
	{
		$headers = "From: $from" . " <" . $from . ">";
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		//$file = $url;
		foreach ($url as $file) {

			$file_name = basename($file);
			$file_size = Self::remote_filesize($file);

			$message .= "--{$mime_boundary}\n";
			//$fp =    @fopen($file, "rb"); 
			$data =  Self::get_content($file);
			//@fclose($fp); 
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"\n" .
				"Content-Description: " . $file_name . "\n" .
				"Content-Disposition: attachment;\n" . " filename=\"" . $file_name . "\"; size=" . $file_size . ";\n" .
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		}
		$message .= "--{$mime_boundary}--";
		$returnpath = "-f" . $from;
		$mail = @mail($to, $subject, $message, $headers, $returnpath);
		$mail = @mail('vishal@virtualheight.com', $subject, $message, $headers, $returnpath);
		return $mail;
	}
	public static function get_content($url)
	{

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		ob_start();

		curl_exec($ch);
		curl_close($ch);
		$string = ob_get_contents();

		return $string;
	}
	public static function remote_filesize($url)
	{
		static $regex = '/^Content-Length: *+\K\d++$/im';
		if (!$fp = @fopen($url, 'rb')) {
			return false;
		}
		if (
			isset($http_response_header) &&
			preg_match($regex, implode("\n", $http_response_header), $matches)
		) {
			return (int)$matches[0];
		}
		return strlen(stream_get_contents($fp));
	}
}

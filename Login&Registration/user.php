<?php
// Importing Libraries in this section

//PDF Library
require_once('..\PDF\EmbedPdfLibrary.php');
//Audio Library
require_once('..\EncryptionAndDecryption\aes.php');
require 'KeyGeneration.php';
require '..\vendor\autoload.php';

include('functions.php');

//S3 Handshake


$credentials = new Aws\Credentials\Credentials('Your secret AWS Key and ID');

$s3 = new Aws\S3\S3Client([
	'version'     => 'latest',
	'region'      => 'Your region here',
	'credentials' => $credentials
]);

//Session 
session_start();
$username = $_SESSION['username'];


// Fetching UID number
$conn = mysqli_connect("localhost", "root", "");
mysqli_select_db($conn, "project1");
$sql = "Select UID from users where username='$username'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) != 0) {
	while ($row = mysqli_fetch_assoc($result))
		$uid = $row['UID'];


	//uploadbutton
	if (isset($_POST["Upload"])) {

		$file_name = $_FILES['file']['name'];
		$file_tmp = $_FILES['file']['tmp_name'];

		move_uploaded_file($file_tmp, "UploadedFiles/" . $file_name);
		$pdfsrc = "UploadedFiles/" . $file_name;
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$ext = strtolower($ext);

		//PDF EMBEDDING
		if ($ext == "pdf") {
			//Embedding data section
			$binary = encodeUID($uid);
			$embed_data = makeUIDPdf($binary);
			embedPdf($pdfsrc, $embed_data);
			//echo "<br> PDF File uploaded";
		}
	
		else {
			echo "<script type='text/javascript'>alert('File Not Supported!');</script>";
		}

		// Encryption 
		$encryptedFile = encryption("UploadedFiles/" . $file_name, generatekey($uid), "UploadedFiles/" . $file_name . ".encrypt");

		//Cloud Storage

		try { //putting an object
			$result = $s3->putObject([
				'Bucket' => 'user' . $uid,
				'Key'    => $uid . "_" . $file_name . '.encrypt',
				'SourceFile' => $encryptedFile
			]);
			$message = "File Uploaded!";
			echo "<script type='text/javascript'>alert('$message');</script>";
		} catch (S3Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}
}

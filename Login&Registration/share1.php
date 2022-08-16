<?php

//Libraries Section
require 'C:Path to Composer Autoload php File';
require_once('..\EncryptionAndDecryption\aes.php');  //Encryption Library
include('functions.php');  //Image Library
require_once('..\PDF\EmbedPdfLibrary.php'); //PDF Library
require 'KeyGeneration.php';

if (isset($_POST['Share'])) {


	$target_Username = $_POST['target_user'];
	if ($target_Username == " ") {
		echo "Error : No Username Entered!";
	} else {

		//S3 Handshake

		$credentials = new Aws\Credentials\Credentials('Your secret AWS Key and ID');

		$s3 = new Aws\S3\S3Client([
			'version'     => 'latest',
			'region'      => 'Your AWS Region',
			'credentials' => $credentials
		]);

		//Session 
		session_start();
		$username = $_SESSION['username'];


		// Fetching Source User UID number
		$conn = mysqli_connect("", "", "");
		mysqli_select_db($conn, "project1");
		$sql = "Select UID from users where username='$username'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) != 0) {
			while ($row = mysqli_fetch_assoc($result))
				$source_uid = $row['UID'];
		}

		//Fetching Target UID
		$sql = "Select UID from users where username='$target_Username'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) != 0) {
			while ($row = mysqli_fetch_assoc($result))
				$target_uid = $row['UID'];
		}

		$filename = $_POST['varname'];
		$sourceBucket = 'user' . $source_uid; //bucket of user who is sharing
		$sourceKeyname = $filename; //file name
		$targetBucket = 'user' . $target_uid; //bucket of reciever

		//Download File from Cloud Code
		$s3->getObject(array(
			'Bucket' => 'user' . $source_uid,
			'Key'    => $filename,
			'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
			'SaveAs' => 'UploadedFiles/' . $filename
		));

		//Decryption of File
		$arrays = explode("_", $filename);
		$author_uid = $arrays[0];
		$dest = str_replace(".encrypt", "", $filename);
		$key = generatekey($author_uid);
		$decryptedFile = decryption("UploadedFiles/" . $filename, $key, $dest);


		//Extension Fetching
		$filename = str_replace(".encrypt", "", $filename);
		$ext = pathinfo("UploadedFiles/" . $filename, PATHINFO_EXTENSION);
		$ext = strtolower($ext);
		//echo $ext;

		$target_file = realpath($decryptedFile);
		//PDF EMBEDDING
		if ($ext == "pdf") {
			$binary = encodeUID($target_uid);
			$embed_data = makeUIDPdf($binary);
			embedPdf($decryptedFile, $embed_data);
			$encryptedFilepdf = encryption($decryptedFile, $key, 'Share/' . $decryptedFile . ".encrypt");
			try { //putting an object
				$result = $s3->putObject([
					'Bucket' => $targetBucket,
					'Key'    => $sourceKeyname,
					'SourceFile' => $encryptedFilepdf
				]);
			} catch (S3Exception $e) {
				echo $e->getMessage() . PHP_EOL;
			}
			$message = "Your File has been Shared!";
			echo "<script type='text/javascript'>alert('$message');</script>";
		}
	}
}

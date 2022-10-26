<?php
require '..\vendor\autoload.php';
require_once('..\EncryptionAndDecryption\aes.php');
require 'KeyGeneration.php';
if(isset($_POST['Download'])){

$credentials = new Aws\Credentials\Credentials('AKIASU53LD6JEZQLWFFQ','pD1G9bjNpTSRPltIxdTej+zlEv4mjqHX1e+gI4cc');


	$s3 = new Aws\S3\S3Client([
	    'version'     => 'latest',
	    'region'      => 'ap-south-1',
	    'credentials' => $credentials
	]);	

	//Session 
	session_start();
	$username = $_SESSION['username'];
	
	
	// Fetching Source User UID number
	$conn = mysqli_connect("localhost","root","");//Your Database info here
	mysqli_select_db($conn,"project1");
	$sql = "Select UID from users where username='$username'";
	$result = mysqli_query($conn,$sql);
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_assoc($result))
			$source_uid = $row['UID'];
	}
	$filename = $_POST['varname'];
//Download File from Cloud Code
	$s3->getObject(array(
    'Bucket' => 'user-bucket'.$source_uid,
    'Key'    => $filename,
    'ResponseContentDisposition' => 'attachment; filename="'.$filename.'"',
    'SaveAs' => 'Download/'.$filename
    ));
    $arrays = explode("_",$filename);
    $author_uid = $arrays[0];
    $dest = str_replace(".encrypt","",$filename);
	$key = generatekey($author_uid);	
		$decryptedFile = decryption("Download/".$filename,$key,"Download/".$dest);
		echo "<script type='text/javascript'>alert('Downloaded!');</script>";
    }
?>
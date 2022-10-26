<?php
require '..\vendor\autoload.php';

if(isset($_POST['Delete'])){

$credentials = new Aws\Credentials\Credentials('','');

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
			$uid = $row['UID'];
	}
$filename = $_POST['varname'];
	$sourceBucket = 'user-bucket'.$uid;
	$sourceKeyname = $filename;//file name

$s3->deleteObject([
    'Bucket' => $sourceBucket,
    'Key'    => $sourceKeyname
]);
echo "<script type='text/javascript'>alert('Your File has been Deleted');</script>";
}
?>

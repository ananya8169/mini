<?php
require '..\vendor\autoload.php';
// S3 Handshake

$credentials = new Aws\Credentials\Credentials('AKIASU53LD6JEZQLWFFQ', 'pD1G9bjNpTSRPltIxdTej+zlEv4mjqHX1e+gI4cc');
					$s3 = new Aws\S3\S3Client([//create s3 cient
					    'version'     => 'latest',
					    'region'      => 'ap-south-1',
					    'credentials' => $credentials
					]);	
	

	if($_SERVER['REQUEST_METHOD']=="GET"){
			$localhost = "localhost";
		$username = "root";
		$password = "";
		$db = "project1";
		
		$name = $_GET['name'];
		$user = $_GET['user'];
		$pass = $_GET['pwd'];
		$gender = $_GET['s'];
		$dob = $_GET['date'];
		$email= $_GET['email'];
		$aadhar = $_GET['aadhar'];
		$pan = $_GET['pan'];
		$address = $_GET['address'];
		$phone = $_GET['phone'];
		
		$conn = mysqli_connect($localhost,$username,$password,$db);
		if(!$conn)
			echo "Connection error : " .mysqli_connect_error();
		else{
		
			$sql = "Insert into Users(Name,Username,Password,Gender,DOB,Email,Aadhar,PAN,Address,Phone) values('$name','$user','$pass','$gender','$dob','$email','$aadhar','$pan','$address','$phone')";
			if($status = mysqli_query($conn,$sql)){
				echo "Data Inserted";
				$sql = "Select UID from users where username='$user'";
				$result = mysqli_query($conn,$sql);
				
				if(mysqli_num_rows($result)!=0){
					while($row = mysqli_fetch_assoc($result))
						$uid = $row['UID'];
				echo $uid;		
				}

			//Bucket creation code
				
				$BUCKET_NAME = 'user-bucket'.$uid;
				try {//create new bucket
				    $result = $s3->createBucket([
				        'Bucket' => $BUCKET_NAME,
				   ]);
				} catch (Aws\Exception\AwsException $e) {
					echo $e->getMessage() . PHP_EOL;
				}
				include("Admin.html");
			}
			else{
				echo "Data not inserted".$status;
			}
		}
	}


?>
<script type="text/javascript">
function uploadFinish()
{
		opener.location="MyAlbum.php";
		close();
	}
</script>
 

<?php
session_start();
extract($_POST); 

include "./Common/Functions.php";
include "./Common/FunctionsUploadPic.php";
include './Common/PageHeader.php';

$link = connectToDatabase();

$selectEmail="SELECT Email from User";

if ( !isset($_SESSION['userEmail']) && $_SESSION['userEmail'] != $selectEmail )
	{
		//header("Location: Login.php");
		//exit();
		
		echo "<script type='text/javascript'> window.close(); </script>";
	}



$txtEmail=$_SESSION['userEmail'];
$userName=$_SESSION['userName'];



define(IMAGE_MAX_WIDTH, 600);
define(IMAGE_MAX_HEIGHT, 400);

//define(THUMB_DESTINATION, "./thumbnails");  
define(THUMB_MAX_WIDTH, 100);
define(THUMB_MAX_HEIGHT, 100);

//Use an array to hold supported image types for convenience
$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);



$destination = './original';       	// define the path to a folder to save the file
$sampled_destination = './sampled';       	// define the path to a folder to save the file
$thumbnails_destination = './thumbnails';       	// define the path to a folder to save the file
	
	if (!file_exists($destination))
	{
		mkdir($destination);
	}
	
	if (!file_exists($sampled_destination))
	{
		mkdir($sampled_destination);
	}
	
	if (!file_exists($thumbnails_destination))
	{
		mkdir($thumbnails_destination);
	}

$valid=true;

if (isset($btnUpload)) 

{
	if(trim($txtTitle) == "")
	{
		$error2= "Title can not be blank!";
		$valid=false;
	}
	
	for ($j = 0; $j < count($_FILES['txtUpload']['tmp_name']); $j++)
	{
	
		if ($_FILES['txtUpload']['error'][$j] == 0)
		{
			
			$fileTempPath = $_FILES['txtUpload']['tmp_name'][$j];
			$filePath = $destination."/".$_FILES['txtUpload']['name'][$j];
		
			$pathInfo = pathinfo($filePath);
			$dir = $pathInfo['dirname'];
			$fileName = $pathInfo['filename'];
			$ext = $pathInfo['extension'];
			
			$fileFullName=$fileName.".".$ext;
				
			$i="";
			while (file_exists($filePath))
			{	
				$i++;
				$filePath = $dir."/".$fileName."_".$i.".".$ext;
				
				$fileFullName=$fileName."_".$i.".".$ext;
			}
			
			$imageDetails = getimagesize($fileTempPath);
		
		    if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes))
		    {
				if($valid==true)
				{
				    move_uploaded_file($fileTempPath, $filePath);
								
				   resamplePicture($filePath, $sampled_destination, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
				   resamplePicture($filePath, $thumbnails_destination, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
				}
				   
			}
			else
		    {
			   $ErrorMsg= "Uploaded file is not a supported type<br/>"; 
			   $valid=false;
			   //unlink($filePath);
		    }
			
		}	
		elseif ($_FILES['txtUpload']['error'][$j]  == 1)
		{			
			$ErrorMsg= "$fileName is too large<br/>";
			$valid=false;
		}
		elseif ($_FILES['txtUpload']['error'][$j]  == 4)
		{
			$ErrorMsg= "No upload file specified<br/>"; 
			$valid=false;
		}
		else
		{
			$ErrorMsg= "Error happened while uploading the file(s). Try again late<br/>"; 
			$valid=false;
		}
	
	if($valid==true){
	       $link = connectToDatabase();
			
			$selectUserId="Select UserId from User Where Email='$txtEmail'";
			
		   if ($result = mysqli_query($link, $selectUserId))
            {
		        $userId = mysqli_fetch_row($result);
            }
		    else
		    {
			   echo "Query fail! Error:  ". mysqli_error($link);
			   $valid=false;
		    }
			
			$Title = $txtTitle;
			$sanitizedDescription = $txtDesp;


			    $pictureInsert = "INSERT INTO Picture (OwnerId, FileName, Title, Description) VALUES(?, ?, ?, ?)";
			    $preparedpictureInsert = mysqli_prepare($link, $pictureInsert);
			    mysqli_stmt_bind_param($preparedpictureInsert, 'isss', $userId[0], $fileFullName, $Title, $sanitizedDescription);					 
			    
				
			if (!mysqli_stmt_execute($preparedpictureInsert))
		    {
				$valid=false;
			    echo "Insert fail! Error: ". mysqli_error($link);
				mysqli_close($link);	
				die( "System is currently unavailable, please try later." );
			}
	}
	
	}
}


?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload File</title>
<link rel="stylesheet" type="text/css" href="Styles.css" />
    
 </head>

<body>

<h4>Upload pictures(accepted picture types: JPEG, GIF,PNG)</h4>

<form action="UploadImage.php" method="post"  enctype="multipart/form-data">
        <table >
            <tbody id="tblUpload">
                <tr>
                    <th> File to Upload: </th> 
					<td><input type="file" text="Browse..." name="txtUpload[]" size="40"/></td>
                     <td style="color:Red"><?php echo $ErrorMsg; ?></td> 
                </tr>
                <tr>
			        <th>Title:</th>
			        <td><input type="text" name="txtTitle" size="60" /></td>
			        <td style="color:Red"><?php echo $error2; ?></td>
		       </tr>
               <tr>
		 	        <th>Description:</th>
                    <td><textarea name="txtDesp"  cols="62" rows="5"></textarea></td>
               </tr>
               <tr><td>&nbsp;</td></tr>
		      <tr><td>&nbsp;</td>
			<td>
				<input type="submit" name="btnUpload" value="Upload" class="button" />&nbsp;&nbsp;
				<input type="submit" name="btnDone" value="Done" class='button' onclick="uploadFinish()"/>
			</td>
		</tr>
     
            </tbody>
        </table> 

 </form>
<?php 
include './Common/PageFooter.php';
?>

</body>
</html>
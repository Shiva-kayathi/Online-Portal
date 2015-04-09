<?php

session_start();
extract($_GET); 

include './Common/Functions.php';
include './Common/FunctionsUploadPic.php';
$link = connectToDatabase();

$selectEmail="SELECT Email from User";

if ( !isset($_SESSION['userEmail']) && $_SESSION['userEmail'] != $selectEmail )
	{
		header("Location: Login.php");
		exit();
	}



$txtEmail=$_SESSION['userEmail'];
$name=$_SESSION['userName'] ;




define(IMAGE_MAX_WIDTH, 600);
define(IMAGE_MAX_HEIGHT, 400);

//define(THUMB_DESTINATION, "./thumbnails");  
define(THUMB_MAX_WIDTH, 100);
define(THUMB_MAX_HEIGHT, 100);

//Use an array to hold supported image types for convenience
$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);

//$link = connectToDatabase();

$selectUserId="Select UserId from User Where Email='$txtEmail'";
			
if ($result = mysqli_query($link, $selectUserId))
{
	$userId = mysqli_fetch_row($result);
}
else
{
	echo "Query fail! Error:  ". mysqli_error($link);
}
	


if(!isset($_SESSION['$start']))
{
	$_SESSION['$start'] =0;
}

$selectPic_full="Select FileName, Title, Description from Picture Where OwnerId='$userId[0]'";

$total_pic=mysqli_num_rows(mysqli_query($link, $selectPic_full));

$start=$_SESSION['$start'];

if(isset($next))
{ 
    if($next==1){//if click the next link
		if($start<($total_pic-7)){
		   $start=$start+7;
	       $_SESSION['$start'] =$start;
		}
	}
	else //else(next==0), click the pre link
	{
		if($start>=7){
	      $start=$start-7;
	      $_SESSION['$start'] =$start;
		}
	}
}


$selectPic="Select FileName, Title, Description from Picture Where OwnerId='$userId[0]' Limit $start,7";
		
if ($result = mysqli_query($link, $selectPic))
{
	
}
else
{
	echo "Query fail! Error:  ". mysqli_error($link);
}	
		
if ($result1 = mysqli_query($link, $selectPic))
{
	
}
else
{
	echo "Query fail! Error:  ". mysqli_error($link);
}			

mysqli_close($link);

if(mysqli_num_rows($result)==0)
{
	$style="visibility:hidden"; //If the database of the Picture=0, hide all the element;
	
	$start_pic=0;
    $end_pic=0;
	//header("Location: UploadImage.php");
	//exit();
}
else
{
	$start_pic=$start+1;
    $end_pic=min($start_pic+6,$total_pic);//fetch the minum of the total picture
}
	

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

include './Common/PageHeader.php';

$j=0;
 while($sel_pic= mysqli_fetch_row($result1))
 {
	 $temp_name="saved".$j;	 
	 
	 if($j==0)
	 {
		 $source_pic=$sampled_destination."/".$sel_pic[0];
		 $title_pic=$sel_pic[1];
		 $des_pic=htmlspecialchars($sel_pic[2]);
	 }
	 
	 if (isset($_GET["$temp_name"]))
	 {
		 $source_pic= $sampled_destination."/".$sel_pic[0];
		 $title_pic=$sel_pic[1];
		 $des_pic=htmlspecialchars($sel_pic[2]);
		 break;
	 }
     $j++;
 }

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload File-lab5</title>
<link rel="stylesheet" type="text/css" href="Styles.css" />


</head>

<body>
<h1 style="text-align:center;"><?php echo $name;?>'s Album</h1>


<span class='error'><?php echo $error;?></span>
<form action="MyAlbum.php" method="get"  id="theForm" enctype="multipart/form-data">
<?php print "<div  id='div1' style='$style'>";      
?>

<table align="center">
            <tbody id="tblUpload">
                <tr>
                    <td colspan="2" style="text-align:center;"><?php echo "<h3>$title_pic</h3>";?></td>
                </tr>
                <tr> 
					<td >
						<?php echo "<input type='image' name='picture' src='$source_pic'/>";?>
	 
					</td> 
                    <td  style="max-height:400; max-width:200;" >
						<?php echo "$des_pic";?>
					</td>
                </tr>
            </tbody>
        </table> 
        
 <hr/>

<?php
echo "<table align='center'>";
echo "<tr><td>";
 $j=0;
 while($sel_pic= mysqli_fetch_row($result))
 {
	 
	 $source= $thumbnails_destination."/".$sel_pic[0];
	 $temp_name="saved".$j;	 
	 echo"&nbsp;&nbsp; ";
	 print("<input  type='image' name='$temp_name' src='$source' onclick='javascript: document.forms['theForm'].submit();' value='left'/>");		 
    
     $j++;
 }
 echo"</td></tr>";
   //echo "<table align='center'>";
   $url1="MyAlbum.php?next=0";
   echo"<br/>";
   echo "<tr><td align='left'><a href=$url1>&lt;prev</a>";
   echo"&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; 
        &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
		&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;
		&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;
		&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;
       Displaying $start_pic to $end_pic of total $total_pic pictures";
   $url2="MyAlbum.php?next=1";
   echo "&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;
         &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
		 &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;
		 &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;
		 &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;
	     <a href=$url2>next&gt;</a></td></tr>";
   echo "</table>";
   echo "</div>";
?>

 </form>

<?php 
include './Common/PageFooter.php';
?>
</body>
</html>
<?php 
	session_start();
	include "./Common/Functions.php";
	

	
	extract($_POST);
	
	$error = false;
	$idErrorMsg = '';
	$pswdErrorMsg = '';
	$nameErrorMsg = '';
	$valid = true;
	
	if(isset($btnRegister))
	{
		$link = connectToDatabase();
		$idErrorMsg = validateEmail(trim($txtEmail)); 
		if($idErrorMsg != "")
		{
			$error = true;
		}
		$countUser = "SELECT count(Email) FROM User WHERE Email='$txtEmail'"; 
				
        if ($result = mysqli_query($link, $countUser ))
        {
		   $User = mysqli_fetch_row($result);
           
		   if($User[0]==1)
				{
					$valid = false;
			        $idErrorMsg .= "A user with this Email already exists";
				}
           
         }
		 else
		 {
			 echo "Query fail! Error:  ". mysqli_error($link);
		 }
		 
		$nameErrorMsg = validateName(trim($txtName));
		if($nameErrorMsg != "")
		{
			$error = true;
		}
		$pswdErrorMsg = validatePassword(trim($txtPswd));
		if($pswdErrorMsg != "")
		{
			$error = true;
		}
		$pswdReErrorMsg = validatePassword2($txtRePswd, $txtPswd);
		if( $pswdReErrorMsg != "" )
		{
			$error = true;
		}
		
		if (!$error)
		{
			$link = connectToDatabase();
			
			$hashedPswd = sha1($txtPswd);
			$txtEmail = htmlentities($txtEmail);
			$txtName = htmlentities($txtName);
			
			$userInsert = "INSERT INTO User (Email, Name, Password) VALUES(?, ?, ?)";
			$preparedUserInsert = mysqli_prepare($link, $userInsert);
			mysqli_stmt_bind_param($preparedUserInsert, 'sss', $txtEmail, $txtName, $hashedPswd);					 
	
			if (mysqli_stmt_execute($preparedUserInsert))
			{			
				//$_SESSION['UserEmail'] = $txtEmail;
				$_SESSION['name'] = $txtName;
				header("Location: Login.php");
				mysqli_close($link);
				exit();
			}
			else
			{
				mysqli_close($link);
				die("The system is not available, try again later");
			}
		}
	}
	
	include './Common/PageHeader.php';
?>
<h3>To create an account, enter your Email, name and a new password</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
	<table>
		<tr>
			<th>Email:</th>
			<td><input type='text' name='txtEmail' size='30' />
			</td><td style='color:Red'><?php echo $idErrorMsg; ?></td>
		</tr>
		<tr>
			<th>Your Name:</th>
			<td><input type='text' name='txtName' size='30' />
			</td><td style='color:Red'><?php echo $nameErrorMsg; ?></td>
		</tr>
		<tr>
			<th>Create a Password:</th>
			<td><input type='password' name='txtPswd' size='30' /></td>
			<td style='color:Red'><?php echo $pswdErrorMsg; ?></td>
		</tr>
		<tr>
			<th>Re-Enter Password:</th>
			<td><input type='password' name='txtRePswd' size='30' /></td>
			<td style='color:Red'><?php echo $pswdErrorMsg; ?></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type='submit' name='btnRegister' value='Register' class='button' />&nbsp;&nbsp;
				<input type='reset' value='Reset' class='button'/>
			</td>
		</tr>
	</table>
</form>

<?php 
include './Common/PageFooter.php';
?>

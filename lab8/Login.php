<?php 
    
	session_start();
	include './Common/Functions.php';
	
   if(isset($_SESSION['userName'])) unset($_SESSION['userName']);
   if(isset($_SESSION['userEmail'])) unset($_SESSION['userEmail']);

	
	extract($_POST);
	$error = false;
	$idErrorMsg = '';
	$pswdErrorMsg = '';
	
	if(isset($btnLogin))
	{
		if(trim($txtEmail) == "")
		{
			$idErrorMsg = "Email can not be blank!";
			$error = true;
		}
		if(trim($txtPswd) == "")
		{
			$pswdErrorMsg = "Password can not be blank!";
			$error = true;
		}
		
		if (!$error)
		{
			$link = connectToDatabase();
			
			$hashedPswd = sha1($txtPswd);
			
			$selectUser = "SELECT Email, Name FROM User WHERE Email = ? and Password = ?";
			
			$preparedSelectUser = mysqli_prepare($link, $selectUser);

			mysqli_stmt_bind_param($preparedSelectUser, 'ss', $txtEmail, $hashedPswd);
			mysqli_stmt_execute($preparedSelectUser);
			mysqli_stmt_bind_result($preparedSelectUser, $Email, $name);
			mysqli_stmt_fetch($preparedSelectUser);
		
			if ($Email === $txtEmail)
			{				
				$_SESSION['userName'] = $name; 
				$_SESSION['userEmail'] = $txtEmail;
				$_SESSION['$start']=0;
				header("Location: MyAlbum.php");
				mysqli_close($link);
				exit();
			}
			else
			{
				$pswdErrorMsg = "Incorrect Email and Password Combination! $Email, $name";
			}
		}
	}
	
	include './Common/PageHeader.php';
?>
<h4>Please Enter your Student ID and Password</h4>
<p>If you are a new user, you need to <a href='Register.php'>register</a> first.<br/></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
	<table>
		<tr>
			<th>Email:</th>
			<td><input type='text' name='txtEmail' size='30' />
			</td><td style='color:Red'><?php echo $idErrorMsg; ?></td>
		</tr>
		<tr>
			<th>Password:</th>
			<td><input type='password' name='txtPswd' size='30' /></td>
			<td style='color:Red'><?php echo $pswdErrorMsg; ?></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type='submit' name='btnLogin' value='Login' class='button' />&nbsp;&nbsp;
				<input type='reset' value='Clear' class='button' />
			</td>
		</tr>
	</table>
</form>

<?php 
include './Common/PageFooter.php';
?>

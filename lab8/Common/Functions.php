<?php
function connectToDatabase()
{
	$link = mysqli_connect('localhost', 'PHPSCRIPT', '1234', 'Album');
	if (!$link)
	{
		echo mysqli_connect_error( );
		die("Error occured while processing your request, please try again later.");
	}
	return $link;
}
function validateEmail($email)
{
	if ($email == "")
	{
		return "Email can not be blank";
	}
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
	{
      return "Invalid email format"; 
    }
	else
	{
		return "";
	}
}
function validateName($name)
{
	if ($name == "")
	{
		return "Name can not be blank";
	}
	else
	{
		return "";
	}
}

function validatePassword($password)
{
	$upperCaseRegex = "/[A-Z]/";
	$lowerCaseRegex = "/[a-z]/";
	$numericRegex = "/[0-9]/";
	$nonAlphnumericRegex = "/\W|_/";
	
	if ($password == "")
	{
		return "Password can not be blank";
	}
	elseif (strlen($password) < 6)
	{
		return "Need at least 6 characters long";
	}
	elseif(!preg_match($upperCaseRegex, $password))
	{
		return "Need at least one upper case letter";
	}
	elseif(!preg_match($lowerCaseRegex, $password))
	{
		return "Need at least one lower case letter";
	}
	elseif(!preg_match($numericRegex, $password))
	{
		return "Need at least one numeric character";
	}
	elseif(!preg_match($nonAlphnumericRegex, $password))
	{
		return "Need at least one non-alphanumeric character";
	}
	else
	{
		return "";
	}
}
function validatePassword2($password, $password2)
{
	if ($password2 == "")
	{
		return "Re-Enter Password can not be blank.";
	}
	elseif ($password != $password2)
	{
		return "Passwords do not match.";
	}
	else
	{
		return "";
	}
}

?>
<?php
$page_title = "Login";
include("acmeHeader.inc.php");
include("includes/dataaccess/UserDataAccess.inc.php");
include("includes/config.inc.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
	
	$username = $_POST['txtUser'];
	$password = $_POST['txtPassword'];
	$confirm_password = $_POST['txtConfirmPassword'];

	$user_da = new UserDataAccess(get_link());
	$user = $user_da->login($username, $password);
	//die(var_dump($user));

	if($user){
		$_SESSION['username'] = $user['user_first_name'];
		$_SESSION['role'] = $user['user_role'];
		var_dump($_SESSION);
		die();
		header("Location: index.php");
	}else{
		header("Location: transactions.php"); //Bypassing login process
	}

}
?>
    <div id="content">
        <form method="POST" action="login-screen.php">
        <br>
        User Name: <input type="text" name="txtUser" >
		<br>

		Password: <input type="password" name="txtPassword" id="txtPassword">
		<br>
		
		Confirm Password: <input type="password" name="txtConfirmPassword" id="txtConfirmPassword">
		<br>
		
		<input type="submit" name="btnSubmit" value="submit">

        </form>
    </div>
<?php
include("acmeFooter.inc.php");
?>
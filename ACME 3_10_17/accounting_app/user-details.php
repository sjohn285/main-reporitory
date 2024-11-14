<?php
$page_title = "Edit/Add User";
include("acmeHeader.inc.php");
include("acmeFooter.inc.php");
include("includes/config.inc.php");
require_once("includes/dataaccess/UserDataAccess.inc.php");
require_once("includes/dataaccess/FileDataAccess.inc.php");

$link = get_link();
$user_da = new UserDataAccess($link);

// set up an empty user obj/array
$user = array();
$user['user_id'] = 0; // set to 0, this will help us determine if we need to update or insert later
$user['user_first_name'] = "";
$user['user_last_name'] = "";
$user['user_email'] = "";
$user['user_password'] = "";
$user['user_confirm_password'] = "";
$user['user_role'] = 0; //default to being a standard user
$user['user_active'] = "yes"; // default to letting the user be 'active'
$user['user_image'] = "";

// the dir to upload user images to
// make sure to include the trailing slash.
$upload_dir = "uploaded-files/"; 


// check to see if the form is being posted (there are other ways to do this)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// The form is being posted, so we must be either
	// creating a new user, or editing an existing one

	// get all the values entered by the user
	$user['user_id'] = $_POST['txtId'];
	$user['user_first_name'] = $_POST['txtFistName'];
	$user['user_last_name'] = $_POST['txtLastName'];
	$user['user_email'] = $_POST['txtEmail'];
	$user['user_role'] = $_POST['selRole'];
	$user['user_password'] = $_POST['txtPassword'];
	$user['user_confirm_password'] = $_POST['txtConfirmPassword'];
	$user['user_active'] = $_POST['rgActive'];
	$user['user_image'] = $_POST['txtUserImage'];

	//die(var_dump($user));

	//VALIDATE THE DATA
	$error_messages = validate_input($user);

	//process the file upload
	if(isset($_FILES['picture_upload']) && !empty($_FILES['picture_upload']['name'])){
		
		// we'll allow these types of files to be uploaded
		$allowed_file_types = array('image/pjpeg','image/jpeg','image/JPG','image/X-PNG','image/PNG','image/png','image/x-png');
		
		// instantiate our data access class			
		$file_da = new FileDataAccess($link);

		// set up the array that we pass into the insert_file() method
		$file = array(
			'file_name' => $_FILES['picture_upload']['name'], 
			'file_extension' => $file_da->get_file_extension($_FILES['picture_upload']['name']), 
			'file_size' =>$_FILES['picture_upload']['size'], 
			'file_uploaded_by_id' => 1, // NOTE WE'LL CHANGE THIS LATER 
			'file_uploaded_date' => date("Y-m-d")
		);

		if($file = $file_da->insert_file($file)){
			
			// now that we've inserted a row for the file in the files table
			// lets upload the file...				
			try{
				$file_da->upload_file("picture_upload", $allowed_file_types, $upload_dir);
			}catch(Exception $e){
				$error_messages['picture_upload'] = $e->getMessage();
			}

			// Now that the image has been uploaded, let's rename it so that it
			// is named by the file id, rather than the original name
			$new_file_name = $file['file_id'] . "." . $file['file_extension'];
			
			if(rename($upload_dir . $file['file_name'], $upload_dir . $new_file_name) === FALSE){
				$error_messages['picture_upload'] = "Unable to rename file after uploading";
			}

			// update the $user array so that it inserts/updates the new file name in the users table
			$user['user_image'] = $new_file_name;
		
		}else{
			$error_messages['picture_upload'] = "Unable to insert file into db.";
		}

	}// end of file uploading process


	// if the data is valid, then the error_message array will be empty
	// and we can insert/update the user in the db
	if(empty($error_messages)){
		if($user['user_id'] > 0){
			// if the user_id is greater than 0, then we are doing an UPDATE
			$user_da->update_user($user);
		}else{
			// if the user_id is NOT greater than 0, then we are doing an INSERT
			$user_da->insert_user($user);
		}
		// We are done processing this user, redirect to user list page
		header('Location: user-list.php');
	}


}else{
	// in this case the form is NOT being posted, so we check
	// for a user_id in the query string of the URL,
	// if there is a user_id, then we use it to get all the data for the user
	// from the DB
	if(isset($_GET['user_id'])){
		$user_id = $_GET['user_id'];
		$user = $user_da->get_user_by_id($user_id);
		//THE CONFIRM PASSWORD IS A LITTLE TRICKY
		$user['user_confirm_password'] = $user['user_password'];
	}

	// Note that if there is no user_id in the query string of the URL
	// then we assume that a new user is being created, and in that case
	// our $user object is full of empty/default values (so our form will appear empty)
}

function validate_input($user){
	
	// we'll popluate an array with all the error message
	// Note that if the form is valid, then this array will be empty
	// and we'll check this to see if we should send the data to the db
	$error_messages = array();

	// first name
	if(empty($user['user_first_name'])){
		$error_messages['user_first_name'] = "You must enter a first name";
	}

	// last name
	if(empty($user['user_last_name'])){
		$error_messages['user_last_name'] = "You must enter a last name";
	}

	// email
	if(empty($user['user_email'])){
		$error_messages['user_email'] = "You must enter an email address";
	}else if(filter_var($user['user_email'], FILTER_VALIDATE_EMAIL) == false){
		$error_messages['user_email'] = "The email entered is not valid";
	}

	// password
	if(empty($user['user_password'])){
		$error_messages['user_password'] = "You must enter a password";
	}else if(empty($user['user_confirm_password'])){
		$error_messages['user_confirm_password'] = "You must confirm your password";
	}else if($user['user_password'] != $user['user_confirm_password']){
		$error_messages['user_confirm_password'] = "The passwords do not match";
	}

	// user role
	// do we need to make sure that the user role is within the valid range of user role ids?
	if($user['user_role'] == 0){
		$error_messages['user_role'] = "Please select a user role";
	}

	//active
	if( ($user['user_active'] == "yes" || $user['user_active'] == "no") == FALSE){
		$error_message['user_active'] = "Active is not valid - I suspect FOWL PLAY!";
		// send email to site admin???
	}

	return $error_messages;
}


?>

<h3>Add/Edit User</h3>
<form method="POST" action="<?php echo($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data">
	
	<input type="hidden" name="txtId" id="txtId" value="<?php echo($user['user_id']); ?>" /><br>
	
	First Name: <input type="text" name="txtFistName" id="txtFistName" value="<?php echo($user['user_first_name']); ?>"/>
	<?php echo(isset($error_messages['user_first_name']) ? $error_messages['user_first_name'] : "" ); ?>
	<br>

	Last Name: <input type="text" name="txtLastName" id="txtLastName" value="<?php echo($user['user_last_name']); ?>"/>
	<?php echo(isset($error_messages['user_last_name']) ? $error_messages['user_last_name'] : "" ); ?>
	<br>

	Email: <input type="text" name="txtEmail" id="txtEmail" value="<?php echo($user['user_email']); ?>"/>
	<?php echo(isset($error_messages['user_email']) ? $error_messages['user_email'] : "" ); ?>
	<br>
	
	Password: <input type="password" name="txtPassword" id="txtPassword" value="<?php echo($user['user_password']); ?>"/>
	<?php echo(isset($error_messages['user_password']) ? $error_messages['user_password'] : "" ); ?>
	<br>
	
	Confirm Password: <input type="password" name="txtConfirmPassword" id="txtConfirmPassword" value="<?php echo($user['user_confirm_password']); ?>"/>
	<?php echo(isset($error_messages['user_confirm_password']) ? $error_messages['user_confirm_password'] : "" ); ?>
	<br>
	
	Role: 
	<?php

	$roles = $user_da->get_user_roles();

	$select_html = '<select id="selRole" name="selRole">';
	foreach($roles as $r){
		// check to see if we need set the option as selected
		$selected_attr = ($r['user_role_id'] == $user['user_role'] ? " selected " : "");
		$select_html .= '<option value="' . $r['user_role_id'] .'" '. $selected_attr . '>' . $r['user_role_name'] . '</option>';
	}
	$select_html .= "</select>";
	echo($select_html);

	echo(isset($error_messages['user_role']) ? $error_messages['user_role'] : "" ); 
	?>
	<br>
	
	Active: 
	<input type="radio" name="rgActive" value="yes" <?php echo( $user['user_active'] == "yes" ? "checked" : "" ); ?> /> Yes 
	<input type="radio" name="rgActive" value="no" <?php echo( $user['user_active'] == "no" ? "checked" : "" ); ?> /> No
	<br>

	Picture:
	<br>
	<?php
	if(empty($user['user_image']) === FALSE){
		echo('<img id="userImg" src="' . $upload_dir . $user['user_image'] . '" />');
	}
	?>
	<br>
	<input type="hidden" name="txtUserImage" value="<?php echo($user['user_image']); ?>" />
	<input type="file" id="picture_upload" name="picture_upload" />
	<br>
	<?php echo(isset($error_messages['picture_upload']) ? $error_messages['picture_upload'] : "" ); ?>
	<br>


	<input type="submit" name="btnSubmit" value="Save Changes" />
</form>

<script>
// THIS IS TEMPORARY, But if you choose an image to upload,
// then it will clear out the existing image from being displayed.
document.getElementById("picture_upload").onchange = function(){
	var img = document.getElementById("userImg");
	if(img){
		img.src = "";
	}
};
</script>
<?php  
$page_title = "Add Transaction";
include("acmeHeader.inc.php");
include("acmeFooter.inc.php");
include("includes/config.inc.php");
require_once("includes/dataaccess/UserDataAccess.inc.php");

$link = get_link();
$user_da = new UserDataAccess($link);

// set up an empty transaction obj/array
$transaction = array();
$transaction['transactionID'] = 0;
$transaction['tName'] = "";
$transaction['tDescription'] = "";
$transaction['tDate'] = "2017-01-01";
$transaction['tAmount'] = 0.00;
$transaction['userID'] = 0;
$transaction['categoryID'] = 0;
$transaction['attachmentsID'] = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // the form is being posted, so we must be either
    // creating a new transaction, or editing an existing one
    
    // get all the values entered by the user that is logged in
    $transaction['transactionID'] = $_POST['txtUser'];
    $transaction['tName'] = $_POST['txtName'];
    $transaction['tDescription'] = $_POST['txtDescription'];
    $transaction['tDate'] = $_POST['DatDate'];
    $transaction['tAmount'] = $_POST['txtAmount'];
    $transaction['userID'] = 0;
    $transaction['categoryID'] = $_POST['selCategory'];
    $transaction['attachmentsID'] = $_POST['fileAttachment'];
    
    
    
}

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<br>
	
        <table id="tblData" width="100%" style="border: 2px solid black">
        	<tr>
                <td>User:</td>
                <td>Name:</td>
                <td>Description:</td>
        		<td>Date:</td>
        		<td>Amount:</td>
                <td>Category:</td>
        		<td>File Attachments:</td>
        	</tr>
        	<tr>
                <td><input type="text" name="txtUser" value="00001"></td>
                <td><input type="text" name="txtName" value=""></td>
                <td><textarea name="txtDescription" cols="25" rows="3">Building rent</textarea></td>
                <td><input type="date" name="datDate" value="2017-02-20"></td>
                <td><input type="text" name="txtAmount" value="1225.30"></td>
                <td><input type="text" name="selCategory" value=""></td>
                <td><input type="file" name="filAttachment"></td>
        	</tr>
        </table>
    
</body>
</html>
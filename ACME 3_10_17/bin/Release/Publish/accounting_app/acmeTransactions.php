<!DOCTYPE html>
<html>
<?php
$page_title = "ACME's Transaction Page";
include("acmeHeader.inc.php");
?>
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <title><?php echo($page_title); ?></title>
	    <meta name="description" content="">
	    <meta name="viewport" content="width=device-width">
	    <link rel=stylesheet href="css/acmeStyle.css" type="text/css">
	</head>
	<body>
    <div id="content">
    	<br></br>
        <button id="btnAdd" value="btnAddTrans">Add new transaction</button>
        <button id="btnAdd" value="btnAddCategory">Add new category</button>
        <br></br>
        <table id="tblData" width="100%" style="border: 2px solid black">
        	<tr>
        		<td width="20%">Date:</td>
        		<td width="20%">User:</td>
        		<td width="20%">Amount:</td>
        		<td width="20%">Description:</td>
        		<td width="20%">File Attachments:</td>
        	</tr>
        	<tr>
        	<td width="20%">
  				<input type="date" name="datDate" value="2017-02-20"></td>
        	<td width="20%">
  				<input type="text" name="txtUser" value="00001"></td>
  			<td width="20%">
  				<input type="text" name="txtAmount" value="1225.30"></td>
  				<td width="20%">
  				<textarea name="txtDescription" cols="25" rows="3">Building rent</textarea></td>
  				<td width="20%">
  				<input type="file" name="filAttachment"></td>
        	</tr>
        </table>
    </div>
    </body>
<?php
include("acmeFooter.inc.php");
?>
</html>
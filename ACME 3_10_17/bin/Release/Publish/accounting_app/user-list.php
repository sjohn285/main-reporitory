<?php
$page_title = "User List";
include("acmeHeader.inc.php");
include("acmeFooter.inc.php");
include("includes/config.inc.php");
require_once("includes/dataaccess/UserDataAccess.inc.php");


$link = get_link();
$user_da = new UserDataAccess($link);
$all_users = $user_da->get_all_users();

echo("<br>");
echo("<a href='user-details.php'>Add New User</a><br>");
// here's how you could use the data to create a table
echo("<table border=\"2\">");

echo("<tr>");
echo("<td>" . "First Name" . "</td>");
echo("<td>" . "Last Name" . "</td>");
echo("<td>" . "Email Address" . "</td>");
echo("</tr>");

foreach($all_users as $user){

	echo("<tr>");
	echo("<td>" . $user['user_first_name'] . "</td>");
	echo("<td>" . $user['user_last_name'] . "</td>");
	echo("<td>" . $user['user_email'] . "</td>");
	echo("<td><a href=\"user-details.php?user_id=" . $user['user_id'] . "\">EDIT</a></td>");
	echo("</tr>");
}
echo("</table>");


?>
<html>
<head>
	<title>Users</title>
</head>
<body>

</body>
</html>
<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */

include '../core/init.php';

/* Process variables */
$_POST['message']		= filter_var($_POST['message'], FILTER_SANITIZE_STRING);
$_POST['type']			= (int) $_POST['type'];
$_POST['reported_id']	= (int) $_POST['reported_id'];

/* Check for errors */
$stmt = $database->prepare("SELECT `id` FROM `reports` WHERE `type` = ? AND `reported_id` = ?");
$stmt->bind_param('ss', $_POST['type'], $_POST['reported_id']);
$stmt->execute();
$stmt->store_result();
$num_rows = $stmt->num_rows;
$stmt->fetch();
$stmt->close();

if($num_rows) {
	$errors[] = $language['errors']['already_reported'];
}
if(!$token->is_valid()) {
	$errors[] = $language['errors']['invalid_token'];
}
if(strlen($_POST['message']) > 512) {
	$errors[] = $language['errors']['message_too_long'];
}
if(strlen($_POST['message']) < 5) {
	$errors[] = $language['errors']['message_too_short'];
}

if(empty($errors)) {
	$date = new DateTime();
	$date = $date->format('Y-m-d H:i:s');

	$stmt = $database->prepare("INSERT INTO `reports` (`ip_address`, `type`, `reported_id`, `message`, `date`) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param('sssss', $_SERVER['REMOTE_ADDR'], $_POST['type'], $_POST['reported_id'], $_POST['message'], $date);
	$stmt->execute();
	$stmt->close();

	echo "success";
} else echo output_errors($errors);
?>

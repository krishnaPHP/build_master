<?php
DEFINE("PAGE_TITLE","Admin Panel");

$test123 =  "sdfsda";

error_reporting('E_ALL ^ ~E_NOTICE');
$dbhost = "127.0.0.1";

$dbuser = "adminuser@123";
$dbpwd = "adminpwd@123";
$database='hybrid_ssc';

$TABLE['SECTION_QUESTIONS'] = "iib_section_questions";
$TABLE['CASE_MASTER'] = "iib_case_master";
$TABLE['CASE_MASTER_UNICODE'] = "iib_case_master_unicode";
$TABLE['SECTION_QUESTIONS_UNICODE']="iib_section_questions_unicode";
$TABLE['SECTION_QUESTIONS_HINDI'] = "iib_section_questions_hindi";

$connect=@mysql_pconnect($dbhost,$dbuser,$dbpwd) or die("Mysql Server may not be running . Please Contact the System administrator for the help. ");
$select_db=@mysql_select_db($database) or die("Database may not exists. Please Contact the System administrator for the help.");
if(!function_exists('open_ses')) include('session_handle.php');
?>



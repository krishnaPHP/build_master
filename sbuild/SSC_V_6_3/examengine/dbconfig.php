<?php
ini_set('error_reporting',E_ALL & ~E_NOTICE);
$masterhost="127.0.0.1"; 
$slavehost = "127.0.0.1"; 
$dbuser = "clientuser@123";
$dbpwd = "clientpwd@123";


$db='hybrid_ssc';
$BROWSERAGENT = 'Sify Secure Browser V3.7';
$secureBrowser = 0;
$AGENTKEY = '!$!fy@dmIN';

$MASTERCONN='';
$SLAVECONN='';

$TABLE['SECTION_QUESTIONS'] = "iib_section_questions";
$TABLE['CASE_MASTER'] = "iib_case_master";
$TABLE['CASE_MASTER_UNICODE'] = "iib_case_master_unicode";
$TABLE['SECTION_QUESTIONS_UNICODE']="iib_section_questions_unicode";
$TABLE['SECTION_QUESTIONS_HINDI'] = "iib_section_questions_hindi";

slaveConnect();

//GET CANDID SYNC TIME FROM EXAM SETTINGS
$getCandSyncTime = "SELECT variable_value FROM exam_settings WHERE variable_name='candidate_time_sync'";
$getCandTimeresult = @mysql_query($getCandSyncTime) or errorlog('err95'," QUERY: $getCandSyncTime  ".mysql_error($SLAVECONN));
list($candSyncTime) = mysql_fetch_row($getCandTimeresult);

// Config. variable Response sync time in seconds
$local_sync_time = ($candSyncTime!='')?$candSyncTime:600;

define('TITLE','Exam');
/****** Constant value of exam engine ***/

$MEMBERSHIP_USER_LABEL	= 'Roll no';
$MEMBERSHIP_PWD_LABEL	= 'Password<br>(Date of Birth)<br>(DDMMYYYY)';	//SSC
//$MEMBERSHIP_PWD_LABEL	= 'Password';
$is_enable_feedback		= 'N';
$is_enable_scorecard	= 'N';
$is_available_DQ		= "N";
$maxAllowedChar = 30000;

/****** End constant value of exam engine ***/
if(function_exists('date_default_timezone_set'))
		date_default_timezone_set('Asia/Calcutta');
		
/**	
	Function to establish connection with the Mysql Slave server
	Output: returns Slave connection String if successfull else error message
**/
function slaveConnect($erroption=true)
{
	global $slavehost;
    global $dbuser;
	global $dbpwd;
	global $db;
	global $SLAVECONN;
	if(!is_resource($SLAVECONN)){
		$SLAVECONN=@mysql_connect($slavehost,$dbuser,$dbpwd);
		$i=0;
		while(!is_resource($SLAVECONN))
		{				
			if($i < 3 && !is_resource($SLAVECONN))
			{						
				sleep(2);			
				$SLAVECONN=@mysql_connect($slavehost,$dbuser,$dbpwd);	
				$i=$i+1;				
			}else{			   
			   break;				
			 }  
		}		
		if(!is_resource($SLAVECONN))		 		    			
				 if($erroption)
				 	 errorlog('err01',mysql_error($SLAVECONN));				 
		}									
		if(is_resource($SLAVECONN)){					
			$dbupd = mysql_select_db($db,$SLAVECONN)  or errorlog('err02',mysql_error($SLAVECONN));				
		}						
}
/**
	Function to establish connection with the Mysql Master server
	Output: returns Master connection String if successfull else error message
**/
function masterConnect($erroption=true)
{		
	global $masterhost;
    global $dbuser;
	global $dbpwd;
	global $db;
	global $MASTERCONN;
	if(!is_resource($MASTERCONN)){
		$MASTERCONN=@mysql_connect($masterhost,$dbuser,$dbpwd);		
		$i=0;
		while(!is_resource($MASTERCONN))
		{				
			if($i < 3 && !is_resource($MASTERCONN))
			{						
				sleep(2);			
				$MASTERCONN=@mysql_connect($masterhost,$dbuser,$dbpwd);	
				$i=$i+1;				
			}else{			   
			   break;				
			 }  
		}		
		if(!is_resource($MASTERCONN))		 		    			
				 if($erroption)
				 	errorlog('err03',@mysql_error($MASTERCONN));				 
		}									
		if(is_resource($MASTERCONN)){					
			$dbupd = mysql_select_db($db,$MASTERCONN)  or errorlog('err04',mysql_error($MASTERCONN));				
		}	
}

/**
	Function to close Mysql slave connection
	Output: NIL
**/
function slaveConnectClose()
{
	global $SLAVECONN;
	if(is_resource($SLAVECONN))
	  @mysql_close($SLAVECONN);
}

/**
	Function to close Mysql master connection
	Output: NIL
**/
function masterConnectClose()
{
	global $MASTERCONN;
	if(is_resource($MASTERCONN)){
		@mysql_close($MASTERCONN);
	}	
}

function errorlog($errcode,$additionalmsg='') 
{
	$arrErrorDetails=array(
		'err01'=>"Connection to MySql slave database failed.",
		'err02'=>"Slave Database - select may not exists.",	
		'err03'=>"Connection to MySql master database failed.",	
		'err04'=>"Master Database - update may not exists.",
		'err05'=>"select failed for table ",
		'err06'=>"update failed for table ",
		'err07'=>"delete failed for table ",
		'err08'=>"insert failed for table ",					
		'err10'=>" Question Paper Not Available. ",
		'err11'=>" Question Paper Assignment Not Done. ",																					 
	);
	$filename="./applicationerror_log/errorlog_".date('ymd').".log";
	$msg = $arrErrorDetails[$errcode];
	$msg.= " ".$additionalmsg. " | ".date('d-m-Y h:i:s A')." | File:".$_SERVER['REQUEST_URI']."\r\n";
	error_log($msg, 3, $filename);
	ob_end_clean();
	echo "<h2>Error Code: ".$errcode. "  <br/>Please contact administrator...";
	exit;
}


?>

<?php
define( 'ABSPATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' );
if(file_exists('cbdom_main.php'))
{
	include_once('cbdom_main.php');
}
function check_module_upload($req_data)
{
	$cbdom = new Cbdom();
	$lincese_key = $req_data['lincese_key'];
	$module_ver =  $req_data['module_version'];
	$module_name =  $req_data['module_name'];
	echo $cbdom->check_module_uploadapi($lincese_key,$module_ver,$module_name);
}
 function newmoduleupdate_now($req_data)
{
	$cbdom 				= new Cbdom();
	$lincese_key 		= $req_data['lincese_key'];
	$module_ver 		=  $req_data['module_version'];
	$module_name 		=  $req_data['module_name'];
	$newmodule_version 	=  $req_data['newmodule_version'];
	$new_cms_ver 	=  $req_data['new_cms_ver'];
	$new_cat_ver 	=  $req_data['new_cat_ver'];
	echo $cbdom->updatemodule_newversionnow($lincese_key,$module_name,$module_ver,$newmodule_version,$new_cms_ver,$new_cat_ver);
} 
$task='';
$req_data=array();

if(isset($_POST['task']))
{
	$task=$_POST['task'];
	$req_data=$_POST;
	
}
if($task=='check_module_upload')
{
	check_module_upload($req_data);
}
else if($task=='newmoduleupdate_now')
{
	newmoduleupdate_now($req_data);
}	

?>
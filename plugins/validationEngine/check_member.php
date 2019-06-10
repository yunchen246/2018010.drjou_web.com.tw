<?php
	$validateValue=$_GET['fieldValue'];
	$validateId=$_GET['fieldId'];
	$arrayToJs=array();
	$arrayToJs[0]=$validateId;
	
	include('../../libs/db.php');
	$db=new db();
	$sql_str="SELECT id FROM member WHERE account='".trim($validateValue)."';";
	$sql_re=$db->query($sql_str);
	if($db->num_rows($sql_re)>0){
		$arrayToJs[1]=false;
	}
	else{
		$arrayToJs[1]=true;
	}
	$db->close();	
	echo json_encode($arrayToJs);
?>
<?php
	if(empty($_POST)){
		unset($_POST);
		header("Location: \index.php"); 
		exit();
	}
	require_once("functions.php");
		
	
	if(isset($_POST['printInvoiceNoConnBtn'])){	
		$filter = 'no_conn';
		$steps = selectUnpayedSteps($_POST['printInvoiceNoConnBtn']);
		$contracts = selectUnpayedContracts($_POST['printInvoiceNoConnBtn']);
	}elseif(isset($_POST['printInvoiceConnBtn'])){
		$filter = 'conn';
		$steps = selectUnpayedSteps($_POST['printInvoiceConnBtn']);
		$contracts = selectUnpayedContracts($_POST['printInvoiceConnBtn']);
	}
		
	foreach($contracts as $contract){
		$clientInfo = getOrdersFrom1C($contract['ІПН/ЄДРПОУ']);
	}
	 
	if( empty($steps) or empty($contracts) or empty($clientInfo) or empty($filter) ){
		unset($_POST);
		unset($steps);
	
		header("Location: \index.php"); 
		exit();
	} 
	
	makeInvoice($clientInfo, $contracts, $steps, $filter);
?>
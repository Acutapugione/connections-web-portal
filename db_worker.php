<?php
	//must have module
    require_once "config.php";
	
	//extends modules
	require_once "dbWorkers/inserts.php";
	require_once "dbWorkers/updates.php";
	require_once "dbWorkers/selects.php";
	
	//mixed functions
	function createTable($table){
		$sqlDropText = sprintf('DROP TABLE IF EXISTS %s;', $table['name']);
		$sqlCreateText = sprintf('CREATE TABLE %s(', $table['name']);
		
		$tmp_cnt = 0;
		foreach ($table['fields'] as $field){
			if($tmp_cnt===count($table['fields'])-1){
				$sqlCreateText = sprintf("%s %s %s %s ", $sqlCreateText, $field['name'], $field['type'], $field['params']);
			} else{
				$sqlCreateText = sprintf("%s %s %s %s, ", $sqlCreateText, $field['name'], $field['type'], $field['params']);
			}
		}
		$sqlCreateText = sprintf('%s) %s ', $sqlCreateText, CHAR_SET);		
	}
	
    function deleteRecord($filter ){
        $query = sprintf(
            "DELETE FROM %s WHERE %s = '%s' ", $filter['tableName'], $filter['key'], $filter['val']);

        $rezult = mysqli_query($GLOBALS['CONNECTION'], $query);
        if(!$rezult ){
			echo '<br>on deleteRecord<br>';
            var_dump(mysqli_error($GLOBALS['CONNECTION']));
			return false;
        } 
		return true;
		
	}
	
	function deleteContract($contract_id){
		$filter = [
				'tableName' => 'conn_contracts',
				'key' 		=> 'id',
				'val'		=> $contract_id,
		];
		return deleteRecord($filter);
	}

?>
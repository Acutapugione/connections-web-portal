<?php
	require_once "config.php";
    require_once "db_worker.php";
	
	function initTables(){
		foreach( $GLOBALS['TABLES'] as $table ){
			
			mysqli_query($GLOBALS['CONNECTION'], $sqlText);
		
	}
	foreach( $GLOBALS['TABLES'] as $table ){
		
	}
	
    if(!empty(SELECT_DB)) {
        foreach ($queries as $query) {
            $myQuery = mysqli_query(CONNECTION, $query);
            if(!$myQuery){
                var_dump(mysqli_error(CONNECTION));
                echo "<p></p>";
            }
        }

    } else {
        echo "<p>Wrong db</p>";
    }
?>
<?php
    require_once "config.php";
	
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
            var_dump(mysqli_error($GLOBALS['CONNECTION']));
        } 
	}
	
    function insertNewRecord($tableName , $params){
        $fields = '';
        $values = '';
        $i = 0;
        foreach ($params as $key => $value) {
            if($i===count($params)-1){
                $fields.= $key;  
                $values.= '"'.$value.'"';
            } else {
                $fields.= $key.', ';
                $values.= '"'.$value.'", ';
            }
            $i++;    
        }
        $query = sprintf("INSERT INTO %s(%s) VALUES (%s)", $tableName, $fields, $values);
        
        $rezult = mysqli_query($GLOBALS['CONNECTION'], $query);
        if(!$rezult ){
			#var_dump($query);
			#echo '<br>';
            #var_dump(mysqli_error($GLOBALS['CONNECTION']));
			return false;
        }
		return true;
    }
	function insertClient($info){
		$params = [
			'login' => $info['login'],
			'password' => $info['password'],
			'mail' => $info['mail'],
			'ipn_key' => $info['ipn_key'],
			];
			
		return insertNewRecord('conn_clients', $params);
		/*
		INSERT INTO `conn_messages`(`id`, `message_type_id`, `src_id`, `dest_id`, `mess_tittle`, `created_at`, `deleted_at`, `mess_ctx`) 
		VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8])*/
	}
	function insertMessage($message){
		$params = [
			'message_type_id' => $message['message_type_id'],
			'src_id' => $message['client_id'],
			'mess_tittle' => $message['message_tittle'],
			'mess_ctx' => $message['message_context'],
			];
			
		return insertNewRecord('conn_messages', $params);
		/*
		INSERT INTO `conn_messages`(`id`, `message_type_id`, `src_id`, `dest_id`, `mess_tittle`, `created_at`, `deleted_at`, `mess_ctx`) 
		VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8])*/
	}
	function updateRecord($filter){
		$setText = '';
		$i = 0;
		foreach($filter["coms"] as $key => $val){
			if($i===count($filter["coms"])-1){
				$setText.=sprintf("%s='%s' ", $key, $val);
            } else {
                $setText.=sprintf("%s='%s', ", $key, $val);
			}
            $i++;
		}		
        	
		$query = sprintf("UPDATE %s SET %s WHERE %s = '%s' ", $filter['tableName'], $setText, $filter['key'], $filter['val']);
        $rezult = mysqli_query($GLOBALS['CONNECTION'], $query);
        if(!$rezult ){
            var_dump(mysqli_error($GLOBALS['CONNECTION']));
			
			echo '<br>';
			var_dump($query);
			echo '<br>';
			
			return false;
        }
		return true;
    }
	function updateClientInfo($info){
		$filter = [
			'tableName' => 'conn_clients', 
			'coms' => [
				'conn_clients.login' => $info['uname'],
				'conn_clients.password' => $info['pswd'],
				'conn_clients.mail' => $info['email'],
				'conn_clients.ipn_key' => $info['ipnKey'],
				],
			'key' => 'id',
			'val' => $info['client_id']
			];
		return updateRecord($filter);
	}
	function selectRecords($tables=[], $fields='', $req='', $groupFields='', $colNames=[], $limit=''){
		
		$sqlFrom = '';
		$sqlWhere = '';
		$res = [];
		if(empty($groupFields)){
			$groupFields = $fields;
		}
		if(!empty($req)){
			$sqlWhere = sprintf('WHERE %s', $req);
		}
		foreach($tables as $item){
			if(empty($item['joinType'])){
				$sqlFrom = $item['tableName'];
			} else {
				$sqlFrom = sprintf('%s %s %s ON %s', $sqlFrom, $item['joinType'], $item['tableName'], $item['on']);
			}
		}
		$sqlText = sprintf('SELECT %s FROM %s %s GROUP BY %s %s', $fields, $sqlFrom, $sqlWhere, $groupFields, $limit);
		#var_dump($sqlText);
		
		$conn = new mysqli(HOST, LOGIN, PASSWORD, DATA_BASE);
		mysqli_query($conn, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
		mysqli_query($conn, "SET CHARACTER SET 'utf8'");
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		$result = $conn->query($sqlText);
		#var_dump($colNames);
		if(is_array($colNames) && count($colNames)>0){
				if ($result && $result->num_rows > 0) {
				  $fields = explode(',',$fields);
				  
				  // output data of each row
				  while($row = $result->fetch_assoc()) { 
					$iter = 0;
					$tmpRow = [];
					foreach($fields as $field){
						$tmpRow[$colNames[$iter]] = $row[explode('.',$field)[1]];
						$iter++;
					}
					$res[]=$tmpRow;
				  }
				} else {
					#echo "0 results";
				}	
			}else{
				if ($result && $result->num_rows > 0) {
				$res = $result->fetch_all(MYSQLI_ASSOC);
				//var_dump($res);
				}	else {
					#echo "0 results";
				}	
			}
		
		$conn->close();
		return $res;
	}
	function selectClient($ipn_key){
		$req=sprintf('conn_clients.ipn_key = %s', $ipn_key);
		return selectRecords([['tableName'=> 'conn_clients']], 'conn_clients.id', $req);
	}
	function selectClientId($login, $password){
		$tables = [['tableName'=> 'conn_clients']];
		$fields = 'conn_clients.id';
		$req = sprintf('conn_clients.login = "%s" and conn_clients.password = "%s"', $login, $password);
		
		return selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
	}
	
	function selectClientMail($login, $password){
		$tables = [['tableName'=> 'conn_clients']];
		$fields = 'conn_clients.mail';
		$req = sprintf('conn_clients.login = "%s" and conn_clients.password = "%s"', $login, $password);
		
		return selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
	}
	
	function selectClientIpn($login, $password){
		$tables = [['tableName'=> 'conn_clients']];
		$fields = 'conn_clients.ipn_key';
		$req = sprintf('conn_clients.login = "%s" and conn_clients.password = "%s"', $login, $password);
		
		$tmp= selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
		#var_dump($tmp);
		return $tmp;
	}
	function selectClientMessageTypes($client){
		$req = sprintf('(conn_messages.src_id = %s) or (conn_messages.dest_id = %s)',$client,$client);
		$tables = [
			[
				'tableName' => 'conn_messages'],
			[
				'tableName' => 'conn_message_types',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_messages.message_type_id = conn_message_types.id',
				]
		];
		$fields = 'conn_message_types.message_type_name';
		#$groupFields = 'mess_ctx, mess_tittle';
		$colNames = ['Тема повідомлення'];
		return selectRecords($tables, $fields, $req, null, $colNames);
	}
	
	function selectClientMessagesByType($client, $message_type){
		$req = sprintf('(conn_message_types.message_type_name LIKE "%s") and ((conn_messages.src_id = %s) or (conn_messages.dest_id = %s))',
		$message_type, $client, $client);
		$tables = [
			[
				'tableName' => 'conn_messages'],
			[
				'tableName' => 'conn_message_types',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_messages.message_type_id = conn_message_types.id',
				]
		];
		$fields = 'conn_messages.created_at, conn_messages.mess_tittle, conn_messages.mess_ctx';
		#$groupFields = 'mess_ctx, mess_tittle';
		$colNames = ['Відправлено', 'Короткий зміст', 'Повний зміст' ];
		return selectRecords($tables, $fields, $req, null, $colNames);
	}
	
	function selectClientOrdersTemplate($client){
		$req=sprintf('conn_clients.id = %s', $client);
		$tables = [
	    [
		   'tableName'=> 'conn_orders',
		],
		[
			'tableName'=> 'conn_contracts',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_orders.contract_id = conn_contracts.id',
		],
		[
			'tableName'=>'conn_clients',
			'joinType'=>'LEFT JOIN',
			'on' => '(conn_contracts.ipn_key = conn_clients.ipn_key) OR (conn_contracts.id = conn_orders.contract_id)',
		]
		];
		
	   return selectRecords($tables, 'conn_orders.order_number', $req, null, ['Номер замовлення']);
    }
	
	function selectOrderStepsTemplate($order){
		$req=sprintf('conn_orders.order_number LIKE "%s"', $order);
		$tables = [
	    [
		   'tableName'=> 'conn_steps',
		],
		[
			'tableName'=> 'conn_step_types',
			'joinType'=>'LEFT JOIN',
			'on'=> 'conn_steps.step_type_id = conn_step_types.id',
		],
		[
			'tableName'=>'conn_orders',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_steps.order_id = conn_orders.id',
		]
		];
		$fieldNames = 'conn_step_types.name, conn_steps.created_at, conn_steps.total, conn_steps.payed_at, conn_steps.completed_at';
		$groupFields = 'conn_steps.step_type_id';
		$colNames = [ 'Етап робіт', 'Дата рахунку', 'Сума рахунку', 'Дата оплати', 'Дата виконання'];  
		return selectRecords($tables, $fieldNames, $req, $groupFields, $colNames);	
    }   
	
	function selectMessageTypes(){
		$req=sprintf('conn_message_types.message_type_name IS NOT NULL');
		$tables = [
	    [
		   'tableName'=> 'conn_message_types',
		]
		];
		$fieldNames = 'conn_message_types.id, conn_message_types.message_type_name';
		$groupFields = $fieldNames;
		$colNames = [ 'Код', 'Тип повідомлення'];
		return selectRecords($tables, $fieldNames, $req, $groupFields, $colNames);	
	}
?>
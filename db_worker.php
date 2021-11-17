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
			var_dump($query);
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
	}
	function insertMessage($params){
		return insertNewRecord('conn_messages', $params);
	}
	function insertOrder(array $params){
		if(
		is_array($params) 
		&& !empty($params) )
		{
			return insertNewRecord('conn_orders', $params);
		}		
	}
	function insertStep($step){
		$ctype = pushStepType($step['step_type_name']);
		#var_dump($ctype);
		$params = [
			'step_type_id' => selectStepTypeId($step['step_type_name']),
			'order_id' => $step['order_id'],
			'created_at' => $step['created_at'],
			'payed_at' => $step['payed_at'], 
			'completed_at' => $step['completed_at'], 
			'price' => $step['price'], 
			'nalog' => $step['nalog'], 
			];
			
		return insertNewRecord('conn_steps', $params);
	}
	function insertStepType($step_type_name){
		if(!selectStepTypeId($step_type_name)){
			$params = [ 
				'name' => $step_type_name,
				];
			return insertNewRecord('conn_step_types', $params);
		}
		return false;
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
	function updateOrder($order){
		$filter = [
			'tableName' => 'conn_orders', 
			'coms' => [
				'conn_orders.client_id' => $order['client_id'],
				'conn_orders.contract_id' => $order['contract_id'],
				],
			'key' => 'id',
			'val' => $order['order_id'],
			];
		return updateRecord($filter);
		
	}
	function updateStep($step){
		$ctype = pushStepType($step['step_type_name']);
		#var_dump($ctype);
		$filter = [
			'tableName' => 'conn_steps', 
			'coms' => [
				'conn_steps.payed_at' => $step['payed_at'],
				'conn_steps.completed_at' => $step['completed_at'],
				'conn_steps.price' => $step['price'],
				'conn_steps.nalog' => $step['nalog'],
				],
			'key' => 'id',
			'val' => $step['step_id'],
			];
		return updateRecord($filter);
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
			if($fields <> '*'){
				$groupFields = 'GROUP BY '.$fields;
			}
		} else {
			if($groupFields <> '*'){
				$groupFields = sprintf('GROUP BY %s',$groupFields);
			}
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
		$sqlText = sprintf('SELECT %s FROM %s %s %s %s', $fields, $sqlFrom, $sqlWhere, $groupFields, $limit);
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
	function selectStepId($type_name, $order_number){
		$tables = [
			[
				'tableName' => 'conn_steps'
			],[
				'tableName' => 'conn_step_types',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_steps.step_type_id = conn_step_types.id',
			],[
				'tableName' => 'conn_orders',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_steps.order_id = conn_orders.id',
			],
		];
		$fields = 'conn_steps.id';
		$req = sprintf('conn_step_types.name = "%s" and conn_orders.order_number = "%s"', $type_name, $order_number);
		$tmp = selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');

		if(is_array($tmp)){
			if(isset($tmp[0]['id'])){
				return $tmp[0]['id'];
			} elseif(isset($tmp['id'])){
				return $tmp['id'];
			}
		}
		return false; 
	}
	function selectClient($ipn_key){
		$req=sprintf('conn_clients.ipn_key = %s', $ipn_key);
		return selectRecords([['tableName'=> 'conn_clients']], 'conn_clients.id', $req);
	}
	function selectContractId($ipn_key){
		$tables = [ ['tableName' => 'conn_contracts' ] ];
		$fields = 'conn_contracts.id';
		$req = sprintf('conn_contracts.ipn_key = "%s"',$ipn_key);
		$tmp = selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
		
		if(is_array($tmp)){
			if(isset($tmp[0]['id'])){
				return $tmp[0]['id'];
			}
		}
		return false; 
	}
	
	function selectClientId($login, $password){
		$tables = [['tableName'=> 'conn_clients']];
		$fields = 'conn_clients.id';
		$req = sprintf('conn_clients.login = "%s" and conn_clients.password = "%s"', $login, $password);
		
		return selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
	}
	function selectStepTypeId($type_name){
		$tables = [['tableName'=> 'conn_step_types']];
		$fields = 'conn_step_types.id';
		$req = sprintf('conn_step_types.name = "%s"', $type_name);
		$tmp = selectRecords($tables, $fields, $req, null, null, 'LIMIT 1');
		
		if(is_array($tmp)){
			if(isset($tmp[0]['id'])){
				return $tmp[0]['id'];
			}
		}
		return false; 
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
		return $tmp;
	}
	function selectClientMessageTypes($client){
		$req = sprintf(
			'conn_messages.src_id = %s',
			$client
		);
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
		$colNames = ['Тип повідомлення'];
		return selectRecords($tables, $fields, $req, null, $colNames);
	}
	function selectClientMessagesByType($client_id, $type_name){
		$req = sprintf(
			'conn_messages.src_id = %s and conn_message_types.message_type_name = "%s"',
			$client_id, $type_name
		);
		$tables = [
			[
				'tableName' => 'conn_messages'],
			[
				'tableName' => 'conn_message_types',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_messages.message_type_id = conn_message_types.id',
				]
		];
		$fields = 'conn_messages.mess_tittle, conn_messages.mess_ctx, conn_messages.created_at';
		$colNames = ['Тема повідомлення', 'Текст повідомлення', 'Відправлено'];
		return selectRecords($tables, $fields, $req, null, $colNames);
	}
	function selectClientOrdersTemplate($client){
		
		$tables = [
	    [
		   'tableName'=> 'conn_clients',
		],
		[
			'tableName'=> 'conn_contracts',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_clients.ipn_key = conn_contracts.ipn_key',
		],
		[
			'tableName'=>'conn_orders',
			'joinType'=>'LEFT JOIN',
			'on' => '(conn_orders.contract_id = conn_contracts.id and conn_orders.order_number is not NULL)',
		]
		];
		$groupFields= sprintf('conn_orders.order_number, conn_clients.id 
		HAVING conn_orders.order_number is not null and conn_clients.id = %s', $client);
		
		
	   return selectRecords($tables, 'conn_orders.order_number', null, $groupFields, ['Номер замовлення']);
    }
	function selectOrderId($order_number){
		$req=sprintf('conn_orders.order_number LIKE "%s"', $order_number);
		$tables = [['tableName' => 'conn_orders']];
		$fieldNames = 'conn_orders.id';
		$tmp = selectRecords($tables, $fieldNames, $req, null, null, 'LIMIT 1');
		if(is_array($tmp)){
			if(isset($tmp[0]['id'])){
				
				return $tmp[0]['id'];
			}
		}
		return false;
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
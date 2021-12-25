<?php
	
	function selectRecords($tables=[], $fields='', $req='', $groupFields='', $colNames=[], $limit='', $order_by=''){
		
		$sqlFrom = '';
		$sqlOrder = ''; 
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
		
		if(!empty($order_by)){
			$sqlOrder = sprintf(' ORDER BY %s ', $order_by);
		}
		$sqlText = sprintf('SELECT %s FROM %s %s %s %s %s', $fields, $sqlFrom, $sqlWhere, $groupFields, $sqlOrder, $limit);
		#var_dump($sqlText);
		#echo '<br><br>';
		
		$conn = new mysqli(HOST, LOGIN, PASSWORD, DATA_BASE);
		mysqli_query($conn, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
		mysqli_query($conn, "SET CHARACTER SET 'utf8'");
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		$result = $conn->query($sqlText);

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
					//"0 results";
				}	
			}else{
				if ($result && $result->num_rows > 0) {
					$res = $result->fetch_all(MYSQLI_ASSOC);
				}	else {
					#"0 results";
				}	
			}
		$conn->close();
		return $res;
	}
	function selectAppeals($type_id, $order_id){
		$tables = array(
			array('tableName' => 'conn_appeals'),
			array(
				'tableName' => 'conn_appeal_types',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_appeals.appeal_type_id = conn_appeal_types.id',
				),
			array(
				'tableName' => 'conn_orders',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_appeals.order_id = conn_orders.id',
				),
		);
		if(!empty($type_id) and !empty($order_id)){
			$req = sprintf('conn_appeals.appeal_type_id = %s AND conn_appeals.order_id = %s', $type_id, $order_id);
		} elseif (!empty($order_id) ){
			$req = sprintf('conn_appeals.order_id = %s', $order_id);
		} elseif (!empty($type_id) ){
			$req = sprintf('conn_appeals.appeal_type_id = %s', $type_id);
		} else {
			$req = '1';
		}
		$fieldNames = 'conn_appeal_types.name, conn_appeals.text, conn_orders.order_number, conn_appeals.status';
		$colNames = ['Тип звернення', 'Текст звернення', 'Замовлення', 'Стан звернення'];
		$groupFields ='conn_appeals.order_id, conn_appeals.appeal_type_id';
		$tmp = selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, '', null);
		return $tmp;
	}
	function selectAppealsInfo($type_id, $order_id){
		if(!empty($type_id) and !empty($order_id)){
			$req = sprintf('conn_appeals.appeal_type_id = %s AND conn_appeals.order_id = %s', $type_id, $order_id);
		} elseif (!empty($order_id) ){
			$req = sprintf('conn_appeals.order_id = %s', $order_id);
		} elseif (!empty($type_id) ){
			$req = sprintf('conn_appeals.appeal_type_id = %s', $type_id);
		} else {
			$req = '1';
		}
		$tables = array(
			array('tableName' => 'conn_appeals'),
			array(
				'tableName' => 'conn_appeal_types',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_appeals.appeal_type_id = conn_appeal_types.id',
				),
			array(
				'tableName' => 'conn_orders',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_appeals.order_id = conn_orders.id',
				),
			array(
				'tableName' => 'conn_contracts',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_orders.contract_id = conn_contracts.id',
				),
			array(
				'tableName' => 'conn_clients',
				'joinType' 	=> 'LEFT JOIN',
				'on'		=> 'conn_contracts.client_id = conn_clients.id',
			)
		);
		
		$fieldNames = ('
			conn_clients.mail,
			conn_appeals.id, 
			conn_appeal_types.name, 
			conn_appeals.text,			
			conn_appeals.created_at, 
			conn_orders.address,
			conn_contracts.ipn_key');
			
		$colNames = [
			'client_email',
			'nomer',
			'tip',
			'text', 
			'data_reg', 
			'adres_object', 
			'client_ipn', 
		];
		$groupFields ='conn_appeals.id';		
		$tmp = selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, '', null);
		return $tmp;
	}
	
	function selectAppealTypes(){
		$tables = array(
			array('tableName' => 'conn_appeal_types'),
		);
		$fieldNames = 'id, name';
		$groupFields ='id, name';
		$tmp = selectRecords($tables, $fieldNames, null, $groupFields,null, '', null);
		return $tmp;
	}
	function selectUnpayedContracts($order_id){
		
		$tables = array(
			array('tableName' => 'conn_steps'),
			array(
				'tableName'=> 'conn_orders',
				'joinType'=>'LEFT JOIN',
				'on'=> 'conn_steps.order_id = conn_orders.id',
				),
			array(
				'tableName'=> 'conn_contracts',
				'joinType'=>'LEFT JOIN',
				'on'=> 'conn_orders.contract_id = conn_contracts.id',
				),
		);
		$fieldNames = 'conn_contracts.ipn_key';
		$groupFields = 'conn_contracts.ipn_key';
		$colNames = [ 'ІПН/ЄДРПОУ' ];  
		$order_by = 'conn_steps.created_at ASC';
		$req = sprintf('conn_steps.order_id = %s AND ( conn_steps.payed_at = "0000-00-00 00:00:00" or conn_steps.payed_at is NULL)', $order_id);
		
		$tmp = selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, 'LIMIT 15', $order_by);
		return $tmp;
	}
	
	function selectUnpayedSteps($order_id){
		
		$tables = array(
			array('tableName' => 'conn_steps'),
			array(
				'tableName'=> 'conn_step_types',
				'joinType'=>'LEFT JOIN',
				'on'=> 'conn_steps.step_type_id = conn_step_types.id',
				),
		);
		$fieldNames = 'conn_step_types.name, conn_steps.total, conn_steps.total, conn_steps.n_dogovor'; #conn_step_types.key_1c, 
		$groupFields = 'conn_steps.step_type_id';
		$colNames = ['Товари (роботи, послуги)', 'Ціна з ПДВ', 'Сума з ПДВ', 'n_dogovor' ];   # 'Код', 
		$order_by = 'conn_steps.created_at ASC';
		$req = sprintf('conn_steps.order_id = %s AND ( conn_steps.payed_at = "0000-00-00 00:00:00" or conn_steps.payed_at is NULL)', $order_id);
		
		$tmp = selectRecords($tables, $fieldNames, $req, $groupFields,$colNames, 'LIMIT 15', $order_by);
		return $tmp;
	}
	
	function selectOrderStepsTemplate($order_id){
		$req=sprintf('conn_orders.id = %s and ( conn_steps.deleted_at = "0000-00-00 00:00:00" or conn_steps.deleted_at is NULL)', $order_id);
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
		$colNames = [ 'Послуга', 'Дата рахунку', 'Сума рахунку', 'Дата оплати', 'Дата виконання'];  
		$order_by = 'conn_steps.created_at ASC';
		
		return selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, '', $order_by);	
    }  
	
	function selectClientIsAdmin($client_id){
		$tables = [ [ 'tableName' => 'conn_clients' ]];
		$fields = 'is_admin';
		$req = sprintf('conn_clients.id = %s', $client_id);
		return selectRecords($tables, $fields, $req, null,null, 'LIMIT 1')[0]['is_admin'];
		
	}
	function selectClients(){
		$tables = [ [ 'tableName' => 'conn_clients' ] ];
		$fields = 'conn_clients.id, conn_clients.mail, conn_clients.password';
		$colNames = ['Код', 'E-mail','Пароль'];
		return selectRecords($tables, $fields, null, null, $colNames, 'LIMIT 25'); 
	}
	function selectOrgStepId($type_name, $order_number=null, $order_id=null){
		$tables = [
			[
				'tableName' => 'conn_org_steps'
			],[
				'tableName' => 'conn_step_types',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_org_steps.step_type_id = conn_step_types.id',
			],[
				'tableName' => 'conn_orders',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_org_steps.order_id = conn_orders.id',
			],
		];
		$fields = 'conn_org_steps.id';
		if(!empty($order_number)){
			$req = sprintf('conn_step_types.name = "%s" and conn_orders.order_number = "%s"', $type_name, $order_number);
		} elseif(!empty($order_id)){
			$req = sprintf('conn_step_types.name = "%s" and conn_orders.id = "%s"', $type_name, $order_id);
		}
		
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
	function selectStepId($type_name, $order_number=null, $order_id=null){
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
		if (!empty($order_id)){
			$req = sprintf('conn_step_types.name = "%s" and conn_orders.id = "%s"', $type_name, $order_id);
		} elseif (!empty($order_number)){
			$req = sprintf('conn_step_types.name = "%s" and conn_orders.order_number = "%s"', $type_name, $order_number);
		}

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
	function selectContract($ipn, $client_id){
		$req = sprintf('conn_contracts.ipn_key = "%s" and conn_contracts.client_id = %s', $ipn, $client_id);
		return selectRecords([['tableName'=> 'conn_contracts']], 'conn_contracts.id', $req);
	}
	function selectClient($mail){
		$req=sprintf('conn_clients.mail = %s', $mail);
		return selectRecords([['tableName'=> 'conn_clients']], 'conn_clients.id', $req);
	}	
	function selectClientId($mail, $password){
		$tables = [['tableName'=> 'conn_clients']];
		$fields = 'conn_clients.id';
		$req = sprintf('conn_clients.mail = "%s" and conn_clients.password = "%s"', $mail, $password);
		
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
	function selectClientContracts($client_id){
		$tables = [
			[ 
				'tableName' => 'conn_clients'],
			[
				'tableName' => 'conn_contracts',
				'joinType' => 'LEFT JOIN',
				'on' => 'conn_clients.id = conn_contracts.client_id',
				]
		];
		$fields = 'conn_contracts.ipn_key';
		$req = sprintf('conn_clients.id = %s', $client_id);
		$colNames = ['ІПН/ЄДРПОУ'];
		$tmp = selectRecords($tables, $fields, $req, null, $colNames );
		
		foreach($tmp as $item){
			foreach($item as $key => $val){
				if(empty($val) || is_null($val) || $val === NULL){
					return false;
				}
			}
		}			
		return $tmp;
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
			'(conn_messages.src_id = %s)or(conn_messages.dest_id = %s)',
			$client,$client
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
			'((conn_messages.src_id = %s)or(conn_messages.dest_id = %s)) and conn_message_types.message_type_name = "%s"',
			$client_id, $client_id, $type_name
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
	function selectUnreadMessagesForClient($client_id){
		
		$req = sprintf('conn_messages.dest_id = %s and conn_messages.is_read = False', $client_id);
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
		
		$tmp = selectRecords($tables, $fields, $req, null, $colNames);
		return $tmp;
	}
	function selectClientOrdersTemplate($client, $contract_id=null){
		
		$tables = [
			[
			   'tableName'=> 'conn_clients',
			],
			[
				'tableName'=> 'conn_contracts',
				'joinType'=>'LEFT JOIN',
				'on' =>'conn_clients.id = conn_contracts.client_id',
			],
			[
				'tableName'=>'conn_orders',
				'joinType'=>'LEFT JOIN',
				'on' => '( conn_orders.contract_id = conn_contracts.id and conn_orders.order_number is not NULL)',
			]
		];
		$req = null;
		if(isset($contract_id)){
			$req = sprintf('conn_contracts.id = %s',$contract_id);
		}
		$groupFields= sprintf('conn_orders.order_number, conn_clients.id 
		HAVING conn_orders.order_number is not null and conn_clients.id = %s', $client);

		return selectRecords($tables, 'conn_orders.order_number', $req, $groupFields, ['Номер замовлення']);
    }
	function selectOrdersTemplate(array $orders){
		$tables = [
			[
			'tableName' => 'conn_orders',
			],
		];
		$in_list = '';
		foreach($orders as $order){
			$in_list = sprintf('%s, %s', $in_list, $order);
		}
		$req = sprintf('conn_orders.id IN(%s)',$in_list);
		
		return selectRecords($tables, 'conn_orders.order_number',$req ,null, ['Номер замовлення']);
	}
	function selectClient_order_id($client_id){
		$tables = [
			[
				'tableName'=>'conn_orders',
			],
			[
				'tableName'=>'conn_contracts',
				'joinType'=>'LEFT JOIN',
				'on' => 'conn_orders.contract_id = conn_contracts.id',
			]
		];
		$req = sprintf('conn_contracts.client_id = %s', $client_id);
		
		$fields = 'conn_orders.id, conn_orders.order_number';
		$groupFields = 'conn_orders.id';
		$colNames = [ 'id', 'Номер замовлення'];  
		
		return selectRecords($tables, $fields, $req ,$groupFields, $colNames);
	}
	function selectOrderId($order_number, $client_id=null, $contract_id=null){
		
		if(isset($client_id)&&!empty($client_id) &&isset($contract_id)&&!empty($contract_id)){
			$req=sprintf(
				'conn_orders.order_number LIKE "%s" and conn_clients.id = %s and conn_contracts.id = %s',
				$order_number, 
				$client_id,
				$contract_id
			);
		}
		elseif (isset($client_id)&&!empty($client_id)){
			$req=sprintf('conn_orders.order_number LIKE "%s" and conn_clients.id = %s', $order_number,  $client_id);
		} elseif (isset($contract_id)&&!empty($contract_id)){
			$req=sprintf('conn_orders.order_number LIKE "%s" and conn_contracts.id = %s', $order_number,  $contract_id);
		} else {
			$req=sprintf('conn_orders.order_number LIKE "%s"', $order_number);
		}
		
		$tables = [
	    [
		   'tableName'=> 'conn_clients',
		],
		[
			'tableName'=> 'conn_contracts',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_clients.id = conn_contracts.client_id',
		],
		[
			'tableName'=>'conn_orders',
			'joinType'=>'LEFT JOIN',
			'on' => '( conn_orders.contract_id = conn_contracts.id and conn_orders.order_number is not NULL)',
		]
		];

		$fieldNames = 'conn_orders.id';
		
		$tmp = selectRecords($tables, $fieldNames, $req, null, null, 'LIMIT 1');
		if(is_array($tmp)){
			if(isset($tmp[0]['id'])){
				return $tmp[0]['id'];
			}
		}
		return false;
	}
	function selectOrderSteps($order_id){
		
		$req=sprintf('conn_orders.id = %s', $order_id);
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
		$fieldNames = 'conn_step_types.id, conn_step_types.name, conn_steps.created_at, conn_steps.total, conn_steps.payed_at, conn_steps.completed_at, conn_steps.deleted_at';
		$groupFields = 'conn_steps.step_type_id';
		$colNames = [ 'step_id', 'step_type_name', 'created_at', 'price', 'payed_at', 'completed_at', 'deleted_at'];  
		$order_by = 'conn_steps.created_at ASC';
		
		return selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, '', $order_by);	
    }   
	function selectOrderOrgSteps($order_id){
		
		$req=sprintf('conn_orders.id = %s', $order_id);
		$tables = [
	    [
		   'tableName'=> 'conn_org_steps',
		],
		[
			'tableName'=> 'conn_step_types',
			'joinType'=>'LEFT JOIN',
			'on'=> 'conn_org_steps.step_type_id = conn_step_types.id',
		],
		[
			'tableName'=>'conn_orders',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_org_steps.order_id = conn_orders.id',
		]
		];
		$fieldNames = 'conn_org_steps.id, conn_step_types.name, conn_org_steps.sustain, conn_org_steps.executor, conn_org_steps.commentary, conn_org_steps.start_at, conn_org_steps.deadline_at, conn_org_steps.done_at, conn_org_steps.deleted_at';
		$groupFields = 'conn_step_types.name, conn_org_steps.sustain, conn_org_steps.executor, conn_org_steps.commentary';
		$colNames = [ 'step_id', 'step_type_name', 'sostoyaniye', 'ispolnitel', 'coment', 'data_nachala', 'srok', 'data_zaversheniya', 'deleted_at'];  
		$order_by = 'conn_org_steps.start_at ASC';
		
		return selectRecords($tables, $fieldNames, $req, null, $colNames, '', $order_by);	
    }   
	function selectOrderOrgStepsTemplate($order_id){
		
		$req=sprintf('conn_orders.id = %s and ( conn_org_steps.deleted_at = "0000-00-00 00:00:00" or conn_org_steps.deleted_at IS NULL)', $order_id);
		$tables = [
	    [
		   'tableName'=> 'conn_org_steps',
		],
		[
			'tableName'=> 'conn_step_types',
			'joinType'=>'LEFT JOIN',
			'on'=> 'conn_org_steps.step_type_id = conn_step_types.id',
		],
		[
			'tableName'=>'conn_orders',
			'joinType'=>'LEFT JOIN',
			'on' =>'conn_org_steps.order_id = conn_orders.id',
		]
		];
		$fieldNames = 'conn_step_types.name, conn_org_steps.sustain, conn_org_steps.executor, conn_org_steps.commentary, conn_org_steps.start_at, conn_org_steps.deadline_at, conn_org_steps.done_at';
		$groupFields = 'conn_step_types.name, conn_org_steps.sustain, conn_org_steps.executor, conn_org_steps.commentary';
		$colNames = [ 'Етап робіт', 'Стан виконання', 'Виконавець', 'Деталі', 'Дата початку', 'Граничний строк виконання', 'Дата завершення'];  
		$order_by = 'conn_org_steps.start_at ASC';
		
		return selectRecords($tables, $fieldNames, $req, null, $colNames, '', $order_by);	
    }   
	function selectOrderInfo($order_id){
		
		$req = sprintf('conn_orders.id = %s', $order_id);
		$tables = [['tableName'=>'conn_orders']];
		$fieldNames = 'conn_orders.address, conn_orders.conn_type_name, conn_orders.project_executor, conn_orders.planned_capacity, conn_orders.technical_condition';
		$groupFields = 'conn_orders.address';
		$colNames = ['Адреса' , 'Тип приєднання' , 'Виконавець проекту' , 'Замовлена потужність' , 'Технічні умови'];
		
		return selectRecords($tables, $fieldNames, $req, $groupFields, $colNames, 'LIMIT 1');
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
/* Вариант № 1 */ 
 {
	 "order_number" 	: "12356",
	 "client_ipn"   	: "25125125125",
	 "contract_ipn" 	: 0,
	 "steps"			: [
	 { 
		"step_type_name"	: "Видача технічних умов на приєднання",
		"created_at"		: "01.01.2011",
		"payed_at"			: "02.01.2011",
		"completed_at"		: "12.01.2011",
		"price"				: 5215125,
		"nalog"				: 18
		},
	{
		"step_type_name"	: "Підключення до газорозподільної системи",
		"created_at"		: "25.01.2011",
		"payed_at"			: "16.02.2011",
		"completed_at"		: "12.05.2011",
		"price"				: 5115,
		"nalog"				: 18	 
		}
	 ]
}
			

/* Вариант № 2 */
[
	 "order_number" 	=> "12356",
	 "client_ipn"   	=> "25125125125",
	 "contract_ipn" 	=> 0,
	 "steps"			=> [
	 [ 
		"step_type_name"	=> "Видача технічних умов на приєднання",
		"created_at"		=> "01.01.2011",
		"payed_at"			=> "02.01.2011",
		"completed_at"		=> "12.01.2011",
		"price"				=> 5215125,
		"nalog"				=> 18
		],
	[
		"step_type_name"	=> "Підключення до газорозподільної системи",
		"created_at"		=> "25.01.2011",
		"payed_at"			=> "16.02.2011",
		"completed_at"		=> "12.05.2011",
		"price"				=> 5115,
		"nalog"				=> 18	 
		]
	 ]
];
			
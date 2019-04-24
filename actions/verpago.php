<?php

use PayPal\Api\Payment;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		$servicios = include_once '../config/servicios.php';

		$id_transaccion = $_POST['id_transaccion'];

		$query = "
			SELECT *
            FROM transacciones t
            LEFT JOIN compradores c
                ON t.clave_comprador = c.clave
            WHERE t.id_transaccion = :id_transaccion
            LIMIT 1
		";
		$values = [
			'id_transaccion' => $id_transaccion
		];
		$query = $servicios->db->prepare($query);
		$query->execute($values);
		$pago = $query->fetchAll(PDO::FETCH_ASSOC);

		$payment = Payment::get($id_transaccion, $servicios->paypal);

		/*$data = $payment;

		//no asociativo
		$manage = json_decode($data);
		echo $manage->transactions[0]->related_resources[0]->sale->id . '<br>';

		//asociativo
		$manage = json_decode($data, true);
		echo $manage['transactions'][0]['related_resources'][0]['sale']['id'] . '<br>';

		//para recorrer los datos de un json
		foreach($manage as $idx => $obj) {
			if($idx == 'transactions') {
				foreach($obj as $idx2 => $obj2) {
					foreach($obj2 as $idx3 => $obj3) {
						if($idx3 == 'related_resources') {
							foreach($obj3 as $idx4 => $obj4) {
								foreach($obj4 as $idx5 => $obj5) {
									if($idx5 == 'sale') {
										foreach($obj5 as $idx6 => $obj6) {
											if($idx6 == 'id') {
												echo $obj6;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}*/
		$output = [];
		foreach($pago as $row) {
			$output['idTransaccion'] = $row['idTransaccion'];
			$output['correo'] = $row['correo'];
			$output['idVenta'] = $row['idVenta'];
			//$output['idVenta'] = $payment->transactions[0]->related_resources[0]->sale->id;
			$output['producto'] = $payment->transactions[0]->item_list->items[0]->name;
			$output['precio'] = $payment->transactions[0]->item_list->items[0]->price;
			$output['cantidad'] = $payment->transactions[0]->item_list->items[0]->quantity;
			$output['total'] = $payment->transactions[0]->amount->total;
			$output['fecha'] = date_format(date_create($row['fechahora']), 'd/m/Y');
			$output['hora'] = date_format(date_create($row['fechahora']), 'h:ia');
			$output['estado'] = $payment->transactions[0]->related_resources[0]->sale->state;
			$output['fechaDev'] = date_format(date_create($row['fechahoraDev']), 'd/m/Y');
			$output['horaDev'] = date_format(date_create($row['fechahoraDev']), 'h:ia');
			$output['data'] = '' . $payment;
		}
		echo json_encode($output);

	} catch(Exception $e) {
		echo '<h1>Algo malio sal</h1><hr>';
		die($e);
	}
}
<?php

use PayPal\Api\Amount;
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;

include 'app/conexion.php';
require 'app/credentials.php';

if(isset($_POST['idVenta'])) {

    $saleId = $_POST['idVenta'];

    try {
        $sale = Sale::get($saleId, $apiContext);
    } catch(Exception $ex) {
        echo '<h1>Algo malio sal</h1><hr>';
        die($ex);
    }

    $currency = $sale->amount->currency;
    $total = $sale->amount->total;

    echo $saleId;
    echo '<br>';
    echo $sale;

    $amt = new Amount();
    $amt->setCurrency($currency)
        ->setTotal($total);

    $refundRequest = new RefundRequest();
    $refundRequest->setAmount($amt);

    $sale = new Sale();
    $sale->setId($saleId);

    /*try {
        $refundedSale = $sale->refundSale($refundRequest, $apiContext);
    } catch(Exception $ex) {
        echo '<h1>Algo malio sal</h1><hr>';
        die($ex);
    }

    //conservar la transaccion y cambiar el estado a 'devuelta'
    // o simplemente borrarla de la tabla de transacciones

    $query = $db->prepare('
        DELETE FROM transacciones
        WHERE idVenta = :idVenta
    ');

    $result = $query->execute([
        'idVenta' => $saleId,
    ]);

    if (!empty($result)) {
        echo 'Data Deleted';
    }*/
}
<?php

namespace Modules\Icommercexpay\Http\Controllers\Api;

// Requests & Response
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Icommercexpay\Http\Requests\InitRequest;

// Base Api
use Modules\Icommerce\Http\Controllers\Api\OrderApiController;
use Modules\Icommerce\Http\Controllers\Api\TransactionApiController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Repositories
use Modules\Icommercexpay\Repositories\IcommerceXpayRepository;

use Modules\Icommerce\Repositories\PaymentMethodRepository;
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;

class IcommerceXpayApiController extends BaseApiController
{

    private $checkmo;
    private $paymentMethod;
    private $order;
    private $orderController;
    private $transaction;
    private $transactionController;
    
    public function __construct(
        IcommerceXpayRepository $checkmo,
        PaymentMethodRepository $paymentMethod,
        OrderRepository $order,
        OrderApiController $orderController,
        TransactionRepository $transaction,
        TransactionApiController $transactionController
    ){

        $this->checkmo = $checkmo;
        $this->paymentMethod = $paymentMethod;
        $this->order = $order;
        $this->orderController = $orderController;
        $this->transaction = $transaction;
        $this->transactionController = $transactionController;
    }
    
    /**
     * Init data
     * @param Requests request
     * @param Requests orderid
     * @return route
     */
    public function init(Request $request){

        try {

            $data = $request->all();
           
            $this->validateRequestApi(new InitRequest($data));
         
            $orderID = $request->orderID;
            \Log::info('Module Icommercexpay: Init-ID:'.$orderID);

            $paymentMethod = $this->getPaymentMethodConfiguration();

            // Order
            //$order = $this->order->find($orderID);
            //$statusOrder = 1; // Processing

            // Create Transaction
            /*
            $transaction = $this->validateResponseApi(
                $this->transactionController->create(new Request( ["attributes" => [
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $statusOrder
                ]]))
            );
            */

            // Search Options Payment

            // Encrip
            $params = array('orderID' => 1, 'transactionID' => 1 );
            $eUrl = xpay_EncriptUrl($params);
           
            $redirectRoute = route('icommercexpay',[$eUrl]);

            // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute
            ]];


          } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);

    }
    
    /**
     * Response Api Method
     * @param Requests request
     * @return route 
     */
    public function response(Request $request){

       

    }

    /**
     * Get Payment Method Configuration
     * @param
     * @return collection
     */
    public function getPaymentMethodConfiguration(){
        $paymentName = config('asgard.icommercexpay.config.paymentName');
        $attribute = array('name' => $paymentName);
        $paymentMethod = $this->paymentMethod->findByAttributes($attribute); 
        
        return $paymentMethod;
    }


}
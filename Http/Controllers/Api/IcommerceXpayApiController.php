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

use Modules\Icommerce\Entities\Transaction as TransEnti;

class IcommerceXpayApiController extends BaseApiController
{

    private $checkmo;
    private $paymentMethod;
    private $order;
    private $orderController;
    private $transaction;
    private $transactionController;

    const URL_PRODUCTION = "https://xpay.cash";
    const URL_SANDBOX = "https://test.xpay.cash";
    
    protected $urlsSandbox;
    
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

        $this->urls= array(
            "getTokenLogin" => "/api/v1/auth/login/",
            "getAccountInformation" => "/api/v1/users/",
            "getCurrencies" => "/api/v1/transactions/available/currencies/",
            "createPayment" => "/api/v1/transactions/create/"
        );
    }
    
    /**
     * Init data
     * @param Requests orderid
     * @return route
     */
    public function init(Request $request){

        try {

            $data = $request->all();
           
            $this->validateRequestApi(new InitRequest($data));
         
            $orderID = $request->orderID;
            //\Log::info('Module Icommercexpay: Init-ID:'.$orderID);

            $paymentMethod = $this->getPaymentMethodConfiguration();

            // Order
            $order = $this->order->find($orderID);
            $statusOrder = 1; // Processing

            // Create Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->create(new Request( ["attributes" => [
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $statusOrder
                ]]))
            );
            
            // Encrip
            $params = array('orderID' => $order->id, 'transactionID' => $transaction->id);
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
     * XPAY API - Get Token Login
     * @param Requests
     * @return token
     */
    public function getTokenLogin(Request $request){

        //\Log::info('Module Icommercexpay: GetTokenLogin');
        try {

            $paymentMethod = $this->getPaymentMethodConfiguration();
            if($paymentMethod->options->mode=="sandbox")
                $endPoint = self::URL_SANDBOX.$this->urls["getTokenLogin"];
            else
                $endPoint = self::URL_PRODUCTION.$this->urls["getTokenLogin"];
            
            //SANDBOX ERROR
            $endPoint = self::URL_PRODUCTION.$this->urls["getTokenLogin"];

            $params = array(
                "email" => $paymentMethod->options->user,
                "password" => $paymentMethod->options->pass
            );

            // SEND DATA xPay AND GET URL
            $client = new \GuzzleHttp\Client();
            $response= $client->request('POST', $endPoint, [
                'body' => json_encode($params),
                'headers' => [
                    'Content-Type'     => 'application/json',
                ]
            ]);

            //\Log::info('Module Icommercexpay: xPay Response Code: '.$response->getStatusCode());
           
        } catch (\Exception $e) {
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
            //\Log::info('Module Icommercexpay: xPay Response Code: '.$e->getMessage());
        }

        return response()->json($response, $status ?? 200);

    }


     /**
     * XPAY API - Get Available currencies
     * @param Requests token
     * @param Requests amount
     * @param Requests currency
     * @return array currencies
     */
    public function getCurrencies(Request $request){

        try {

            $paymentMethod = $this->getPaymentMethodConfiguration();
            if($paymentMethod->options->mode=="sandbox")
                $endPoint = self::URL_SANDBOX.$this->urls["getTokenLogin"];
            else
                $endPoint = self::URL_PRODUCTION.$this->urls["getTokenLogin"];
           
            //SANDBOX ERROR
            $endPoint = self::URL_PRODUCTION.$this->urls["getCurrencies"]."{$request->amount}/{$request->currency}/";

            // SEND DATA xPay AND GET URL
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $endPoint, [
                'headers' => [
                    'Authorization' => "Token ".$request->token,
                ]
            ]);
           
        } catch (\Exception $e) {
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);

    }


     /**
     * XPAY API - Create Payment Transaction
     * @param Requests Token
     * @param Requests encrp
     * @param Requests srcCurrency
     * @param Requests exchangeId
     * @return Json Information
     */
    public function createPayment(Request $request){
        try {

            $data = $request['attributes'] ?? [];//Get data
            
            $paymentMethod = $this->getPaymentMethodConfiguration();
            if($paymentMethod->options->mode=="sandbox")
                $endPoint = self::URL_SANDBOX.$this->urls["getTokenLogin"];
            else
                $endPoint = self::URL_PRODUCTION.$this->urls["getTokenLogin"];
           
            //SANDBOX ERROR
            $endPoint = self::URL_PRODUCTION.$this->urls["createPayment"];
            
            $infor = xpay_DecriptUrl($data['encrp']);
            $order = $this->order->find($infor[0]);
            
            $params = array(
                "src_currency" =>  $data['srcCurrency'],
                "amount" => $order->total,
                "exchange_id" => $data['exchangeId'],
                "tgt_currency" => $order->currency_code,
	            "callback" => route('icommercexpay.api.xpay.response')
            );

            // SEND DATA xPay AND GET URL
            /*
            $client = new \GuzzleHttp\Client();
            $response= $client->request('POST', $endPoint, [
                'body' => json_encode($params),
                'headers' => [
                    'Content-Type'     => 'application/json',
                    'Authorization' => "Token ".$data['token']
                ]
            ]);

            dd($response);
            //$data = json_decode($response->getBody());
            if($response->id){
                $transactionUp = $this->validateResponseApi(
                    $this->transactionController->update($infor[1],new Request([
                        'external_code' => $response->id
                    ]))
                );
            }

            */
            

            // Just Testing
            $qr = "bitcoin:2NFDrzKrRJWiDf8G2A6zgWhJoDZSVPksDYK?amount=0.00355401&label=Fruits+by+Xpay&message=Pago+en+Fruits+by+Xpay&xpay=payprovider%3B6fctLV6";
            $qrImg = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=".$qr."&choe=UTF-8";
            
            $response = [
                "status" => "sending",
                "wallet" => "XXXxxXXXXXXXXXxxxxXXXXXxxxXX",
                "amount_to_paid" => "0.00000000",
                "currency_to_paid" => "BTC",
                "waiting_time" => "1200.00",
                "waiting_time_unit" => "seconds",
                "string_waiting_time" => "1200.00 seconds",
                "qrImg" => $qrImg
            ];

           
   
    

        }catch(\Exception $e){
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
            \Log::error('Module Icommercexpay: Message: '.$e->getMessage());
            \Log::error('Module Icommercexpay: Code: '.$e->getCode());
        }

        return response()->json($response, $status ?? 200);
    }


     /**
     * Response Callback
     * @param Requests 
     * @return Json Information
     */
    public function response(Request $request){
        try {

            \Log::info('Module Icommercexpay: Response - '.time());

            $transaction = TransEnti::where('external_code',$request->id)->latest()->first();

            \Log::info('Module Icommercexpay: orderID: '.$transaction->order->id);
            \Log::info('Module Icommercexpay: transactionID: '.$transaction->id);


            // Update Order
            $orderUP = $this->updateInformation($request->status,$transaction);
           
        }catch(\Exception $e){

            //Log Error
            \Log::error('Module Icommercexpay: Response - Message: '.$e->getMessage());
            \Log::error('Module Icommercexpay: Response - Code: '.$e->getCode());

        }

        return response('Recibido', 200);
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

     /**
     * Update Information 
     * @param
     * @return orderUpdated
     */
    public function updateInformation($orderStatus,$transaction){
       
        if($orderStatus=="sending"){
            //waiting for payment
            $newstatusOrder = 1; //Processing

        }else if($orderStatus == "refunded"){
            //amount exceeded or total amount received was refunded
            $newstatusOrder = 8; //refunded

        }else if($orderStatus == "approved"){
            //payment received
            $newstatusOrder = 13; //Processed

        }else if($orderStatus == "cancelled"){
            //time expired,;
            $newstatusOrder = 14; //expired

        }else if($orderStatus == "rejected"){
            //payment received, but out of valid time.
            $newstatusOrder = 5; //denied
            
        }else{
            $newstatusOrder = 7; // Status Order Failed
        }

        \Log::info('Module Icommercecredibanco: New Status Order: '.$newstatusOrder);

         // Update Transaction
         $transaction = $this->validateResponseApi(
            $this->transactionController->update($transaction->id,new Request([
                'status' => $newstatusOrder
            ]))
        );
        \Log::info('Module Icommercecredibanco: Transaction Updated');

        // Update Order
        $orderUP = $this->validateResponseApi(
            $this->orderController->update($order->id,new Request(
              ["attributes" =>[
                'order_id' => $transaction->order->id,
                'status_id' => $newstatusOrder
              ]
              ]))
        ); 
        \Log::info('Module Icommercecredibanco: Order Updated');
       
        return $orderUP;

    }


}
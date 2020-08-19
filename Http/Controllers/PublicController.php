<?php

namespace Modules\Icommercexpay\Http\Controllers;

// Requests & Response
use Illuminate\Http\Request;

// Base
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Icommercexpay\Http\Controllers\Api\IcommerceXpayApiController;

//Others
use Modules\Setting\Contracts\Setting;

class PublicController extends BasePublicController
{
  
    private $setting;
    private $xpayApiController;

    public function __construct(
        Setting $setting,
        IcommerceXpayApiController $xpayApiController
    )
    {
        $this->setting = $setting;
        $this->xpayApiController = $xpayApiController;
    }

     /**
     * Show Voucher
     * @param  $request
     * @return view
     */
    public function index(Request $request){

        $dataError["status"] = false;
        $dataError["msj"] = false;
        
        try{

            // Decr
            $infor = xpay_DecriptUrl($request->eUrl);
            $orderID = $infor[0];
            $transactionID = $infor[1];

            // SEARCH ORDER

            // GET TOKEN
            /*
            $response = $this->xpayApiController->getTokenLogin($request);
            $infor = $response->getData();

            if(isset($infor->data)){
                $data =  $infor->data;
            }else{
                throw new \Exception($infor->errors, 204);
            }
            */
        
            
            // GET CURRENCIES
            /*
            $response = $this->xpayApiController->getCurrencies(new Request([
                "token" => "123",
                "amount" => 25000,
                "currency" => "COP"
            ]));
            $inforCurrencies = $response->getData();

            if(isset($inforCurrencies->data)){
                $currencies =  $inforCurrencies->data;
            }else{
                throw new \Exception($inforCurrencies->errors, 204);
            }
            */
          
            $data = [
                'tXpay' => "token",
                'currencies' => "currencies",
                'orderID' => $orderID,
                'transactionID' => $transactionID
            ];
      
        }catch(\Exception $e){

            //\Log::error('Module Icommercexpay - index: Message: '.$e->getMessage());
            //\Log::error('Module Icommercexpay - index: Code: '.$e->getCode());
            $dataError["status"] = true;
            $dataError["msj"] = json_encode($e->getMessage());
        }


        $tpl ='icommercexpay::frontend.index';
        return view($tpl,compact('data','dataError'));
       
    }

}
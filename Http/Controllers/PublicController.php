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
    private $credibancoApiController;

    public function __construct(
        Setting $setting,
        IcommerceXpayApiController $credibancoApiController
    )
    {
        $this->setting = $setting;
        $this->credibancoApiController = $credibancoApiController;
    }

     /**
     * Show Voucher
     * @param  $request
     * @return view
     */
    public function index(Request $request){

        try{

            // Decr
            $infor = xpay_DecriptUrl($request->eUrl);
            $orderID = $infor[0];
            $transactionID = $infor[1];

            \Log::info('Module Icommercexpay: Index-ID:'.$orderID);


            $tpl ='icommercexpay::frontend.index';

            return view($tpl);

           
    
        }catch(\Exception $e){

            echo "Ooops, ha ocurrido un error comuniquese con el administrador";

            \Log::error('Module Icommercexpay - index: Message: '.$e->getMessage());
            \Log::error('Module Icommercexpay - index: Code: '.$e->getCode());

        }
       
    }

}
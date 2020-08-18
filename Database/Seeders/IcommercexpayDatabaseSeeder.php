<?php

namespace Modules\Icommercexpay\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercexpayDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $options['init'] = "Modules\Icommercexpay\Http\Controllers\Api\IcommerceXpayApiController";
        
        $options['mainimage'] = null;
        $options['user'] = null;
        $options['pass'] = null;
        $options['mode'] = "sandbox";
        $options['token'] = null;

        $titleTrans = 'icommercexpay::icommercexpays.single';
        $descriptionTrans = 'icommercexpay::icommercexpays.description';

        foreach (['en', 'es'] as $locale) {

            if($locale=='en'){
                $params = array(
                    'title' => trans($titleTrans),
                    'description' => trans($descriptionTrans),
                    'name' => config('asgard.icommercexpay.config.paymentName'),
                    'active' => 1,
                    'options' => $options
                );

                $paymentMethod = PaymentMethod::create($params);
                
            }else{

                $title = trans($titleTrans,[],$locale);
                $description = trans($descriptionTrans,[],$locale);

                $paymentMethod->translateOrNew($locale)->title = $title;
                $paymentMethod->translateOrNew($locale)->description = $description;

                $paymentMethod->save();
            }

        }// Foreach
 
    }
}

<div v-if="currentStep==2" class="step-payment">

    <h5 class="card-title font-weight-bold">Proceso de Pago</h5>

    <div v-if="dataPayment && dataPayment.status=='sending' " class="infor">

        <div class="qr">
            <img :src="dataPayment.qrImg" alt="QR code">
        </div>
    
        <div class="infor row justify-content-center ">
            <div class="col-sm-8">
            <ul class="list-group list-group-flush text-left">
                <li class="list-group-item">
                    <strong>Monto a pagar:</strong> 
                        @{{dataPayment.amount_to_paid}} @{{dataPayment.currency_to_paid}}
                </li>
                <li class="list-group-item">
                    <strong>Wallet:</strong>
                    @{{dataPayment.wallet}}
                </li>
                <li class="list-group-item d-flex align-items-start">
                    <strong>Tiempo:</strong>
                    <div class="timer ml-2 badge badge-secondary p-2 text-wrap" style="font-size:1rem">
                        <b id="xpay-timer"></b>
                    </div>
                </li>
            </ul>
            </div>
        </div>
    
       

    </div>
   
</div>
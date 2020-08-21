<div v-if="currentStep==2" class="step-payment">

    <h5 class="card-title font-weight-bold">Proceso de Pago</h5>

    <div v-if="dataPayment && dataPayment.status=='sending' " class="infor">

        <div class="qr">
            <img :src="dataPayment.qrImg" alt="QR code">
        </div>
    
        <div class="infor">
    
            <div class="amount mb-2">
                <h6>Monto a pagar en: @{{dataPayment.currency_to_paid}}</h6>
                <div class="cant">@{{dataPayment.amount_to_paid}}</div>
            </div>
    
            <div class="wallet mb-2">
                <h6>Wallet</h6>
                <div class="cod">@{{dataPayment.wallet}}</div>
            </div>
            
        </div>
    
        <div class="timer">
            <b id="xpay-timer"></b>
        </div>

    </div>
   
</div>
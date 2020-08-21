<div v-if="currentStep==2" class="step-payment">

    <h5 class="card-title font-weight-bold">Proceso de Pago</h5>

    <div v-if="dataPayment" class="qr">
        <img :src="dataPayment.qrImg" alt="QR code">
    </div>

    <div class="infor">

        <div class="amount mb-2">
            <h6>Monto a pagar en: XXX</h6>
            <div class="cant">0.000000</div>
        </div>

        <div class="wallet mb-2">
            <h6>Wallet</h6>
            <div class="cod">2NFDrzKrRJWiDf8G2A6zgWhJoDZSVPksDYK</div>
        </div>
        
    </div>
    
</div>
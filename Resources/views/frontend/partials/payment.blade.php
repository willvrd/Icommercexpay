<div v-if="currentStep==2" class="step-payment">

    <h5 class="card-title font-weight-bold">Proceso de Pago</h5>

    <form @submit.prevent="onStep(3)">

       
        <p>Moneda:@{{selectedCurrency}}</p>
        

    </form>
    
</div>
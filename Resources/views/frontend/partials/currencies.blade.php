<div v-if="currentStep==1" class="step-currencies">

    <h5 class="card-title font-weight-bold">Seleccione la Moneda</h5>

    <form @submit.prevent="onStep(2)">

        <select class="custom-select" v-model="selectedCurrency" required>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
        </select>

        <button type="submit" class="btn btn-primary mt-3">Aceptar</button>

    </form>
    
</div>
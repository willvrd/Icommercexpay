<div v-if="currentStep==1" class="step-currencies">

    <h5 class="card-title font-weight-bold">Seleccione la Moneda</h5>

    <form @submit.prevent="onStep(2)">

        <select class="custom-select" v-model="selectedCurrency" required>
            <option v-for="(crc,index) in data.currencies" :key="index" :value="crc">
                @{{crc.currency.name}} - Monto: @{{crc.amount}}
            </option>
        </select>

        <button type="submit" class="btn btn-primary mt-3">Aceptar</button>

    </form>
    
</div>
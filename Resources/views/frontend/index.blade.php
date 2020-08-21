@php
  $layout = 'layouts.master';
  if(!(View::exists($layout))){
    $layout ="icommercexpay::frontend.layouts.master";
  }
@endphp

@extends($layout)

@section('title')
  xPay | @parent
@stop

@section('content')

<div class="icommerce_xpay_index py-5">
  <div id="content_xpay">
  
    <div class="container">

      <h2 class="text-center font-weight-bold mt-5">Bienvenido</h2>
      <hr>

      <div class="row my-5 justify-content-center">

        <div class="col-xs-12 col-sm-8">
        
          <div v-if="success" class="card text-center">

            <div class="card-header bg-secondary">
              <h3 class="font-weight-bold text-white">xPay</h3>
            </div>

            @include("icommercexpay::frontend.partials.loading")

            <div v-if="!dataError" class="card-body py-5 px-4">

                @include("icommercexpay::frontend.partials.currencies")
  
                @include("icommercexpay::frontend.partials.payment")

            </div>

            <div v-else class="card-body py-5 px-4">
              <div class="alert alert-danger" role="alert">
                Ha ocurrido un error comuniquese con el administrador
              </div>
            </div>

            <div class="card-footer text-muted">
              xPay - {{date('Y')}}
            </div>

          </div>
      
        </div>


        <div class="col-xs-12 col-sm-4">
          @include("icommercexpay::frontend.partials.information")
        </div>
      
      </div>

      <hr>

    </div>

  </div>
</div>

@stop

@section('scripts')
@parent
<script type="text/javascript">
var index_xpay = new Vue({
  el: '#content_xpay',
  created() {
    this.$nextTick(function () {
      this.init();
    })
  },
  data: {
    currentStep: 1,
    loading: true,
    success: false,
    dataError: {!! $dataError['status'] ? $dataError['status']: 0 !!},
    dataErrorMsj: {!! $dataError['msj'] ? $dataError['msj']: 0 !!},
    data: {!! json_encode($data) !!},
    dataPayment:null,
    currencies: [
      {
        "currency": {
            "code": "XPAY",
            "name": "XPAY",
            "symbol": "COC",
            "type": "wallet"
        },
        "amount": "1000.00",
        "exchange": 0
      },
      {
        "currency": {
          "code": "WGOC",
          "name": "GoCrypto",
          "symbol": "COC",
          "type": "wallet"
        },
        "amount": "1000.00",
        "exchange": 6
      }
    ],
    selectedCurrency: null
  }, 
  methods: {
    init(){
      this.success = true;
      this.loading = false;

      if(this.dataError)
        console.error(this.dataErrorMsj)

      //console.warn(this.currencies)
    },
    generatePayment(){

      //console.warn(this.selectedCurrency)

      this.loading = true;
      let path = "{{route('icommercexpay.api.xpay.createPayment')}}"
      let attributes2 = {
        token:'123',
        encrp:this.data.encrp,
        srcCurrency: this.selectedCurrency.currency.code,
        exchangeId: this.selectedCurrency.exchange
      }

      axios.post(path, {attributes:attributes2})
      .then(response => {
        this.dataPayment = response.data;

        console.warn(this.dataPayment)

        this.initTime();
        
      })
      .catch(error => {

        if (error.response)
          console.log(error.response.data.errors)

        this.dataError = true;
       
      })
      .finally(() => this.loading = false)
      
     
    },
    onStep(nextStep){
      this.currentStep = nextStep;
      this.generatePayment();
    },
    initTime(){
      if(this.dataPayment){
        let xpaytoreload = 0;
        let waitingTime = this.dataPayment.waiting_time;
        var xpaytimer = setInterval(function(){
          if (typeof jQuery == 'undefined') return;
          var $ = jQuery;
          var t = $('#xpay-timer');
          if (t.length > 0) {
            ++xpaytoreload;
            var s = waitingTime;
            --s;
            --waitingTime;
            if (s < 0) {
              t.html('EXPIRADO');

              let redirect = "{{url("/")}}"; 
              window.location.href= redirect;

              clearInterval(xpaytimer);
              return;
            }
            var mm = parseInt(s/60);
            var ss = s%60;
            if (mm < 10) mm = '0'+mm;
            if (ss < 10) ss = '0'+ss;
            t.html(mm+':'+ss);
          }
        }, 950);
      }
    }
   
  }
})

</script>
@stop

<style>
  #capa {
      width: 100%;
      top: 0;
      left: 0;
      background-color: rgba(255, 255, 255, 0.95);
      z-index: 1;
  }
  .flex-center{
      align-items: center;
      display: flex;
      justify-content: center;
  }
  .full-height {
      height: 100%;
  }
</style>
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
    selectedCurrency: null
  }, 
  methods: {
    init(){
      this.success = true;
      this.loading = false;

      if(this.dataError)
        console.error(this.dataErrorMsj)
        
    },
    generatePayment(){

      this.loading = true;
      let path = "{{route('icommercexpay.api.xpay.createPayment')}}"
      let attributes2 = {token:'123'}

      axios.post(path, {attributes:attributes2})
      .then(response => {
                //this.data = response.data.data;
        console.warn(response)  

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
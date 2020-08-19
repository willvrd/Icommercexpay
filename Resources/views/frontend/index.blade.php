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

      <div class="row my-5 justify-content-center">
      <div class="col-xs-12 col-sm-8">

        <div v-if="success && !loading" class="card text-center">

          <div class="card-header bg-secondary">
            <h2 class="font-weight-bold text-white">xPay</h2>
          </div>

          <div class="card-body py-5 px-4">

              @include("icommercexpay::frontend.partials.currencies")
 
              @include("icommercexpay::frontend.partials.payment")
              
          </div>

          <div class="card-footer text-muted">
            xPay - {{date('Y')}}
          </div>

        </div>
    
      </div>
      </div>

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
    selectedCurrency: null
  }, 
  methods: {
    init(){
      this.success = true;
      this.loading = false;
    },
    onStep(nextStep){
      this.currentStep = nextStep;
    }
  }
})

</script>
@stop
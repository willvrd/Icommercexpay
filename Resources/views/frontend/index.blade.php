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

      @include('icommercexpay::frontend.partials.header')
    
      <div class="row my-5 justify-content-center">
       
          <h2>@{{prueba}}</h2>
    
      </div>

      @include('icommercexpay::frontend.partials.footer')

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
    prueba:"Esto es un texto de prueba"
  }, 
  methods: {
    init(){
      console.warn("INICIANDO VUE")
    }
  }
})

</script>
@stop
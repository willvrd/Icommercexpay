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

  <div class="container">

    @include('icommercexpay::frontend.partials.header')
  
    <div class="row my-5 justify-content-center">
     
        asdasdasd
  
    </div>

    @include('icommercexpay::frontend.partials.footer')

  </div>

</div>

@stop


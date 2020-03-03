@extends('unmit::layouts.master')
@section('app')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Errors Section -->
@if ($errors->any())
    <div id="page-content-wrapper">
        <ul class="alert alert-danger text-center" style="list-style: none">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Messages Section -->
@if (session('message'))
    <div id="page-content-wrapper">
        <div class="alert alert-success text-center">
            {{ session('message') }}
        </div>
    </div>
@endif

<div class="content-container curved-box" class="row">
    <div class="content" class="col-12">
        <!-- Your Page Content Here -->
        @yield('content')
    </div>
</div>
@endsection
<!-- END App Container-->
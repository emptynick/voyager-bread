@extends('voyager::master')
@section('page_title', '')

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li><a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a></li>
    <li><a href="#">...</a></li>
    <li class="active">...</li>
</ol>
@endsection

@section('content')

@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>

</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection

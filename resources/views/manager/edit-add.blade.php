@extends('voyager::master')
@section('page_title', __('bread::manager.manager'))

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

@extends('bread::master')
@section('page_title', 'BREAD Manager')

@section('content')
<div id="manager-browse">
    <manager-browse></manager-browse>
</div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>
var builder = new Vue({
    el: "#manager-browse",
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection

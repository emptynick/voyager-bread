@extends('voyager::master')

@section('page_title', __('bread::manager.edit_view_for', ['bread' => $view->bread->display_name_plural]))

@section('page_header')
<h1 class="page-title">
	<i class="voyager-bread"></i>
	{{ __('bread::manager.edit_view_for', ['bread' => $view->bread->display_name_plural]) }}
</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
	@if(count(Request::segments()) == 1)
		<li class="active"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</li>
	@else
		<li class="active">
			<a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
		</li>
	@endif
	<li class="active"><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread.edit', $view->bread->table_name) }}">{{ $view->bread->display_name_plural }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread.edit', $view->bread->table_name) }}#views">{{ __('bread::manager.views') }}</a></li>
	<li class="active">{{ $view->name }}</li>
	<li>{{ __('voyager::generic.edit') }}</li>
</ol>
@endsection

@section('content')
<div id="view-builder">

</div>
@endsection

@section('javascript')
<script src="//cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
new Vue({
    el: '#view-builder',
    components: {

    },
    data: {

    },
});
</script>
@endsection

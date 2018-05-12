@extends('voyager::master')

@section('page_title', __('bread::manager.edit_view_for', ['bread' => $bread->display_name_plural]))

@section('page_header')
<h1 class="page-title">
	<i class="voyager-bread"></i>
	{{ __('bread::manager.edit_view_for', ['bread' => $bread->display_name_plural]) }}
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
	<li class="active"><a href="{{ route('voyager.bread.edit', $bread->name) }}">{{ $bread->display_name_plural }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread.edit', $bread->name) }}#views">{{ __('bread::manager.views') }}</a></li>
	<li class="active">{{ $view->name }}</li>
	<li>{{ __('voyager::generic.edit') }}</li>
</ol>
@endsection
@section('content')
<div class="page-content container-fluid">
	<div class="row clearfix" id="view-builder">
		<vue-snotify></vue-snotify>
        <div class="col-md-12">
            <div class="dropdown" style="display:inline">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    Add Element
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">Formfields</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('element_type', 'formfield') as $formfield)
					<li>
                        <a href="#" v-on:click="addFormfield('formfield', '{{ $formfield->getCodeName() }}')">
                            {{ $formfield->getName() }}
                        </a>
                    </li>
					@endforeach
                    <li class="divider"></li>

                    <li class="dropdown-header">Layout Elements</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('element_type', 'layout_element') as $formfield)
					<li>
                        <a href="#" v-on:click="addFormfield('layout_element', '{{ $formfield->getCodeName() }}')">
                            {{ $formfield->getName() }}
                        </a>
                    </li>
					@endforeach
                    <li class="divider"></li>

                    <li class="dropdown-header">Relationships</li>
                </ul>
            </div>
            <div style="display:inline">
                <form method="post" action="{{ route('voyager.bread.store.layout', ['table' => $table, 'name' => $name]) }}" style="display:inline">
                    <input type="hidden" name="content" :value="JSON.stringify(this.layout)">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Save View</button>
                </form>
            </div>
        </div>
		<div class="col-md-12">
			<grid-layout
                :layout="layout.elements"
                :row-height="{{ config('bread.views.row_height', 50) }}"
                :max-rows="{{ config('bread.views.max_rows', 'Infinity') }}"
                :col-num="{{ config('bread.views.col_num', 12) }}"
                :margin="[{{ config('bread.views.margin_x', 10) }}, {{ config('bread.views.margin_y', 10) }}]">
				<grid-item v-for="element in layout.elements"
				:x="element.x"
				:y="element.y"
				:w="element.w"
				:h="element.h"
				:i="element.i"
				:key="element.i"
				drag-ignore-from=".no-drag"
				drag-allow-from=".draggable-handle">
				<div :class="'panel panel-bordered '+element.class"
					 style="height:100%;"
					 v-tooltip.notrigger="{ html: element.i+'_options', visible: is_options_open(element.i), class: 'options-tooltip' }">
					<div class="panel-heading draggable-handle">
						<h3 class="panel-title">@{{ element.title }}</h3>
						<div class="panel-actions">
							<a class="panel-action voyager-trash" @click="delete_element(element.i)"></a>
							<a class="panel-action voyager-settings open-settings" @click="open_options(element.i)"></a>
						</div>
					</div>
					<div class="panel-body no-drag">
						<component :is="element.element_type+'-'+element.type" v-bind="element">
							<div slot="options">
								<div class="pull-left">
									<h4>Options</h4>
								</div>
								<div class="pull-right" @click="open_options(null)">
									<span class="voyager-x" style="cursor:pointer;"></span>
								</div>
								<div class="clearfix"></div>
								<div class="form-group" v-if=" v-if="element.element_type == 'formfield'"">
						            <label>Field</label>
                                    <select class="form-control" v-model="element.field">
                                        <option v-for="field in fields">
                                            @{{ field }}
                                        </option>
                                    </select>
						        </div>
								<div class="form-group">
						            <label>Color</label>
						            <select class="form-control" v-model="element.class">
										<option value="panel-primary">Blue</option>
										<option value="panel-danger">Red</option>
										<option value="panel-warning">Yellow</option>
										<option value="panel-success">Green</option>
										<option value="">None</option>
									</select>
						        </div>
								<div class="form-group">
						            <label>Title</label>
						            <input type="text" class="form-control" v-model="element.title">
						        </div>
							</div>

							<div slot="options_after">
                                <div class="checkbox" v-if="element.element_type == 'formfield'">
									<label><input type="checkbox" v-model="element.translatable">Translatable</label>
								</div>
								<div class="form-group" v-if="element.element_type == 'formfield'">
						            <label>Validation</label>
						            <input type="text" class="form-control" v-model="element.title">
						        </div>
							</div>
						</component>
					</div>
				</div>
				</grid-item>
			</grid-layout>
		</div>
	</div>
</div>
@endsection
@section('javascript')
<script src="{{ asset('vendor/bread/js/app.js') }}"></script>

@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->mockup())
<input type="hidden" id="{{ $formfield->getCodeName() }}_options" value="{{ $formfield->getOptions() }}">
@endforeach
@include($formfield->mockup())
<script>
var builder = new Vue({
	el: '#view-builder',
	data: {
		layout: {!! json_encode($view) !!},
		editing: true,
		read: false,
		edit: false,
		add: false,
		current_options: null,
		current_options_el: null,
        fields: {!! json_encode($fields) !!},
	},
	components: {

	},
	methods: {
		addFormfield: function(type, codename) {
            var i = this.layout.elements.length;
			var item = {
				"x": 0,
				"y": 0,
				"w": 6,
				"h": 3,
				"i": ""+i,
				"type": codename,
				"element_type": type,
				"class": "panel-primary",
				"options": JSON.parse($('#'+codename+'_options').val())
			};
			this.layout.elements.push(item);
		},
		is_options_open: function(i) {
			return (this.current_options === i);
		},
		open_options: function(i) {
			this.current_options = (this.is_options_open(i) ? null : i);
			this.current_options_el = document.getElementById(this.current_options+'_options');
		},
		delete_element: function(i) {
			this.$snotify.confirm('Are you sure you want to delete this element?', 'Delete Element?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: 'Yes', action: (toast) => { this.layout.elements.splice(i, 1); this.$snotify.remove(toast.id) }, bold: false},
					{text: 'No', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
		}
	},
	mounted: function() {
		var vm = this;
		window.addEventListener('keyup', function(event) {
			if (event.keyCode == 27) {
				vm.open_options(null);
			}
		});
		window.addEventListener('click', function(event) {
			if (!event.target.className.includes('open-settings') && vm.current_options_el !== null && event.path.indexOf(vm.current_options_el) == -1) {
				vm.open_options(null);
			}
		});
    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/bread/css/app.css') }}">
<style>
.options-tooltip {
	z-index: 10000 !important;
	width: 150rem;
    background-color: #353d47 !important;
}
</style>
@endsection

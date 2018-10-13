@extends('voyager::master')
@section('page_title', __('voyager::generic.view').' '.$bread->display_name_singular)

@section('content')
<div id="bread-edit">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i>
            {{ __('voyager::generic.viewing') }} {{ ucfirst($bread->display_name_singular) }}

            @can('edit', $content)
            <a href="{{ route('voyager.'.$bread->slug.'.edit', $content->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                {{ __('voyager::generic.edit') }}
            </a>
            @endcan
            @can('delete', $content)
            <a href="#" class="btn btn-danger delete" v-on:click="this.delete">
                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
            </a>
            @endcan

            <a href="{{ route('voyager.'.$bread->slug.'.index') }}" class="btn btn-warning">
                <span class="glyphicon glyphicon-list"></span>&nbsp;
                {{ __('voyager::generic.return_to_list') }}
            </a>
        </h1>
    </div>
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('voyager::alerts')
                <div v-for="(item, key) in elements" :class="'col-md-'+item.width">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="form-group">
                                <component
                                    :is="item.element_type+'-'+item.type"
                                    :options="item.options"
                                    :name="item.field"
                                    :show="'read'"
                                    :input="getContentForField(item.field)"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent('view'))
@endforeach
<script>
new Vue({
    el: "#bread-edit",
    data: {
        elements: {!! $layout->elements->toJson() !!},
        content: {!! $content ? collect($content)->toJson() : 'null' !!},
        errors: {!! $errors->toJson() !!},
    },
    methods: {
        getContentForField: function(field) {
            if (this.content) {
                return this.content[field];
            } else {
                return '';
            }
        },
        delete: function() {
            this.$snotify.confirm('Are you sure you want to delete this {{ $bread->display_name_singular }}?', 'Delete {{ $bread->display_name_singular }}?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.$http.post("{{ route('voyager.'.$bread->slug.'.destroy', $content->getKey()) }}", { _token: '{{ csrf_token() }}', _method: 'delete' }).then(response => {
                            this.$snotify.remove(toast.id);
                            window.location.href = '{{ route('voyager.'.$bread->slug.'.index') }}';
                        }, response => {

                        });
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        }
    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection

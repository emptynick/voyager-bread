@extends('voyager::master')
@section('page_title', __('voyager::generic.view').' '.get_translated_value($bread->display_name_singular))

@section('content')
<div id="bread-read">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i>
            {{ __('voyager::generic.viewing') }} {{ ucfirst(get_translated_value($bread->display_name_singular)) }}

            @can('edit', $content)
            <a href="{{ route('voyager.'.get_translated_value($bread->slug).'.edit', $content->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                {{ __('voyager::generic.edit') }}
            </a>
            @endcan
            @can('delete', $content)
            <a href="#" class="btn btn-danger delete" v-on:click="this.delete">
                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
            </a>
            @endcan

            <a href="{{ route('voyager.'.get_translated_value($bread->slug).'.index') }}" class="btn btn-warning">
                <span class="glyphicon glyphicon-list"></span>&nbsp;
                {{ __('voyager::generic.return_to_list') }}
            </a>
        </h1>
        @if ($model->isTranslatable)
        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
        @endif
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
                                    :is="'formfield-'+item.type"
                                    :options="item.options"
                                    :computed="item.computed"
                                    :name="item.field"
                                    :ref="item.field"
                                    :show="'read'"
                                    :locale="'{{ app()->getLocale() }}'"
                                    :input="getContentForField(item.field)"
                                ></component>
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
@include('bread::components.language-switcher')
<script>
new Vue({
    el: "#bread-read",
    data: {
        elements: {!! $layout->elements->toJson() !!},
        content: {!! $content ? collect($content)->toJson() : 'null' !!},
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
            this.$snotify.confirm('Are you sure you want to delete this {{ get_translated_value($bread->display_name_singular) }}?', 'Delete {{ get_translated_value($bread->display_name_singular) }}?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.$http.post("{{ route('voyager.'.get_translated_value($bread->slug).'.destroy', $content->getKey()) }}", { _token: '{{ csrf_token() }}', _method: 'delete' }).then(response => {
                            this.$snotify.remove(toast.id);
                            window.location.href = '{{ route('voyager.'.get_translated_value($bread->slug).'.index') }}';
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

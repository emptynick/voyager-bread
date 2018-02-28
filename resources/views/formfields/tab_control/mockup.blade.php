<ul class="nav nav-tabs">
    @foreach((isset($options['tabs']) ? $options['tabs'] : []) as $tab)
    <li class="{{ ($loop->first ? 'active' : '') }}"><a data-toggle="tab" href="#{{ str_slug($tab['name']) }}">{{ $tab['name'] }}</a></li>
    @endforeach
</ul>

<div class="tab-content">
    @foreach((isset($options['tabs']) ? $options['tabs'] : []) as $tab)
    <div id="{{ str_slug($tab['name']) }}" class="tab-pane fade {{ ($loop->first ? 'in active' : '') }} nested-tab">

    </div>
    @endforeach
</div>

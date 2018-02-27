@if($multiple)
	@foreach($data as $single)
	{{ $single->$attribute }}
	@if(!$loop->last) , @endif
	@endforeach
@else
<div class="readmore">{{ mb_strlen($data) > 200 ? mb_substr($data, 0, 200) . ' ...' : $data }}</div>
@endif

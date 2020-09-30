{!! "<?php"; !!}

{!! "return ["; !!}
    @foreach($countries as $country)
        @json($country['iso2']) => @json($country['name'])  @if(!$loop->last),@endif

    @endforeach
{!! "];" !!}

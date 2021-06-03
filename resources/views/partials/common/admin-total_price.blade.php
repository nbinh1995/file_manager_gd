<h3 class="text-right">@if(isset($thisMonth)) {{__('Total this Month: ')}} @else {{__('Total: ')}} @endif<span
            class="text-primary text-bold">{{number_format($totalYen ?? 0, 0)}}JPY</span>
    - <span class="text-danger text-bold">{{number_format($total, 0)}}USD</span></h3>
@if($rate>0)
    <p class="text-right rate text-md"><b>{{__('Rate: ')}}</b> <span class="text-danger text-bold">1USD</span> = <span
                class="text-primary text-bold">{{$rate > 0 ? round(1/$rate) : ''}}JPY</span></p>
@else
    <p class="text-right rate text-md"><span
                class="text-bold text-danger">{{__('Could not get the exchange rate. Please try again later.')}}</span>
    </p>
@endif
<form action="{{route('customers.update',['customerID'=>$customer->ID])}}" method="post" enctype="multipart/form-data"
    class="{{'form-edit'}}">
    {{ csrf_field() }}
    {{ method_field('PUT')}}
    <input type="hidden" name="id" value="{{$customer->ID}}">
    <div class="input-group text-xs">
        <input type="text" class="form-control text-xs mw-300" name="Name" required placeholder="Customer Name"
            value="{{$oldName ??$customer->Name}}">
        <div class='input-group-prepend'>
            <div class="input-group-text text-xs">
                <button class=' btn btn-success btn-xs mr-2 text-xs' type="submit" data-id='{{$customer->ID}}'><i class=' far
                    fa-save text-xs' style='pointer-events: none'></i> Save </button>
                <button class='btn btn-secondary btn-xs back text-xs' data-name='{{$customer->Name}}'
                    data-id='{{$customer->ID}}'><i class='fas fa-times-circle text-xs' style='pointer-events: none'>
                        Back</i></button>
            </div>
        </div>
    </div>
    @isset($err)
    <div class="text-danger text-xs" role="alert">
        @foreach ($err['Name'] as $message)
        <strong>{{ $message  }}</strong>
        @endforeach
    </div>
    @endisset
</form>
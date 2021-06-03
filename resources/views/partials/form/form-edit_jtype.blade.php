<form action="{{route('jtypes.update',['jtypeID'=>$jtype->ID])}}" method="post" enctype="multipart/form-data"
    class="{{'form-edit'}}">
    {{ csrf_field() }}
    {{ method_field('PUT')}}
    <input type="hidden" name="id" value="{{$jtype->ID}}">
    <div class="input-group text-xs">
        <input type="text" class="form-control text-xs mw-300" name="Name" required placeholder="Method Name"
            value="{{$oldName ??$jtype->Name}}">
        <div class='input-group-prepend'>
            <div class="input-group-text">
                <button class=' btn btn-success btn-xs mr-2 text-xs' type="submit" data-id='{{$jtype->ID}}'><i class=' far
                    fa-save' style='pointer-events: none'></i> Save </button>
                <button class='btn btn-secondary btn-xs back text-xs' data-name='{{$jtype->Name}}'
                    data-id='{{$jtype->ID}}'><i class='fas fa-times-circle' style='pointer-events: none'>
                        Back</i></button>
            </div>
        </div>
    </div>
    @isset($err)
    <div class="text-danger" role="alert">
        @foreach ($err['Name'] as $message)
        <strong>{{ $message  }}</strong>
        @endforeach
    </div>
    @endisset
</form>
<form action="{{route('jtypes.store')}}" method="post" id="form-create" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="input-group ">
        <div class="input-group-prepend">
            <div class="input-group-text">
                {{ __('Type Name') }}<span class="text-danger">*</span>
            </div>
        </div>
        <input type="text" class="form-control" name="Name" required placeholder="Type Name" value="{{ old('Name')}}">
    </div>
    @isset($err)
    <div class="text-danger" role="alert">
        @foreach ($err['Name'] as $message)
        <strong>{{ $message  }}</strong>
        @endforeach
    </div>
    @endisset
</form>
<div class="card">
    <div class="card-body">
        <label for="name">{{__('Dir')}}</label>
        <select name="dir"  class="form-control dir">
            <option selected>----/----</option>
            @foreach ($dir as $item)
            <option value="{{$item['path']}}">{{$item['filename']}}</option>
            @endforeach
        </select>
    </div>
    <div class="card-body position-relative pl-5">
        <i class="far fa-trash-alt" style="    position: absolute;
        left:28px;
        bottom: 34px;"></i>
        <select name="dir"  class="form-control dir">
            <option selected>----/----</option>
            @foreach ($dir as $item)
            <option value="{{$item['path']}}">{{$item['filename']}}</option>
            @endforeach
        </select>
    </div>
</div>
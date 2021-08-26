<div class="card-header">
    <div class="row">
        <div class="col-6">
            <form action="{{route('logs')}}">
                <div class="form-group">
                    <label>From</label>
                    <input type="text" name="firstTime" data-datepicker placeholder="From" value="{{$firstTime}}" class="form-control">
                </div>
                <div class="form-group">
                    <label>To</label>
                    <input type="text" name="lastTime"  data-datepicker placeholder="To"value="{{$lastTime}}" class="form-control">
                </div>
                <div class="form-group">
                    <label >{{__('UserName:')}}</label>
                        <select name="user_id"  class="form-control" id="select2">
                            <option value=""></option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}" {{$user_id == $user->id ? 'selected' : ''}}>{{$user->username}}</option>
                            @endforeach
                        </select>
                </div>
                <button class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>
</div>

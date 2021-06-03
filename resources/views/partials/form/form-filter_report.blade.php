<form id="form-report" action="{{route('reports.index')}}">
    <div class="row">
        <div class="mr-2 form-group width-120">
            <label class="control-label" for="from-date">{{__('From')}}</label>
            <input class="form-control" name="fromDate" id="from-date"
                   value="{{$fromDate ?? ''}}">
        </div>
        <div class="mr-2 form-group width-120">
            <label class="control-label" for="to-date">{{__('To')}}</label>
            <input class="form-control" name="toDate" id="to-date"
                   value="{{$toDate ?? ''}}">
        </div>
        <div class="mr-2 form-group">
            <label class="control-label" for="report-type">{{__('Mode')}}</label>
            <select class="form-control" name="reportType" id="report-type">
                <option{{$reportType != 2 ? ' selected' : ''}} value
                ="1">{{__('Sale')}}</option>
                <option{{$reportType == 2 ? ' selected' : ''}} value
                ="2">{{__('Payment')}}</option>
            </select>
        </div>
        <div class="mr-2 form-group width-160">
            <label class="control-label" for="customer">{{__('Customer')}}</label>
            <select class="form-control" name="CustomerID" id="customer" style="width: 160px;">
                <option value="0">{{__('ALL')}}</option>
                @foreach($customers as $customer)
                    <option{{request()->has('CustomerID') && $customer->ID == request()->get('CustomerID') ? ' selected' : ''}} value
                    ="{{$customer->ID}}">{{$customer->Name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-2 form-group width-160">
            <label class="control-label" for="type">{{__('Type')}}</label>
            <select class="form-control" name="TypeID" id="type">
                <option value="0">{{__('ALL')}}</option>
                @foreach($types as $type)
                    <option{{request()->has('TypeID') && $type->ID == request()->get('TypeID') ? ' selected' : ''}} value
                    ="{{$type->ID}}">{{$type->Name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-2 form-group width-160">
            <label class="control-label" for="method">{{__('Payment Method')}}</label>
            <select class="form-control" name="MethodID" id="method">
                <option value="0">{{__('ALL')}}</option>
                @foreach($methods as $method)
                    <option{{request()->has('MethodID') && $method->ID == request()->get('MethodID') ? ' selected' : ''}} value
                    ="{{$method->ID}}">{{$method->Name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-2 form-group">
            <label class="control-label" for="paid">{{__('Paid')}}</label>
            <select class="form-control" name="Paid" id="paid">
                <option{{!in_array($paid, [1,2]) ? ' selected' : ''}} value
                ="0">{{__('ALL')}}</option>
                <option{{$paid == 1 ? ' selected' : ''}} value
                ="1">{{__('YES')}}</option>
                <option{{$paid == 2 ? ' selected' : ''}} value
                ="2">{{__('NO')}}</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <button class="btn btn-primary">{{__('View')}}</button>
            <button type="button" class="btn btn-secondary view-chart" data-toggle="modal"
                    data-target="#modal-report">{{__('Chart')}}</button>
        </div>
    </div>
</form>

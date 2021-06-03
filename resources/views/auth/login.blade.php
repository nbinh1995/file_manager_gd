<!DOCTYPE html>
<html>

<head>
    @include('partials.common.admin-head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/toastr/toastr.min.css')}}">
    <title>{{__('Dashboard')}} | {{__('Login')}}</title>
</head>

<body class="hold-transition login-page">

    @include('partials.common.admin-login')

    @include('partials.common.admin-script')

    <script src="{{asset('/AdminLTE/plugins/toastr/toastr.min.js')}}"></script>
    @if(session()->has('message'))
        <script>
            toastr.error("{{session()->get('message')}}");
        </script>
    @endif
</body>

</html>
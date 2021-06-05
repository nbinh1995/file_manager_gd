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
    @if(session()->has('message_success'))
    <script>
        toastr.success("{{session()->get('message_success')}}");
    </script>
    @endif
    <script>
        var show_pw = document.getElementById('show_pw');
        var hide_pw = document.getElementById('hide_pw');
        var pw = document.getElementById('password');
        show_pw.addEventListener('click',function(){
            pw.type = 'text';
            this.style.display = 'none';
            hide_pw.style.display = 'block';
        })

        hide_pw.addEventListener('click',function(){
            pw.type = 'password';
            this.style.display = 'none';
            show_pw.style.display = 'block';
        })
    </script>
</body>

</html>
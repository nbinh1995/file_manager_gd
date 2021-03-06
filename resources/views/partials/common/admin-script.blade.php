<!-- jQuery -->
<script src="{{asset('AdminLTE/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('/AdminLTE/plugins/toastr/toastr.min.js')}}"></script>

<script src="{{asset('AdminLTE/dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('js/layout.js')}}"></script>
<script type="text/javascript">
    @if(session()->has('flash_success') || isset($flashSuccess))
        window.flashSuccess = '{{session()->get('flash_success') ?? $flashSuccess}}';
    @endif
    @if(session()->has('flash_danger') || isset($flashDanger))
        window.flashDanger = '{{session()->get('flash_danger') ?? $flashDanger}}';
    @endif
    @if(session()->has('flash_info') || isset($flashInfo))
        window.flashInfo = '{{session()->get('flash_info') ?? $flashInfo}}';
    @endif
    @if(session()->has('flash_warning') || isset($flashWarning))
        window.flashWarning = '{{session()->get('flash_warning') ?? $flashWarning}}';
    @endif
</script>
@stack('script')
<!-- AdminLTE App -->

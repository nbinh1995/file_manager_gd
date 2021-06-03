<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('home')}}"><b>{{__('JOB MANAGER')}} </b>{{__('Dashboard')}}</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{__('Sign in to start your session')}}</p>

            <form action="{{ route('login') }}" method="post">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="{{ __('Username') }}"
                           value="{{ old('name') }}" name="name" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @if ($errors->has('name'))
                        @foreach ($errors->get('name') as $message)
                            <label class="text-danger">{{ $message  }}</label>
                        @endforeach
                    @endif
                </div>
                <div class="input-group mb-3">
                    <input name="password" type="password" class="form-control" placeholder="{{ __('Password') }}"
                           value="{{ old('password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <div class="text-danger" role="alert">
                            @foreach ($errors->get('password') as $message)
                                <strong>{{ $message  }}</strong>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <label for="remember" class="link">
                                <input type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <p class="mb-1">
            @if (Route::has('password.request'))
                <div><a class="link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a></div>
                @endif
                </p>
                {{-- <p class="mb-0">
                    <a href="register.html" class="text-center">Register a new membership</a>
                </p> --}}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
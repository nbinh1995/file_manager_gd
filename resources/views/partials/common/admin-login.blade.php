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
                    <input type="text" class="form-control" placeholder="{{ __('Email') }}"
                           value="{{ old('email') }}" name="email" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        @foreach ($errors->get('email') as $message)
                            <p class="text-danger text-sm">{{ $message  }}</p>
                        @endforeach
                    @endif
                </div>
                <div class="input-group mb-3">
                    <input name="password" type="password" id="password" class="form-control" placeholder="{{ __('Password') }}"
                           value="{{ old('password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"  id="show_pw" style="cursor: pointer"></span>
                            <span class="fas fa-unlock" id="hide_pw" style="display: none;cursor: pointer"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <label for="remember" class="link">
                                <input type="checkbox" name="remember" id="remember" checked
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
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
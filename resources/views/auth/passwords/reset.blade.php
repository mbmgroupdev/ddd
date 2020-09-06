<!doctype html>
<html lang="en">

<head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>MBM - ERP</title>
      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ asset('images/mbm.ico')}} " />

      <!-- Styles -->
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
      <style>
        .sign-in-from {
            padding: 20px 60px;
            border-radius: 37px;
            position: absolute;
            top: 6%;
            bottom: 6%;
            left: 0;
            right: 0;
            background: #fff;
        }
      </style>  
      <!-- Style CSS -->
      <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
      <!-- Responsive CSS -->
      <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>
   <body>
      <!-- loader END -->
      <div id="app">
        <!-- Sign in Start -->
        <section class="sign-in-page">
            <div class="container sign-in-page-bg mt-4 p-0">
                <div class="row no-gutters pl-5">
                    <div class="col-md-6 text-center ">
                        <div class="sign-in-detail text-white">
                            <div class="owl-carousel" data-autoplay="true" data-loop="true" data-nav="false" data-dots="true" data-items="1" data-items-laptop="1" data-items-tab="1" data-items-mobile="1" data-items-mobile-sm="1" data-margin="0">
                                <div class="item login-slider">
                                    <img src="{{ asset('images/login/2.jpg') }}" class="img-fluid mb-4 radius-10" alt="logo">
                                    <h4 class="mb-1 text-white">MBM Group</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 position-relative">
                        <div class="sign-in-from">
                             @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <a class="sign-in-logo text-center mb-3 " href="#">
                                    <img src="{{ asset('images/login/logo.png') }}" class="img-fluid" alt="MBM">
                                </a>
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input name="email" type="email" class="form-control mb-0 @error('email') is-invalid @enderror" id="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                                    @error('email')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Sign in END -->
      </div>
      <script src="{{ asset('js/app.js') }}"></script>
     
   </body>

</html>


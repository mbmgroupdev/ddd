<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - MBM ERP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
    

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
      
    <!-- Bootstrap CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}"> --}}
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/typography.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.rawgit.com/tonystar/bootstrap-float-label/v3.0.1/dist/bootstrap-float-label.min.css"/>
    @stack('css')
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>
<body>
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!-- Sidebar  -->
            <div class="iq-sidebar">
                <div class="iq-sidebar-logo d-flex justify-content-between">
                   <a href="{{ url('/') }}">
                   <img src="{{ asset('images/mbm-logo-w.png') }}" class="img-fluid" alt="MBM">
                   {{-- <span>MBM</span> --}}
                   </a>
                   <div class="iq-menu-bt-sidebar">
                      <div class="iq-menu-bt align-self-center">
                         <div class="wrapper-menu">
                            <div class="main-circle"><i class="ri-more-fill"></i></div>
                            <div class="hover-circle"><i class="ri-more-2-fill"></i></div>
                         </div>
                      </div>
                   </div>
                </div>
                <div id="sidebar-scrollbar">
                    @yield('nav')
                    <div class="p-3"></div>
                </div>
            </div>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                <!-- TOP Nav Bar -->
                <div class="iq-top-navbar">
                   <div class="iq-navbar-custom">
                      <div class="iq-sidebar-logo">
                         <div class="top-logo">
                            <a href="index-2.html" class="logo">
                            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="">
                            <span>MBM Group</span>
                            </a>
                         </div>
                      </div>
                      <nav class="navbar navbar-expand-lg navbar-light p-0">
                         <div class="iq-search-bar">
                            <form action="#" class="searchbox">
                               <input type="text" class="text search-input" placeholder="Type here to search...">
                               <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                            </form>
                         </div>
                         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                         <i class="ri-menu-3-line"></i>
                         </button>
                         <div class="iq-menu-bt align-self-center">
                            <div class="wrapper-menu">
                               <div class="main-circle"><i class="ri-more-fill"></i></div>
                               <div class="hover-circle"><i class="ri-more-2-fill"></i></div>
                            </div>
                         </div>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list">
                               <!-- <li class="nav-item">
                                  <a class="search-toggle iq-waves-effect language-title" href="#"><img src="{{ asset('assets/images/small/flag-01.png') }}" alt="img-flaf" class="img-fluid mr-1" style="height: 16px; width: 16px;" /> English <i class="ri-arrow-down-s-line"></i></a>
                                  <div class="iq-sub-dropdown">
                                     <a class="iq-sub-card" href="#"><img src="{{ asset('assets/images/small/flag-02.png') }}" alt="img-flaf" class="img-fluid mr-2" />French</a>
                                     <a class="iq-sub-card" href="#"><img src="{{ asset('assets/images/small/flag-03.png') }}" alt="img-flaf" class="img-fluid mr-2" />Spanish</a>
                                     <a class="iq-sub-card" href="#"><img src="{{ asset('assets/images/small/flag-04.png') }}" alt="img-flaf" class="img-fluid mr-2" />Italian</a>
                                     <a class="iq-sub-card" href="#"><img src="{{ asset('assets/images/small/flag-05.png') }}" alt="img-flaf" class="img-fluid mr-2" />German</a>
                                     <a class="iq-sub-card" href="#"><img src="{{ asset('assets/images/small/flag-06.png') }}" alt="img-flaf" class="img-fluid mr-2" />Japanese</a>
                                  </div>
                               </li> -->
                               <li class="nav-item iq-full-screen">
                                  <a href="#" class="iq-waves-effect" id="btnFullscreen"><i class="ri-fullscreen-line"></i></a>
                               </li>
                               <li class="nav-item">
                                  <a href="#" class="search-toggle iq-waves-effect">
                                  <i class="ri-notification-3-fill"></i>
                                  <span class="bg-danger dots"></span>
                                  </a>
                                  <div class="iq-sub-dropdown">
                                     <div class="iq-card shadow-none m-0">
                                        <div class="iq-card-body p-0 ">
                                           <div class="bg-primary p-3">
                                              <h5 class="mb-0 text-white">All Notifications<small class="badge  badge-light float-right pt-1">4</small></h5>
                                           </div>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/01.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Emma Watson Bini</h6>
                                                    <small class="float-right font-size-12">Just Now</small>
                                                    <p class="mb-0">95 MB</p>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/02.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">New customer is join</h6>
                                                    <small class="float-right font-size-12">5 days ago</small>
                                                    <p class="mb-0">Jond Bini</p>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/03.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Two customer is left</h6>
                                                    <small class="float-right font-size-12">2 days ago</small>
                                                    <p class="mb-0">Jond Bini</p>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/04.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">New Mail from Fenny</h6>
                                                    <small class="float-right font-size-12">3 days ago</small>
                                                    <p class="mb-0">Jond Bini</p>
                                                 </div>
                                              </div>
                                           </a>
                                        </div>
                                     </div>
                                  </div>
                               </li>
                               <li class="nav-item dropdown">
                                  <a href="#" class="search-toggle iq-waves-effect">
                                  <i class="ri-mail-open-fill"></i>
                                  <span class="bg-primary count-mail"></span>
                                  </a>
                                  <div class="iq-sub-dropdown">
                                     <div class="iq-card shadow-none m-0">
                                        <div class="iq-card-body p-0 ">
                                           <div class="bg-primary p-3">
                                              <h5 class="mb-0 text-white">All Messages<small class="badge  badge-light float-right pt-1">5</small></h5>
                                           </div>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/01.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Bini Emma Watson</h6>
                                                    <small class="float-left font-size-12">13 Jun</small>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/02.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Lorem Ipsum Watson</h6>
                                                    <small class="float-left font-size-12">20 Apr</small>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/03.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Why do we use it?</h6>
                                                    <small class="float-left font-size-12">30 Jun</small>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/04.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Variations Passages</h6>
                                                    <small class="float-left font-size-12">12 Sep</small>
                                                 </div>
                                              </div>
                                           </a>
                                           <a href="#" class="iq-sub-card" >
                                              <div class="media align-items-center">
                                                 <div class="">
                                                    <img class="avatar-40 rounded" src="{{ asset('assets/images/user/05.jpg') }}" alt="">
                                                 </div>
                                                 <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Lorem Ipsum generators</h6>
                                                    <small class="float-left font-size-12">5 Dec</small>
                                                 </div>
                                              </div>
                                           </a>
                                        </div>
                                     </div>
                                  </div>
                               </li>
                            </ul>
                         </div>
                         <ul class="navbar-list">
                            <li>
                               <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                                  <img src='{{ auth()->user()->employee != null?asset(auth()->user()->employee['as_pic'] ):(auth()->user()->employee['as_gender'] == 'Female'?asset('assets/images/user/02.jpg'):asset('assets/images/user/01.jpg')) }}' class="img-fluid rounded mr-3" alt="{{ auth()->user()->name }}" onError='this.onerror=null;this.src="{{ (auth()->user()->employee['as_gender'] == 'Female'?asset('assets/images/user/02.jpg'):asset('assets/images/user/01.jpg')) }}";'>
                                  <div class="caption">
                                     <h6 class="mb-0 line-height">{{ auth()->user()->name }}</h6>
                                     {{-- <span class="font-size-12">Available</span> --}}
                                  </div>
                               </a>
                               <div class="iq-sub-dropdown iq-user-dropdown">
                                  <div class="iq-card shadow-none m-0">
                                     <div class="iq-card-body p-0 ">
                                        <div class="bg-primary p-3">
                                           <h5 class="mb-0 text-white line-height">Hello {{ auth()->user()->name }}</h5>
                                           {{-- <span class="text-white font-size-12">Available</span> --}}
                                        </div>
                                        <a href="profile.html" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="ri-file-user-line"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">My Profile</h6>
                                                 <p class="mb-0 font-size-12">View personal profile details.</p>
                                              </div>
                                           </div>
                                        </a>
                                        <a href="profile-edit.html" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="ri-profile-line"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">Edit Profile</h6>
                                                 <p class="mb-0 font-size-12">Modify your personal details.</p>
                                              </div>
                                           </div>
                                        </a>
                                        <a href="account-setting.html" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="ri-account-box-line"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">Account settings</h6>
                                                 <p class="mb-0 font-size-12">Manage your account parameters.</p>
                                              </div>
                                           </div>
                                        </a>
                                        {{-- <a href="privacy-setting.html" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="ri-lock-line"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">Privacy Settings</h6>
                                                 <p class="mb-0 font-size-12">Control your privacy parameters.</p>
                                              </div>
                                           </div>
                                        </a> --}}
                                        <div class="d-inline-block w-100 text-center p-3">
                                           
                                           <a class="bg-primary iq-sign-btn" role="button" href="{{ route('logout') }}"
                                              onclick="event.preventDefault();
                                              document.getElementById('logout-form').submit();">
                                              {{ __('Sign out') }} <i class="ri-login-box-line ml-2"></i>
                                           </a>

                                           <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                               @csrf
                                           </form>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </li>
                         </ul>
                      </nav>
                   </div>
                </div>
                {{-- main content --}}
                <main class="">
                    @yield('content')
                </main>
                <!-- Footer -->
                <footer class="bg-white iq-footer">
                   <div class="container-fluid">
                      <div class="row">
                         <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                               <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                               <li class="list-inline-item"><a href="#">Terms of Use</a></li>
                            </ul>
                         </div>
                         <div class="col-lg-6 text-right">
                            Copyright 2018 - {{date('Y')}} <a>MBM Group</a> All Rights Reserved.
                         </div>
                      </div>
                   </div>
                </footer>
                <!-- Footer END -->
            </div>
            

        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/jquery.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/popper.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('plugins/form-validator/jquery.form-validator.min.js') }}"></script> --}}
    <!-- Appear JavaScript -->
    {{-- <script src="{{ asset('assets/js/jquery.appear.js') }}"></script> --}}

    <!-- Magnific Popup JavaScript -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Smooth Scrollbar JavaScript -->
    <script src="{{ asset('assets/js/smooth-scrollbar.js') }}"></script>
    <!-- am animated JavaScript -->
    <script src="{{ asset('assets/js/animated.js') }}"></script>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('assets/mbmerp.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
    @stack('js')
</body>
</html>

@extends('auth.admin.layouts.app')

@section('css')
    <style>
        .logo-login-default {
            width: 100px;
            min-height: 35px;
            object-position: left;
            object-fit: cover;
            background-repeat: no-repeat;
        }
    </style>
@endsection

@section('content')
    <div class="col-12 col-lg-auto h-100 pb-4 px-4 pt-0 p-lg-0">
        <div class="sw-lg-70 min-h-100 bg-foreground d-flex justify-content-center align-items-center shadow-deep py-5 full-page-content-right-border">
        <div class="sw-lg-50 px-5">
            <div class="sh-11 mb-5">
                <a href="{{ url('/') }}">
                    <div class="logo-login-default">
                        <img src="https://3.bp.blogspot.com/-84AZcdvvo6k/XxcAS-ve2mI/AAAAAAAAatg/MsweQPwt57oqf95KhA5Qg-Y2GUnqeqp4gCLcBGAsYHQ/s1600/Lambang-Kabupaten-Madiun_237-design.png" class="img-fluid">
                    </div>
                </a>
            </div>
            <div class="mb-5">
            <h2 class="cta-1 mb-0 text-primary">Selamat Datang</h2>
            <h2 class="cta-1 text-primary">Sistem Pengendalian dan Evaluasi Kabupaten Madiun</h2>
            </div>
            <div class="mb-5">
            {{-- <p class="h6">
                If you are not a member, please
                <a href="Pages.Authentication.Register.html">register</a>
                .
            </p> --}}
            </div>
            <div>
                <ul class="nav nav-tabs nav-tabs-line card-header-tabs responsive-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#first" role="tab" type="button" aria-selected="true">
                        OPD LOGIN
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#second" role="tab" type="button" aria-selected="false">BAPPEDA LOGIN</button>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade" id="first" role="tabpanel">
                            <form action="{{ route('opd.login.submit') }}" method="POST" class="tooltip-end-bottom" novalidate>
                                @csrf
                                <div class="mb-3 filled form-group tooltip-end-top">
                                    <i data-acorn-icon="email"></i>
                                    <input class="form-control" placeholder="Email" name="email" />
                                </div>
                                <div class="mb-3 filled form-group tooltip-end-top">
                                    <i data-acorn-icon="lock-off"></i>
                                    <input class="form-control pe-7" name="password" type="password" placeholder="Password" />
                                    {{-- <a class="text-small position-absolute t-3 e-3" href="Pages.Authentication.ForgotPassword.html">Forgot?</a> --}}
                                </div>
                                <button type="submit" class="btn btn-lg btn-primary mb-3">Login</button>
                                {{-- <div>
                                    <p class="h6">
                                        Jika ingin login sebagai admin,
                                        <a href="{{ route('admin.login') }}">Login Admin</a>
                                        .
                                    </p>
                                </div> --}}
                            </form>
                        </div>
                        <div class="tab-pane fade active show" id="second" role="tabpanel">
                            <form action="{{ route('admin.login.submit') }}" method="POST" class="tooltip-end-bottom" novalidate>
                                @csrf
                                <div class="mb-3 filled form-group tooltip-end-top">
                                    <i data-acorn-icon="email"></i>
                                    <input class="form-control" placeholder="Email" name="email" />
                                </div>
                                <div class="mb-3 filled form-group tooltip-end-top">
                                    <i data-acorn-icon="lock-off"></i>
                                    <input class="form-control pe-7" name="password" type="password" placeholder="Password" />
                                    {{-- <a class="text-small position-absolute t-3 e-3" href="Pages.Authentication.ForgotPassword.html">Forgot?</a> --}}
                                </div>
                                <button type="submit" class="btn btn-lg btn-primary mb-3">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

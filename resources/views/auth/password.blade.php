<!-- resources/views/auth/password.blade.php -->

@extends('layouts.navibar_guest')

@section('title', '忘记密码')

@section('content')

    <style>
        .loginpanel {
            /*position:absolute;*/
            z-index:1;
            top:20%;
            height:140px;
            /* max-width:350px;*/
            margin-top: 20px;
            background-color:#FFFFFF;
            border-radius:5px;
            border: 1px solid #2aabd2;
        }
    </style>

    <div class="container">
        <div class="row" style="margin-top: 20px">
            <center><img src="/img/0820_2.gif" alt="Cautious doodle" /></center>
        </div>
        <div class="row">
            <center><h1>Whoops~</h1></center>
        </div>
        @if (count($errors) > 0)
            <div class=" row col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
                @foreach ($errors->all() as $error)
                    <div class="alert alert-warning">
                        <a href="#" class="close" data-dismiss="alert">
                            &times;
                        </a>
                        {{ $error }}
                    </div>
                @endforeach
            </div>

        @endif
        <div class="loginpanel row col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
            <form method="POST" action="/password/email" class="form-horizontal">
                {!! csrf_field() !!}

                <div class="col-xs-12"> <br/>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" required="true" placeholder="电子邮箱">
                    <br/>
                    <!--<input class="btn btn-default" type="button" id="Button2" value="注册" onclick="window.location.href='reg'"/>-->
                    <input class="btn btn-primary" type="submit" id="Button1" style="width:100%; margin-bottom:10px;" value="发送重置密码邮件" />
            </form>
        </div>
    </div>

@endsection


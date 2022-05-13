@extends('layouts.default')

@section('content')
    <form class="rounded shadow m-auto w-25 p-4 mt-5" style="min-width: 460px;" action="{{ route('register') }}" method="post">
        @csrf

        <div class="form-outline mb-4">
            <input type="text" id="loginUsername" name="username" class="form-control" spellcheck="false" autofocus />
            <label class="form-label" for="loginUsername">Username</label>
        </div>

        <div class="form-outline mb-4">
            <input type="email" id="loginEmail" name="email" class="form-control" />
            <label class="form-label" for="loginEmail">Email</label>
        </div>

        <div class="form-outline mb-4">
            <input type="password" id="loginPassword" name="password" class="form-control" />
            <label class="form-label" for="loginPassword">Password</label>
        </div>
        
        <div class="form-outline mb-4">
            <input type="password" id="loginPassword" name="password_confirmation" class="form-control" />
            <label class="form-label" for="loginPassword">Confirm your Password</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4 w-100">Register</button>

        <div class="text-center">
            <p>Login instead? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </form>
@stop

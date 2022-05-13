@extends('layouts.default')

@section('content')
    <form class="rounded shadow m-auto w-25 p-4 mt-5" style="min-width: 460px;" action="{{ route('login') }}" method="post">
        @csrf

        <div class="form-outline mb-4">
            <input type="text" id="loginUsername" name="username" class="form-control" spellcheck="false" autofocus />
            <label class="form-label" for="loginUsername">Username</label>
        </div>

        <div class="form-outline mb-4">
            <input type="password" id="loginPassword" name="password" class="form-control" />
            <label class="form-label" for="loginPassword">Password</label>
        </div>

        <div class="row mb-4">
            <div class="col d-flex justify-content-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="stayLoggedIn" name="stayLoggedIn" checked />
                    <label class="form-check-label" for="stayLoggedIn"> Remember me </label>
                </div>
            </div>

            <div class="col">
                <a href="#!">Forgot password?</a>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4 w-100">Sign in</button>

        <div class="text-center">
            <p>Sign-up instead? <a href="{{ route('register') }}">Register</a></p>
        </div>
    </form>
@stop

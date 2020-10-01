<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-primary border-bottom">
            <div class="container">
                <img src="{{ asset(config('app.logo')) }}">
                <span class="ml-2 mr-2 text-white font-weight-bold"> | </span>
                <a class="navbar-brand text-white font-weight-bold text-uppercase" href="{{ route('home') }}">
                    {{ config('app.name') }}
                </a>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown mr-5">
                        <a id="managingDropdown" class="nav-link dropdown-toggle text-white" href= "#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fas fa-globe"></i>
                            {{ trans('general.language') }}
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="managingDropdown">
                            <a class="dropdown-item" href="{{ route('localization', ['en']) }}">
                                {{ trans('general.english') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('localization', ['vi']) }}">
                                {{ trans('general.vietnamese') }}
                            </a>
                        </div>
                    </li>
                    @guest
                        <li class="nav-item dropdown mr-5">
                            <a id="managingDropdown" class="nav-link text-white" href="#" role="button"v-pre>
                                {{ trans('login') }}
                                <span class="caret"></span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown mr-5">
                            <a id="managingDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ auth()->user()->name }}
                                <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="managingDropdown">
                                <a class="dropdown-item" href="">
                                    <i class="fas fa-user-cog"></i>
                                    {{ trans('general.setting') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                     <button class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {{ trans('logout') }}
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                .container {
                    max-width: 1170px;
                    margin: 0 auto;
                    display: flex;
                    height: 100vh;
                    align-items: center;
                    justify-content: center;
                }
                .navbar-brand {
                    display: flex;
                    flex-direction: row;
                    padding: 0px;
                    align-items: center;
                    text-decoration: none;
                }

                .navbar-brand svg {
                    width: 300px;
                    fill: #0274d6;
                    margin-right: 0.9375rem;
                    max-width: 100%;
                }

                .navbar-brand .navbar-text {
                    position: relative;
                    height: 100%;
                    color: #0274d6;
                    font-size: 40px;
                    font-weight: 900;
                    font-family: "Roboto", sans-serif;
                    text-decoration: none;
                }

                .navbar-brand .navbar-text:before {
                    border-left: 1px solid #0274d6;
                    content: '';
                    left: 0;
                    padding-left: 0.9375rem;
                }            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <div class="container" style="flex-direction: column; text-align: center;">
        <a href="/homepage" class="navbar-brand" style="flex-direction: column;">
            <img
                src="{{ asset('images/netherlands.jpg') }}"
                alt="Naar Nederland - Netherlands"
                style="max-width: 500px; width: 100%; border-radius: 12px; margin-bottom: 20px;"
            >

            <span class="navbar-text" style="border: none; font-size: 42px;">
            Naar Nederland
        </span>
        </a>

        <!-- Links section -->
        <div style="margin-top: 30px;">
            <a href="/vocab-test" style="margin: 0 15px; color: #0274d6; font-weight: 600;">
                Test vocab
            </a>
            <a href="/vocab/article-match/1" style="margin: 0 15px; color: #0274d6; font-weight: 600;">
                Article
            </a>
        </div>
    </div>


    @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>

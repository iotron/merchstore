<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name')}}</title>
    {{-- @vite(['resources/css/filament/admin/theme.css','resources/js/app.js']) --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
    <livewire:styles />
    <style>
        .no-scroll::-webkit-scrollbar {
            display: none;
        }

        .no-scroll {
            scrollbar-width: none;
        }
    </style>
</head>
<body class="w-screen h-screen flex flex-col bg-purple-950 text-white overflow-x-hidden">

    <main class="flex-grow w-full h-full">
        {{ $slot }}
    </main>

<!-- Javascript -->
@stack('javascript')
@stack('script')
<livewire:scripts />
</body>
</html>

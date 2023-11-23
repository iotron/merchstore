<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <x-invoice.layout-style />
</head>
<body>


<header class="clearfix">
    <div id="logo">
        <img src="{{ public_path('/logo.webp') }}">
    </div>
    <div id="company">
        <h2 class="name">{{config('app.name')}}</h2>
        <div>455 Foggy Heights, AZ 85004, US</div>
        <div>(602) 519-0450</div>
        <div><a href="mailto:company@example.com">company@jetpax.in</a></div>
    </div>
    </div>
</header>
<main>
    {{$slot}}
</main>




<footer>
    Invoice was created on a computer and is valid without the signature and seal.
</footer>

</body>
</html>

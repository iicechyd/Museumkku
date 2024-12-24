@props(['bodyClass'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('material/assets/css/material-dashboard.css?v=3.0.0') }}" rel="stylesheet" />

</head>
<body class="{{ $bodyClass }}">
{{ $slot }}
@stack('js')

<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="{{ asset('material/assets/js/material-dashboard.min.js') }}"></script>
</body>
</html>

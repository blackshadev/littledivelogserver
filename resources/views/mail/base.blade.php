<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        h1 {
            font-weight: bold;
        }

        h1, p, a {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center">
            <h1>@yield('title')</h1>
            @yield('content')
        </td>
    </tr>
</body>
</html>

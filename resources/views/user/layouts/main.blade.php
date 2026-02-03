<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .user-navbar {
            background: #2563eb;
            color: white;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-navbar h3 {
            font-size: 18px;
        }

        .user-navbar form button {
            background: white;
            color: #2563eb;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .user-content {
            padding: 30px;
        }

        .user-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f9fafb;
        }
    </style>
</head>
<body>

<div class="user-navbar">
    <h3>Dashboard User</h3>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>

<div class="user-content">
    <div class="user-card">
        @yield('content')
    </div>
</div>

</body>
</html>

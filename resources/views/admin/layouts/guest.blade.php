<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Login' }} | {{ config('app.name', 'MOROCCO 2030') }} Admin</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --panel: #ffffff;
            --line: #d9dfec;
            --ink: #122033;
            --muted: #627086;
            --primary: #0f5cc0;
            --primary-dark: #0a458f;
            --danger: #c0392b;
            --success: #1f7a45;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #eef3fb 0%, #f9fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: min(440px, 100%);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 25px 60px rgba(18, 32, 51, 0.08);
        }
        .auth-card h1 { margin: 0 0 8px; font-size: 28px; }
        .auth-card p { margin: 0 0 24px; color: var(--muted); }
        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .field input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
        }
        .field input:focus {
            outline: 2px solid rgba(15, 92, 192, 0.15);
            border-color: var(--primary);
        }
        .checkbox {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--muted);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover { background: var(--primary-dark); }
        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
        }
        .alert-danger {
            background: #fff1ef;
            border: 1px solid #f2c1bb;
            color: var(--danger);
        }
        .alert-success {
            background: #eef9f1;
            border: 1px solid #bddfca;
            color: var(--success);
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>

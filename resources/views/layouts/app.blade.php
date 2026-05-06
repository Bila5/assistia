<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AssistIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Lato', sans-serif; }
        h1,h2,h3,h4 { font-family: 'Playfair Display', serif; }
        body { background: #f4f6fb; color: #1a2a4a; min-height: 100vh; }
        .navbar { background: #0f1f3d; border-bottom: 2px solid #c9a84c; padding: 0 2rem; height: 65px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #c9a84c; text-decoration: none; letter-spacing: 1px; }
        .navbar-brand span { color: #ffffff; }
        .navbar-right { display: flex; align-items: center; gap: 1.5rem; }
        .navbar-user { color: #a0b0c8; font-size: 0.9rem; }
        .navbar-logout { color: #c9a84c; font-size: 0.85rem; text-decoration: none; border: 1px solid #c9a84c; padding: 6px 14px; border-radius: 4px; transition: all 0.2s; background: none; cursor: pointer; }
        .navbar-logout:hover { background: #c9a84c; color: #0f1f3d; }
        .navbar-link { color: #a0b0c8; font-size: 0.9rem; text-decoration: none; transition: color 0.2s; }
        .navbar-link:hover { color: #c9a84c; }
        .main-content { padding: 2rem; max-width: 1100px; margin: 0 auto; }
        .card { background: #ffffff; border: 1px solid #dce4f0; border-radius: 10px; padding: 1.8rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .card-title { font-family: 'Playfair Display', serif; color: #0f1f3d; font-size: 1.1rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #c9a84c; display: inline-block; }
        .btn-gold { background: #c9a84c; color: #0f1f3d; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; text-decoration: none; display: inline-block; }
        .btn-gold:hover { background: #e8c76a; }
        .btn-blue { background: #0f1f3d; color: #ffffff; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; width: 100%; }
        .btn-blue:hover { background: #1a3a6e; }
        .btn-green { background: #1a5a3a; color: #ffffff; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; width: 100%; }
        .btn-green:hover { background: #2a8a5a; }
        .btn-danger { background: none; border: none; color: #e05555; cursor: pointer; font-size: 0.85rem; }
        .btn-danger:hover { color: #ff3333; }
        .form-input { width: 100%; background: #f8faff; border: 1px solid #dce4f0; color: #1a2a4a; padding: 10px 14px; border-radius: 6px; margin-bottom: 1rem; font-size: 0.95rem; box-sizing: border-box; }
        .form-input:focus { outline: none; border-color: #c9a84c; background: #fff; }
        .form-label { color: #5a6a8a; font-size: 0.85rem; margin-bottom: 4px; display: block; }
        .alert-success { background: #f0fff4; border: 1px solid #2a8a2a; color: #1a6a1a; padding: 12px 16px; border-radius: 6px; margin-bottom: 1rem; }
        .alert-error { background: #fff0f0; border: 1px solid #8a2a2a; color: #6a1a1a; padding: 12px 16px; border-radius: 6px; margin-bottom: 1rem; }
        .badge { font-size: 0.72rem; padding: 3px 8px; border-radius: 20px; background: #e8eef8; color: #5a6a8a; margin-left: 8px; }
        .message-item { padding: 10px 0; border-bottom: 1px solid #eef2f8; }
        .message-sender { color: #c9a84c; font-weight: 700; font-size: 0.9rem; }
        .message-content { color: #2a3a5a; margin-top: 2px; }
        .task-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eef2f8; }
        .task-done { text-decoration: line-through; color: #aabbcc; }
        .btn-toggle-done { background: #f0fff4; border: 1px solid #2a8a2a; color: #1a6a1a; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; }
        .btn-toggle-pending { background: #f0f4ff; border: 1px solid #2a5aae; color: #1a3a8a; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; }
        .summary-box { background: #fffbf0; border-left: 3px solid #c9a84c; padding: 14px 18px; border-radius: 0 6px 6px 0; color: #2a3a5a; line-height: 1.7; }
        .page-title { font-family: 'Playfair Display', serif; color: #0f1f3d; font-size: 1.8rem; margin-bottom: 1.5rem; }
        .conversation-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: #ffffff; border: 1px solid #dce4f0; border-radius: 8px; margin-bottom: 0.8rem; transition: border-color 0.2s; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .conversation-item:hover { border-color: #c9a84c; }
        .conversation-link { color: #0f1f3d; text-decoration: none; font-size: 1rem; font-weight: 600; }
        .conversation-link:hover { color: #c9a84c; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media(max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('conversations.index') }}" class="navbar-brand">Assist<span>IA</span></a>
        <div class="navbar-right">
            @auth
                <a href="{{ route('dashboard') }}" class="navbar-link">Dashboard</a>
                <a href="{{ route('conversations.index') }}" class="navbar-link">Conversas</a>
                <a href="{{ route('organization.show') }}" class="navbar-link">🏢 Organização</a>
                <span class="navbar-user">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="navbar-logout">Sair</button>
                </form>
            @endauth
        </div>
    </nav>
    <div class="main-content">
        {{ $slot }}
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up — WebhookLogs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Syne:wght@400;600;700;800&display=swap');
        * { font-family: 'Syne', sans-serif; box-sizing: border-box; }
        code, pre, .mono { font-family: 'JetBrains Mono', monospace; }

        .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { opacity:0; animation: fadeUp .5s ease forwards; }
        .d1 { animation-delay:.05s; }
        .d2 { animation-delay:.15s; }
        .d3 { animation-delay:.25s; }
        .d4 { animation-delay:.35s; }

        @keyframes pulse-ring {
            0%   { transform:scale(.9); opacity:1; }
            70%  { transform:scale(1.6); opacity:0; }
            100% { transform:scale(1.6); opacity:0; }
        }
        .pulse-dot { position:relative; display:inline-block; width:8px; height:8px; }
        .pulse-dot::before {
            content:'';
            position:absolute;
            inset:0;
            border-radius:50%;
            background:#4ade80;
            animation: pulse-ring 2s cubic-bezier(.455,.03,.515,.955) infinite;
        }
        .pulse-inner { position:relative;z-index:1;width:8px;height:8px;border-radius:50%;background:#22c55e;display:block; }

        .field {
            width:100%;
            background:#09090b;
            border:1px solid #27272a;
            border-radius:10px;
            padding:10px 14px;
            color:#f4f4f5;
            font-size:14px;
            font-family:'JetBrains Mono', monospace;
            outline:none;
            transition: border-color .15s;
        }
        .field::placeholder { color:#52525b; }
        .field:focus { border-color:#52525b; }
        .field.error { border-color:#7f1d1d !important; background:#0c0505; }

        .pw-wrap { position:relative; }
        .pw-wrap .field { padding-right:44px; }
        .pw-toggle {
            position:absolute; right:12px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer; color:#52525b;
            padding:4px; line-height:0; transition:color .15s;
        }
        .pw-toggle:hover { color:#a1a1aa; }

        .btn-primary {
            width:100%;
            background:#f4f4f5;
            color:#09090b;
            border:none;
            border-radius:10px;
            padding:11px 0;
            font-family:'Syne',sans-serif;
            font-weight:700;
            font-size:14px;
            cursor:pointer;
            transition: background .15s, opacity .15s;
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-primary:hover { background:#ffffff; }
        .btn-primary:disabled { opacity:.5; cursor:not-allowed; }

        .divider { display:flex; align-items:center; gap:12px; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background:#27272a; }

        .glow { box-shadow: 0 0 80px 0 rgba(34,197,94,.08); }

        @keyframes spin { to { transform:rotate(360deg); } }
        .spinner { animation:spin .7s linear infinite; }
    </style>
</head>
<body class="grid-bg bg-zinc-950 text-zinc-100 min-h-screen flex flex-col items-center justify-center px-4 py-12">

    <div class="fixed inset-0 pointer-events-none flex items-center justify-center">
        <div style="width:500px;height:500px;border-radius:50%;background:radial-gradient(ellipse at center,rgba(34,197,94,.06) 0%,transparent 70%);"></div>
    </div>

    <a href="{{ route('home') }}" class="fade-up d1 relative z-10 flex items-center gap-2.5 mb-8">
        <span class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center">
            <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </span>
        <span class="text-base font-extrabold tracking-tight">WebhookLogs</span>
    </a>

    <div class="fade-up d2 glow relative z-10 w-full max-w-sm bg-zinc-900 border border-zinc-800 rounded-2xl p-8">

        <div class="mb-7">
            <div class="flex items-center gap-2 mb-5">
                <span class="pulse-dot"><span class="pulse-inner"></span></span>
                <span class="mono text-xs text-green-400 font-semibold">Create account</span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight mb-1">Sign up</h1>
            <p class="text-sm text-zinc-400">Get started with WebhookLogs today.</p>
        </div>

        @if($errors->any())
        <div class="mb-5 bg-red-950/60 border border-red-900 rounded-xl px-4 py-3 mono text-xs text-red-300 space-y-1">
            @foreach($errors->all() as $error)
            <p>⚠ {{ $error }}</p>
            @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="mb-5 bg-green-950/60 border border-green-900 rounded-xl px-4 py-3 mono text-xs text-green-300">
            ✓ {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
            @csrf

            <div class="mb-4">
                <label for="name" class="block mono text-xs text-zinc-400 mb-1.5">Full name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    value="{{ old('name') }}"
                    placeholder="John Doe"
                    class="field {{ $errors->has('name') ? 'error' : '' }}"
                    required
                />
                @error('name')
                <p class="mt-1.5 mono text-xs text-red-400">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block mono text-xs text-zinc-400 mb-1.5">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    class="field {{ $errors->has('email') ? 'error' : '' }}"
                    required
                />
                @error('email')
                <p class="mt-1.5 mono text-xs text-red-400">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block mono text-xs text-zinc-400 mb-1.5">Password</label>
                <div class="pw-wrap">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••••••"
                        class="field {{ $errors->has('password') ? 'error' : '' }}"
                        required
                    />
                    <button type="button" class="pw-toggle" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                        <svg id="eye-icon-pw" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="mt-1.5 mono text-xs text-red-400">⚠ {{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary" id="submit-btn">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Create account
            </button>
        </form>

        @if(Route::has('login'))
        <div class="mt-6">
            <div class="divider">
                <span class="mono text-xs text-zinc-600">or</span>
            </div>
            <p class="mt-5 text-center mono text-xs text-zinc-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white transition underline underline-offset-2">
                    Sign in
                </a>
            </p>
        </div>
        @endif
    </div>

    <p class="fade-up d3 relative z-10 mt-6 mono text-xs text-zinc-600 text-center">
        <a href="{{ route('home') }}" class="hover:text-zinc-400 transition">← Back to home</a>
    </p>

    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = fieldId === 'password' 
            ? document.getElementById('eye-icon-pw')
            : document.getElementById('eye-icon-confirm');
        // Fallback in case confirm field icon doesn't exist (we removed confirm field)
        if (!icon) return;
        const show = field.type === 'password';
        field.type = show ? 'text' : 'password';
        icon.innerHTML = show
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }

    // No password strength check, no confirm password fields, thus no validation needed.
    // Only submission loading state remains
    document.getElementById('register-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-4 h-4 spinner" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Creating account…`;
    });
    </script>
</body>
</html>
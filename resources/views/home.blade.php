<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Real-time webhook inspection & delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Syne:wght@400;600;700;800&display=swap');
        *, *::before, *::after { font-family: 'Syne', sans-serif; box-sizing: border-box; -webkit-font-smoothing: antialiased; }
        code, pre, .mono { font-family: 'JetBrains Mono', monospace; }

        .badge-yellow { background: #fef08a; color: #713f12; }
        .badge-blue   { background: #bfdbfe; color: #1e3a8a; }
        .badge-green  { background: #bbf7d0; color: #14532d; }
        .badge-red    { background: #fecaca; color: #7f1d1d; }
        .badge-gray   { background: #e5e7eb; color: #374151; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }

        nav { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }

        .btn-primary { transition: background .2s ease, transform .15s ease, box-shadow .15s ease; }
        .btn-primary:hover { background: #ffffff; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.45); }
        .btn-primary:active { transform: translateY(0); }
        .btn-ghost { transition: color .2s, border-color .2s, background .2s; }
        .btn-ghost:hover { color: #e4e4e7; border-color: #52525b; background: rgba(39,39,42,.5); }
        .btn-nav { transition: color .2s, background .2s; }
        .btn-nav:hover { color: #f4f4f5; background: #27272a; }

        .hero-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        @keyframes pulse-ring {
            0%   { transform: scale(.9); opacity: 1; }
            70%  { transform: scale(1.6); opacity: 0; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        .pulse-dot { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 10px; height: 10px; flex-shrink: 0; }
        .pulse-dot::before { content:''; position:absolute; inset:0; border-radius:50%; background:#4ade80; animation: pulse-ring 2.2s cubic-bezier(.455,.03,.515,.955) infinite; }
        .pulse-dot-inner { position:relative; z-index:1; width:8px; height:8px; border-radius:50%; background:#22c55e; display:block; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(22px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { opacity:0; animation: fadeUp .65s cubic-bezier(.22,.68,0,1.2) forwards; }
        .delay-1 { animation-delay: .06s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .28s; }
        .delay-4 { animation-delay: .42s; }
        .delay-5 { animation-delay: .56s; }
        .delay-6 { animation-delay: .70s; }
        .delay-7 { animation-delay: .84s; }

        .scroll-reveal { opacity:0; transform:translateY(18px); transition: opacity .55s cubic-bezier(.22,.68,0,1.1), transform .55s cubic-bezier(.22,.68,0,1.1); }
        .scroll-reveal.visible { opacity:1; transform:translateY(0); }

        @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .ticker-track { animation: ticker 30s linear infinite; display:flex; width:max-content; }
        .ticker-track:hover { animation-play-state: paused; }

        .grad-text { background: linear-gradient(135deg,#f4f4f5 0%,#71717a 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .grad-text-green { background: linear-gradient(135deg,#4ade80 0%,#22d3ee 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }

        @keyframes glow-pulse {
            0%,100% { box-shadow: 0 0 60px 0 rgba(34,197,94,.10); }
            50%      { box-shadow: 0 0 90px 0 rgba(34,197,94,.22); }
        }
        .glow-green { animation: glow-pulse 3s ease-in-out infinite; }
        .glow-badge { box-shadow: 0 0 0 1px rgba(34,197,94,.25), inset 0 0 14px rgba(34,197,94,.05); }

        .feat-card { transition: border-color .25s, background .25s, transform .25s cubic-bezier(.22,.68,0,1.2); }
        .feat-card:hover { border-color: #52525b; background: #18181b; transform: translateY(-3px); }

        .step-card { transition: border-color .25s, background .25s; }
        .step-card:hover { border-color: #3f3f46; background: #18181b; }

        .mock-frame { background:#09090b; border:1px solid #27272a; border-radius:14px; overflow:hidden; box-shadow:0 32px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.04); }
        .mock-topbar { background:#111113; border-bottom:1px solid #1f1f23; padding:10px 14px; display:flex; align-items:center; gap:8px; }
        .mock-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

        .code-window { background:#09090b; border:1px solid #27272a; border-radius:12px; overflow:hidden; }
        .code-titlebar { background:#18181b; border-bottom:1px solid #27272a; padding:10px 16px; display:flex; align-items:center; gap:8px; }
        .dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }

        @keyframes slideInRow {
            from { opacity:0; transform:translateX(-12px); }
            to   { opacity:1; transform:translateX(0); }
        }
        .log-row { animation: slideInRow .45s cubic-bezier(.22,.68,0,1.2) both; }
        .log-row:nth-child(1) { animation-delay: .85s; }
        .log-row:nth-child(2) { animation-delay:1.10s; }
        .log-row:nth-child(3) { animation-delay:1.35s; }
        .log-row:nth-child(4) { animation-delay:1.60s; }

        @keyframes countUp {
            from { opacity:0; transform:scale(.85); }
            to   { opacity:1; transform:scale(1); }
        }
        .stat-card { animation: countUp .5s cubic-bezier(.22,.68,0,1.2) both; }
        .stat-card:nth-child(1) { animation-delay:1.05s; }
        .stat-card:nth-child(2) { animation-delay:1.20s; }
        .stat-card:nth-child(3) { animation-delay:1.35s; }

        .footer-link { transition: color .2s; }
        .footer-link:hover { color: #d4d4d8; }

        .mobile-menu { max-height:0; overflow:hidden; transition: max-height .35s cubic-bezier(.22,.68,0,1.1), opacity .3s ease; opacity:0; }
        .mobile-menu.open { max-height:320px; opacity:1; }

        .card { transition: border-color .2s ease; }
        .card:hover { border-color: #3f3f46; }

        .compare-row { transition: background .2s; }
        .compare-row:hover { background: rgba(39,39,42,.4); }

        @media (max-width:640px) {
            .hero-h1 { font-size: clamp(2.1rem,9.5vw,3.5rem); }
            .hero-sub { font-size: clamp(.9rem,4vw,1.1rem); }
            .section-h2 { font-size: clamp(1.5rem,6.5vw,2.5rem); }
        }

        @keyframes blinkCursor { 0%,100%{opacity:1} 50%{opacity:0} }
        .blink { animation: blinkCursor 1s step-end infinite; }

        .token-display { word-break: break-all; font-family: 'JetBrains Mono', monospace; font-size: 12px; background: #0a0a0a; padding: 12px; border-radius: 8px; border: 1px solid #3f3f3f; color: #86efac; }

        @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .slide-down { animation: slideDown .3s cubic-bezier(.22,.68,0,1.1); }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen overflow-x-hidden">

<nav class="fixed top-0 inset-x-0 z-50 bg-zinc-950/85 border-b border-zinc-800/60">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0">
            <span class="w-7 h-7 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </span>
            <span class="text-sm font-bold tracking-tight">{{ config('app.name') }}</span>
        </a>

        <div class="hidden sm:flex items-center gap-2">
            @auth
                <span class="mono text-xs text-zinc-500 mr-1">{{ auth()->user()->name }}</span>
                <a href="{{ route('webhook.logs') }}" class="btn-nav mono text-xs text-zinc-400 px-3 py-1.5 rounded-lg">Logs</a>
                <a href="{{ route('webhook.logs') }}" class="btn-primary mono text-xs bg-zinc-100 text-zinc-900 px-4 py-1.5 rounded-lg font-semibold">Dashboard →</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-nav mono text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg">Logout</button>
                </form>
            @else
                <a href="#features" class="btn-nav mono text-xs text-zinc-400 px-3 py-1.5 rounded-lg">Features</a>
                <a href="#how-it-works" class="btn-nav mono text-xs text-zinc-400 px-3 py-1.5 rounded-lg">How it works</a>
                <a href="{{ route('login') }}" class="btn-nav mono text-xs text-zinc-400 px-3 py-1.5 rounded-lg">Login</a>
                <a href="{{ route('register') }}" class="btn-primary mono text-xs bg-zinc-100 text-zinc-900 px-4 py-1.5 rounded-lg font-semibold">Sign up free →</a>
            @endauth
        </div>

        <button id="menu-btn" class="sm:hidden flex flex-col gap-1.5 p-2 rounded-lg hover:bg-zinc-800 transition" aria-label="Toggle menu">
            <span class="menu-bar block w-5 h-0.5 bg-zinc-400 transition-all duration-300 origin-center"></span>
            <span class="menu-bar block w-5 h-0.5 bg-zinc-400 transition-all duration-300 origin-center"></span>
            <span class="menu-bar block w-5 h-0.5 bg-zinc-400 transition-all duration-300 origin-center"></span>
        </button>
    </div>

    <div id="mobile-menu" class="mobile-menu sm:hidden border-t border-zinc-800/60 bg-zinc-950/95">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-2">
            @auth
                <div class="flex items-center gap-2 pb-3 border-b border-zinc-800">
                    <div class="w-7 h-7 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                        <span class="mono text-[10px] text-zinc-400 font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <span class="mono text-xs text-zinc-400">{{ auth()->user()->name }}</span>
                </div>
                <a href="{{ route('webhook.logs') }}" class="mono text-sm text-zinc-300 hover:text-white px-3 py-2.5 rounded-lg hover:bg-zinc-800 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Logs
                </a>
                <a href="{{ route('webhook.logs') }}" class="mono text-sm bg-zinc-100 text-zinc-900 px-4 py-2.5 rounded-xl font-semibold text-center transition hover:bg-white">Open Dashboard →</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left mono text-sm text-red-400 hover:text-red-300 px-3 py-2.5 rounded-lg hover:bg-zinc-800 transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mono text-sm text-zinc-300 hover:text-white px-3 py-2.5 rounded-lg hover:bg-zinc-800 transition">Login</a>
                <a href="{{ route('register') }}" class="mono text-sm bg-zinc-100 text-zinc-900 px-4 py-2.5 rounded-xl font-semibold text-center transition hover:bg-white">Sign up free →</a>
            @endauth
        </div>
    </div>
</nav>

<section class="hero-grid relative min-h-screen flex flex-col items-center justify-center pt-14 pb-20 px-4 overflow-hidden">
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none" aria-hidden="true">
        <div class="w-[min(700px,100vw)] h-[min(700px,100vw)] rounded-full"
             style="background:radial-gradient(ellipse at center, rgba(34,197,94,.07) 0%, transparent 65%);"></div>
    </div>

    <div class="relative z-10 w-full max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            <div class="text-left">
                <div class="fade-up delay-1 inline-flex items-center gap-2 glow-badge border border-zinc-700/60 bg-zinc-900/80 rounded-full px-4 py-1.5 mb-7">
                    <span class="pulse-dot"><span class="pulse-dot-inner"></span></span>
                    <span class="mono text-xs text-green-400 font-semibold">Live event capture enabled</span>
                </div>

                <h1 class="hero-h1 fade-up delay-2 text-5xl sm:text-6xl font-extrabold tracking-tight leading-[1.06] mb-5">
                    Inspect every<br>
                    <span class="grad-text-green">webhook event</span><br>
                    <span class="grad-text">in real time.</span>
                </h1>

                <p class="hero-sub fade-up delay-3 text-zinc-400 text-lg leading-relaxed mb-8">
                    Dispatch, capture, and debug webhook payloads with zero&nbsp;configuration.
                    Full logs, signature verification, and a built-in test mode.
                </p>

                <div class="fade-up delay-4 flex flex-col sm:flex-row items-start gap-3">
                    <a href="{{ route('webhook.logs') }}"
                       class="btn-primary w-full sm:w-auto mono text-sm bg-zinc-100 text-zinc-900 px-6 py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Open dashboard
                    </a>
                    <a href="#how-it-works"
                       class="btn-ghost w-full sm:w-auto mono text-sm text-zinc-400 border border-zinc-700 px-6 py-3.5 rounded-xl text-center">
                        See how it works ↓
                    </a>
                </div>

                <div class="fade-up delay-5 mt-8 flex flex-wrap gap-x-5 gap-y-2.5 text-xs mono text-zinc-500">
                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Zero config</span>
                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>HMAC verified</span>
                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Live &amp; Test modes</span>
                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Free forever</span>
                </div>
            </div>

            <div class="fade-up delay-6 hidden lg:block">
                <div class="mock-frame">
                    <div class="mock-topbar">
                        <span class="mock-dot bg-red-500/80"></span>
                        <span class="mock-dot bg-yellow-500/80"></span>
                        <span class="mock-dot bg-green-500/80"></span>
                        <span class="mono text-[11px] text-zinc-500 ml-3">Webhook Logs</span>
                        <div class="ml-auto flex items-center gap-1.5">
                            <span class="pulse-dot" style="width:8px;height:8px;"><span class="pulse-dot-inner" style="width:6px;height:6px;"></span></span>
                            <span class="mono text-[10px] text-green-400">live</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-px bg-zinc-800" style="border-bottom:1px solid #27272a;">
                        <div class="stat-card bg-zinc-900 px-4 py-3">
                            <p class="mono text-[10px] text-zinc-500 uppercase tracking-wider mb-0.5">Total</p>
                            <p class="mono text-lg font-bold text-zinc-100">1,284</p>
                        </div>
                        <div class="stat-card bg-zinc-900 px-4 py-3">
                            <p class="mono text-[10px] text-zinc-500 uppercase tracking-wider mb-0.5">Delivered</p>
                            <p class="mono text-lg font-bold text-green-400">1,271</p>
                        </div>
                        <div class="stat-card bg-zinc-900 px-4 py-3">
                            <p class="mono text-[10px] text-zinc-500 uppercase tracking-wider mb-0.5">Failed</p>
                            <p class="mono text-lg font-bold text-red-400">13</p>
                        </div>
                    </div>

                    <div class="divide-y divide-zinc-800/60">
                        @php
                        $mockLogs = [
                            ['event'=>'user.created',          'id'=>'a3f2b1c0', 'ago'=>'2s ago',  'badge'=>'badge-green',  'label'=>'Processed'],
                            ['event'=>'payment.succeeded',     'id'=>'9d1e4f7a', 'ago'=>'14s ago', 'badge'=>'badge-green',  'label'=>'Processed'],
                            ['event'=>'subscription.cancelled','id'=>'c8b05e3d', 'ago'=>'1m ago',  'badge'=>'badge-yellow', 'label'=>'Pending'],
                            ['event'=>'invoice.failed',        'id'=>'2f7a9c1b', 'ago'=>'3m ago',  'badge'=>'badge-red',    'label'=>'Failed'],
                        ];
                        @endphp
                        @foreach($mockLogs as $log)
                        <div class="log-row flex items-center justify-between px-4 py-3 hover:bg-zinc-900/60 transition-colors cursor-default">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="mono text-[10px] text-zinc-600 w-16 shrink-0 truncate">{{ $log['id'] }}…</span>
                                <span class="text-xs font-semibold text-zinc-100 truncate">{{ $log['event'] }}</span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="mono text-[10px] text-zinc-600">{{ $log['ago'] }}</span>
                                <span class="mono text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $log['badge'] }}">{{ $log['label'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-zinc-900/50 border-t border-zinc-800 px-4 py-2.5 flex items-center gap-2">
                        <span class="mono text-[10px] text-zinc-600">Showing 1–4 of 1,284</span>
                        <div class="ml-auto flex items-center gap-1">
                            <span class="mono text-[10px] text-zinc-600 bg-zinc-800 px-2 py-0.5 rounded">←</span>
                            <span class="mono text-[10px] text-zinc-300 bg-zinc-700 px-2 py-0.5 rounded">1</span>
                            <span class="mono text-[10px] text-zinc-600 bg-zinc-800 px-2 py-0.5 rounded">2</span>
                            <span class="mono text-[10px] text-zinc-600 bg-zinc-800 px-2 py-0.5 rounded">→</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 ml-4 inline-flex items-center gap-2 bg-zinc-900/90 border border-zinc-700 rounded-full px-3.5 py-1.5 backdrop-blur-sm" style="animation: fadeUp .5s 2s cubic-bezier(.22,.68,0,1.2) both; opacity:0;">
                    <svg class="w-3 h-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span class="mono text-[11px] text-zinc-300">Webhook dispatched</span>
                    <span class="mono text-[11px] text-green-400 font-semibold">200 OK</span>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="border-y border-zinc-800 py-3 overflow-hidden bg-zinc-900/40">
    <div class="ticker-track gap-10" aria-hidden="true">
        @foreach(range(1,2) as $_)
            <span class="flex items-center gap-10 pr-10">
                @foreach(['user.created','payment.succeeded','order.placed','subscription.cancelled','invoice.paid','checkout.completed','refund.issued','session.started','lead.captured','trial.started'] as $evt)
                    <span class="mono text-xs text-zinc-500 flex items-center gap-2 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500/50 flex-shrink-0"></span>{{ $evt }}
                    </span>
                @endforeach
            </span>
        @endforeach
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 py-20 sm:py-24 scroll-reveal" id="features">
    <div class="text-center mb-12">
        <p class="mono text-xs text-zinc-500 uppercase tracking-widest mb-3">What you get</p>
        <h2 class="section-h2 text-3xl sm:text-4xl font-extrabold tracking-tight">Everything to debug webhooks<br class="hidden sm:block"> without the guesswork.</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
        $features = [
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
              'color'=>'text-blue-400','bg'=>'bg-blue-400/10','title'=>'Full payload inspection',
              'desc'=>'See the complete JSON body of every event — pretty-printed with syntax highlighting. Nothing hidden.'],
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
              'color'=>'text-green-400','bg'=>'bg-green-400/10','title'=>'Signature verification',
              'desc'=>'HMAC signature badges tell you instantly whether a request is authentic or tampered with.'],
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
              'color'=>'text-yellow-400','bg'=>'bg-yellow-400/10','title'=>'Built-in test mode',
              'desc'=>'Fire against a local echo endpoint without needing an external server. Iterate in seconds, not minutes.'],
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
              'color'=>'text-purple-400','bg'=>'bg-purple-400/10','title'=>'Live dispatch',
              'desc'=>'Send webhooks to any external callback URL directly from the dashboard — with a field builder or raw JSON.'],
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
              'color'=>'text-rose-400','bg'=>'bg-rose-400/10','title'=>'Paginated event log',
              'desc'=>'Browse all past events with timestamps, delivery status, and source tracking. Delete entries you no longer need.'],
            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
              'color'=>'text-amber-400','bg'=>'bg-amber-400/10','title'=>'Exception capture',
              'desc'=>'Delivery failures are surfaced with full exception traces — so you know exactly why it broke.'],
        ];
        @endphp

        @foreach($features as $i => $f)
        <div class="feat-card scroll-reveal bg-zinc-900 border border-zinc-800 rounded-xl p-6" style="transition-delay:{{ $i * 0.07 }}s">
            <div class="w-10 h-10 rounded-lg {{ $f['bg'] }} flex items-center justify-center mb-4">
                <svg class="w-5 h-5 {{ $f['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $f['icon'] !!}</svg>
            </div>
            <h3 class="font-bold text-sm text-zinc-100 mb-1.5">{{ $f['title'] }}</h3>
            <p class="text-sm text-zinc-400 leading-relaxed">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="border-t border-zinc-800 py-20 sm:py-24 px-4" id="how-it-works">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14 scroll-reveal">
            <p class="mono text-xs text-zinc-500 uppercase tracking-widest mb-3">How it works</p>
            <h2 class="section-h2 text-3xl sm:text-4xl font-extrabold tracking-tight">Three steps to clarity.</h2>
        </div>

        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            @php
            $steps = [
                ['n'=>'01','title'=>'Dispatch a webhook','desc'=>'Paste your callback URL and JSON payload into the live dispatcher, or switch to test mode to fire locally without a server.','badge'=>'● Live','badgeClass'=>'bg-green-400/10 text-green-400 border-green-800'],
                ['n'=>'02','title'=>'Event is captured','desc'=>'The event is logged instantly with its payload, source, timestamp, and HTTP status code — no manual setup required.','badge'=>'◎ Captured','badgeClass'=>'bg-blue-400/10 text-blue-400 border-blue-800'],
                ['n'=>'03','title'=>'Inspect & debug','desc'=>'Expand any log entry to view the full payload, delivery details, exception trace, and signature verification badge.','badge'=>'✓ Verified','badgeClass'=>'bg-yellow-400/10 text-yellow-400 border-yellow-800'],
            ];
            @endphp

            @foreach($steps as $i => $step)
            <div class="step-card scroll-reveal relative bg-zinc-900 border border-zinc-800 rounded-xl p-6" style="transition-delay:{{ $i * 0.12 }}s">
                <div class="mono text-4xl font-extrabold text-zinc-800 mb-4 leading-none select-none">{{ $step['n'] }}</div>
                <span class="inline-block mono text-xs font-semibold px-2.5 py-1 rounded-full border {{ $step['badgeClass'] }} mb-4">{{ $step['badge'] }}</span>
                <h3 class="font-bold text-zinc-100 text-base mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-zinc-400 leading-relaxed">{{ $step['desc'] }}</p>
                @if($i < 2)
                    <div class="hidden md:flex absolute -right-3.5 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-zinc-900 border border-zinc-700 items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <div class="md:hidden flex items-center justify-center py-1">
                        <svg class="w-5 h-5 text-zinc-700 rotate-90 mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 pb-20 sm:pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
        <div class="scroll-reveal">
            <p class="mono text-xs text-zinc-500 uppercase tracking-widest mb-4">What a log entry looks like</p>
            <h2 class="section-h2 text-3xl font-extrabold tracking-tight mb-4">Every detail.<br>Nothing hidden.</h2>
            <p class="text-zinc-400 leading-relaxed mb-6 text-sm sm:text-base">
                Each event expands on demand — showing meta, delivery info, the raw JSON payload, and any exception that occurred during delivery.
            </p>
            <div class="space-y-3">
                @foreach(['Full JSON payload (pretty-printed)', 'Callback URL + delivery status', 'Processed & failed timestamps', 'Exception trace on failure', 'One-click entry deletion'] as $item)
                <div class="flex items-center gap-3 text-sm text-zinc-300">
                    <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $item }}
                </div>
                @endforeach
            </div>
        </div>

        <div class="code-window scroll-reveal" style="transition-delay:.1s">
            <div class="code-titlebar flex-wrap gap-y-2">
                <span class="dot bg-red-500"></span>
                <span class="dot bg-yellow-500"></span>
                <span class="dot bg-green-500"></span>
                <span class="mono text-xs text-zinc-500 ml-2">event · user.created</span>
                <span class="ml-auto mono text-xs text-green-400 font-semibold">● processed</span>
            </div>
            <div class="p-4 sm:p-5 overflow-x-auto">
                <p class="mono text-[10px] text-zinc-500 uppercase tracking-widest mb-3">Meta</p>
                <div class="space-y-1 mb-5 mono text-xs">
                    <p><span class="text-zinc-500">ID:</span>         <span class="text-zinc-300">a3f2b1c0-4d9e-...</span></p>
                    <p><span class="text-zinc-500">Event:</span>      <span class="text-green-300">user.created</span></p>
                    <p><span class="text-zinc-500">Source:</span>     <span class="text-zinc-300">api.yourapp.com</span></p>
                    <p><span class="text-zinc-500">Created:</span>    <span class="text-zinc-300">2025-05-12 14:32:01</span></p>
                    <p><span class="text-zinc-500">Processed:</span>  <span class="text-green-400">2025-05-12 14:32:02</span></p>
                </div>
                <p class="mono text-[10px] text-zinc-500 uppercase tracking-widest mb-2">Payload</p>
                <pre class="text-xs text-green-300 leading-relaxed whitespace-pre-wrap sm:whitespace-pre">{
  <span class="text-zinc-400">"event"</span>: <span class="text-yellow-300">"user.created"</span>,
  <span class="text-zinc-400">"data"</span>: {
    <span class="text-zinc-400">"id"</span>:    <span class="text-blue-300">847</span>,
    <span class="text-zinc-400">"email"</span>: <span class="text-yellow-300">"ada@example.com"</span>,
    <span class="text-zinc-400">"plan"</span>:  <span class="text-yellow-300">"pro"</span>
  }
}</pre>
                <div class="mt-4 pt-4 border-t border-zinc-800 flex items-center gap-2">
                    <span class="mono text-[10px] bg-green-950 text-green-300 border border-green-800 px-2.5 py-1 rounded-full font-semibold">✓ Signature valid</span>
                    <span class="mono text-[10px] text-zinc-500">HMAC-SHA256</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="border-t border-zinc-800 py-20 sm:py-24 px-4 scroll-reveal">
    <div class="max-w-3xl mx-auto text-center">
        <p class="mono text-xs text-zinc-500 uppercase tracking-widest mb-4">Your personal endpoint</p>
        <h2 class="section-h2 text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">One URL. Every event.</h2>
        <p class="text-zinc-400 text-base max-w-lg mx-auto mb-8">
            Sign up and you instantly get a permanent webhook URL. Point any service at it and start receiving events immediately.
        </p>
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5 mb-8 text-left max-w-xl mx-auto">
            <p class="mono text-[10px] text-zinc-500 uppercase tracking-wider mb-2">Example</p>
            <pre class="text-xs text-zinc-400 mono bg-zinc-950 rounded-lg p-3 overflow-x-auto whitespace-pre">curl -X POST https://{{ rtrim(config('app.url'), '/') }}/token/<span class="text-violet-400">{your-token}</span> \
  -H "Content-Type: application/json" \
  -d '{"event":"order.placed","data":{"id":1}}'</pre>
        </div>
        <a href="{{ route('register') }}"
           class="btn-primary inline-flex items-center gap-2 mono text-sm bg-zinc-100 text-zinc-900 px-7 py-3.5 rounded-xl font-semibold">
            Get your webhook URL →
        </a>
    </div>
</section>

<section class="border-t border-zinc-800">
    <div class="max-w-6xl mx-auto px-4 py-20 sm:py-24 text-center scroll-reveal">
        <div class="inline-flex items-center gap-2 bg-zinc-900 border border-zinc-700 rounded-full px-4 py-1.5 mb-8">
            <span class="pulse-dot"><span class="pulse-dot-inner"></span></span>
            <span class="mono text-xs text-green-400 font-semibold">Ready when you are</span>
        </div>

        <h2 class="section-h2 text-4xl sm:text-5xl font-extrabold tracking-tight mb-5">
            Stop guessing.<br>Start inspecting.
        </h2>

        <p class="text-zinc-400 text-base sm:text-lg max-w-md mx-auto mb-10 px-4">
            Open the dashboard and dispatch your first webhook in under 30 seconds.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('register') }}"
               class="glow-green btn-primary inline-flex items-center gap-2 mono text-sm bg-zinc-100 text-zinc-900 px-8 py-3.5 rounded-xl font-semibold">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Create free account
            </a>
            <a href="{{ route('webhook.logs') }}"
               class="btn-ghost inline-flex items-center gap-2 mono text-sm text-zinc-400 border border-zinc-700 px-8 py-3.5 rounded-xl">
                Go to webhook logs →
            </a>
        </div>
    </div>
</section>

<footer class="border-t border-zinc-800 py-8 px-4">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                <svg class="w-3 h-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </span>
            <span class="mono text-xs text-zinc-500">{{ config('app.name') }}</span>
        </div>
        <div class="flex items-center gap-6">
            <a href="{{ route('webhook.logs') }}" class="footer-link mono text-xs text-zinc-500 transition">Logs</a>
            <a href="{{ route('webhook.logs') }}" class="footer-link mono text-xs text-zinc-500 transition">Dashboard</a>
            <a href="{{ route('register') }}" class="footer-link mono text-xs text-zinc-500 transition">Sign up</a>
        </div>
        <p class="mono text-xs text-zinc-600">Built with Laravel · {{ date('Y') }}</p>
    </div>
</footer>

<script>
    const menuBtn    = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const bars       = menuBtn.querySelectorAll('.menu-bar');
    let menuOpen = false;

    menuBtn.addEventListener('click', () => {
        menuOpen = !menuOpen;
        mobileMenu.classList.toggle('open', menuOpen);
        if (menuOpen) {
            bars[0].style.cssText = 'transform:translateY(8px) rotate(45deg);';
            bars[1].style.cssText = 'opacity:0; transform:scaleX(0);';
            bars[2].style.cssText = 'transform:translateY(-8px) rotate(-45deg);';
        } else {
            bars.forEach(b => b.style.cssText = '');
        }
    });

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.10, rootMargin: '0px 0px -36px 0px' });

    document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                if (menuOpen) {
                    menuOpen = false;
                    mobileMenu.classList.remove('open');
                    bars.forEach(b => b.style.cssText = '');
                }
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
</body>
</html>
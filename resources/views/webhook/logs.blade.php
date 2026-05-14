<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Logs & API Token Manager</title>
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
        details summary { cursor: pointer; list-style: none; }
        details summary::-webkit-details-marker { display: none; }
        details[open] .chevron { transform: rotate(90deg); }
        .chevron { transition: transform .25s cubic-bezier(.22,.68,0,1.2); display: inline-block; }
        details { transition: background .2s ease; }
        details[open] { background: #18181b; }
        .tab-btn { transition: all .2s cubic-bezier(.22,.68,0,1.1); }
        .tab-btn.active-live  { background: #bbf7d0; color: #14532d; border-color: #16a34a; }
        .tab-btn.active-test  { background: #fef08a; color: #713f12; border-color: #ca8a04; }
        .tab-btn.active-poll  { background: #e9d5ff; color: #4c1d95; border-color: #7c3aed; }
        .copy-btn { transition: all .2s ease; }
        .copy-btn:active { transform: scale(0.95); }
        .token-display { word-break: break-all; font-family: 'JetBrains Mono', monospace; font-size: 12px; background: #0a0a0a; padding: 12px; border-radius: 8px; border: 1px solid #3f3f3f; color: #86efac; }
        input, textarea, select { transition: border-color .2s ease, box-shadow .2s ease; }
        input:focus, textarea:focus, select:focus { box-shadow: 0 0 0 2px rgba(113,113,122,.25); }
        .btn-primary { transition: background .2s ease, transform .15s ease, box-shadow .15s ease; }
        .btn-primary:hover { background: #ffffff; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }
        .btn-danger   { transition: background .2s ease, border-color .2s ease, color .2s ease; }
        .btn-secondary{ transition: background .2s ease, border-color .2s ease; }
        .card { transition: border-color .2s ease; }
        .card:hover { border-color: #3f3f46; }
        summary { transition: background .15s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        details[open] > div { animation: slideDown .25s cubic-bezier(.22,.68,0,1.1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn .3s cubic-bezier(.22,.68,0,1.1) forwards; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .animate-spin { animation: spin .7s linear infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
        .pulse-dot { animation: pulse-dot 1.2s ease-in-out infinite; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 3px; }

        .payload-preview { font-family: 'JetBrains Mono', monospace; font-size: 11px; background: #0a0a0a; padding: 12px; border-radius: 8px; border: 1px solid #27272a; color: #86efac; white-space: pre-wrap; word-break: break-all; min-height: 80px; max-height: 260px; overflow-y: auto; line-height: 1.6; }
        .toggle-mode-btn { font-family: 'JetBrains Mono', monospace; font-size: 11px; padding: 4px 10px; border-radius: 6px; border: 1px solid #3f3f46; background: transparent; color: #71717a; cursor: pointer; transition: all .15s ease; }
        .toggle-mode-btn:hover { border-color: #a1a1aa; color: #d4d4d8; }
        .toggle-mode-btn.active { border-color: #6366f1; color: #a5b4fc; background: #1e1b4b; }

        .field-input { background: #09090b; border: 1px solid #3f3f46; border-radius: 8px; padding: 7px 10px; color: #f4f4f5; font-family: 'JetBrains Mono', monospace; font-size: 12px; width: 100%; outline: none; }
        .field-input::placeholder { color: #52525b; }
        .field-input:focus { border-color: #52525b; }
        .field-select { background: #09090b; border: 1px solid #3f3f46; border-radius: 8px; padding: 7px 10px; color: #a1a1aa; font-family: 'JetBrains Mono', monospace; font-size: 12px; outline: none; cursor: pointer; }
        .field-select option { background: #09090b; }
        .field-select:focus { border-color: #52525b; }
        .remove-btn { background: transparent; border: 1px solid #3f3f46; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #71717a; cursor: pointer; transition: all .15s ease; flex-shrink: 0; }
        .remove-btn:hover { border-color: #ef4444; color: #ef4444; background: #450a0a; }
        .add-field-btn { background: transparent; border: 1px dashed #3f3f46; border-radius: 8px; padding: 7px 14px; color: #71717a; font-size: 12px; font-family: 'JetBrains Mono', monospace; cursor: pointer; transition: all .15s ease; display: flex; align-items: center; gap: 6px; }
        .add-field-btn:hover { border-color: #a1a1aa; color: #d4d4d8; background: #18181b; }
        .add-obj-btn { background: transparent; border: 1px dashed #4338ca; border-radius: 8px; padding: 6px 12px; color: #818cf8; font-size: 11px; font-family: 'JetBrains Mono', monospace; cursor: pointer; transition: all .15s ease; display: flex; align-items: center; gap: 5px; }
        .add-obj-btn:hover { border-color: #6366f1; color: #a5b4fc; background: #1e1b4b; }
        .add-arr-btn { background: transparent; border: 1px dashed #0e7490; border-radius: 8px; padding: 6px 12px; color: #22d3ee; font-size: 11px; font-family: 'JetBrains Mono', monospace; cursor: pointer; transition: all .15s ease; display: flex; align-items: center; gap: 5px; }
        .add-arr-btn:hover { border-color: #06b6d4; color: #67e8f9; background: #0c4a6e; }

        .nested-block { border-left: 2px solid #27272a; padding-left: 12px; margin-top: 8px; }
        .nested-block.indent-1 { border-color: #3f3f46; }
        .nested-block.indent-2 { border-color: #52525b; }
        .nested-block.indent-3 { border-color: #71717a; }
        .nested-block.arr-block { border-color: #155e75; }

        .obj-header { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .obj-label { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #818cf8; background: #1e1b4b; border: 1px solid #3730a3; border-radius: 5px; padding: 2px 8px; }
        .arr-label { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #22d3ee; background: #0c4a6e; border: 1px solid #155e75; border-radius: 5px; padding: 2px 8px; }
        .item-label { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #71717a; background: #18181b; border: 1px solid #27272a; border-radius: 4px; padding: 1px 6px; }

        .scalar-row { display: grid; grid-template-columns: 1fr 100px 1fr auto; gap: 6px; align-items: center; margin-bottom: 6px; }
        .scalar-row-arr { display: grid; grid-template-columns: 100px 1fr auto; gap: 6px; align-items: center; margin-bottom: 6px; }

        .required-badge { font-family: 'JetBrains Mono', monospace; font-size: 9px; color: #f59e0b; background: #451a03; border: 1px solid #78350f; border-radius: 4px; padding: 1px 5px; }
        .locked-input { opacity: 0.6; pointer-events: none; }

        @media (max-width: 600px) {
            .scalar-row { grid-template-columns: 1fr auto; grid-template-rows: auto auto; }
            .scalar-row .type-col { grid-column: 1; }
            .scalar-row .val-col { grid-column: 1; }
            .scalar-row .rm-col { grid-row: 1; grid-column: 2; align-self: start; }
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen">
<div class="max-w-6xl mx-auto px-4 py-8 sm:py-10">

    <div class="mb-8 sm:mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Webhook Logs</h1>
                <p class="text-zinc-400 text-sm mt-1 mono">{{ $events->total() }} total events</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                @auth
                <div class="flex items-center gap-2 bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-2 flex-1 sm:flex-none min-w-0">
                    <div class="w-7 h-7 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center flex-shrink-0">
                        <span class="mono text-[10px] text-zinc-300 font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-zinc-100 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-zinc-500 mono truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-danger text-xs mono bg-red-950/50 hover:bg-red-950 text-red-400 hover:text-red-300 px-3 py-2 rounded-lg border border-red-800 hover:border-red-600 flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
                @endauth
                <a href="{{ route('webhook.logs') }}" class="btn-secondary text-xs mono bg-zinc-800 hover:bg-zinc-700 px-3 py-2 rounded-lg border border-zinc-700 whitespace-nowrap">
                    ↻ Refresh
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-950/60 border border-green-800 rounded-xl p-4 fade-in">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mono text-sm text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="card bg-zinc-900 border border-zinc-800 rounded-xl p-5 sm:p-6 mb-6 sm:mb-8">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold">🔗 Your Webhook URL</h2>
                <p class="text-xs text-zinc-400 mono mt-1">Send POST requests to this URL to create webhook events. One URL per user — permanent.</p>
            </div>
        </div>
        <div class="bg-zinc-950 rounded-lg p-4 border border-zinc-700">
            <div class="flex items-center gap-2">
                <div class="flex-1 bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2.5 mono text-xs text-violet-300 break-all select-all" id="webhook-url-display">
                    {{ rtrim(config('app.url'), '/') }}/token/{{ auth()->user()->webhook_token }}
                </div>
                <button onclick="copyWebhookUrl()" id="copy-url-btn"
                        class="copy-btn flex-shrink-0 mono text-xs bg-violet-700 hover:bg-violet-600 text-white px-3 py-2.5 rounded-lg transition whitespace-nowrap">
                    📋 Copy
                </button>
            </div>
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs mono">
                <p><span class="text-zinc-500">Method:</span> <span class="text-zinc-300">POST</span></p>
                <p><span class="text-zinc-500">Content-Type:</span> <span class="text-zinc-300">application/json</span></p>
                <p><span class="text-zinc-500">Auth:</span> <span class="text-green-400">Token embedded in URL</span></p>
            </div>
            <div class="mt-3 pt-3 border-t border-zinc-800">
                <p class="text-[10px] text-zinc-500 mono mb-2 uppercase tracking-wider">Example curl</p>
                <pre class="text-[10px] text-zinc-400 mono bg-zinc-900 rounded p-2 overflow-x-auto whitespace-pre">curl -X POST {{ rtrim(config('app.url'), '/') }}/token/{{ auth()->user()->webhook_token }} \
  -H "Content-Type: application/json" \
  -d '{"event":"order.placed","data":{"id":1}}'</pre>
            </div>
        </div>
    </div>

    <div class="card bg-zinc-900 border border-zinc-800 rounded-xl p-5 sm:p-6 mb-6 sm:mb-8">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-5 sm:mb-6">
            <span class="text-sm font-semibold text-zinc-300">Mode:</span>
            <button id="tab-live" onclick="switchTab('live')"
                    class="tab-btn active-live text-xs mono px-4 py-1.5 rounded-full border border-green-700 font-semibold">
                ● Live
            </button>
            <button id="tab-test" onclick="switchTab('test')"
                    class="tab-btn text-xs mono px-4 py-1.5 rounded-full border border-zinc-700 text-zinc-400 font-semibold">
                ◎ Test
            </button>
            <button id="tab-poll" onclick="switchTab('poll')"
                    class="tab-btn text-xs mono px-4 py-1.5 rounded-full border border-zinc-700 text-zinc-400 font-semibold flex items-center gap-1.5">
                <span id="poll-dot" class="w-1.5 h-1.5 rounded-full bg-zinc-500 inline-block"></span>
                Poll
            </button>
            <span id="mode-badge" class="text-xs mono text-zinc-500 hidden sm:inline ml-auto"></span>
        </div>
        <span id="mode-badge-mobile" class="block sm:hidden text-xs mono text-zinc-500 mb-4 -mt-2"></span>

        <div id="send-alert-dispatch" class="hidden mb-4 px-4 py-3 rounded-lg mono text-xs fade-in"></div>

        <div id="live-panel">
            <div class="mb-4">
                <label class="block text-xs text-zinc-400 mono mb-1.5">Callback URL</label>
                <input id="cb-url" type="url" placeholder="https://your-server.com/receive"
                       class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-100 mono placeholder-zinc-600 focus:outline-none focus:border-zinc-500"/>
            </div>
            <div id="builder-wrapper-live">
                <div id="structured-live">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-zinc-400 mono font-semibold">Payload Builder</p>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleRawMode('live')" id="raw-toggle-live" class="toggle-mode-btn">{ } Raw JSON</button>
                            <button onclick="loadSample('live')" class="text-xs mono text-zinc-400 hover:text-zinc-200 border border-zinc-700 hover:border-zinc-500 px-3 py-1 rounded-lg transition">
                                Load sample
                            </button>
                        </div>
                    </div>
                    <div id="payload-builder-live" class="space-y-3"></div>
                </div>
                <div id="raw-live" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-zinc-400 mono font-semibold">Raw JSON</p>
                        <button onclick="toggleRawMode('live')" id="raw-toggle-live-2" class="toggle-mode-btn active">⬅ Builder</button>
                    </div>
                    <textarea id="payload-raw-live" rows="14" oninput="onRawInput('live')"
                              class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-green-300 mono placeholder-zinc-600 focus:outline-none focus:border-zinc-500 resize-y"
                              placeholder='{ "event": "user.created", "data": { "id": 1 } }'></textarea>
                    <p id="json-err-live" class="hidden mt-1 text-xs text-red-400 mono">⚠ Invalid JSON</p>
                </div>
            </div>
            <div class="mt-4 mb-4">
                <p class="text-[10px] text-zinc-600 mono uppercase tracking-wider mb-1.5">Preview</p>
                <div id="preview-live" class="payload-preview">{}</div>
            </div>
            <button onclick="sendWebhook()" id="send-btn-live"
                    class="btn-primary w-full sm:w-auto mono text-sm bg-zinc-100 text-zinc-900 hover:bg-white px-5 py-2.5 rounded-lg font-semibold flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Dispatch webhook
            </button>
        </div>

        <div id="test-panel" class="hidden">
            <div class="bg-yellow-950/40 border border-yellow-800/50 rounded-lg px-4 py-3 mb-5 mono text-xs text-yellow-300 leading-relaxed">
                ◎ Test mode — fires against the local <span class="text-yellow-200 font-semibold">/webhook/echo</span> endpoint. No external URL needed.
            </div>
            <div id="builder-wrapper-test">
                <div id="structured-test">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-zinc-400 mono font-semibold">Payload Builder</p>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleRawMode('test')" id="raw-toggle-test" class="toggle-mode-btn">{ } Raw JSON</button>
                            <button onclick="loadSample('test')" class="text-xs mono text-zinc-400 hover:text-zinc-200 border border-zinc-700 hover:border-zinc-500 px-3 py-1 rounded-lg transition">
                                Load sample
                            </button>
                        </div>
                    </div>
                    <div id="payload-builder-test" class="space-y-3"></div>
                </div>
                <div id="raw-test" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-zinc-400 mono font-semibold">Raw JSON</p>
                        <button onclick="toggleRawMode('test')" id="raw-toggle-test-2" class="toggle-mode-btn active">⬅ Builder</button>
                    </div>
                    <textarea id="payload-raw-test" rows="14" oninput="onRawInput('test')"
                              class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-green-300 mono placeholder-zinc-600 focus:outline-none focus:border-zinc-500 resize-y"
                              placeholder='{ "event": "order.placed", "data": { "id": 1 } }'></textarea>
                    <p id="json-err-test" class="hidden mt-1 text-xs text-red-400 mono">⚠ Invalid JSON</p>
                </div>
            </div>
            <div class="mt-4 mb-4">
                <p class="text-[10px] text-zinc-600 mono uppercase tracking-wider mb-1.5">Preview</p>
                <div id="preview-test" class="payload-preview">{}</div>
            </div>
            <button onclick="sendTest()" id="send-btn-test"
                    class="btn-primary w-full sm:w-auto mono text-sm bg-yellow-300 text-yellow-950 hover:bg-yellow-200 px-5 py-2.5 rounded-lg font-semibold flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Run test
            </button>
            <div id="test-response" class="hidden mt-6 fade-in">
                <p class="text-zinc-500 uppercase tracking-widest text-[10px] mono mb-2">Echo response</p>
                <pre id="test-response-body" class="bg-zinc-950 rounded-lg p-4 text-xs text-green-300 mono overflow-x-auto whitespace-pre-wrap break-all"></pre>
                <div id="sig-badge" class="mt-2 inline-flex items-center gap-1.5 text-xs mono px-3 py-1 rounded-full border"></div>
            </div>
        </div>

        <div id="poll-panel" class="hidden">
            <div class="bg-violet-950/40 border border-violet-800/50 rounded-lg px-4 py-3 mb-5 mono text-xs text-violet-300 leading-relaxed flex items-center gap-2">
                <span id="poll-status-dot" class="w-2 h-2 rounded-full bg-violet-700 flex-shrink-0"></span>
                <span id="poll-status-text">Polling paused — click Start to begin fetching every 4 s</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <button onclick="startPolling()" id="poll-start-btn"
                        class="mono text-xs bg-violet-700 hover:bg-violet-600 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Start
                </button>
                <button onclick="stopPolling()" id="poll-stop-btn" disabled
                        class="mono text-xs bg-zinc-800 text-zinc-500 px-4 py-2 rounded-lg font-semibold flex items-center gap-2 border border-zinc-700 transition cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"/></svg>
                    Stop
                </button>
                <button onclick="clearPollResults()" class="mono text-xs text-zinc-400 hover:text-zinc-200 border border-zinc-700 hover:border-zinc-500 px-4 py-2 rounded-lg transition">
                    Clear
                </button>
                <span class="mono text-xs text-zinc-500 ml-auto" id="poll-counter"></span>
            </div>
            <div id="poll-next-bar" class="hidden mb-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="mono text-[10px] text-zinc-500 uppercase tracking-wider">Next fetch in</span>
                    <span id="poll-countdown" class="mono text-xs text-violet-300">4s</span>
                </div>
                <div class="h-1 bg-zinc-800 rounded-full overflow-hidden">
                    <div id="poll-progress" class="h-full bg-violet-500 rounded-full" style="width:100%; transition: width 1s linear;"></div>
                </div>
            </div>
            <div id="poll-results" class="space-y-3 max-h-[480px] overflow-y-auto pr-1">
                <div id="poll-empty" class="text-center py-12 text-zinc-600">
                    <p class="text-3xl mb-2">📡</p>
                    <p class="mono text-xs">No events received yet. Start polling to listen.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('deleted'))
    <div class="mb-6 bg-red-950 border border-red-700 text-red-300 text-sm px-4 py-3 rounded-lg mono fade-in">
        {{ session('deleted') }}
    </div>
    @endif

    @if($events->isEmpty())
        <div class="text-center py-20 text-zinc-500">
            <div class="text-5xl mb-4">📭</div>
            <p class="text-lg font-semibold">No webhook events yet</p>
            <p class="text-sm mt-1">Events will appear here once webhooks are sent.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
            <details class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden group transition-all duration-200">
                <summary class="flex items-center justify-between px-4 sm:px-5 py-4 hover:bg-zinc-800/60 transition-colors select-none">
                    <div class="flex items-center gap-2 sm:gap-4 min-w-0 flex-1">
                        <span class="chevron text-zinc-500 text-xs flex-shrink-0">▶</span>
                        <span class="mono text-xs text-zinc-500 w-16 sm:w-24 shrink-0 truncate hidden xs:block" title="{{ $event->id }}">
                            {{ substr($event->id, 0, 8) }}…
                        </span>
                        <span class="font-semibold text-sm text-zinc-100 truncate">{{ $event->event }}</span>
                        <span class="mono text-xs text-zinc-500 hidden md:block truncate max-w-[160px]">{{ $event->source }}</span>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0 ml-2">
                        <span class="text-xs mono text-zinc-500 hidden lg:block whitespace-nowrap">
                            {{ $event->created_at->diffForHumans() }}
                        </span>
                        <span class="text-xs font-semibold px-2 sm:px-2.5 py-1 rounded-full mono badge-{{ $event->statusColor() }} whitespace-nowrap">
                            {{ $event->statusLabel() }}
                        </span>
                    </div>
                </summary>
                <div class="border-t border-zinc-800 px-4 sm:px-5 py-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs mono">
                        <div class="bg-zinc-950 rounded-lg p-3 space-y-1.5">
                            <p class="text-zinc-500 uppercase tracking-widest text-[10px] mb-2">Meta</p>
                            <p><span class="text-zinc-400">ID:</span> <span class="text-zinc-200 break-all">{{ $event->id }}</span></p>
                            <p><span class="text-zinc-400">Event:</span> <span class="text-zinc-200">{{ $event->event }}</span></p>
                            <p><span class="text-zinc-400">Source:</span> <span class="text-zinc-200">{{ $event->source ?? '—' }}</span></p>
                            <p><span class="text-zinc-400">Created:</span> <span class="text-zinc-200">{{ $event->created_at->toDateTimeString() }}</span></p>
                            @if($event->processed_at)
                            <p><span class="text-zinc-400">Processed:</span> <span class="text-green-400">{{ $event->processed_at->toDateTimeString() }}</span></p>
                            @endif
                            @if($event->failed_at)
                            <p><span class="text-zinc-400">Failed:</span> <span class="text-red-400">{{ $event->failed_at->toDateTimeString() }}</span></p>
                            @endif
                        </div>
                        <div class="bg-zinc-950 rounded-lg p-3 space-y-1.5">
                            <p class="text-zinc-500 uppercase tracking-widest text-[10px] mb-2">Delivery</p>
                            <p class="text-zinc-400">Callback URL:</p>
                            <p class="text-zinc-200 break-all">{{ $event->callback_url ?? '—' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-zinc-500 uppercase tracking-widest text-[10px] mono mb-2">Payload</p>
                        <pre class="bg-zinc-950 rounded-lg p-3 sm:p-4 text-xs text-green-300 mono overflow-x-auto whitespace-pre-wrap break-all leading-relaxed">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                    @if($event->exception)
                    <div>
                        <p class="text-zinc-500 uppercase tracking-widest text-[10px] mono mb-2">Exception</p>
                        <pre class="bg-red-950/50 border border-red-900 rounded-lg p-3 sm:p-4 text-xs text-red-300 mono overflow-x-auto whitespace-pre-wrap break-all">{{ $event->exception }}</pre>
                    </div>
                    @endif
                    <div class="flex justify-end pt-1">
                        <form method="POST" action="{{ route('webhook.destroy', $event->id) }}"
                              onsubmit="return confirm('Delete this log entry?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs mono text-red-400 hover:text-red-300 border border-red-800 hover:border-red-600 px-3 py-1.5 rounded-lg transition">
                                Delete Entry
                            </button>
                        </form>
                    </div>
                </div>
            </details>
            @endforeach
        </div>
        <div class="mt-8">{{ $events->links() }}</div>
    @endif
</div>

<script>
const csrfToken = '{{ csrf_token() }}';
const echoUrl   = '{{ route("webhook.echo") }}';
const appUrl    = '{{ rtrim(config("app.url"), "/") }}';
let currentTab  = 'live';

let pollInterval  = null;
let pollTimer     = null;
let pollCount     = 0;
let pollSeenIds   = new Set();
let pollCountdown = 4;
const POLL_SECS   = 4;

const rawModes = { live: false, test: false };

const builderState = {
    live: null,
    test: null
};

function h(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function uid() {
    return Math.random().toString(36).slice(2, 9);
}

function makeScalarNode(key, value, type, locked) {
    return { _id: uid(), _kind: 'scalar', key, value: value !== undefined ? value : '', type: type || 'string', locked: !!locked };
}

function makeObjectNode(key, children, locked) {
    return { _id: uid(), _kind: 'object', key, locked: !!locked, children: children || [] };
}

function makeArrayNode(key, items, locked) {
    return { _id: uid(), _kind: 'array', key, locked: !!locked, items: items || [] };
}

function makeArrayItemNode(children) {
    return { _id: uid(), _kind: 'array-item', children: children || [] };
}

function defaultState() {
    return {
        event: '',
        data: makeObjectNode('data', [
            makeScalarNode('id', '', 'string', false)
        ], true)
    };
}

function stateToPayload(state) {
    const out = {};
    if (state.event) out.event = state.event;
    out.data = nodeToValue(state.data);
    return out;
}

function nodeToValue(node) {
    if (node._kind === 'scalar') {
        return castValue(node.value, node.type);
    }
    if (node._kind === 'object') {
        const obj = {};
        node.children.forEach(c => {
            const key = c.key || c._id;
            obj[key] = nodeToValue(c);
        });
        return obj;
    }
    if (node._kind === 'array') {
        return node.items.map(item => {
            const obj = {};
            item.children.forEach(c => {
                const key = c.key || c._id;
                obj[key] = nodeToValue(c);
            });
            return obj;
        });
    }
    return null;
}

function castValue(v, type) {
    if (type === 'null') return null;
    if (type === 'number') return v === '' ? 0 : Number(v);
    if (type === 'boolean') return v.trim().toLowerCase() !== 'false' && v.trim() !== '0' && v.trim() !== '';
    return v;
}

function renderBuilder(mode) {
    const state = builderState[mode];
    const container = document.getElementById('payload-builder-' + mode);
    container.innerHTML = '';

    const eventRow = document.createElement('div');
    eventRow.className = 'bg-zinc-950 border border-zinc-800 rounded-xl p-4 mb-1';
    eventRow.innerHTML = `
        <div class="flex items-center gap-2 mb-2">
            <span class="obj-label">root</span>
            <span class="required-badge">required</span>
        </div>
        <div class="scalar-row">
            <input class="field-input" type="text" value="${h(state.event)}" placeholder="user.payment_updated" oninput="onEventInput(this,'${mode}')"/>
            <div class="field-input locked-input" style="color:#71717a;cursor:default;">string</div>
            <div></div>
            <div class="w-7"></div>
        </div>
        <div class="text-[10px] mono text-zinc-600 mt-1 px-1">event <span class="required-badge">required</span></div>
    `;
    container.appendChild(eventRow);

    const dataBlock = document.createElement('div');
    dataBlock.className = 'bg-zinc-950 border border-zinc-800 rounded-xl p-4';
    dataBlock.innerHTML = `<div class="flex items-center gap-2 mb-3">
        <span class="obj-label">data</span>
        <span class="required-badge">required</span>
    </div>`;

    const idRow = document.createElement('div');
    const idNode = state.data.children.find(c => c.key === 'id');
    idRow.innerHTML = renderScalarRow(idNode, mode, 'data', true);
    idRow.className = 'mb-2';
    dataBlock.appendChild(idRow);

    const extraChildren = state.data.children.filter(c => c.key !== 'id');
    const extraContainer = document.createElement('div');
    extraContainer.id = 'data-children-' + mode;
    extraContainer.className = 'space-y-2';
    extraChildren.forEach(child => {
        extraContainer.appendChild(renderNode(child, mode, 'data', 1));
    });
    dataBlock.appendChild(extraContainer);

    const addBtns = document.createElement('div');
    addBtns.className = 'flex flex-wrap gap-2 mt-3 pt-3 border-t border-zinc-800';
    addBtns.innerHTML = `
        <button class="add-field-btn" onclick="addScalarToData('${mode}')">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add field
        </button>
        <button class="add-obj-btn" onclick="addObjectToData('${mode}')">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h8"/></svg>
            Add object
        </button>
        <button class="add-arr-btn" onclick="addArrayToData('${mode}')">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Add array
        </button>
    `;
    dataBlock.appendChild(addBtns);
    container.appendChild(dataBlock);

    updatePreview(mode);
}

function renderScalarRow(node, mode, parentPath, locked) {
    const typeOpts = ['string','number','boolean','null'].map(t =>
        `<option value="${t}" ${t === node.type ? 'selected' : ''}>${t}</option>`
    ).join('');
    const isNull = node.type === 'null';
    const placeholder = node.type === 'boolean' ? 'true / false' : node.type === 'null' ? '(null)' : node.type === 'number' ? '0' : 'value';
    const lockedClass = locked ? 'locked-input' : '';

    return `<div class="scalar-row" data-node-id="${node._id}" data-mode="${mode}" data-parent="${parentPath}">
        <input class="field-input ${lockedClass}" type="text" value="${h(node.key)}" placeholder="key" ${locked ? 'readonly' : `oninput="onKeyInput(this,'${node._id}','${mode}')"`}/>
        <select class="field-select" onchange="onTypeChange(this,'${node._id}','${mode}')">
            ${typeOpts}
        </select>
        <input class="field-input value-input" type="text" value="${h(node.type === 'null' ? '' : String(node.value))}" placeholder="${placeholder}" ${isNull ? 'disabled' : ''} oninput="onValueInput(this,'${node._id}','${mode}')"/>
        ${locked ? '<div class="w-7"></div>' : `<button class="remove-btn" onclick="removeNode('${node._id}','${mode}')"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>`}
    </div>`;
}

function renderNode(node, mode, parentPath, depth) {
    const el = document.createElement('div');
    if (node._kind === 'scalar') {
        el.innerHTML = renderScalarRow(node, mode, parentPath, false);
    } else if (node._kind === 'object') {
        el.appendChild(renderObjectBlock(node, mode, parentPath, depth));
    } else if (node._kind === 'array') {
        el.appendChild(renderArrayBlock(node, mode, parentPath, depth));
    }
    return el;
}

function renderObjectBlock(node, mode, parentPath, depth) {
    const indent = Math.min(depth, 3);
    const wrapper = document.createElement('div');
    wrapper.dataset.nodeId = node._id;
    wrapper.dataset.mode = mode;
    wrapper.className = `nested-block indent-${indent}`;

    const header = document.createElement('div');
    header.className = 'obj-header';
    header.innerHTML = `
        <input class="field-input" style="max-width:160px;" type="text" value="${h(node.key)}" placeholder="object key" oninput="onKeyInput(this,'${node._id}','${mode}')"/>
        <span class="obj-label">{ }</span>
        <button class="remove-btn ml-auto" onclick="removeNode('${node._id}','${mode}')"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
    `;
    wrapper.appendChild(header);

    const children = document.createElement('div');
    children.className = 'space-y-2 mb-2';
    node.children.forEach(child => {
        children.appendChild(renderNode(child, mode, parentPath + '.' + node.key, depth + 1));
    });
    wrapper.appendChild(children);

    const addBtns = document.createElement('div');
    addBtns.className = 'flex flex-wrap gap-2 mt-2';
    addBtns.innerHTML = `
        <button class="add-field-btn" style="font-size:10px;padding:4px 10px;" onclick="addChildScalar('${node._id}','${mode}')">+ field</button>
        <button class="add-obj-btn" style="font-size:10px;padding:4px 10px;" onclick="addChildObject('${node._id}','${mode}')">+ object</button>
        <button class="add-arr-btn" style="font-size:10px;padding:4px 10px;" onclick="addChildArray('${node._id}','${mode}')">+ array</button>
    `;
    wrapper.appendChild(addBtns);
    return wrapper;
}

function renderArrayBlock(node, mode, parentPath, depth) {
    const indent = Math.min(depth, 3);
    const wrapper = document.createElement('div');
    wrapper.dataset.nodeId = node._id;
    wrapper.dataset.mode = mode;
    wrapper.className = `nested-block arr-block`;

    const header = document.createElement('div');
    header.className = 'obj-header';
    header.innerHTML = `
        <input class="field-input" style="max-width:160px;" type="text" value="${h(node.key)}" placeholder="array key" oninput="onKeyInput(this,'${node._id}','${mode}')"/>
        <span class="arr-label">[ ]</span>
        <button class="remove-btn ml-auto" onclick="removeNode('${node._id}','${mode}')"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
    `;
    wrapper.appendChild(header);

    node.items.forEach((item, idx) => {
        const itemWrapper = document.createElement('div');
        itemWrapper.dataset.nodeId = item._id;
        itemWrapper.dataset.mode = mode;
        itemWrapper.className = 'nested-block indent-1 mb-2';

        const itemHeader = document.createElement('div');
        itemHeader.className = 'obj-header mb-2';
        itemHeader.innerHTML = `
            <span class="item-label">[${idx}]</span>
            <button class="remove-btn ml-auto" onclick="removeArrayItem('${node._id}','${item._id}','${mode}')"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
        `;
        itemWrapper.appendChild(itemHeader);

        const itemChildren = document.createElement('div');
        itemChildren.className = 'space-y-2 mb-2';
        item.children.forEach(child => {
            itemChildren.appendChild(renderNode(child, mode, parentPath + '.' + node.key + '[' + idx + ']', depth + 1));
        });
        itemWrapper.appendChild(itemChildren);

        const itemAddBtns = document.createElement('div');
        itemAddBtns.className = 'flex flex-wrap gap-2 mt-1';
        itemAddBtns.innerHTML = `
            <button class="add-field-btn" style="font-size:10px;padding:4px 10px;" onclick="addFieldToArrayItem('${node._id}','${item._id}','${mode}')">+ field</button>
            <button class="add-obj-btn" style="font-size:10px;padding:4px 10px;" onclick="addObjectToArrayItem('${node._id}','${item._id}','${mode}')">+ object</button>
        `;
        itemWrapper.appendChild(itemAddBtns);
        wrapper.appendChild(itemWrapper);
    });

    const addItemBtn = document.createElement('button');
    addItemBtn.className = 'add-arr-btn mt-2';
    addItemBtn.style.fontSize = '11px';
    addItemBtn.innerHTML = `<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg> Add item`;
    addItemBtn.onclick = () => addArrayItem(node._id, mode);
    wrapper.appendChild(addItemBtn);

    return wrapper;
}

function findNodeById(id, state) {
    function search(node) {
        if (node._id === id) return node;
        if (node._kind === 'object' && node.children) {
            for (const c of node.children) { const found = search(c); if (found) return found; }
        }
        if (node._kind === 'array' && node.items) {
            for (const item of node.items) {
                if (item._id === id) return item;
                for (const c of item.children) { const found = search(c); if (found) return found; }
            }
        }
        return null;
    }
    return search(state.data);
}

function removeNodeFromTree(id, node) {
    if (node._kind === 'object') {
        node.children = node.children.filter(c => c._id !== id);
        node.children.forEach(c => removeNodeFromTree(id, c));
    }
    if (node._kind === 'array') {
        node.items.forEach(item => {
            item.children = item.children.filter(c => c._id !== id);
            item.children.forEach(c => removeNodeFromTree(id, c));
        });
    }
}

function onEventInput(input, mode) {
    builderState[mode].event = input.value;
    updatePreview(mode);
}

function onKeyInput(input, nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node) { node.key = input.value; updatePreview(mode); }
}

function onValueInput(input, nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node) { node.value = input.value; updatePreview(mode); }
}

function onTypeChange(select, nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node) {
        node.type = select.value;
        const row = select.closest('.scalar-row');
        if (row) {
            const valInput = row.querySelector('.value-input');
            if (select.value === 'null') {
                valInput.disabled = true;
                valInput.value = '';
                valInput.placeholder = '(null)';
            } else {
                valInput.disabled = false;
                valInput.placeholder = select.value === 'boolean' ? 'true / false' : select.value === 'number' ? '0' : 'value';
            }
        }
        updatePreview(mode);
    }
}

function removeNode(nodeId, mode) {
    const state = builderState[mode];
    if (state.data._id === nodeId) return;
    removeNodeFromTree(nodeId, state.data);
    renderBuilder(mode);
}

function removeArrayItem(arrayId, itemId, mode) {
    const arrNode = findNodeById(arrayId, builderState[mode]);
    if (arrNode) {
        arrNode.items = arrNode.items.filter(i => i._id !== itemId);
        renderBuilder(mode);
    }
}

function addScalarToData(mode) {
    builderState[mode].data.children.push(makeScalarNode('', '', 'string', false));
    renderBuilder(mode);
}

function addObjectToData(mode) {
    builderState[mode].data.children.push(makeObjectNode('', [], false));
    renderBuilder(mode);
}

function addArrayToData(mode) {
    builderState[mode].data.children.push(makeArrayNode('', [], false));
    renderBuilder(mode);
}

function addChildScalar(nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node && node.children) { node.children.push(makeScalarNode('', '', 'string', false)); renderBuilder(mode); }
}

function addChildObject(nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node && node.children) { node.children.push(makeObjectNode('', [], false)); renderBuilder(mode); }
}

function addChildArray(nodeId, mode) {
    const node = findNodeById(nodeId, builderState[mode]);
    if (node && node.children) { node.children.push(makeArrayNode('', [], false)); renderBuilder(mode); }
}

function addArrayItem(arrayId, mode) {
    const arrNode = findNodeById(arrayId, builderState[mode]);
    if (arrNode) { arrNode.items.push(makeArrayItemNode([])); renderBuilder(mode); }
}

function addFieldToArrayItem(arrayId, itemId, mode) {
    const arrNode = findNodeById(arrayId, builderState[mode]);
    if (arrNode) {
        const item = arrNode.items.find(i => i._id === itemId);
        if (item) { item.children.push(makeScalarNode('', '', 'string', false)); renderBuilder(mode); }
    }
}

function addObjectToArrayItem(arrayId, itemId, mode) {
    const arrNode = findNodeById(arrayId, builderState[mode]);
    if (arrNode) {
        const item = arrNode.items.find(i => i._id === itemId);
        if (item) { item.children.push(makeObjectNode('', [], false)); renderBuilder(mode); }
    }
}

function updatePreview(mode) {
    if (rawModes[mode]) return;
    try {
        const payload = stateToPayload(builderState[mode]);
        document.getElementById('preview-' + mode).textContent = JSON.stringify(payload, null, 2);
    } catch(e) {}
}

function getFinalPayload(mode) {
    if (rawModes[mode]) {
        const raw = document.getElementById('payload-raw-' + mode).value.trim();
        try { return JSON.parse(raw); } catch(e) { return null; }
    }
    return stateToPayload(builderState[mode]);
}

function toggleRawMode(mode) {
    rawModes[mode] = !rawModes[mode];
    const structured = document.getElementById('structured-' + mode);
    const raw = document.getElementById('raw-' + mode);
    if (rawModes[mode]) {
        const payload = stateToPayload(builderState[mode]);
        document.getElementById('payload-raw-' + mode).value = JSON.stringify(payload, null, 2);
        structured.classList.add('hidden');
        raw.classList.remove('hidden');
        document.getElementById('preview-' + mode).textContent = JSON.stringify(payload, null, 2);
    } else {
        const rawText = document.getElementById('payload-raw-' + mode).value.trim();
        structured.classList.remove('hidden');
        raw.classList.add('hidden');
        if (rawText) {
            try {
                const parsed = JSON.parse(rawText);
                builderState[mode] = jsonToBuilderState(parsed);
            } catch(e) {}
        }
        renderBuilder(mode);
    }
}

function jsonToBuilderState(parsed) {
    const state = defaultState();
    if (parsed.event) state.event = parsed.event;
    if (parsed.data && typeof parsed.data === 'object') {
        state.data.children = [];
        if ('id' in parsed.data) {
            const v = parsed.data.id;
            state.data.children.push(makeScalarNode('id', v === null ? '' : String(v), v === null ? 'null' : typeof v === 'boolean' ? 'boolean' : typeof v === 'number' ? 'number' : 'string', false));
        } else {
            state.data.children.push(makeScalarNode('id', '', 'string', false));
        }
        Object.entries(parsed.data).forEach(([k, v]) => {
            if (k === 'id') return;
            state.data.children.push(valueToNode(k, v));
        });
    }
    return state;
}

function valueToNode(key, value) {
    if (value === null) return makeScalarNode(key, '', 'null', false);
    if (typeof value === 'boolean') return makeScalarNode(key, String(value), 'boolean', false);
    if (typeof value === 'number') return makeScalarNode(key, String(value), 'number', false);
    if (typeof value === 'string') return makeScalarNode(key, value, 'string', false);
    if (Array.isArray(value)) {
        const items = value.map(item => {
            if (typeof item === 'object' && item !== null) {
                return makeArrayItemNode(Object.entries(item).map(([k, v]) => valueToNode(k, v)));
            }
            return makeArrayItemNode([makeScalarNode('value', String(item), typeof item === 'number' ? 'number' : 'string', false)]);
        });
        return makeArrayNode(key, items, false);
    }
    if (typeof value === 'object') {
        return makeObjectNode(key, Object.entries(value).map(([k, v]) => valueToNode(k, v)), false);
    }
    return makeScalarNode(key, String(value), 'string', false);
}

function onRawInput(mode) {
    const raw = document.getElementById('payload-raw-' + mode).value.trim();
    const errEl = document.getElementById('json-err-' + mode);
    if (!raw) { errEl.classList.add('hidden'); document.getElementById('preview-' + mode).textContent = '{}'; return; }
    try {
        const p = JSON.parse(raw);
        errEl.classList.add('hidden');
        document.getElementById('preview-' + mode).textContent = JSON.stringify(p, null, 2);
    } catch(e) { errEl.classList.remove('hidden'); }
}

function loadSample(mode) {
    rawModes[mode] = false;
    document.getElementById('raw-' + mode).classList.add('hidden');
    document.getElementById('structured-' + mode).classList.remove('hidden');

    const sample = {
        event: mode === 'live' ? 'user.payment_updated' : 'order.placed',
        data: {
            id: 1,
            name: 'roland',
            payment: {
                customer_id: 'cus_987654321',
                currency: 'USD',
                wallet_balance: 45.5,
                payment_methods: [
                    { type: 'credit_card', brand: 'Visa', last4: '4242', is_default: true },
                    { type: 'paypal', email: 'roland@example.com', is_default: false }
                ],
                transactions: [
                    { transaction_id: 'tx_101', amount: 120, status: 'succeeded', timestamp: new Date().toISOString(), description: 'Premium Subscription Upgrade' },
                    { transaction_id: 'tx_102', amount: 15.99, status: 'failed', timestamp: new Date().toISOString(), failure_reason: 'Insufficient funds' }
                ],
                billing_address: {
                    street: '123 Main Street',
                    city: 'Lagos',
                    country: 'Nigeria',
                    postal_code: '100001'
                }
            }
        }
    };
    builderState[mode] = jsonToBuilderState(sample);
    renderBuilder(mode);
}

function copyWebhookUrl() {
    const url = document.getElementById('webhook-url-display').textContent.trim();
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('copy-url-btn');
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ Copied!';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}

function showDispatchAlert(msg, type) {
    const el = document.getElementById('send-alert-dispatch');
    el.className = 'mb-4 px-4 py-3 rounded-lg mono text-xs fade-in border ' +
        (type === 'success' ? 'bg-green-950 text-green-300 border-green-800' : 'bg-red-950 text-red-300 border-red-800');
    el.innerHTML = msg;
    el.classList.remove('hidden');
}

function switchTab(tab) {
    if (tab !== 'poll') stopPolling();
    currentTab = tab;
    document.getElementById('live-panel').classList.toggle('hidden', tab !== 'live');
    document.getElementById('test-panel').classList.toggle('hidden', tab !== 'test');
    document.getElementById('poll-panel').classList.toggle('hidden', tab !== 'poll');
    document.getElementById('send-alert-dispatch').classList.add('hidden');
    document.getElementById('test-response').classList.add('hidden');
    const styles = { live: 'active-live border-green-700', test: 'active-test border-yellow-600', poll: 'active-poll border-violet-600' };
    ['live','test','poll'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        const base = 'tab-btn text-xs mono px-4 py-1.5 rounded-full border font-semibold ';
        const extra = t === 'poll' ? ' flex items-center gap-1.5' : '';
        btn.className = base + (t === tab ? styles[t] : 'border-zinc-700 text-zinc-400') + extra;
    });
    const labels = { live: 'Sends to real external URL', test: 'Fires locally — no external server needed', poll: 'Auto-fetches new events every 4 s' };
    document.getElementById('mode-badge').textContent        = labels[tab];
    document.getElementById('mode-badge-mobile').textContent = labels[tab];
}

function startPolling() {
    if (pollInterval) return;
    const startBtn = document.getElementById('poll-start-btn');
    const stopBtn  = document.getElementById('poll-stop-btn');
    startBtn.disabled = true;
    startBtn.classList.add('opacity-50','cursor-not-allowed');
    stopBtn.disabled = false;
    stopBtn.classList.remove('cursor-not-allowed','bg-zinc-800','text-zinc-500','border-zinc-700');
    stopBtn.classList.add('bg-red-950','text-red-300','border-red-800','hover:bg-red-900','border');
    document.getElementById('poll-dot').classList.add('pulse-dot');
    document.getElementById('poll-dot').style.background = '#a78bfa';
    document.getElementById('poll-next-bar').classList.remove('hidden');
    document.getElementById('poll-status-dot').style.background = '#a78bfa';
    document.getElementById('poll-status-dot').classList.add('pulse-dot');
    document.getElementById('poll-status-text').textContent = 'Polling active — fetching every 4 s';
    fetchPollEvents();
    startCountdown();
    pollInterval = setInterval(() => { fetchPollEvents(); startCountdown(); }, POLL_SECS * 1000);
}

function stopPolling() {
    clearInterval(pollInterval);
    clearInterval(pollTimer);
    pollInterval = null;
    pollTimer    = null;
    const startBtn = document.getElementById('poll-start-btn');
    const stopBtn  = document.getElementById('poll-stop-btn');
    startBtn.disabled = false;
    startBtn.classList.remove('opacity-50','cursor-not-allowed');
    stopBtn.disabled = true;
    stopBtn.classList.add('cursor-not-allowed','bg-zinc-800','text-zinc-500');
    stopBtn.classList.remove('bg-red-950','text-red-300','border-red-800','hover:bg-red-900');
    document.getElementById('poll-dot').classList.remove('pulse-dot');
    document.getElementById('poll-dot').style.background = '#71717a';
    document.getElementById('poll-next-bar').classList.add('hidden');
    document.getElementById('poll-status-dot').style.background = '#6d28d9';
    document.getElementById('poll-status-dot').classList.remove('pulse-dot');
    document.getElementById('poll-status-text').textContent = 'Polling paused — click Start to begin fetching every 4 s';
}

function startCountdown() {
    pollCountdown = POLL_SECS;
    clearInterval(pollTimer);
    const progress  = document.getElementById('poll-progress');
    const countdown = document.getElementById('poll-countdown');
    progress.style.transition = 'none';
    progress.style.width      = '100%';
    requestAnimationFrame(() => { progress.style.transition = 'width 1s linear'; });
    pollTimer = setInterval(() => {
        pollCountdown--;
        countdown.textContent = Math.max(pollCountdown, 0) + 's';
        progress.style.width  = ((Math.max(pollCountdown, 0) / POLL_SECS) * 100) + '%';
        if (pollCountdown <= 0) { clearInterval(pollTimer); progress.style.width = '100%'; pollCountdown = POLL_SECS; }
    }, 1000);
}

async function fetchPollEvents() {
    try {
        const res = await fetch('/webhook/latest', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
        if (!res.ok) return;
        const data = await res.json();
        const eventData = data.data?.payload;
        if (!eventData) return;
        const eventId = eventData.data?.id;
        if (eventId && !pollSeenIds.has(eventId)) {
            pollSeenIds.add(eventId);
            pollCount++;
            prependPollEvent({ id: eventId, event: eventData.event, payload: eventData });
            document.getElementById('poll-counter').textContent = pollCount + ' event' + (pollCount !== 1 ? 's' : '');
            document.getElementById('poll-empty').classList.add('hidden');
        }
    } catch (e) { console.error('Poll fetch error:', e); }
}

function prependPollEvent(evt) {
    const container = document.getElementById('poll-results');
    const empty     = document.getElementById('poll-empty');
    const badgeCls  = 'badge-gray';
    const label     = 'Processed';
    const card      = document.createElement('div');
    card.className  = 'bg-zinc-950 border border-zinc-800 rounded-xl p-4 fade-in';
    const displayPayload = evt.payload || {};
    const displayEvent = evt.event || displayPayload.event || 'Unknown Event';
    const displayId = evt.id || displayPayload.data?.id || 'N/A';
    card.innerHTML  = `
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
                <p class="font-semibold text-sm text-zinc-100 truncate">${h(displayEvent)}</p>
                <p class="mono text-[10px] text-zinc-500 mt-0.5">${h(String(displayId).substring(0,16))}…</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-[10px] mono text-zinc-600">${new Date().toLocaleTimeString()}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full mono ${badgeCls}">${label}</span>
            </div>
        </div>
        <pre class="bg-zinc-900 rounded-lg p-3 text-xs text-green-300 mono overflow-x-auto whitespace-pre-wrap break-all max-h-40">${h(JSON.stringify(displayPayload, null, 2))}</pre>`;
    container.insertBefore(card, empty.nextSibling);
}

function clearPollResults() {
    pollSeenIds.clear();
    pollCount = 0;
    document.getElementById('poll-counter').textContent = '';
    Array.from(document.getElementById('poll-results').children).forEach(c => { if (c.id !== 'poll-empty') c.remove(); });
    document.getElementById('poll-empty').classList.remove('hidden');
}

function setLoading(btnId, loading, restoreHtml) {
    const btn = document.getElementById(btnId);
    btn.disabled = loading;
    if (loading) btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Sending…';
    else if (restoreHtml) btn.innerHTML = restoreHtml;
}

async function sendWebhook() {
    const url = document.getElementById('cb-url').value.trim();
    if (!url) { showDispatchAlert('⚠ Callback URL is required.', 'error'); return; }
    const payload = getFinalPayload('live');
    if (!payload) { showDispatchAlert('⚠ Fix the JSON before sending.', 'error'); return; }
    if (!payload.event) { showDispatchAlert('⚠ Event name is required.', 'error'); return; }
    const restoreHtml = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Dispatch webhook';
    setLoading('send-btn-live', true);
    try {
        const res  = await fetch('/webhook/send', { method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':csrfToken}, body:JSON.stringify({callback_url:url,payload}) });
        const data = await res.json();
        if (res.ok && data.success) showDispatchAlert('✓ Webhook queued! ID: <span class="opacity-70">'+(data.id??'—')+'</span> — <a href="/webhook/logs" class="underline">Refresh to see log →</a>','success');
        else showDispatchAlert('✗ '+(data.message||'Server error.'),'error');
    } catch(e) { showDispatchAlert('✗ Request failed: '+e.message,'error'); }
    finally { setLoading('send-btn-live',false,restoreHtml); }
}

async function sendTest() {
    const payload = getFinalPayload('test');
    if (!payload) { showDispatchAlert('⚠ Fix the JSON before sending.', 'error'); return; }
    if (!payload.event) { showDispatchAlert('⚠ Event name is required.', 'error'); return; }
    const restoreHtml = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Run test';
    setLoading('send-btn-test',true);
    document.getElementById('test-response').classList.add('hidden');
    try {
        const res  = await fetch('/webhook/send',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({callback_url:echoUrl,payload})});
        const data = await res.json();
        if (res.ok && data.success) {
            showDispatchAlert('✓ Test dispatched! Fetching echo result…','success');
            setTimeout(async () => {
                try {
                    const logRes  = await fetch('/webhook/verify/'+data.id,{headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken}});
                    const logData = await logRes.json();
                    document.getElementById('test-response-body').textContent = JSON.stringify(logData,null,2);
                    const resp = document.getElementById('test-response');
                    resp.classList.remove('hidden'); resp.classList.add('fade-in');
                    const valid = logData.status==='processed';
                    const sb    = document.getElementById('sig-badge');
                    sb.className   = 'mt-2 inline-flex items-center gap-1.5 text-xs mono px-3 py-1 rounded-full border '+(valid?'bg-green-950 text-green-300 border-green-800':'bg-red-950 text-red-300 border-red-800');
                    sb.textContent = valid?'✓ Delivered & verified':'✗ Delivery pending / failed';
                } catch(e) { showDispatchAlert('✗ Could not fetch log: '+e.message,'error'); }
            }, 2000);
        } else showDispatchAlert('✗ '+(data.message||'Server error.'),'error');
    } catch(e) { showDispatchAlert('✗ Request failed: '+e.message,'error'); }
    finally { setLoading('send-btn-test',false,restoreHtml); }
}

builderState.live = defaultState();
builderState.test = defaultState();
switchTab('live');
renderBuilder('live');
renderBuilder('test');
</script>
</body>
</html>
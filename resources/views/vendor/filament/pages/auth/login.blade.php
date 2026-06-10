<div class="min-h-screen grid lg:grid-cols-2" style="background: #f8fafc;">
    <div class="login-left hidden lg:flex relative p-12 flex-col justify-between overflow-hidden"
         style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
        <div class="absolute inset-0 opacity-30"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.15) 0%, transparent 50%),
                               radial-gradient(circle at 80% 70%, rgba(255,255,255,.1) 0%, transparent 50%),
                               radial-gradient(circle at 50% 50%, rgba(99,102,241,.3) 0%, transparent 70%);">
        </div>
        <div class="absolute top-16 left-8 w-72 h-72 rounded-full" style="background: rgba(255,255,255,.05); animation: floatSlow 6s ease-in-out infinite;"></div>
        <div class="absolute bottom-40 right-12 w-48 h-48 rounded-full" style="background: rgba(129,140,248,.1); animation: floatPulse 5s ease-in-out infinite;"></div>
        <div class="absolute bottom-16 right-8 w-56 h-56 rounded-full" style="background: rgba(255,255,255,.03); animation: floatSlow 6s ease-in-out 3s infinite;"></div>
        <div class="absolute -bottom-16 -right-16 text-[16rem] opacity-[.06] select-none">⚡</div>

        <div class="relative" style="animation: slideInLeft .7s cubic-bezier(.16,1,.3,1) both;">
            <div class="flex items-center gap-2.5 text-white">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,.2); backdrop-filter: blur(8px);">
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                </div>
                <span class="font-bold text-2xl tracking-tight" style="font-family: Inter, sans-serif;">{{ config('app.name') }}</span>
            </div>
        </div>

        <div class="relative text-white">
            <h2 class="font-bold text-4xl lg:text-5xl leading-tight mb-4" style="font-family: Inter, sans-serif; animation: fadeSlideUp .7s .05s cubic-bezier(.16,1,.3,1) both;">
                Admin Panel
            </h2>
            <p class="text-lg leading-relaxed mb-8 max-w-md" style="color: #c7d2fe; animation: fadeSlideUp .7s .12s cubic-bezier(.16,1,.3,1) both;">
                Kelola toko online Anda dengan panel administrasi yang lengkap dan mudah digunakan.
            </p>
            <div class="grid grid-cols-3 gap-3 max-w-md">
                @php $cards = [['Produk', 'box'], ['Laporan', 'chart-bar'], ['Sistem', 'cog']]; @endphp
                @foreach($cards as $i => $card)
                <div class="rounded-xl p-3.5 text-center border cursor-pointer"
                     style="background: rgba(255,255,255,.1); backdrop-filter: blur(8px); border-color: rgba(255,255,255,.1);
                            transition: transform .35s, box-shadow .35s; animation: fadeSlideUp .7s {{ .2 + $i * .08 }}s cubic-bezier(.16,1,.3,1) both;"
                     onmouseenter="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,.15)'"
                     onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <svg class="w-6 h-6 mb-1.5 mx-auto" style="color: #a5b4fc;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        @if($card[1] === 'box')<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        @elseif($card[1] === 'chart-bar')<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        @else<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />@endif
                    </svg>
                    <span class="text-white font-semibold" style="font-size: 11px;">{{ $card[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="relative text-xs" style="color: rgba(199,210,254,.6); animation: fadeSlideUp .7s .52s cubic-bezier(.16,1,.3,1) both;">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Laravel + Filament
        </div>
    </div>

    {{-- Right: Login Form --}}
    <div class="flex items-center justify-center p-6 lg:p-16 bg-white">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center" style="background: #eef2ff;">
                    <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                </div>
                <h2 class="text-xl font-bold text-stone-900">{{ config('app.name') }}</h2>
            </div>

            <h1 class="font-bold text-3xl text-stone-900 mb-1" style="font-family: Inter, sans-serif; animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) both;">Masuk Admin</h1>
            <p class="text-stone-500 mb-8" style="animation: fadeSlideUp .7s .05s cubic-bezier(.16,1,.3,1) both;">Panel administrasi {{ config('app.name') }}</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700" style="animation: scaleInBounce .6s cubic-bezier(.16,1,.3,1) both;">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span class="font-semibold">Oops! Ada yang salah</span>
                    </div>
                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="animation: fadeSlideUp .7s .12s cubic-bezier(.16,1,.3,1) both;">
                {{ $this->form }}
            </div>

            <div class="flex items-center gap-3 my-6" style="animation: fadeSlideUp .7s .36s cubic-bezier(.16,1,.3,1) both;">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">demo</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            <div class="bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm" style="animation: scaleInBounce .6s .44s cubic-bezier(.16,1,.3,1) both;">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg>
                    <span class="font-semibold text-stone-800">Demo Login</span>
                </div>
                <div class="space-y-1.5 text-stone-600 text-xs" style="font-family: JetBrains Mono, monospace;">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md font-semibold" style="font-size: 10px;">Admin</span>
                        <span class="text-stone-500">admin@tokoonline.test / password</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-md font-semibold" style="font-size: 10px;">Customer</span>
                        <span class="text-stone-500">customer@tokoonline.test / password</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" style="animation: fadeSlideUp .7s .52s cubic-bezier(.16,1,.3,1) both;">
                <a href="{{ url('/') }}" class="text-sm text-stone-400 hover:text-stone-600" style="transition: color .2s;">
                    <svg class="w-3.5 h-3.5 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Kembali ke toko
                </a>
            </div>
        </div>
    </div>
</div>

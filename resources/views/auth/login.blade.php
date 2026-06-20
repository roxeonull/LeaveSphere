<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeaveSphere – Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body { background: #0f172a; }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
        .input-field { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #e2e8f0; }
        .input-field::placeholder { color: #64748b; }
        .input-field:focus { outline: none; border-color: #3b82f6; background: rgba(59,130,246,0.08); }
        .grid-bg {
            background-image: linear-gradient(rgba(59,130,246,0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(59,130,246,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center grid-bg relative overflow-hidden">

    <!-- Glow blobs -->
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-sm mx-4" x-data="{ showPass: false, loading: false }">

        <!-- Logo & Brand -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg mx-auto mb-4 shadow-lg shadow-blue-500/25">LS</div>
            <h1 class="text-2xl font-bold text-white">LeaveSphere</h1>
            <p class="text-slate-400 text-sm mt-1">HR Leave Management System</p>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-2xl p-7">
            <h2 class="text-white font-semibold text-base mb-5">Sign in to your account</h2>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                <p class="text-red-400 text-xs flex items-center gap-1.5">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    {{ $errors->first() }}
                </p>
            </div>
            @endif

            @if(session('status'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                <p class="text-green-400 text-xs">{{ session('status') }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" @submit="loading = true" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Email</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="input-field w-full rounded-xl px-4 py-3 text-sm pl-10"
                            placeholder="admin@company.com">
                        <i data-lucide="mail" class="absolute left-3 top-3.5 w-4 h-4 text-slate-500"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            class="input-field w-full rounded-xl px-4 py-3 text-sm pl-10 pr-10"
                            placeholder="••••••••">
                        <i data-lucide="lock" class="absolute left-3 top-3.5 w-4 h-4 text-slate-500"></i>
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-3.5 text-slate-500 hover:text-slate-300 transition-colors">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-600 bg-transparent text-blue-500">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                        Forgot password?
                    </a>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2 mt-2"
                    :class="loading ? 'opacity-75 cursor-not-allowed' : ''">
                    <template x-if="loading">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
                </button>
            </form>

            <!-- Demo credentials hint -->
            <div class="mt-5 pt-4 border-t border-white/10">
                <p class="text-xs text-slate-500 text-center mb-2">Demo credentials</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-white/5 rounded-lg p-2.5">
                        <p class="text-slate-400 font-medium">Super Admin</p>
                        <p class="text-slate-500 mt-0.5">admin@company.com</p>
                        <p class="text-slate-500">password</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-2.5">
                        <p class="text-slate-400 font-medium">Manager</p>
                        <p class="text-slate-500 mt-0.5">manager@company.com</p>
                        <p class="text-slate-500">password</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">
            &copy; {{ date('Y') }} LeaveSphere · Kelompok 7 · Telkom University
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
        document.addEventListener('alpine:init', () => setTimeout(() => lucide.createIcons(), 50));
    </script>
</body>
</html>

@extends('layouts.app')

@php
    $jsonPath = public_path('translation/login.json');
    $loginTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $loginTrans['meta_title'][$lang] ?? 'Login - LUMINA')

@section('content')
<style>
    #mainNavbar { display: none !important; }
    .mobile-bottom-nav { display: none !important; }
</style>
@include('components.luxury-navbar')
<div class="min-h-[100dvh] w-full flex flex-col md:flex-row bg-white text-zinc-900 antialiased pt-16 md:pt-20">
    <!-- Left: Form -->
    <section class="flex-1 flex items-start md:items-center justify-center p-4 md:p-10 overflow-y-auto">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-3">
                <h1 class="animate-element animate-delay-100 text-3xl md:text-4xl font-light leading-tight tracking-tighter">
                    {{ $loginTrans['welcome_title_1'][$lang] ?? 'Welcome' }} <span class="font-semibold">{{ $loginTrans['welcome_title_2'][$lang] ?? 'back' }}</span>
                </h1>
                <p class="animate-element animate-delay-200 text-sm text-zinc-500">{{ $loginTrans['welcome_desc'][$lang] ?? 'Access your account and continue your journey with LUMINA.' }}</p>

                @if($errors->any())
                    <div class="animate-element animate-delay-250 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="animate-element animate-delay-300">
                        <label for="email" class="text-sm font-medium text-zinc-500">{{ $loginTrans['label_email'][$lang] ?? 'Email Address' }}</label>
                        <div class="mt-1 rounded-2xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ $loginTrans['placeholder_email'][$lang] ?? 'Enter your email address' }}" class="w-full bg-transparent text-sm p-2.5 rounded-2xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-400">
                        <label for="password" class="text-sm font-medium text-zinc-500">{{ $loginTrans['label_password'][$lang] ?? 'Password' }}</label>
                        <div class="mt-1 rounded-2xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <div class="relative">
                                <input type="password" id="password" name="password" required placeholder="{{ $loginTrans['placeholder_password'][$lang] ?? 'Enter your password' }}" class="w-full bg-transparent text-sm p-2.5 pr-12 rounded-2xl focus:outline-none">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-zinc-400 hover:text-zinc-700" aria-label="Toggle password">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a19.77 19.77 0 014.22-5.36M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a19.77 19.77 0 01-3.16 4.19M1 1l22 22M14.12 14.12a3 3 0 11-4.24-4.24"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="animate-element animate-delay-500 flex items-center justify-between text-sm">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-zinc-300 text-violet-500 focus:ring-violet-400" checked>
                            <span class="text-zinc-700">{{ $loginTrans['remember_me'][$lang] ?? 'Keep me signed in' }}</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-violet-500 hover:underline transition-colors">{{ $loginTrans['btn_reset'][$lang] ?? 'Reset password' }}</a>
                    </div>

                    <button type="submit" class="animate-element animate-delay-600 w-full rounded-2xl bg-zinc-900 py-2.5 font-medium text-white hover:bg-zinc-800 transition-colors">
                        {{ $loginTrans['btn_submit'][$lang] ?? 'Sign In' }}
                    </button>
                </form>

                <div class="animate-element animate-delay-700 relative flex items-center justify-center">
                    <span class="w-full border-t border-zinc-200"></span>
                    <span class="px-4 text-sm text-zinc-500 bg-white absolute">{{ $loginTrans['divider_text'][$lang] ?? 'Or continue with' }}</span>
                </div>

                <button type="button" id="googleSignInBtn" class="animate-element animate-delay-800 flex w-full items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    {{ $loginTrans['btn_google'][$lang] ?? 'Sign in with Google' }}
                </button>

                <p class="animate-element animate-delay-900 text-center text-sm text-zinc-500">
                    {{ $loginTrans['footer_text'][$lang] ?? 'New to LUMINA?' }} <a href="{{ route('register') }}" class="text-violet-500 hover:underline transition-colors">{{ $loginTrans['btn_register'][$lang] ?? 'Create Account' }}</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Right: Hero Image + Testimonials -->
    <section class="hidden md:block flex-1 relative p-4">
        <div class="animate-slide-right animate-delay-300 absolute inset-4 rounded-3xl bg-cover bg-center" style="background-image: url('{{ asset('storage/model-1.jpg') }}');"></div>
    </section>
</div>
@endsection

@push('styles')
        <style>
        .marquee-container { display: flex; overflow: hidden; }
        .marquee-content { display: flex; animation: marquee 30s linear infinite; white-space: nowrap; }
        .marquee-item { font-size: 11px; letter-spacing: 0.15em; font-weight: 600; padding-right: 1rem; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        @keyframes fadeSlideIn { from { opacity: 0; filter: blur(8px); transform: translateY(20px); } to { opacity: 1; filter: blur(0); transform: translateY(0); } }
        @keyframes slideRightIn { from { opacity: 0; filter: blur(8px); transform: translateX(40px); } to { opacity: 1; filter: blur(0); transform: translateX(0); } }
        @keyframes testimonialIn { from { opacity: 0; filter: blur(8px); transform: translateY(20px) scale(0.95); } to { opacity: 1; filter: blur(0); transform: translateY(0) scale(1); } }
        .animate-element { opacity: 0; animation: fadeSlideIn 0.8s ease forwards; }
        .animate-slide-right { opacity: 0; animation: slideRightIn 0.9s ease forwards; }
        .animate-testimonial { opacity: 0; animation: testimonialIn 0.8s ease forwards; }
        .animate-delay-50 { animation-delay: 50ms; }
        .animate-delay-100 { animation-delay: 100ms; }
        .animate-delay-200 { animation-delay: 200ms; }
        .animate-delay-250 { animation-delay: 250ms; }
        .animate-delay-300 { animation-delay: 300ms; }
        .animate-delay-400 { animation-delay: 400ms; }
        .animate-delay-500 { animation-delay: 500ms; }
        .animate-delay-600 { animation-delay: 600ms; }
        .animate-delay-700 { animation-delay: 700ms; }
        .animate-delay-800 { animation-delay: 800ms; }
        .animate-delay-900 { animation-delay: 900ms; }
        .animate-delay-1000 { animation-delay: 1000ms; }
        .animate-delay-1200 { animation-delay: 1200ms; }
        .animate-delay-1400 { animation-delay: 1400ms; }
    </style>
@endpush

@push('scripts')
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script>
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAKtin9WevURPmDUdoBwanJNS9kc0prh_A",
            authDomain: "skincare-fba4f.firebaseapp.com",
            projectId: "skincare-fba4f",
            storageBucket: "skincare-fba4f.firebasestorage.app",
            messagingSenderId: "660330989978",
            appId: "1:660330989978:web:70e570eb75b668fdef9223",
            measurementId: "G-6KZ86FEM4K"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();

        // Google Sign-In
        const googleSignInBtn = document.getElementById('googleSignInBtn');
        if (googleSignInBtn) {
            googleSignInBtn.addEventListener('click', async function() {
                const provider = new firebase.auth.GoogleAuthProvider();
                
                try {
                    const result = await auth.signInWithPopup(provider);
                    const idToken = await result.user.getIdToken();
                    
                    // Send token to backend
                    const response = await fetch('{{ route('auth.firebase') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            idToken: idToken,
                            displayName: result.user.displayName,
                            email: result.user.email,
                            photoURL: result.user.photoURL
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        window.location.href = data.redirect || '{{ route('home') }}';
                    } else {
                        alert(data.message || 'Login failed');
                        await auth.signOut();
                    }
                } catch (error) {
                    console.error('Google sign-in error:', error);
                    alert('Gagal login dengan Google: ' + error.message);
                }
            });
        }
    </script>
    <script>
        (function () {
            const toggle = document.querySelector('[data-mobile-menu-toggle]');
            const menu = document.querySelector('[data-mobile-menu]');
            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    menu.classList.toggle('hidden', isOpen);
                });
            }
        })();
        (function () {
            const toggle = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            const eyeOff = document.getElementById('eyeOffIcon');
            if (!toggle || !input) return;
            toggle.addEventListener('click', function () {
                const isPwd = input.type === 'password';
                input.type = isPwd ? 'text' : 'password';
                eye.classList.toggle('hidden', isPwd);
                eyeOff.classList.toggle('hidden', !isPwd);
            });
        })();
    </script>
@endpush
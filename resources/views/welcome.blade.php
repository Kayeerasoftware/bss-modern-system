<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bunya C15 investments System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-15px) rotate(5deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        @keyframes border-draw { 0% { stroke-dashoffset: 400; } 100% { stroke-dashoffset: 0; } }
        @keyframes btn-glow { 0%, 100% { background: rgba(255,255,255,0.1); } 50% { background: rgba(255,255,255,0.25); } }
        @keyframes pulse-green { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        @keyframes green-glow { 0%, 100% { background: rgba(34, 197, 94, 0.3); box-shadow: 0 0 15px rgba(34, 197, 94, 0.5); } 50% { background: rgba(34, 197, 94, 0.5); box-shadow: 0 0 25px rgba(34, 197, 94, 0.8); } }
        @keyframes colorShift { 0%, 45%, 100% { background: white; color: #9333ea; } 50%, 95% { background: #3b82f6; color: white; } }
        @keyframes textGlow { 0%, 100% { text-shadow: 0 0 20px rgba(255,255,255,0.5), 0 0 40px rgba(255,255,255,0.3); } 50% { text-shadow: 0 0 30px rgba(255,255,255,0.8), 0 0 60px rgba(255,255,255,0.5); } }
        .title-glow { animation: textGlow 3s ease-in-out infinite; }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/for-web-2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            z-index: -1;
        }
        .btn-animate { animation: colorShift 14.8s ease-in-out infinite; transition: all 0.6s ease; }
        #btnText { transition: opacity 0.3s ease; display: inline-block; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeIn { animation: fadeIn 1s ease-out forwards; }
        .animate-pulse { animation: pulse 3s ease-in-out infinite; }
        .gradient-bg { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }
        .nav-card { background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .feature-card-1 { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(37, 99, 235, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.3); }
        .feature-card-2 { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(16, 185, 129, 0.3); }
        .feature-card-3 { background: linear-gradient(135deg, rgba(168, 85, 247, 0.2) 0%, rgba(147, 51, 234, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(168, 85, 247, 0.3); }
        .feature-card-4 { background: linear-gradient(135deg, rgba(236, 72, 153, 0.2) 0%, rgba(219, 39, 119, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(236, 72, 153, 0.3); }
        .feature-card-5 { background: linear-gradient(135deg, rgba(251, 191, 36, 0.2) 0%, rgba(245, 158, 11, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(251, 191, 36, 0.3); }
        .feature-card-6 { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(239, 68, 68, 0.3); }
        .feature-card-7 { background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(79, 70, 229, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(99, 102, 241, 0.3); }
        .feature-card-8 { background: linear-gradient(135deg, rgba(20, 184, 166, 0.2) 0%, rgba(13, 148, 136, 0.2) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(20, 184, 166, 0.3); }
        .stats-card { background: linear-gradient(135deg, rgba(71, 85, 105, 0.3) 0%, rgba(51, 65, 85, 0.3) 100%); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .shimmer { background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); background-size: 1000px 100%; animation: shimmer 6s infinite; }
        .login-btn { position: relative; overflow: visible; animation: btn-glow 3s ease-in-out infinite; }
        .login-btn svg { position: absolute; inset: -4px; width: calc(100% + 8px); height: calc(100% + 8px); pointer-events: none; }
        .login-btn rect { fill: none; stroke: red; stroke-width: 3; stroke-dasharray: 400; stroke-dashoffset: 400; animation: border-draw 3s linear infinite; }
        .online-btn { position: relative; overflow: visible; animation: green-glow 2s ease-in-out infinite; }
        .online-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block; margin-right: 8px; box-shadow: 0 0 10px #22c55e; animation: pulse-green 2s ease-in-out infinite; will-change: opacity; }
        @media (max-width: 768px) {
            .nav-mobile { flex-direction: column; gap: 0.5rem !important; padding: 0.75rem !important; }
            .nav-title { font-size: 0.875rem; text-align: center; }
            .nav-underline { width: 100% !important; }
            .nav-buttons { width: 100%; justify-content: center; gap: 0.5rem !important; }
            .nav-btn { padding: 0.375rem 0.75rem !important; font-size: 0.625rem !important; }
            .content-mobile { padding: 0.5rem !important; gap: 0.5rem !important; }
            .card-mobile { padding: 0.5rem !important; gap: 0.25rem !important; }
            .stats-mobile { padding: 0.75rem !important; }
        }
        ::-webkit-scrollbar { display: none; }
        * { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-screen overflow-hidden p-2 md:p-8">
    @include('components.loading-screen')
    <div class="h-full flex items-center justify-center overflow-y-auto">
        <div class="w-full max-w-5xl gradient-bg rounded-3xl shadow-2xl my-2">
            <div class="max-w-5xl mx-auto h-full flex flex-col px-2 py-2 md:px-4 md:py-4">
            <nav class="nav-card rounded-2xl animate-fadeIn mb-2 md:mb-4">
                <div class="px-3 py-2 md:px-6 md:py-3 flex flex-col md:flex-row justify-between items-center gap-2 md:gap-8 nav-mobile">
                    <div class="w-full md:w-auto">
                        <h2 class="text-sm md:text-3xl font-black text-white tracking-wider drop-shadow-2xl text-center md:text-left nav-title title-glow">
                            <span class="bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent">Welcome to</span>
                            <span class="block md:inline text-base md:text-3xl font-extrabold bg-gradient-to-r from-yellow-200 via-white to-yellow-200 bg-clip-text text-transparent">BSS Investment Group</span>
                        </h2>
                        <div class="h-1 md:h-1.5 w-full bg-gradient-to-r from-transparent via-white to-transparent rounded-full shimmer mt-1 md:mt-2 mx-auto md:mx-0 nav-underline shadow-lg shadow-white/50"></div>
                    </div>
                    <div class="flex gap-2 md:gap-4 items-center flex-shrink-0 nav-buttons">
                        <span class="online-btn text-white px-2 py-1 md:px-5 md:py-2 rounded-lg md:rounded-xl text-[10px] md:text-xs font-semibold cursor-default flex items-center nav-btn">
                            <span class="online-dot w-1.5 h-1.5 md:w-2 md:h-2 mr-1 md:mr-2"></span>
                            Online
                        </span>
                        <a href="/login" class="login-btn text-white hover:bg-white/30 px-2 py-1 md:px-5 md:py-2 rounded-lg md:rounded-xl transition-all duration-300 text-[10px] md:text-xs font-semibold relative z-10 nav-btn">
                            <svg><rect x="0" y="0" width="100%" height="100%" rx="12"/></svg>
                            Login
                        </a>
                        <a href="/register" id="navBtn" data-text="Register" class="btn-animate px-2 py-1 md:px-5 md:py-2 rounded-lg md:rounded-xl font-bold hover:shadow-2xl text-[10px] md:text-xs inline-block text-center nav-btn" style="min-width: 100px; width: 100px;"><span id="btnText" style="transition: opacity 0.3s ease;">Get Started</span></a>
                    </div>
                </div>
            </nav>

            <div class="flex-1 flex flex-col justify-center">
            <div class="text-center mb-2 md:mb-4 animate-fadeIn">
                <p class="text-xs md:text-sm lg:text-base text-white/95 mb-2 md:mb-3 font-medium">Comprehensive Financial Management for Savings & Credit Organizations</p>
                <div class="flex gap-1 md:gap-2 justify-center flex-wrap">
                    <a href="/login" class="bg-white text-purple-600 px-4 py-1.5 md:px-6 md:py-2 rounded-full font-bold text-[10px] md:text-xs hover:scale-110 hover:shadow-2xl transition-all duration-300 transform">Start Here</a>
                    <a href="/learn-more" class="nav-card text-white px-4 py-1.5 md:px-6 md:py-2 rounded-full font-bold text-[10px] md:text-xs hover:scale-110 transition-all duration-300 transform">Learn More</a>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-1.5 md:gap-2 mb-2 md:mb-4 content-mobile">
                <div class="feature-card-1 rounded-xl md:rounded-2xl p-2 md:p-3 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn card-mobile" style="animation-delay: 0.1s">
                    <div class="bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center mb-1 animate-float mx-auto shadow-lg">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-[10px] md:text-xs font-bold text-white text-center">Member Management</h3>
                </div>

                <div class="feature-card-2 rounded-xl md:rounded-2xl p-2 md:p-3 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn card-mobile" style="animation-delay: 0.15s">
                    <div class="bg-gradient-to-br from-green-400 via-green-500 to-green-600 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center mb-1 animate-float mx-auto shadow-lg" style="animation-delay: 1s">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-[10px] md:text-xs font-bold text-white text-center">Loan Management</h3>
                </div>

                <div class="feature-card-3 rounded-xl md:rounded-2xl p-2 md:p-3 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn card-mobile" style="animation-delay: 0.2s">
                    <div class="bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center mb-1 animate-float mx-auto shadow-lg" style="animation-delay: 2s">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-[10px] md:text-xs font-bold text-white text-center">Financial Reports</h3>
                </div>

                <div class="feature-card-4 rounded-xl md:rounded-2xl p-2 md:p-3 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn card-mobile" style="animation-delay: 0.25s">
                    <div class="bg-gradient-to-br from-pink-400 via-pink-500 to-pink-600 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center mb-1 animate-float mx-auto shadow-lg" style="animation-delay: 3s">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="text-[10px] md:text-xs font-bold text-white text-center">Transactions</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-1.5 md:gap-2 mb-2 md:mb-4 content-mobile">
                <div class="feature-card-5 rounded-2xl p-4 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn" style="animation-delay: 0.3s">
                    <div class="bg-gradient-to-br from-yellow-400 via-orange-500 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center mb-2 animate-float mx-auto shadow-lg" style="animation-delay: 4s">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white text-center">Savings Tracking</h3>
                </div>

                <div class="feature-card-6 rounded-2xl p-4 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn" style="animation-delay: 0.35s">
                    <div class="bg-gradient-to-br from-red-400 via-red-500 to-red-600 w-12 h-12 rounded-xl flex items-center justify-center mb-2 animate-float mx-auto shadow-lg" style="animation-delay: 5s">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white text-center">SMS Notifications</h3>
                </div>

                <div class="feature-card-7 rounded-2xl p-4 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn" style="animation-delay: 0.4s">
                    <div class="bg-gradient-to-br from-indigo-400 via-indigo-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center mb-2 animate-float mx-auto shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white text-center">Secure System</h3>
                </div>

                <div class="feature-card-8 rounded-2xl p-4 hover:scale-110 hover:shadow-2xl transition-all duration-300 transform animate-fadeIn" style="animation-delay: 0.45s">
                    <div class="bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 w-12 h-12 rounded-xl flex items-center justify-center mb-2 animate-float mx-auto shadow-lg" style="animation-delay: 1.5s">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white text-center">6 User Roles</h3>
                </div>
            </div>

            <div class="stats-card rounded-xl md:rounded-2xl p-2 md:p-5 text-center animate-fadeIn mb-2 md:mb-4 hover:shadow-2xl transition-all duration-300 stats-mobile" style="animation-delay: 0.5s">
                <p class="text-white/95 text-[10px] md:text-sm mb-2 md:mb-3 font-semibold"><span class="font-extrabold text-white">Role-Based Access:</span> Admin, Cashier, TD, CEO, Shareholder & Client</p>
                <div class="grid grid-cols-3 md:grid-cols-6 gap-1.5 md:gap-3">
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">100%</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Secure</div></div>
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">24/7</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Support</div></div>
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">SMS</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Alerts</div></div>
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">API</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Ready</div></div>
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">Cloud</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Based</div></div>
                    <div class="text-center bg-white/10 rounded-lg md:rounded-xl p-1 md:p-2 hover:bg-white/20 transition-all duration-300"><div class="text-sm md:text-xl font-bold text-white">Reports</div><div class="text-white/80 text-[8px] md:text-xs font-medium">Analytics</div></div>
                </div>
            </div>

            </div>
        </div>
    </div>
    <script>
        const btnText = document.getElementById('btnText');
        const texts = ['Get Started', 'Register'];
        let index = 0;
        setInterval(() => {
            index = (index + 1) % texts.length;
            btnText.style.transition = 'opacity 1.2s ease-in-out';
            btnText.style.opacity = '0';
            setTimeout(() => {
                btnText.textContent = texts[index];
                btnText.style.opacity = '1';
            }, 1200);
        }, 7400);
    </script>
</body>
</html>

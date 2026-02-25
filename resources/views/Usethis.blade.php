<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSS Investment Group - Welcome Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.5)' },
                            '50%': { boxShadow: '0 0 30px rgba(59, 130, 246, 0.8)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .dashboard-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        }

        .role-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            background: linear-gradient(135deg, var(--icon-start), var(--icon-end));
            color: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
        }

        .glowing-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glowing-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .glowing-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .glowing-btn:hover::before {
            left: 100%;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .feature-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--dot-color);
            margin-right: 10px;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="font-inter">
    <!-- Navigation -->
    <nav class="nav-gradient shadow-2xl">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center animate-float">
                            <i class="fas fa-university text-2xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white animate-pulse"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">BSS Investment Group</h1>
                        <p class="text-blue-100 text-sm">Building Sustainable Success</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-white font-medium">Welcome back,</p>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-white">{{ Auth::user()->name }}</span>
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="relative group">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center cursor-pointer">
                            <span class="text-white font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full px-4 py-3 text-left text-red-600 hover:bg-red-50 rounded-xl flex items-center">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-12">
        <!-- Welcome Header -->
        <div class="dashboard-header animate-fade-in">
            <div class="relative z-10">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-gem text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-4xl font-bold">Welcome to BSS Dashboard</h2>
                        <p class="text-blue-100 mt-2">Access your personalized dashboard with advanced analytics and tools</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
                    <div class="stat-card animate-slide-up" style="animation-delay: 0.1s">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 mb-1">Active Members</p>
                                    <p class="text-3xl font-bold">{{ $stats['totalMembers'] }}</p>
                                </div>
                                <i class="fas fa-users text-3xl opacity-20"></i>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <i class="fas fa-arrow-up text-green-300 mr-2"></i>
                                <span class="text-green-300">+{{ $stats['memberGrowth'] }}% this month</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card animate-slide-up" style="animation-delay: 0.2s; background: linear-gradient(135deg, #059669 0%, #10b981 100%)">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 mb-1">Total Assets</p>
                                    <p class="text-3xl font-bold">UGX {{ number_format($stats['totalAssets'] / 1000000, 1) }}M</p>
                                </div>
                                <i class="fas fa-chart-line text-3xl opacity-20"></i>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <i class="fas fa-arrow-up text-green-300 mr-2"></i>
                                <span class="text-green-300">+{{ $stats['assetGrowth'] }}% growth</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card animate-slide-up" style="animation-delay: 0.3s; background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%)">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 mb-1">Active Projects</p>
                                    <p class="text-3xl font-bold">{{ $stats['activeProjects'] }}</p>
                                </div>
                                <i class="fas fa-project-diagram text-3xl opacity-20"></i>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <i class="fas fa-clock text-yellow-300 mr-2"></i>
                                <span class="text-yellow-300">{{ $stats['nearingCompletion'] }} nearing completion</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card animate-slide-up" style="animation-delay: 0.4s; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%)">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 mb-1">Pending Loans</p>
                                    <p class="text-3xl font-bold">{{ $stats['pendingLoans'] ?? 8 }}</p>
                                </div>
                                <i class="fas fa-hand-holding-usd text-3xl opacity-20"></i>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <i class="fas fa-clock text-yellow-300 mr-2"></i>
                                <span class="text-yellow-300">Require attention</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Selection Dashboard Cards -->
        <div class="mt-16 mb-12">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Select Your Dashboard</h3>
                    <p class="text-gray-600 mt-2">Choose your role dashboard to access specialized features and analytics</p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    <span>Your current role: <span class="font-bold text-blue-600">{{ ucfirst(Auth::user()->role) }}</span></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($dashboards as $role => $dashboard)
                    @php
                        $isActive = Auth::user()->role === $role;
                        $isRecommended = in_array($role, ['ceo', 'admin']);
                    @endphp

                    <div class="dashboard-card animate-slide-up"
                         style="animation-delay: {{ $loop->index * 0.1 }}s;
                                --gradient-start: {{ $dashboard['gradientStart'] }};
                                --gradient-end: {{ $dashboard['gradientEnd'] }};"
                         onclick="{{ !$isActive ? "showRoleAlert('{$role}', '" . ucfirst(Auth::user()->role) . "')" : '' }}">

                        @if($isRecommended)
                            <div class="absolute top-4 left-4 px-3 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold rounded-full">
                                <i class="fas fa-crown mr-1"></i> Recommended
                            </div>
                        @endif

                        @if($isActive)
                            <div class="role-badge animate-pulse-glow">
                                <i class="fas fa-check mr-1"></i> Active Role
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div class="w-14 h-14 rounded-2xl mb-4 flex items-center justify-center"
                                         style="background: linear-gradient(135deg, {{ $dashboard['gradientStart'] }}, {{ $dashboard['gradientEnd'] }})">
                                        <i class="fas {{ $dashboard['icon'] }} text-2xl text-white"></i>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-800">{{ $dashboard['title'] }}</h4>
                                    <p class="text-gray-600 text-sm mt-1">{{ $dashboard['subtitle'] }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-gray-800">{{ $dashboard['stat'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $dashboard['statLabel'] }}</div>
                                </div>
                            </div>

                            <div class="space-y-3 mb-8">
                                @foreach($dashboard['features'] as $feature)
                                    <div class="flex items-center">
                                        <div class="feature-dot" style="--dot-color: {{ $dashboard['gradientStart'] }}"></div>
                                        <span class="text-sm text-gray-700">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @if($isActive)
                                <a href="{{ $dashboard['url'] }}" class="glowing-btn block text-center">
                                    <i class="fas fa-rocket mr-2"></i> Launch Dashboard
                                </a>
                            @else
                                <div class="px-4 py-3 bg-gray-100 rounded-lg text-center cursor-not-allowed">
                                    <div class="flex items-center justify-center text-gray-500">
                                        <i class="fas fa-lock mr-2"></i>
                                        <span>Access Restricted</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Switch to {{ ucfirst($role) }} role to access</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Features -->
        <div class="mt-20 mb-12">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Comprehensive Investment Management</h3>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">Modern platform with advanced features designed for efficient group investment management</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="feature-icon" style="--icon-start: #3b82f6; --icon-end: #6366f1">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Advanced Analytics</h4>
                    <p class="text-gray-600 text-sm">Real-time charts, graphs, and comprehensive reporting for data-driven decisions</p>
                </div>

                <div class="text-center">
                    <div class="feature-icon" style="--icon-start: #10b981; --icon-end: #059669">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Bank-Level Security</h4>
                    <p class="text-gray-600 text-sm">Role-based access control, encryption, and audit trails for complete security</p>
                </div>

                <div class="text-center">
                    <div class="feature-icon" style="--icon-start: #8b5cf6; --icon-end: #7c3aed">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Real-Time Processing</h4>
                    <p class="text-gray-600 text-sm">Instant transaction processing and live updates across all modules</p>
                </div>

                <div class="text-center">
                    <div class="feature-icon" style="--icon-start: #f59e0b; --icon-end: #d97706">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Mobile Responsive</h4>
                    <p class="text-gray-600 text-sm">Seamless experience across all devices with responsive design</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 mt-12">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Quick Access</h4>
                    <p class="text-gray-600">Common actions and shortcuts for your role</p>
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <button class="px-6 py-3 bg-white rounded-xl shadow hover:shadow-md transition-shadow flex items-center">
                        <i class="fas fa-file-invoice-dollar text-blue-600 mr-3"></i>
                        <span>View Reports</span>
                    </button>
                    <button class="px-6 py-3 bg-white rounded-xl shadow hover:shadow-md transition-shadow flex items-center">
                        <i class="fas fa-bell text-purple-600 mr-3"></i>
                        <span>Notifications</span>
                    </button>
                    <button class="px-6 py-3 bg-white rounded-xl shadow hover:shadow-md transition-shadow flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-3"></i>
                        <span>Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-20 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-white"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">BSS Investment Group</p>
                        <p class="text-sm text-gray-600">Building Sustainable Success Together</p>
                    </div>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Privacy Policy</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Terms of Service</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Support</a>
                </div>
                <div class="mt-4 md:mt-0 text-center text-gray-500 text-sm">
                    <p>&copy; {{ date('Y') }} BSS Investment Group. All rights reserved.</p>
                    <p class="mt-1">Version 3.0 • Modern Investment Management System</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function showRoleAlert(requestedRole, userRole) {
            const roleNames = {
                'admin': 'Administrator',
                'ceo': 'Chief Executive Officer',
                'td': 'Technical Director',
                'cashier': 'Cashier',
                'shareholder': 'Shareholder',
                'client': 'Client'
            };

            Swal.fire({
                title: 'Access Restricted',
                html: `
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-3xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Dashboard Access Required</h3>
                        <p class="text-gray-600 mb-4">
                            You need <span class="font-bold">${roleNames[requestedRole]}</span> privileges to access this dashboard.
                        </p>
                        <div class="bg-gray-100 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                Your current role: <span class="font-bold text-blue-600">${roleNames[userRole]}</span>
                            </p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        }

        // Add sweetalert2 for better alerts
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);

        // Add hover effects for dashboard cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (this.getAttribute('onclick')) {
                        this.style.cursor = 'pointer';
                        this.style.transform = 'translateY(-10px) scale(1.02)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>

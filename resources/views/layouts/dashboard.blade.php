<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('onboarding.brand_name', 'Raftfg') }}</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #00286f;
            --primary-dark: #001d4d;
            --secondary-color: #001d4d;
            --accent-color: #10b981;
            --sidebar-width: 260px;
            --header-height: 70px;
            --bg-color: #f5f7fa;
            --text-color: #333;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }

        .header {
            height: var(--header-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .content-area {
            padding: 30px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        @include('onboarding::dashboard.partials.sidebar')
        
        <div class="main-content">
            @include('onboarding::dashboard.partials.header')
            
            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @stack('scripts')
</body>
</html>

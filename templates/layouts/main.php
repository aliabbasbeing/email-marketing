<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Email Marketing Tool' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Custom animations and styles */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">
    <!-- Header -->
    <header class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-400 hover:text-white lg:hidden mr-3">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-blue-400">
                        <i class="fas fa-envelope-open-text mr-2"></i>
                        Email Marketing
                    </h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="userMenuToggle" class="flex items-center text-gray-300 hover:text-white">
                            <i class="fas fa-user-circle text-xl mr-2"></i>
                            <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        
                        <!-- User dropdown menu -->
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-gray-700 rounded-md shadow-lg z-50">
                            <div class="py-1">
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-600">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="/settings" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-600">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <div class="border-t border-gray-600"></div>
                                <form method="POST" action="/logout" class="block">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-600">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-800 shadow-lg hidden lg:block">
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <a href="/dashboard" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                        Dashboard
                    </a>
                    
                    <a href="/campaigns" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-bullhorn mr-3 text-gray-400"></i>
                        Campaigns
                    </a>
                    
                    <a href="/emails" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-envelope mr-3 text-gray-400"></i>
                        Email Lists
                    </a>
                    
                    <a href="/templates" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-file-alt mr-3 text-gray-400"></i>
                        Templates
                    </a>
                    
                    <a href="/analytics" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-chart-line mr-3 text-gray-400"></i>
                        Analytics
                    </a>
                    
                    <div class="border-t border-gray-700 my-3"></div>
                    
                    <a href="/settings/smtp" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-server mr-3 text-gray-400"></i>
                        SMTP Settings
                    </a>
                    
                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <a href="/admin/users" class="nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-users mr-3 text-gray-400"></i>
                        User Management
                    </a>
                    <?php endif; ?>
                </div>
            </nav>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden"></div>

        <!-- Mobile sidebar -->
        <aside id="mobileSidebar" class="fixed inset-y-0 left-0 w-64 bg-gray-800 z-50 lg:hidden transform -translate-x-full transition-transform duration-300">
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white">Menu</h2>
                <button id="closeSidebar" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Same navigation as desktop -->
            <nav class="mt-5 px-2">
                <!-- Copy the same nav links here -->
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-x-hidden">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <?php if (isset($success) && $success): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded fade-in">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error) && $error): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <!-- Page content -->
                <?= $content ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle user menu
        document.getElementById('userMenuToggle').addEventListener('click', function() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const toggle = document.getElementById('userMenuToggle');
            const menu = document.getElementById('userMenu');
            
            if (!toggle.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const overlay = document.getElementById('sidebarOverlay');
            const sidebar = document.getElementById('mobileSidebar');
            
            overlay.classList.remove('hidden');
            sidebar.classList.remove('-translate-x-full');
        });

        // Close mobile sidebar
        function closeMobileSidebar() {
            const overlay = document.getElementById('sidebarOverlay');
            const sidebar = document.getElementById('mobileSidebar');
            
            overlay.classList.add('hidden');
            sidebar.classList.add('-translate-x-full');
        }

        document.getElementById('closeSidebar')?.addEventListener('click', closeMobileSidebar);
        document.getElementById('sidebarOverlay')?.addEventListener('click', closeMobileSidebar);

        // Active nav link highlighting
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('bg-gray-700', 'text-white');
            }
        });
    </script>
</body>
</html>
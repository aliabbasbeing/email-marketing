<?php
// Define the base URL based on the environment (local or online server)
$base_url = '';
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local server
    $base_url = 'http://localhost/e-marketing/';
} else {
    // Online (production) server
    $base_url = 'https://alijoy.site/';
}

// Helper function to extract <title> from the current page
function get_page_title($file_path) {
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        preg_match("/<title>(.*?)<\/title>/", $content, $matches);
        return $matches[1] ?? 'E-Marketing';
    }
    return 'E-Marketing';
}

// Get the title dynamically
$current_page = basename($_SERVER['PHP_SELF']); // Get current page file name
$page_title = get_page_title($current_page); // Extract title from the page
?>

<header class="flex justify-between items-center mb-6 bg-gray-800 p-4">
    <div class="flex items-center">
        <!-- Hamburger Menu -->
        <button id="sidenavToggle" class="text-gray-200 text-2xl focus:outline-none mr-4">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-3xl font-semibold text-blue-400">
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
    </div>
</header>

<!-- Sidenav -->
<div id="sidenavContainer" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 transition-opacity duration-300">
    <div id="sidenav" class="w-64 bg-gray-800 h-full shadow-lg transform -translate-x-full transition-transform duration-300">
        <button id="sidenavClose" class="text-gray-400 text-2xl p-4 focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
        <nav class="p-4">
            <ul class="space-y-4">
            <li>
                    <a href="<?php echo $base_url; ?>" class="block text-gray-200 hover:bg-blue-600 px-4 py-2 rounded">
                        <i class="fas fa-home-alt"></i> Home
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>dashboard.php" class="block text-gray-200 hover:bg-blue-600 px-4 py-2 rounded">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>smtp.php" class="block text-gray-200 hover:bg-blue-600 px-4 py-2 rounded">
                        <i class="fas fa-cog"></i> SMTP Settings
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>campaign_results.php" class="block text-gray-200 hover:bg-blue-600 px-4 py-2 rounded">
                        <i class="fas fa-envelope"></i> Campaign Results
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>email_manager.php" class="block text-gray-200 hover:bg-blue-600 px-4 py-2 rounded">
                        <i class="fas fa-envelope"></i> Email Managemnet
                    </a>
                </li>
                <li>
                    <form action="<?php echo $base_url; ?>logout.php" method="POST">
                        <button type="submit" class="w-full text-left text-gray-200 hover:bg-red-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</div>

<script>
    const sidenavContainer = document.getElementById('sidenavContainer');
    const sidenav = document.getElementById('sidenav');
    const sidenavToggle = document.getElementById('sidenavToggle');
    const sidenavClose = document.getElementById('sidenavClose');

    // Open sidenav
    sidenavToggle.addEventListener('click', function () {
        sidenavContainer.classList.remove('hidden');
        setTimeout(() => {
            sidenavContainer.classList.replace('opacity-0', 'opacity-100');
            sidenav.classList.replace('-translate-x-full', 'translate-x-0');
        }, 10);
    });

    // Close sidenav
    function closeSidenav() {
        sidenavContainer.classList.replace('opacity-100', 'opacity-0');
        sidenav.classList.replace('translate-x-0', '-translate-x-full');
        setTimeout(() => sidenavContainer.classList.add('hidden'), 300);
    }

    sidenavClose.addEventListener('click', closeSidenav);

    // Close sidenav on clicking outside
    sidenavContainer.addEventListener('click', function (e) {
        if (e.target === sidenavContainer) {
            closeSidenav();
        }
    });
</script>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 text-white">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="mt-2 text-blue-100">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>! Here's your email marketing overview.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-bullhorn text-3xl text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Campaigns</p>
                    <p class="text-2xl font-semibold text-white"><?= $stats['totalCampaigns'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-play-circle text-3xl text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Active Campaigns</p>
                    <p class="text-2xl font-semibold text-white"><?= $stats['activeCampaigns'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-3xl text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Emails This Month</p>
                    <p class="text-2xl font-semibold text-white"><?= number_format($stats['monthlyEmails'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-3xl text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Success Rate</p>
                    <p class="text-2xl font-semibold text-white"><?= $stats['successRate'] ?? 0 ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/campaigns/create" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 text-center transition-colors">
                <i class="fas fa-plus-circle text-2xl mb-2"></i>
                <p class="font-medium">Create Campaign</p>
            </a>
            
            <a href="/emails" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 text-center transition-colors">
                <i class="fas fa-upload text-2xl mb-2"></i>
                <p class="font-medium">Import Emails</p>
            </a>
            
            <a href="/analytics" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 text-center transition-colors">
                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                <p class="font-medium">View Analytics</p>
            </a>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-white">Recent Campaigns</h2>
            <a href="/campaigns" class="text-blue-400 hover:text-blue-300">View All</a>
        </div>
        
        <?php if (!empty($recentCampaigns)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-300">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Campaign Name</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Created</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCampaigns as $campaign): ?>
                            <tr class="bg-gray-800 border-b border-gray-700 hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-white">
                                    <?= htmlspecialchars($campaign['name']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php
                                        switch ($campaign['status']) {
                                            case 'completed':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'running':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'failed':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= ucfirst($campaign['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?= date('M j, Y', strtotime($campaign['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="/campaigns/<?= $campaign['id'] ?>" class="text-blue-400 hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>No campaigns yet. <a href="/campaigns/create" class="text-blue-400 hover:text-blue-300">Create your first campaign</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
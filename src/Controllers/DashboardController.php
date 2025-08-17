<?php

namespace Controllers;

class DashboardController extends BaseController
{
    private $userModel;
    private $campaignModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new \Models\User();
        $this->campaignModel = new \Models\Campaign();
    }
    
    /**
     * Show dashboard
     */
    public function index()
    {
        $this->requireAuth();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent campaigns
        $recentCampaigns = $this->campaignModel->getRecent(5);
        
        // Get flash messages
        $success = $this->getFlash('success');
        $error = $this->getFlash('error');
        
        $this->render('dashboard', [
            'stats' => $stats,
            'recentCampaigns' => $recentCampaigns,
            'success' => $success,
            'error' => $error,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $userId = $_SESSION['user_id'];
        
        // Total campaigns
        $totalCampaigns = $this->campaignModel->count('user_id = ?', [$userId]);
        
        // Active campaigns
        $activeCampaigns = $this->campaignModel->count('user_id = ? AND status = ?', [$userId, 'running']);
        
        // Total emails sent (this month)
        $sql = "SELECT COUNT(*) FROM email_logs el 
                JOIN campaigns c ON el.campaign_id = c.id 
                WHERE c.user_id = ? AND el.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $monthlyEmails = $this->campaignModel->queryFirst($sql, [$userId])['COUNT(*)'] ?? 0;
        
        // Success rate
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as successful
                FROM email_logs el 
                JOIN campaigns c ON el.campaign_id = c.id 
                WHERE c.user_id = ?";
        $emailStats = $this->campaignModel->queryFirst($sql, [$userId]);
        $successRate = $emailStats['total'] > 0 ? 
                      round(($emailStats['successful'] / $emailStats['total']) * 100, 2) : 0;
        
        return [
            'totalCampaigns' => $totalCampaigns,
            'activeCampaigns' => $activeCampaigns,
            'monthlyEmails' => $monthlyEmails,
            'successRate' => $successRate
        ];
    }
}
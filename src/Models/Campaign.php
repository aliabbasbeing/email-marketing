<?php

namespace Models;

class Campaign extends BaseModel
{
    protected $table = 'campaigns';
    protected $fillable = ['name', 'subject', 'body', 'user_id', 'status', 'scheduled_at', 'total_emails', 'sent_emails', 'failed_emails'];
    
    /**
     * Get campaigns by user
     */
    public function getByUser($userId)
    {
        return $this->where('user_id = ?', [$userId]);
    }
    
    /**
     * Get campaigns by status
     */
    public function getByStatus($status)
    {
        return $this->where('status = ?', [$status]);
    }
    
    /**
     * Update campaign progress
     */
    public function updateProgress($campaignId, $sentEmails, $failedEmails)
    {
        return $this->update($campaignId, [
            'sent_emails' => $sentEmails,
            'failed_emails' => $failedEmails
        ]);
    }
    
    /**
     * Mark campaign as completed
     */
    public function markCompleted($campaignId)
    {
        return $this->update($campaignId, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mark campaign as failed
     */
    public function markFailed($campaignId, $reason = null)
    {
        return $this->update($campaignId, [
            'status' => 'failed',
            'failed_at' => date('Y-m-d H:i:s'),
            'failure_reason' => $reason
        ]);
    }
    
    /**
     * Get campaign statistics
     */
    public function getStats($campaignId)
    {
        $campaign = $this->find($campaignId);
        if (!$campaign) {
            return null;
        }
        
        // Get email logs for this campaign
        $sql = "SELECT 
                    COUNT(*) as total_sent,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as successful_sends,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_sends,
                    SUM(CASE WHEN is_opened = 1 THEN 1 ELSE 0 END) as opens,
                    COUNT(DISTINCT CASE WHEN is_opened = 1 THEN email END) as unique_opens
                FROM email_logs 
                WHERE campaign_id = ?";
        
        $stats = $this->queryFirst($sql, [$campaignId]);
        
        return array_merge($campaign, $stats ?: []);
    }
    
    /**
     * Get recent campaigns
     */
    public function getRecent($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?";
        return $this->query($sql, [$limit]);
    }
}
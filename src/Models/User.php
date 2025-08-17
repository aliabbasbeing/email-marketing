<?php

namespace Models;

class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'role', 'status'];
    
    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        return $this->whereFirst('username = ?', [$username]);
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->whereFirst('email = ?', [$email]);
    }
    
    /**
     * Create a new user with hashed password
     */
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        $data['status'] = $data['status'] ?? 'active';
        $data['role'] = $data['role'] ?? 'user';
        
        return $this->create($data);
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword($user, $password)
    {
        return password_verify($password, $user['password']);
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        return $this->where('role = ?', [$role]);
    }
    
    /**
     * Get active users
     */
    public function getActive()
    {
        return $this->where('status = ?', ['active']);
    }
    
    /**
     * Update last login time
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
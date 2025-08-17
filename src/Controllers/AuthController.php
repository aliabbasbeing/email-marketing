<?php

namespace Controllers;

class AuthController extends BaseController
{
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new \Models\User();
    }
    
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already authenticated
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $error = $this->getFlash('error');
        $success = $this->getFlash('success');
        
        $this->render('auth/login', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrf()
        ], 'auth');
    }
    
    /**
     * Handle login
     */
    public function login()
    {
        $this->validateCsrf();
        
        $username = $this->input('username');
        $password = $this->input('password');
        
        // Validate input
        $errors = $this->validate($_POST, [
            'username' => 'required',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->flash('error', 'Username and password are required');
            $this->redirect('/login');
        }
        
        // Find user
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            $this->flash('error', 'Invalid username or password');
            $this->redirect('/login');
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            $this->flash('error', 'Account is not active');
            $this->redirect('/login');
        }
        
        // Verify password
        if (!$this->userModel->verifyPassword($user, $password)) {
            $this->flash('error', 'Invalid username or password');
            $this->redirect('/login');
        }
        
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Log activity
        $this->log("User {$user['username']} logged in", 'info');
        
        $this->redirect('/dashboard');
    }
    
    /**
     * Show registration form
     */
    public function showRegister()
    {
        // Redirect if already authenticated
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $error = $this->getFlash('error');
        $success = $this->getFlash('success');
        
        $this->render('auth/register', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrf()
        ], 'auth');
    }
    
    /**
     * Handle registration
     */
    public function register()
    {
        $this->validateCsrf();
        
        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password_confirm'),
            'role' => $this->input('role', 'user')
        ];
        
        // Validate input
        $errors = $this->validate($data, [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:255'
        ]);
        
        // Check password confirmation
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        // Check if username exists
        if ($this->userModel->findByUsername($data['username'])) {
            $errors['username'] = 'Username already exists';
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($data['email'])) {
            $errors['email'] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            $this->flash('error', implode('<br>', $errors));
            $this->redirect('/register');
        }
        
        // Create user
        try {
            $user = $this->userModel->createUser($data);
            
            $this->log("New user registered: {$data['username']}", 'info');
            $this->flash('success', 'Registration successful! You can now login.');
            $this->redirect('/login');
            
        } catch (\Exception $e) {
            $this->log("Registration failed for {$data['username']}: " . $e->getMessage(), 'error');
            $this->flash('error', 'Registration failed. Please try again.');
            $this->redirect('/register');
        }
    }
    
    /**
     * Handle logout
     */
    public function logout()
    {
        $username = $_SESSION['username'] ?? 'unknown';
        
        // Destroy session
        session_destroy();
        
        $this->log("User {$username} logged out", 'info');
        $this->redirect('/login');
    }
}
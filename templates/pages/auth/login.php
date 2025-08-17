<form method="POST" action="/login" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div>
        <label for="username" class="block text-sm font-medium text-gray-200 mb-2">
            <i class="fas fa-user mr-2"></i>Username
        </label>
        <input 
            type="text" 
            id="username" 
            name="username" 
            required 
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your username"
        >
    </div>
    
    <div>
        <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
            <i class="fas fa-lock mr-2"></i>Password
        </label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            required 
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your password"
        >
    </div>
    
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input 
                id="remember" 
                name="remember" 
                type="checkbox" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-200">
                Remember me
            </label>
        </div>
        
        <div class="text-sm">
            <a href="/forgot-password" class="text-blue-300 hover:text-blue-200">
                Forgot your password?
            </a>
        </div>
    </div>
    
    <button 
        type="submit" 
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent"
    >
        <i class="fas fa-sign-in-alt mr-2"></i>
        Sign In
    </button>
    
    <div class="text-center">
        <p class="text-gray-200">
            Don't have an account? 
            <a href="/register" class="text-blue-300 hover:text-blue-200 font-medium">
                Sign up here
            </a>
        </p>
    </div>
</form>
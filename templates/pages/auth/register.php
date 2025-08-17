<form method="POST" action="/register" class="space-y-6">
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
            minlength="3"
            maxlength="50"
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Choose a username"
        >
    </div>
    
    <div>
        <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
            <i class="fas fa-envelope mr-2"></i>Email Address
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your email"
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
            minlength="8"
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Choose a strong password"
        >
        <p class="text-xs text-gray-300 mt-1">At least 8 characters long</p>
    </div>
    
    <div>
        <label for="password_confirm" class="block text-sm font-medium text-gray-200 mb-2">
            <i class="fas fa-lock mr-2"></i>Confirm Password
        </label>
        <input 
            type="password" 
            id="password_confirm" 
            name="password_confirm" 
            required 
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Confirm your password"
        >
    </div>
    
    <div>
        <label for="role" class="block text-sm font-medium text-gray-200 mb-2">
            <i class="fas fa-user-tag mr-2"></i>Account Type
        </label>
        <select 
            id="role" 
            name="role" 
            class="w-full px-4 py-3 bg-white bg-opacity-20 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
            <option value="user" class="text-gray-800">User</option>
            <option value="admin" class="text-gray-800">Admin</option>
        </select>
    </div>
    
    <div class="flex items-center">
        <input 
            id="terms" 
            name="terms" 
            type="checkbox" 
            required
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        >
        <label for="terms" class="ml-2 block text-sm text-gray-200">
            I agree to the <a href="/terms" class="text-blue-300 hover:text-blue-200">Terms and Conditions</a>
        </label>
    </div>
    
    <button 
        type="submit" 
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent"
    >
        <i class="fas fa-user-plus mr-2"></i>
        Create Account
    </button>
    
    <div class="text-center">
        <p class="text-gray-200">
            Already have an account? 
            <a href="/login" class="text-blue-300 hover:text-blue-200 font-medium">
                Sign in here
            </a>
        </p>
    </div>
</form>

<script>
// Password confirmation validation
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (confirm && password !== confirm) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirm = document.getElementById('password_confirm');
    if (confirm.value) {
        confirm.dispatchEvent(new Event('input'));
    }
});
</script>
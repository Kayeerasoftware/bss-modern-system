<!-- BSS Loading Screen Component -->
<div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
    <div class="text-center">
        <!-- BSS Logo -->
        <div class="mb-8">
            <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Investment Group" class="w-32 h-32 mx-auto animate-bounce">
        </div>
        
        <!-- Company Name -->
        <h1 class="text-4xl font-black bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
            BSS Investment Group
        </h1>
        
        <!-- Tagline -->
        <p class="text-lg text-gray-600 mb-8">Financial Management System</p>
        
        <!-- Loading Spinner -->
        <div class="flex justify-center mb-6">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600"></div>
        </div>
        
        <!-- Loading Progress Bar -->
        <div class="w-80 bg-gray-200 rounded-full h-3 mx-auto mb-4">
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 h-3 rounded-full transition-all duration-300 ease-out" style="width: 0%" id="loading-progress"></div>
        </div>
        
        <!-- Loading Text -->
        <p class="text-gray-600 text-sm" id="loading-text">Initializing system...</p>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#loading-screen {
    animation: fadeInUp 0.5s ease-out;
}

#loading-screen img {
    filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    const progressBar = document.getElementById('loading-progress');
    const loadingText = document.getElementById('loading-text');
    
    const loadingMessages = [
        'Initializing system...',
        'Loading BSS components...',
        'Connecting to database...',
        'Preparing dashboard...',
        'Almost ready...'
    ];
    
    let messageIndex = 0;
    let progress = 0;
    
    // Update loading messages
    const messageInterval = setInterval(() => {
        if (messageIndex < loadingMessages.length - 1) {
            messageIndex++;
            loadingText.textContent = loadingMessages[messageIndex];
        }
    }, 800);
    
    // Simulate loading progress
    const progressInterval = setInterval(() => {
        progress += Math.random() * 12 + 3;
        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            clearInterval(messageInterval);
            
            loadingText.textContent = 'Ready!';
            
            // Hide loading screen after completion
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                loadingScreen.style.transform = 'scale(0.95)';
                loadingScreen.style.transition = 'all 0.6s ease-out';
                
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 600);
            }, 800);
        }
        progressBar.style.width = progress + '%';
    }, 150);
});
</script>
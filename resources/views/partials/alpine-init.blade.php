<!-- Alpine.js Scripts -->
<script src="{{ asset('assets/js/main2.js') }}"></script>
<script src="{{ asset('js/chat.js') }}"></script>
<script>
    // Ensure functions are available before Alpine initializes
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized');
        console.log('adminPanel available:', typeof window.adminPanel);
        console.log('chatModule available:', typeof window.chatModule);
    });
</script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

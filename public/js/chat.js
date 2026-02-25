// WhatsApp-like Chat System
window.chatModule = function() {
    return {
        showChatModal: false,
        chatMessages: [],
        chatInput: '',
        showMemberChatModal: false,
        selectedMemberChat: null,
        memberChatMessages: [],
        memberChatInput: '',
        memberChatSearch: '',
        filteredMembersChat: [],
        chatFilter: 'all',
        isTyping: false,
        onlineMembers: [],
        recordingAudio: false,
        showEmojiPicker: false,
        replyingTo: null,
        selectedMessages: [],
        showMessageOptions: null,
        lastSeen: {},
        currentMemberId: null,

        initChat() {
            this.chatMessages = [{sender: 'support', text: 'Hello! How can I help you today?', time: this.getCurrentTime(), timestamp: Date.now()}];
            this.filteredMembersChat = this.members || [];
            this.simulateOnlineStatus();
            setInterval(() => this.simulateOnlineStatus(), 30000);
            this.updateLastSeen();
            this.getCurrentMemberId();
        },

        async getCurrentMemberId() {
            try {
                const response = await fetch('/api/current-member');
                if (response.ok) {
                    const data = await response.json();
                    this.currentMemberId = data.member_id;
                }
            } catch (error) {
                console.error('Error getting current member:', error);
            }
        },

        getCurrentTime() {
            return new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
        },

        formatMessageTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            if (diff < 86400000) return date.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
            if (diff < 604800000) return date.toLocaleDateString('en-US', {weekday: 'short'});
            return date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
        },

        simulateOnlineStatus() {
            this.onlineMembers = (this.members || []).filter(() => Math.random() > 0.6).map(m => m.id || m.member_id);
        },

        isOnline(memberId) {
            return this.onlineMembers.includes(memberId);
        },

        updateLastSeen() {
            (this.members || []).forEach(m => {
                const memberId = m.id || m.member_id;
                if (!this.isOnline(memberId)) {
                    this.lastSeen[memberId] = new Date(Date.now() - Math.random() * 3600000).toLocaleTimeString();
                }
            });
        },

        sendMessage() {
            if (!this.chatInput.trim()) return;
            const msg = {sender: 'user', text: this.chatInput, time: this.getCurrentTime(), timestamp: Date.now(), status: 'sent', reply: this.replyingTo};
            this.chatMessages.push(msg);
            this.chatInput = '';
            this.replyingTo = null;
            this.scrollToBottom('chatMessages');
            setTimeout(() => { msg.status = 'delivered'; setTimeout(() => { msg.status = 'read'; this.autoReply(); }, 1000); }, 500);
        },

        autoReply() {
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.chatMessages.push({sender: 'support', text: 'Thank you! Our team will assist you shortly.', time: this.getCurrentTime(), timestamp: Date.now()});
                this.scrollToBottom('chatMessages');
            }, 2000);
        },

        async sendMemberMessage() {
            if (!this.memberChatInput.trim() || !this.selectedMemberChat) return;
            
            const messageText = this.memberChatInput;
            this.memberChatInput = '';
            
            // Optimistic UI update
            const tempMsg = {
                sender: 'me', 
                text: messageText, 
                time: this.getCurrentTime(), 
                timestamp: Date.now(), 
                status: 'sending',
                id: 'temp-' + Date.now()
            };
            this.memberChatMessages.push(tempMsg);
            this.scrollToBottom('memberChatMessages');
            
            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        receiver_id: this.selectedMemberChat.member_id,
                        message: messageText
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Replace temp message with real one
                    const index = this.memberChatMessages.findIndex(m => m.id === tempMsg.id);
                    if (index !== -1) {
                        this.memberChatMessages[index] = data.message;
                    }
                } else {
                    tempMsg.status = 'failed';
                }
            } catch (error) {
                console.error('Error sending message:', error);
                tempMsg.status = 'failed';
            }
        },

        async selectMemberChat(member) {
            this.selectedMemberChat = member;
            this.selectedMessages = [];
            this.replyingTo = null;
            await this.loadChatHistory(member.member_id);
        },

        async loadChatHistory(memberId) {
            if (!this.currentMemberId) {
                await this.getCurrentMemberId();
            }
            
            try {
                const response = await fetch(`/chat/messages/${this.currentMemberId}/${memberId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.memberChatMessages = data.messages;
                    this.scrollToBottom('memberChatMessages');
                }
            } catch (error) {
                console.error('Error loading chat history:', error);
                this.memberChatMessages = [];
            }
        },

        filterMembersForChat() {
            const search = (this.memberChatSearch || '').toLowerCase();
            const filter = this.chatFilter || 'all';
            const source = this.originalMembers || this.members || [];
            
            this.filteredMembersChat = source.filter(member => {
                const name = (member.full_name || member.name || '').toLowerCase();
                const id = (member.member_id || member.id || '').toString().toLowerCase();
                const email = (member.email || '').toLowerCase();
                const role = (member.role || '').toLowerCase();
                
                const matchSearch = !search || name.includes(search) || id.includes(search) || email.includes(search);
                const matchRole = filter === 'all' || role === filter;
                
                return matchSearch && matchRole;
            });
        },

        async getUnreadCount(memberId) {
            // This would be fetched from API in production
            return 0;
        },

        replyToMessage(msg) {
            this.replyingTo = msg;
        },

        cancelReply() {
            this.replyingTo = null;
        },

        deleteMessage(msgId) {
            if (confirm('Delete this message?')) {
                this.memberChatMessages = this.memberChatMessages.filter(m => m.id !== msgId);
            }
        },

        forwardMessage(msg) {
            alert('Forward to: ' + msg.text);
        },

        copyMessage(msg) {
            navigator.clipboard.writeText(msg.text);
            alert('Message copied!');
        },

        toggleMessageSelection(msgId) {
            const idx = this.selectedMessages.indexOf(msgId);
            if (idx > -1) this.selectedMessages.splice(idx, 1);
            else this.selectedMessages.push(msgId);
        },

        deleteSelectedMessages() {
            if (confirm(`Delete ${this.selectedMessages.length} messages?`)) {
                this.memberChatMessages = this.memberChatMessages.filter(m => !this.selectedMessages.includes(m.id));
                this.selectedMessages = [];
            }
        },

        insertEmoji(emoji) {
            this.memberChatInput += emoji;
            this.showEmojiPicker = false;
        },

        startVoiceRecording() {
            this.recordingAudio = true;
            setTimeout(() => {
                this.recordingAudio = false;
                this.memberChatMessages.push({sender: 'me', text: '🎤 Voice message', time: this.getCurrentTime(), timestamp: Date.now(), status: 'sent', type: 'audio', id: Date.now()});
                this.scrollToBottom('memberChatMessages');
            }, 2000);
        },

        sendQuickMessage(msg) {
            this.chatInput = msg;
            this.sendMessage();
        },

        getMessageStatus(status) {
            if (status === 'sent' || status === 'sending') return 'fa-check text-gray-400';
            if (status === 'delivered') return 'fa-check-double text-gray-400';
            if (status === 'read') return 'fa-check-double text-blue-500';
            if (status === 'failed') return 'fa-exclamation-circle text-red-500';
            return '';
        },

        scrollToBottom(id) {
            this.$nextTick(() => {
                const el = document.getElementById(id);
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        getLastMessage(member) {
            return 'Click to chat';
        },

        getLastMessageTime(member) {
            return '';
        }
    };
}

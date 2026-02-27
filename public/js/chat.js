// WhatsApp-like chat behavior for member-to-member conversations.
window.chatModule = function () {
    return {
        showChatModal: false,
        showImagePreview: false,
        imagePreviewUrl: null,
        imagePreviewName: '',
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
        emojiList: ['😀', '😂', '😍', '👍', '🙏', '🎉', '🔥', '💯', '😎', '🤝', '✅', '❤️', '😢', '😡', '🤔', '👏'],
        replyingTo: null,
        selectedMessages: [],
        showMessageOptions: null,
        lastSeen: {},
        currentMemberId: null,
        refreshTimerId: null,
        chatRefreshing: false,
        selectedAttachment: null,
        showAttachmentMenu: false,
        showCameraMenu: false,
        attachmentAccept: '*/*',
        attachmentFilterLabel: 'Any File',
        mediaRecorder: null,
        mediaStream: null,
        recordingChunks: [],
        recordingSeconds: 0,
        recordingIntervalId: null,
        sendRecordedAudioOnStop: false,

        initChat() {
            this.chatMessages = [
                { sender: 'support', text: 'Hello! How can I help you today?', time: this.getCurrentTime(), timestamp: Date.now() }
            ];

            this.prepareMembers();
            this.filteredMembersChat = this.members || [];
            this.bootstrapChatData();
        },

        async bootstrapChatData() {
            await this.getCurrentMemberId();
            await this.refreshConversations();
            this.startChatPolling();
        },

        prepareMembers() {
            const source = this.originalMembers || this.members || [];
            const normalized = source
                .map((member) => {
                    const memberId = member.member_id || member.id || null;
                    return {
                        ...member,
                        member_id: memberId,
                        full_name: member.full_name || member.name || 'Unknown User',
                        unread: Number(member.unread || 0),
                        last_message: member.last_message || '',
                        timestamp: Number(member.timestamp || 0),
                    };
                })
                .filter((member) => member.member_id);

            this.members = normalized;
            this.originalMembers = normalized;
            this.filterMembersForChat();
        },

        startChatPolling() {
            if (this.refreshTimerId) {
                clearInterval(this.refreshTimerId);
            }

            this.refreshTimerId = setInterval(async () => {
                await this.refreshConversations();

                if (this.selectedMemberChat?.member_id) {
                    await this.loadChatHistory(this.selectedMemberChat.member_id, false);
                }
            }, 2000);
        },

        async getCurrentMemberId() {
            try {
                const response = await fetch('/chat/me', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) return;
                const data = await response.json();
                if (data.success) {
                    this.currentMemberId = data.member_id;
                }
            } catch (error) {
                console.error('Error getting current member:', error);
            }
        },

        async refreshConversations() {
            try {
                const response = await fetch('/chat/conversations', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) return;

                const data = await response.json();
                if (!data.success) return;

                const conversationMap = new Map((data.conversations || []).map((c) => [String(c.member_id), c]));

                this.members = (this.originalMembers || this.members || []).map((member) => {
                    const key = String(member.member_id);
                    const conversation = conversationMap.get(key);

                    if (!conversation) {
                        return {
                            ...member,
                            unread: Number(member.unread || 0),
                            last_message: member.last_message || '',
                            timestamp: Number(member.timestamp || 0),
                        };
                    }

                    return {
                        ...member,
                        role: conversation.role || member.role,
                        profile_picture: conversation.profile_picture || member.profile_picture,
                        unread: Number(conversation.unread || 0),
                        last_message: conversation.last_message || '',
                        last_time: conversation.last_time || '',
                        timestamp: Number(conversation.timestamp || 0),
                    };
                });

                this.updateOnlineMembers();
                this.sortMembers();
                this.filterMembersForChat();
            } catch (error) {
                console.error('Error refreshing conversations:', error);
            }
        },

        async refreshChatOnly() {
            if (this.chatRefreshing) return;
            this.chatRefreshing = true;

            try {
                await this.refreshConversations();

                if (this.selectedMemberChat?.member_id) {
                    await this.markAsRead(this.selectedMemberChat.member_id);
                    await this.loadChatHistory(this.selectedMemberChat.member_id, false);
                }
            } catch (error) {
                console.error('Error refreshing chat:', error);
            } finally {
                this.chatRefreshing = false;
            }
        },

        updateOnlineMembers() {
            const now = Date.now();
            const activeWindowMs = 5 * 60 * 1000;

            this.onlineMembers = (this.members || [])
                .filter((member) => member.timestamp && (now - Number(member.timestamp) <= activeWindowMs))
                .map((member) => member.member_id);
        },

        sortMembers() {
            this.members = [...(this.members || [])].sort((a, b) => {
                const aUnread = Number(a.unread || 0);
                const bUnread = Number(b.unread || 0);

                if (aUnread !== bUnread) {
                    return bUnread - aUnread;
                }

                return Number(b.timestamp || 0) - Number(a.timestamp || 0);
            });
        },

        getCurrentTime() {
            return new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        },

        formatMessageTime(timestamp) {
            if (!timestamp) return '';

            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 86400000) return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            if (diff < 604800000) return date.toLocaleDateString('en-US', { weekday: 'short' });
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },

        isOnline(memberId) {
            return this.onlineMembers.includes(memberId);
        },

        sendMessage() {
            if (!this.chatInput.trim()) return;
            const msg = {
                sender: 'user',
                text: this.chatInput,
                time: this.getCurrentTime(),
                timestamp: Date.now(),
                status: 'sent',
                reply: this.replyingTo
            };
            this.chatMessages.push(msg);
            this.chatInput = '';
            this.replyingTo = null;
            this.scrollToBottom('chatMessages');
            setTimeout(() => {
                msg.status = 'delivered';
                setTimeout(() => {
                    msg.status = 'read';
                    this.autoReply();
                }, 1000);
            }, 500);
        },

        autoReply() {
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.chatMessages.push({
                    sender: 'support',
                    text: 'Thank you! Our team will assist you shortly.',
                    time: this.getCurrentTime(),
                    timestamp: Date.now()
                });
                this.scrollToBottom('chatMessages');
            }, 1500);
        },

        async sendMemberMessage() {
            if (!this.memberChatInput.trim() && !this.selectedAttachment) return;
            if (!this.selectedMemberChat?.member_id) {
                alert('Unable to send: selected user is not a valid member chat target.');
                return;
            }

            const messageText = this.memberChatInput.trim();
            const attachment = this.selectedAttachment;
            this.memberChatInput = '';
            this.resetMessageInputHeight();
            this.selectedAttachment = null;
            if (this.$refs.chatAttachmentInput) {
                this.$refs.chatAttachmentInput.value = '';
            }
            if (this.$refs.chatCameraPhotoInput) {
                this.$refs.chatCameraPhotoInput.value = '';
            }
            if (this.$refs.chatCameraVideoInput) {
                this.$refs.chatCameraVideoInput.value = '';
            }

            const tempMsg = {
                sender: 'me',
                text: messageText,
                time: this.getCurrentTime(),
                timestamp: Date.now(),
                status: 'sending',
                id: `temp-${Date.now()}`,
                attachment_name: attachment?.name || null
            };

            this.memberChatMessages.push(tempMsg);
            this.scrollToBottom('memberChatMessages');

            try {
                const formData = new FormData();
                formData.append('receiver_id', this.selectedMemberChat.member_id);
                formData.append('message', messageText);
                if (attachment) {
                    formData.append('attachment', attachment);
                }

                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (!data.success) {
                    tempMsg.status = 'failed';
                    return;
                }

                const index = this.memberChatMessages.findIndex((m) => m.id === tempMsg.id);
                if (index !== -1) {
                    this.memberChatMessages[index] = data.message;
                }

                await this.refreshConversations();
            } catch (error) {
                console.error('Error sending message:', error);
                tempMsg.status = 'failed';
            }
        },

        triggerAttachmentPicker() {
            if (this.$refs.chatAttachmentInput) {
                this.$refs.chatAttachmentInput.click();
            }
        },

        toggleAttachmentMenu() {
            this.showCameraMenu = false;
            this.showAttachmentMenu = !this.showAttachmentMenu;
        },

        selectAttachmentType(accept, label) {
            this.attachmentAccept = accept || '*/*';
            this.attachmentFilterLabel = label || 'Any File';
            this.showAttachmentMenu = false;
            this.$nextTick(() => this.triggerAttachmentPicker());
        },

        handleAttachmentChange(event) {
            const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
            if (!file) return;

            this.selectedAttachment = file;
            this.showAttachmentMenu = false;
        },

        clearAttachment() {
            this.selectedAttachment = null;
            this.attachmentFilterLabel = 'Any File';
            this.attachmentAccept = '*/*';
            if (this.$refs.chatAttachmentInput) {
                this.$refs.chatAttachmentInput.value = '';
            }
            if (this.$refs.chatCameraPhotoInput) {
                this.$refs.chatCameraPhotoInput.value = '';
            }
            if (this.$refs.chatCameraVideoInput) {
                this.$refs.chatCameraVideoInput.value = '';
            }
        },

        toggleCameraMenu() {
            this.showAttachmentMenu = false;
            this.showCameraMenu = !this.showCameraMenu;
        },

        openCameraPhotoCapture() {
            this.showCameraMenu = false;
            this.attachmentFilterLabel = 'Camera Photo';
            if (this.$refs.chatCameraPhotoInput) {
                this.$refs.chatCameraPhotoInput.click();
            }
        },

        openCameraVideoCapture() {
            this.showCameraMenu = false;
            this.attachmentFilterLabel = 'Camera Video';
            if (this.$refs.chatCameraVideoInput) {
                this.$refs.chatCameraVideoInput.click();
            }
        },

        handleCameraPhotoChange(event) {
            const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
            if (!file) return;

            this.selectedAttachment = file;
            this.showCameraMenu = false;
        },

        handleCameraVideoChange(event) {
            const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
            if (!file) return;

            this.selectedAttachment = file;
            this.showCameraMenu = false;
        },

        canSendMemberMessage() {
            return Boolean((this.memberChatInput && this.memberChatInput.trim()) || this.selectedAttachment);
        },

        normalizePhoneNumber(rawPhone) {
            if (!rawPhone) return null;
            let digits = String(rawPhone).replace(/[^\d+]/g, '');
            if (digits.startsWith('00')) digits = '+' + digits.slice(2);
            if (!digits.startsWith('+') && digits.length >= 9) {
                // Keep local numbers as-is for tel:, but convert for wa.me below.
                return digits;
            }
            return digits;
        },

        normalizePhoneForWhatsApp(rawPhone) {
            const phone = this.normalizePhoneNumber(rawPhone);
            if (!phone) return null;
            let digitsOnly = phone.replace(/[^\d]/g, '');
            if (digitsOnly.startsWith('0') && digitsOnly.length === 10) {
                // Uganda local format fallback.
                digitsOnly = '256' + digitsOnly.slice(1);
            }
            return digitsOnly.length >= 10 ? digitsOnly : null;
        },

        getSelectedMemberPhone() {
            return this.selectedMemberChat?.phone || this.selectedMemberChat?.contact || null;
        },

        startDirectCall() {
            const rawPhone = this.getSelectedMemberPhone();
            const phone = this.normalizePhoneNumber(rawPhone);

            if (!phone) {
                alert('This user does not have a phone number saved.');
                return;
            }

            window.location.href = `tel:${phone.replace(/[^\d+]/g, '')}`;
        },

        async startVideoCall() {
            if (!this.selectedMemberChat?.member_id) {
                alert('Select a member first.');
                return;
            }

            const caller = this.currentMemberId || 'caller';
            const receiver = this.selectedMemberChat.member_id;
            const room = `bss-${caller}-${receiver}`.replace(/[^a-zA-Z0-9-]/g, '').toLowerCase();
            const meetingUrl = `https://meet.jit.si/${room}`;
            const inviteText = `Video call invite: ${meetingUrl}`;

            // Send invite into chat for auditability and receiver visibility.
            this.memberChatInput = inviteText;
            await this.sendMemberMessage();

            const waPhone = this.normalizePhoneForWhatsApp(this.getSelectedMemberPhone());
            if (waPhone) {
                const waText = encodeURIComponent(`Join my video call now: ${meetingUrl}`);
                window.open(`https://wa.me/${waPhone}?text=${waText}`, '_blank', 'noopener');
            }

            window.open(meetingUrl, '_blank', 'noopener');
        },

        handleComposerEnter(event) {
            if (event.shiftKey) {
                this.memberChatInput += '\n';
                this.$nextTick(() => this.autoResizeFromRef());
                return;
            }

            this.sendMemberMessage();
        },

        autoResizeMessageInput(event) {
            const el = event?.target || this.$refs?.messageInput;
            if (!el) return;

            el.style.height = 'auto';
            const maxHeight = 128;
            el.style.height = Math.min(el.scrollHeight, maxHeight) + 'px';
            el.style.overflowY = el.scrollHeight > maxHeight ? 'auto' : 'hidden';
        },

        autoResizeFromRef() {
            this.autoResizeMessageInput({ target: this.$refs?.messageInput });
        },

        resetMessageInputHeight() {
            const el = this.$refs?.messageInput;
            if (!el) return;
            el.style.height = 'auto';
            el.style.overflowY = 'hidden';
        },

        async selectMemberChat(member) {
            if (!member?.member_id) {
                alert('This user cannot be targeted for chat because member ID is missing.');
                return;
            }

            this.selectedMemberChat = member;
            this.selectedMessages = [];
            this.replyingTo = null;

            await this.markAsRead(member.member_id);
            await this.loadChatHistory(member.member_id);
            await this.refreshConversations();
        },

        async loadChatHistory(memberId, shouldScroll = true) {
            try {
                const response = await fetch(`/chat/messages/${memberId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) return;

                const data = await response.json();
                if (!data.success) return;

                this.memberChatMessages = data.messages || [];
                if (shouldScroll) {
                    this.scrollToBottom('memberChatMessages');
                }
            } catch (error) {
                console.error('Error loading chat history:', error);
            }
        },

        async markAsRead(memberId) {
            if (!memberId) return;

            try {
                await fetch(`/chat/mark-read/${memberId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        filterMembersForChat() {
            const search = (this.memberChatSearch || '').toLowerCase();
            const filter = this.chatFilter || 'all';
            const source = this.members || [];

            this.filteredMembersChat = source.filter((member) => {
                const name = (member.full_name || member.name || '').toLowerCase();
                const id = (member.member_id || '').toString().toLowerCase();
                const email = (member.email || '').toLowerCase();
                const role = (member.role || '').toLowerCase();

                const matchSearch = !search || name.includes(search) || id.includes(search) || email.includes(search);
                const matchRole = filter === 'all' || role === filter;

                return matchSearch && matchRole;
            });
        },

        replyToMessage(msg) {
            this.replyingTo = msg;
        },

        cancelReply() {
            this.replyingTo = null;
        },

        deleteMessage(msgId) {
            if (confirm('Delete this message?')) {
                this.memberChatMessages = this.memberChatMessages.filter((m) => m.id !== msgId);
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
                this.memberChatMessages = this.memberChatMessages.filter((m) => !this.selectedMessages.includes(m.id));
                this.selectedMessages = [];
            }
        },

        insertEmoji(emoji) {
            this.memberChatInput += emoji;
            this.$nextTick(() => this.autoResizeFromRef());
        },

        startVoiceRecording() {
            if (this.recordingAudio) {
                this.stopVoiceRecording(true);
                return;
            }

            this.beginVoiceRecording();
        },

        async beginVoiceRecording() {
            if (!this.selectedMemberChat?.member_id) {
                alert('Select a member first.');
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Voice recording is not supported in this browser.');
                return;
            }

            try {
                this.mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.recordingChunks = [];
                this.recordingSeconds = 0;

                const mimeType = MediaRecorder.isTypeSupported('audio/webm')
                    ? 'audio/webm'
                    : (MediaRecorder.isTypeSupported('audio/ogg') ? 'audio/ogg' : '');

                this.mediaRecorder = mimeType
                    ? new MediaRecorder(this.mediaStream, { mimeType })
                    : new MediaRecorder(this.mediaStream);
                this.sendRecordedAudioOnStop = false;

                this.mediaRecorder.ondataavailable = (event) => {
                    if (event.data && event.data.size > 0) {
                        this.recordingChunks.push(event.data);
                    }
                };

                this.mediaRecorder.onstop = async () => {
                    const chunks = [...this.recordingChunks];
                    const recorderMime = this.mediaRecorder?.mimeType || 'audio/webm';
                    const shouldSend = this.sendRecordedAudioOnStop;

                    this.cleanupRecorder();

                    if (!shouldSend || chunks.length === 0) {
                        return;
                    }

                    const ext = recorderMime.includes('ogg')
                        ? 'ogg'
                        : (recorderMime.includes('mp4') || recorderMime.includes('m4a') ? 'm4a' : 'webm');
                    const blob = new Blob(chunks, { type: recorderMime });

                    if (!blob.size) {
                        alert('Voice note is empty. Please try recording again.');
                        return;
                    }

                    const fileName = `voice-note-${Date.now()}.${ext}`;
                    this.selectedAttachment = new File([blob], fileName, { type: recorderMime });
                    await this.sendMemberMessage();
                };

                this.mediaRecorder.start();
                this.recordingAudio = true;
                this.recordingIntervalId = setInterval(() => {
                    this.recordingSeconds += 1;
                }, 1000);
            } catch (error) {
                console.error('Voice recording error:', error);
                alert('Microphone access was denied or unavailable.');
                this.cleanupRecorder();
            }
        },

        stopVoiceRecording(sendAfterStop = false) {
            if (!this.mediaRecorder || this.mediaRecorder.state === 'inactive') {
                this.cleanupRecorder();
                return;
            }

            this.sendRecordedAudioOnStop = sendAfterStop;
            this.mediaRecorder.stop();

            // Stop active media tracks immediately; recorder will still emit onstop.
            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach((track) => track.stop());
            }
            if (this.recordingIntervalId) {
                clearInterval(this.recordingIntervalId);
                this.recordingIntervalId = null;
            }
            this.recordingAudio = false;
        },

        cancelVoiceRecording() {
            this.stopVoiceRecording(false);
        },

        cleanupRecorder() {
            if (this.recordingIntervalId) {
                clearInterval(this.recordingIntervalId);
                this.recordingIntervalId = null;
            }

            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach((track) => track.stop());
                this.mediaStream = null;
            }

            this.recordingAudio = false;
            this.mediaRecorder = null;
            this.sendRecordedAudioOnStop = false;
            this.recordingChunks = [];
            this.recordingSeconds = 0;
        },

        formatRecordingTime() {
            const minutes = Math.floor(this.recordingSeconds / 60).toString().padStart(2, '0');
            const seconds = (this.recordingSeconds % 60).toString().padStart(2, '0');
            return `${minutes}:${seconds}`;
        },

        isAudioAttachment(msg) {
            const name = (msg?.attachment_name || '').toLowerCase();
            return /\.(webm|ogg|wav|mp3|m4a|aac)$/.test(name);
        },

        isImageAttachment(msg) {
            const name = (msg?.attachment_name || '').toLowerCase();
            return /\.(jpg|jpeg|png|webp|gif)$/.test(name);
        },

        openImagePreview(url, name = 'Image') {
            if (!url) return;
            this.imagePreviewUrl = url;
            this.imagePreviewName = name || 'Image';
            this.showImagePreview = true;
        },

        closeImagePreview() {
            this.showImagePreview = false;
            this.imagePreviewUrl = null;
            this.imagePreviewName = '';
        },

        openCameraCapture() {},
        handleCameraChange() {},

        sendQuickMessage(msg) {
            this.chatInput = msg;
            this.sendMessage();
        },

        getMessageStatus(status) {
            if (status === 'sent' || status === 'sending') return 'fa-check text-gray-400';
            if (status === 'delivered') return 'fa-check-double text-gray-400';
            if (status === 'read') return 'fa-check-double text-[#53bdeb]';
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
            const text = member?.last_message || '';
            if (!text) return 'Click to start chat';
            if (text.length <= 42) return text;
            return text.slice(0, 42) + '...';
        },

        getLastMessageTime(member) {
            if (!member?.timestamp) return '';
            return this.formatMessageTime(member.timestamp);
        }
    };
};

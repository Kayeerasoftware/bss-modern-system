<div x-show="showMemberChatModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" style="display: none;" x-cloak @mousedown.self="showMemberChatModal = false">
    <div class="bg-white rounded-2xl w-full max-w-4xl h-[600px] flex shadow-2xl overflow-hidden border border-gray-200">
        <div class="w-80 flex-shrink-0 flex flex-col bg-gradient-to-b from-gray-50 via-white to-gray-50 border-r border-gray-200 shadow-lg relative z-10" :class="{'hidden': selectedMemberChat && window.innerWidth < 640}">
            <div class="p-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <template x-if="profilePicture">
                            <button type="button" @click="openImagePreview(profilePicture, 'My Profile Picture')" class="block">
                                <img :src="profilePicture" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-lg cursor-zoom-in">
                            </button>
                        </template>
                        <template x-if="!profilePicture">
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center border-2 border-white shadow-lg">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                        </template>
                        <div>
                            <h3 class="text-sm font-bold">{{ auth()->user()->name }}</h3>
                            <p class="text-[10px] text-blue-100">{{ ucfirst(auth()->user()->role) }}</p>
                            <div class="flex gap-2 text-[9px] text-blue-200 mt-0.5">
                                <span><i class="fas fa-id-card"></i> {{ auth()->user()->member->member_id ?? 'N/A' }}</span>
                                <span><i class="fas fa-phone"></i> {{ auth()->user()->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="showMemberChatModal = false" class="text-white hover:bg-white/20 p-1.5 rounded-full">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <input type="text" x-model="memberChatSearch" @input="filterMembersForChat()" placeholder="Search..." class="w-full px-3 py-2 rounded-lg bg-white/20 text-white placeholder-white/70 text-xs mb-3">
                <div class="flex gap-2 flex-wrap">
                    <button @click="chatFilter = 'all'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'all' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">All</button>
                    <button @click="chatFilter = 'admin'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'admin' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">Admin</button>
                    <button @click="chatFilter = 'ceo'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'ceo' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">CEO</button>
                    <button @click="chatFilter = 'td'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'td' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">TD</button>
                    <button @click="chatFilter = 'cashier'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'cashier' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">Cashier</button>
                    <button @click="chatFilter = 'shareholder'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'shareholder' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">Shareholder</button>
                    <button @click="chatFilter = 'client'; filterMembersForChat()" class="px-3 py-1 rounded text-xs" :class="chatFilter === 'client' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'">Client</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 relative z-10">
                <div class="p-3">
                    <p class="px-3 py-2 text-[10px] font-bold text-gray-600 uppercase tracking-wider flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-users text-blue-600"></i> Contacts</span>
                        <span class="bg-blue-600 text-white px-2.5 py-1 rounded-full text-[9px] font-bold shadow-sm" x-text="filteredMembersChat.length"></span>
                    </p>
                    <template x-for="member in filteredMembersChat" :key="member.member_id">
                        <div @click.stop="selectMemberChat(member)" 
                             :class="selectedMemberChat?.member_id === member.member_id ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-600 shadow-md' : 'hover:bg-gray-50 border-l-4 border-transparent'" 
                             class="p-3 mb-2 rounded-xl cursor-pointer transition-all duration-200 relative z-20"
                             style="pointer-events: auto;">
                            <div class="flex items-center space-x-3">
                                <div class="relative flex-shrink-0">
                                    <template x-if="member.profile_picture">
                                        <button type="button" @click.stop="openImagePreview(member.profile_picture, (member.full_name || member.name || 'User') + ' Profile Picture')" class="block">
                                            <img :src="member.profile_picture" class="w-10 h-10 rounded-full object-cover shadow-lg border-2 border-white cursor-zoom-in">
                                        </button>
                                    </template>
                                    <template x-if="!member.profile_picture">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg text-xs">
                                            <span x-text="(member.full_name || member.name)?.charAt(0) || 'U'"></span>
                                        </div>
                                    </template>
                                    <div x-show="isOnline(member.member_id)" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <p class="text-xs font-semibold text-gray-800 truncate" x-text="member.full_name || member.name"></p>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[10px] text-gray-500 truncate flex items-center gap-1 flex-1" x-text="getLastMessage(member)"></p>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            <span class="text-[9px] text-gray-400" x-text="getLastMessageTime(member)"></span>
                                            <span x-show="member.unread > 0" class="bg-green-500 text-white text-[9px] min-w-[18px] h-[18px] px-1 rounded-full inline-flex items-center justify-center font-bold" x-text="member.unread"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredMembersChat.length === 0" class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-friends text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">No contacts found</p>
                        <p class="text-xs text-gray-400">Try adjusting your filters</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 flex flex-col bg-gradient-to-br from-gray-50 to-gray-100" :class="{'w-full': window.innerWidth < 640, 'hidden': !selectedMemberChat && window.innerWidth < 640}">
            <div x-show="selectedMemberChat" class="p-4 bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 flex justify-between items-center shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="selectedMemberChat = null" class="sm:hidden p-1.5 -ml-1 text-gray-600 hover:bg-gray-100 rounded-full transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </button>
                    <div class="relative">
                        <template x-if="selectedMemberChat?.profile_picture">
                            <button type="button" @click="openImagePreview(selectedMemberChat.profile_picture, (selectedMemberChat?.full_name || selectedMemberChat?.name || 'User') + ' Profile Picture')" class="block">
                                <img :src="selectedMemberChat.profile_picture" class="w-10 h-10 rounded-full object-cover shadow-lg border-2 border-white cursor-zoom-in">
                            </button>
                        </template>
                        <template x-if="!selectedMemberChat?.profile_picture">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg text-xs">
                                <span x-text="selectedMemberChat?.full_name?.charAt(0) || 'U'"></span>
                            </div>
                        </template>
                        <div x-show="isOnline(selectedMemberChat?.member_id)" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800" x-text="selectedMemberChat?.full_name || selectedMemberChat?.name || ''"></h3>
                        <p class="text-[10px] flex items-center" :class="isOnline(selectedMemberChat?.member_id) ? 'text-green-600' : 'text-gray-500'">
                            <span x-show="isOnline(selectedMemberChat?.member_id)" class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                            <span x-text="isOnline(selectedMemberChat?.member_id) ? 'Active now' : 'Offline'"></span>
                        </p>
                        <div class="mt-1 flex flex-wrap gap-1.5 text-[9px]">
                            <span class="px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700" x-text="(selectedMemberChat?.role || 'client').toUpperCase()"></span>
                            <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">
                                <i class="fas fa-id-card mr-1"></i><span x-text="selectedMemberChat?.member_id || 'N/A'"></span>
                            </span>
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 max-w-[210px] truncate">
                                <i class="fas fa-envelope mr-1"></i><span x-text="selectedMemberChat?.email || 'No email'"></span>
                            </span>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">
                                <i class="fas fa-phone mr-1"></i><span x-text="selectedMemberChat?.phone || selectedMemberChat?.contact || 'N/A'"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition disabled:opacity-60"
                            title="Refresh Chat"
                            :disabled="chatRefreshing"
                            @click="refreshChatOnly()">
                        <i class="fas fa-rotate-right text-xs" :class="chatRefreshing ? 'animate-spin' : ''"></i>
                    </button>
                    <button class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition" title="Video Call" @click="startVideoCall()">
                        <i class="fas fa-video text-xs"></i>
                    </button>
                    <button class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition" title="Voice Call" @click="startDirectCall()">
                        <i class="fas fa-phone text-xs"></i>
                    </button>
                </div>
            </div>
            <div x-show="selectedMemberChat" class="flex-1 p-3 overflow-y-auto bg-gradient-to-br from-blue-50/50 to-indigo-50/50" id="memberChatMessages">
                <div class="flex justify-center mb-4">
                    <span class="bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] text-gray-600 shadow-sm font-medium">Today</span>
                </div>
                <div class="space-y-3">
                    <template x-for="(msg, index) in memberChatMessages" :key="index">
                        <div :class="msg.sender === 'me' ? 'flex justify-end' : 'flex justify-start'" class="animate-fade-in">
                            <div class="max-w-xs">
                                <div :class="msg.sender === 'me' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm shadow-lg'" class="p-3 relative">
                                    <template x-if="msg.attachment_url && isImageAttachment(msg)">
                                        <button type="button" @click="openImagePreview(msg.attachment_url, msg.attachment_name || 'Image')" class="block mb-2">
                                            <img :src="msg.attachment_url" alt="attachment" class="max-w-[220px] max-h-[220px] rounded-lg object-cover cursor-zoom-in">
                                        </button>
                                    </template>
                                    <template x-if="msg.attachment_url && isAudioAttachment(msg)">
                                        <div class="block mb-2 p-2 rounded-lg bg-black/10">
                                            <audio controls :src="msg.attachment_url" class="w-[220px] max-w-full"></audio>
                                        </div>
                                    </template>
                                    <template x-if="msg.attachment_url && !isAudioAttachment(msg) && !isImageAttachment(msg)">
                                        <a :href="msg.attachment_url || '#'" target="_blank" class="block mb-2 p-2 rounded-lg text-xs bg-black/10 hover:bg-black/20 transition">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            <span x-text="msg.attachment_name || 'Attachment'"></span>
                                        </a>
                                    </template>
                                    <p x-show="msg.text" class="text-xs leading-relaxed" x-text="msg.text"></p>
                                    <div class="flex items-center justify-end space-x-1 mt-1">
                                        <span class="text-[9px] opacity-70" x-text="msg.time"></span>
                                        <template x-if="msg.sender === 'me' && msg.status">
                                            <i class="fas text-[9px]" :class="getMessageStatus(msg.status)"></i>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="isTyping" class="flex justify-start animate-fade-in">
                        <div class="bg-white rounded-2xl rounded-tl-sm shadow-lg p-3">
                            <div class="flex space-x-1.5">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="!selectedMemberChat" class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-gray-50 p-6">
                <div class="text-center max-w-sm">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-comments text-4xl text-blue-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">BSS Investment Chat</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Select a member from the list to start a conversation</p>
                </div>
            </div>
            <div x-show="selectedMemberChat" class="p-4 bg-white border-t border-gray-200">
                <div x-show="selectedAttachment" class="mb-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-xs text-blue-700">
                    <i class="fas fa-paperclip"></i>
                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800" x-text="attachmentFilterLabel"></span>
                    <span class="max-w-[220px] truncate" x-text="selectedAttachment?.name"></span>
                    <button @click="clearAttachment()" class="text-blue-700 hover:text-blue-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div x-show="recordingAudio" class="mb-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs text-red-700">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span>Recording</span>
                    <span x-text="formatRecordingTime()"></span>
                    <button @click="cancelVoiceRecording()" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-stop"></i>
                    </button>
                </div>

                <div x-show="showEmojiPicker" class="mb-2 p-2 rounded-xl border border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-8 gap-1">
                        <template x-for="emoji in emojiList" :key="emoji">
                            <button @click="insertEmoji(emoji)" class="p-1.5 text-base rounded hover:bg-white" x-text="emoji"></button>
                        </template>
                    </div>
                </div>

                <input type="file" x-ref="chatAttachmentInput" :accept="attachmentAccept" @change="handleAttachmentChange($event)" class="hidden">
                <input type="file" x-ref="chatCameraPhotoInput" accept="image/*" capture="environment" @change="handleCameraPhotoChange($event)" class="hidden">
                <input type="file" x-ref="chatCameraVideoInput" accept="video/*" capture="environment" @change="handleCameraVideoChange($event)" class="hidden">

                <div class="flex items-center gap-2">
                    <div class="flex-1 flex items-end gap-2 bg-gray-100 border border-gray-300 rounded-2xl px-3 py-2">
                        <button @click="showEmojiPicker = !showEmojiPicker" class="text-gray-500 hover:text-gray-700">
                            <i class="far fa-face-smile text-lg"></i>
                        </button>
                        <div class="relative" @click.away="showAttachmentMenu = false" @click.stop>
                            <button @click.stop.prevent="toggleAttachmentMenu()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-paperclip text-lg"></i>
                            </button>
                            <div x-show="showAttachmentMenu" @click.stop x-transition class="absolute bottom-10 left-0 w-56 bg-white border border-gray-200 rounded-xl shadow-xl p-2 z-30">
                                <p class="text-[10px] uppercase tracking-wide text-gray-500 px-2 py-1">Attach As</p>
                                <button @click="selectAttachmentType('image/*,.jpg,.jpeg,.png,.webp,.gif', 'Image')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Image (.jpg, .png, .webp)</button>
                                <button @click="selectAttachmentType('video/*,.mp4,.webm,.mov,.avi,.mkv', 'Video')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Video (.mp4, .webm, .mov)</button>
                                <button @click="selectAttachmentType('audio/*,.mp3,.wav,.ogg,.m4a,.aac,.webm', 'Audio')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Audio (.mp3, .wav, .m4a)</button>
                                <button @click="selectAttachmentType('.pdf', 'PDF')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">PDF (.pdf)</button>
                                <button @click="selectAttachmentType('.doc,.docx', 'Word')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Word (.doc, .docx)</button>
                                <button @click="selectAttachmentType('.xls,.xlsx,.csv', 'Spreadsheet')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Spreadsheet (.xls, .xlsx, .csv)</button>
                                <button @click="selectAttachmentType('.ppt,.pptx', 'Presentation')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Presentation (.ppt, .pptx)</button>
                                <button @click="selectAttachmentType('.txt,.rtf', 'Text')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Text (.txt, .rtf)</button>
                                <button @click="selectAttachmentType('.zip,.rar,.7z,.tar,.gz', 'Archive')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Archive (.zip, .rar, .7z)</button>
                                <button @click="selectAttachmentType('*/*', 'Any File')" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">Any File (all extensions)</button>
                            </div>
                        </div>
                        <div class="relative" @click.away="showCameraMenu = false" @click.stop>
                            <button @click.stop.prevent="toggleCameraMenu()" class="text-gray-500 hover:text-gray-700" title="Camera">
                                <i class="fas fa-camera text-lg"></i>
                            </button>
                            <div x-show="showCameraMenu" @click.stop x-transition class="absolute bottom-10 left-0 w-44 bg-white border border-gray-200 rounded-xl shadow-xl p-2 z-30">
                                <p class="text-[10px] uppercase tracking-wide text-gray-500 px-2 py-1">Camera</p>
                                <button @click="openCameraPhotoCapture()" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">
                                    <i class="fas fa-image mr-1"></i>Take Photo
                                </button>
                                <button @click="openCameraVideoCapture()" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100">
                                    <i class="fas fa-video mr-1"></i>Record Video
                                </button>
                            </div>
                        </div>
                        <textarea x-ref="messageInput"
                                  x-model="memberChatInput"
                                  @input="autoResizeMessageInput($event)"
                                  @keydown.enter.prevent="handleComposerEnter($event)"
                                  rows="1"
                                  placeholder="Type a message"
                                  class="flex-1 bg-transparent text-sm focus:outline-none resize-none overflow-y-auto max-h-32 min-h-[24px] leading-6 py-0.5"></textarea>
                    </div>
                    <button @click="canSendMemberMessage() ? sendMemberMessage() : startVoiceRecording()"
                            class="w-11 h-11 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center transition-all">
                        <i class="fas" :class="recordingAudio ? 'fa-stop' : (canSendMemberMessage() ? 'fa-paper-plane' : 'fa-microphone')"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showImagePreview" x-transition.opacity class="fixed inset-0 z-[70] bg-black/85 flex items-center justify-center p-4" x-cloak @click.self="closeImagePreview()">
        <button @click="closeImagePreview()" class="absolute top-4 right-4 text-white/90 hover:text-white text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="max-w-full max-h-full overflow-auto rounded-lg">
            <img :src="imagePreviewUrl" :alt="imagePreviewName" class="w-auto h-auto max-w-none max-h-none">
        </div>
    </div>
</div>

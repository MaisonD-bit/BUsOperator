<?php $__env->startSection('title', 'Chat'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-4">
    <div class="row">
        <!-- Channels Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm" style="padding: 0px;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-weight: bold;">Channels</h5>
                    <button class="btn btn-sm btn-light" id="createChannelBtn">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="channels-list" style="height: calc(100vh - 200px); overflow-y: auto; padding: 0px;">
                    <div class="text-center p-3 text-muted">
                        <i class="fa-regular fa-comment-dots"></i>
                        <p class="mt-2" >Loading channels...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-9">
            <div class="card shadow-sm" style="height: calc(100vh - 120px); padding: 0px;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" id="channel-name" style="font-weight: bold;">Select a channel to start chatting</h5>
                    <small class="text-muted" id="channel-members"></small>
                </div>
                <div class="card-body overflow-auto" id="messages-container" style="flex: 1; max-height: calc(100vh - 280px);">
                    <div class="text-center text-muted mt-5">
                        <p class="mt-3">No channel selected</p>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <form id="message-form" class="d-flex gap-2">
                        <input 
                            type="text" 
                            id="message-input" 
                            class="form-control" 
                            placeholder="Type a message..."
                            disabled
                        >
                        <button type="submit" class="btn btn-primary" disabled id="send-btn">
                            <i class="bi bi-send"></i> Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Channel Modal -->
<div class="modal fade" id="createChannelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Channel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="create-channel-form">
                    <div class="mb-3">
                        <label class="form-label">Channel Name</label>
                        <input type="text" class="form-control" id="channel-name-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Members</label>
                        <select class="form-select" id="members-select" multiple size="5" required>
                            <option value="">Loading users...</option>
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="create-channel-submit">Create</button>
            </div>
        </div>
    </div>
</div>

<style>
.message-item {
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
}

.message-item.own {
    flex-direction: row-reverse;
}

.message-bubble {
    max-width: 70%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    position: relative;
}

.message-item.own .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 0.25rem;
}

.message-item:not(.own) .message-bubble {
    background: #f1f3f5;
    color: #333;
    border-bottom-left-radius: 0.25rem;
}

.message-author {
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.message-text {
    margin: 0;
    word-wrap: break-word;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-top: 0.25rem;
}


.channel-item {
    padding: 0;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s;
}

.channel-item > div {
    padding: 1rem;
}

.channel-item:hover {
    background: #f8f9fa;
}

.channel-item.active {
    background: #e7f3ff;
    border-left: 3px solid #007bff;
}

.channel-item .channel-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.channel-item .channel-last-message {
    font-size: 0.85rem;
    color: #6c757d;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.delete-channel-btn {
    opacity: 0;
    transition: opacity 0.2s;
}

.channel-item:hover .delete-channel-btn {
    opacity: 1;
}

.channel-content {
    cursor: pointer;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/stream-chat@8"></script>
<script>
    const { StreamChat } = window;
    const apiKey = '<?php echo e($streamApiKey); ?>';
    const userId = '<?php echo e($userId); ?>';
    const userToken = '<?php echo e($streamToken); ?>';
    const userName = '<?php echo e($userName); ?>';

    let chatClient;
    let currentChannel;
    let channels = [];

    // Initialize chat
    async function initChat() {
        try {
            chatClient = StreamChat.getInstance(apiKey);
            
            await chatClient.connectUser(
                {
                    id: userId,
                    name: userName,
                },
                userToken
            );

            console.log('Connected to Stream Chat');
            
            // Load channels
            await loadChannels();
            
            // Load users for create channel modal
            await loadUsers();

        } catch (error) {
            console.error('Error initializing chat:', error);
            alert('Failed to connect to chat service');
        }
    }

    // Load channels
    async function loadChannels() {
        try {
            const filter = { 
                type: 'messaging', 
                members: { $in: [userId] } 
            };
            const sort = [{ last_message_at: -1 }];
            
            channels = await chatClient.queryChannels(filter, sort, { 
                watch: true, 
                state: true 
            });
            
            const channelsList = document.getElementById('channels-list');
            
            if (channels.length === 0) {
                channelsList.innerHTML = `
                    <div class="text-center p-3 text-muted">
                        <i class="fa-solid fa-comment-medical" style="font-size: 3rem;"></i>
                        <p class="mt-2">No channels yet. Create one to start chatting!</p>
                    </div>
                `;
                return;
            }
            
            channelsList.innerHTML = '';
            
            channels.forEach(channel => {
                const channelDiv = document.createElement('div');
                channelDiv.className = 'channel-item';
                channelDiv.dataset.channelId = channel.id;
                
                const lastMessage = channel.state.messages[channel.state.messages.length - 1];
                const lastMessageText = lastMessage ? lastMessage.text : 'No messages yet';
                
                channelDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="channel-content">
                            <div class="channel-name">${channel.data.name || 'Unnamed Channel'}</div>
                            <div class="channel-last-message">${lastMessageText}</div>
                        </div>
                        <button class="btn btn-sm btn-danger delete-channel-btn" data-channel-id="${channel.id}" 
                                style="padding: 0.25rem 0.5rem;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;
                
                // Add click event for channel content only
                channelDiv.querySelector('.channel-content').onclick = () => loadChannel(channel);
                
                // Add click event for delete button
                channelDiv.querySelector('.delete-channel-btn').onclick = (e) => {
                    e.stopPropagation();
                    deleteChannel(channel.id);
                };
                
                channelsList.appendChild(channelDiv);
            });
            
        } catch (error) {
            console.error('Error loading channels:', error);
        }
    }


    // Load channel and messages
    async function loadChannel(channel) {
        try {
            currentChannel = channel;
            
            // Remove active class from all channels
            document.querySelectorAll('.channel-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to selected channel
            document.querySelector(`[data-channel-id="${channel.id}"]`)?.classList.add('active');
            
            // Update header
            document.getElementById('channel-name').textContent = channel.data.name || 'Chat';
            
            const memberCount = Object.keys(channel.state.members).length;
            document.getElementById('channel-members').textContent = `${memberCount} members`;
            
            // Enable input
            document.getElementById('message-input').disabled = false;
            document.getElementById('send-btn').disabled = false;
            
            // Load messages
            const state = await channel.watch();
            displayMessages(state.messages);
            
            // Listen for new messages
            channel.off('message.new'); // Remove previous listeners
            channel.on('message.new', event => {
                appendMessage(event.message);
            });
            
        } catch (error) {
            console.error('Error loading channel:', error);
        }
    }

    // Display messages
    function displayMessages(messages) {
        const container = document.getElementById('messages-container');
        container.innerHTML = '';
        messages.forEach(msg => appendMessage(msg));
        scrollToBottom();
    }

    // Append single message
    function appendMessage(message) {
        const container = document.getElementById('messages-container');
        const div = document.createElement('div');
        const isOwn = message.user.id === userId;
        
        div.className = `message-item ${isOwn ? 'own' : ''}`;
        
        div.innerHTML = `
            <div class="message-bubble">
                ${!isOwn ? `<div class="message-author">${message.user.name}</div>` : ''}
                <div class="message-text">${escapeHtml(message.text)}</div>
                <div class="message-time">${formatTime(message.created_at)}</div>
            </div>
        `;
        
        container.appendChild(div);
        scrollToBottom();
    }

    // Send message
    document.getElementById('message-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('message-input');
        
        if (input.value.trim() && currentChannel) {
            try {
                await currentChannel.sendMessage({ text: input.value.trim() });
                input.value = '';
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message');
            }
        }
    });

    // Load users for channel creation
    async function loadUsers() {
        try {
            const response = await fetch('/chat/users');
            const users = await response.json();
            
            const select = document.getElementById('members-select');
            select.innerHTML = '';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.role})`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    // Create channel modal
    document.getElementById('createChannelBtn').addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('createChannelModal'));
        modal.show();
    });

    // Create channel submit
    document.getElementById('create-channel-submit').addEventListener('click', async () => {
        const channelName = document.getElementById('channel-name-input').value;
        const select = document.getElementById('members-select');
        const selectedMembers = Array.from(select.selectedOptions).map(opt => opt.value);
        
        if (!channelName || selectedMembers.length === 0) {
            alert('Please enter a channel name and select at least one member');
            return;
        }
        
        try {
            // First, register selected users in Stream via backend
            const registerResponse = await fetch('/chat/register-users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    user_ids: selectedMembers
                })
            });

            if (!registerResponse.ok) {
                throw new Error('Failed to register users');
            }
            
            // Add current user to members
            const members = [userId, ...selectedMembers];
            
            // Create channel with unique ID
            const channelId = 'channel-' + Date.now();
            const channel = chatClient.channel('messaging', channelId, {
                name: channelName,
            });
            
            // Create channel and add members
            await channel.create();
            await channel.addMembers(members);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createChannelModal'));
            modal.hide();
            
            // Clear form
            document.getElementById('channel-name-input').value = '';
            document.getElementById('members-select').selectedIndex = -1;
            
            // Reload channels
            await loadChannels();
            
            // Open the new channel
            await loadChannel(channel);
            
        } catch (error) {
            console.error('Error creating channel:', error);
            alert('Failed to create channel');
        }
    });

    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Just now';
        if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
        if (diff < 86400000) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    }

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }

    
    // Updates channels list to include delete buttons
    channels.forEach(channel => {
        const channelDiv = document.createElement('div');
        channelDiv.className = 'channel-item';
        channelDiv.dataset.channelId = channel.id;
        
        const lastMessage = channel.state.messages[channel.state.messages.length - 1];
        const lastMessageText = lastMessage ? lastMessage.text : 'No messages yet';
        
        channelDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1 channel-content">
                    <div class="channel-name">${channel.data.name || 'Unnamed Channel'}</div>
                    <div class="channel-last-message">${lastMessageText}</div>
                </div>
                <button class="btn btn-sm btn-danger delete-channel-btn" data-channel-id="${channel.id}" 
                        style="padding: 0.25rem 0.5rem;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        // Add click event for channel content only
        channelDiv.querySelector('.channel-content').onclick = () => loadChannel(channel);
        
        // Add click event for delete button
        channelDiv.querySelector('.delete-channel-btn').onclick = (e) => {
            e.stopPropagation();
            deleteChannel(channel.id);
        };
        
        channelsList.appendChild(channelDiv);
    });


    // Delete channel
    async function deleteChannel(channelId) {
        if (!confirm('Are you sure you want to delete this channel? This action cannot be undone.')) {
            return;
        }
        
        try {
            const channel = channels.find(c => c.id === channelId);
            
            if (!channel) {
                throw new Error('Channel not found');
            }
            
            // Delete the channel
            await channel.delete();
            
            // If this was the current channel, clear the chat area
            if (currentChannel && currentChannel.id === channelId) {
                currentChannel = null;
                document.getElementById('channel-name').textContent = 'Select a channel to start chatting';
                document.getElementById('channel-members').textContent = '';
                document.getElementById('messages-container').innerHTML = `
                    <div class="text-center text-muted mt-5">
                        <i class="bi bi-chat-left-text fs-1"></i>
                        <p class="mt-3">Select a channel to view messages</p>
                    </div>
                `;
                document.getElementById('message-input').disabled = true;
                document.getElementById('send-btn').disabled = true;
            }
            
            // Reload channels list
            await loadChannels();
            
            alert('Channel deleted successfully');
            
        } catch (error) {
            console.error('Error deleting channel:', error);
            alert('Failed to delete channel: ' + error.message);
        }
    }

    // Initialize on page load
    initChat();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.apptwo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kylecb\Desktop\BUsOperator\resources\views/panels/chat.blade.php ENDPATH**/ ?>
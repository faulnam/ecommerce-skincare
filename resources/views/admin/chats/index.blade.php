@extends('layouts.admin')

@section('page-title', 'Live Chat')

@section('content')
<style>
    .chat-layout {
        display: flex;
        height: calc(100vh - 120px);
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .chat-sidebar {
        width: 300px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f9fafb;
    }
    .chat-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #f3f4f6;
        font-weight: 600;
    }
    .chat-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.2s;
    }
    .chat-item:hover, .chat-item.active {
        background: #f3f4f6;
    }
    .chat-item .name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }
    .chat-item .meta {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-item .status {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status.waiting { background: #f59e0b; }
    .status.active { background: #10b981; }

    .chat-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .msg {
        display: flex;
        flex-direction: column;
        max-width: 75%;
    }
    .msg.bot, .msg.user {
        align-self: flex-start;
        align-items: flex-start;
    }
    .msg.admin {
        align-self: flex-end;
        align-items: flex-end;
    }
    .msg-bubble {
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }
    .msg.bot .msg-bubble {
        background: #e5e7eb;
        border-bottom-left-radius: 4px;
    }
    .msg.user .msg-bubble {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: 4px;
    }
    .msg.admin .msg-bubble {
        background: #3b82f6;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .msg-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .chat-input-area {
        padding: 16px;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
    }
    .chat-input-area input {
        flex: 1;
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 20px;
        outline: none;
    }
    .chat-input-area button {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 500;
    }
    .chat-input-area button:hover {
        background: #2563eb;
    }
</style>

<div class="chat-layout">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            Daftar Chat
        </div>
        <div class="chat-list" id="chat-list">
            <!-- Render via JS -->
        </div>
    </div>
    <div class="chat-main">
        <div class="chat-header" id="chat-header" style="display: none;">
            <div>
                <h3 class="font-bold">Customer <span id="current-chat-status"></span></h3>
            </div>
            <button onclick="closeCurrentChat()" class="text-red-500 hover:text-red-700 text-sm border border-red-500 px-3 py-1 rounded">Tutup Sesi</button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="flex items-center justify-center h-full text-gray-400">
                Pilih chat untuk mulai membalas
            </div>
        </div>
        <div class="chat-input-area" id="chat-input-area" style="display: none;">
            <input type="text" id="admin-message-input" placeholder="Ketik balasan Anda..." onkeypress="if(event.key === 'Enter') sendAdminMessage()">
            <button onclick="sendAdminMessage()">Kirim</button>
        </div>
    </div>
</div>

<script>
    let currentChatId = null;
    let lastMessageId = 0;
    let chatListInterval = null;
    let messageInterval = null;

    function loadChatList() {
        fetch('{{ route("admin.chats.list") }}')
            .then(res => res.json())
            .then(chats => {
                const list = document.getElementById('chat-list');
                list.innerHTML = '';
                if(chats.length === 0) {
                    list.innerHTML = '<div class="p-4 text-gray-500 text-sm text-center">Tidak ada chat aktif.</div>';
                    return;
                }
                chats.forEach(chat => {
                    const isActive = chat.id === currentChatId ? 'active' : '';
                    const lastMsg = chat.messages.length > 0 ? chat.messages[0].message : 'Sesi baru';
                    const div = document.createElement('div');
                    div.className = `chat-item ${isActive}`;
                    div.onclick = () => openChat(chat.id);
                    div.innerHTML = `
                        <div class="name"><span class="status ${chat.status}"></span> Guest #${chat.id}</div>
                        <div class="meta">${lastMsg}</div>
                    `;
                    list.appendChild(div);
                });
            });
    }

    function openChat(id) {
        currentChatId = id;
        lastMessageId = 0;
        document.getElementById('chat-header').style.display = 'flex';
        document.getElementById('chat-input-area').style.display = 'flex';
        document.getElementById('chat-messages').innerHTML = '';
        
        loadChatList(); // Refresh list to set active class
        fetchMessages(true);
        
        if(messageInterval) clearInterval(messageInterval);
        messageInterval = setInterval(() => fetchMessages(false), 2000);
    }

    function fetchMessages(scrollDown = false) {
        if(!currentChatId) return;
        fetch(`{{ url('admin/chats') }}/${currentChatId}/messages?last_id=${lastMessageId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('chat-messages');
                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;
                        const div = document.createElement('div');
                        div.className = `msg ${msg.sender_type}`;
                        div.innerHTML = `
                            <div class="msg-bubble">${msg.message}</div>
                            <div class="msg-time">${new Date(msg.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</div>
                        `;
                        container.appendChild(div);
                        scrollDown = true;
                    }
                });
                if(scrollDown) container.scrollTop = container.scrollHeight;
                
                if (data.status === 'closed') {
                    document.getElementById('chat-input-area').style.display = 'none';
                    if (messageInterval) clearInterval(messageInterval);
                }
            });
    }

    function sendAdminMessage() {
        if(!currentChatId) return;
        const input = document.getElementById('admin-message-input');
        const text = input.value.trim();
        if(!text) return;
        
        input.value = '';
        
        fetch(`{{ url('admin/chats') }}/${currentChatId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        }).then(() => fetchMessages(true));
    }

    function closeCurrentChat() {
        if(!currentChatId) return;
        if(confirm('Yakin ingin menutup sesi ini?')) {
            fetch(`{{ url('admin/chats') }}/${currentChatId}/close`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                currentChatId = null;
                document.getElementById('chat-header').style.display = 'none';
                document.getElementById('chat-input-area').style.display = 'none';
                document.getElementById('chat-messages').innerHTML = '<div class="flex items-center justify-center h-full text-gray-400">Pilih chat untuk mulai membalas</div>';
                if(messageInterval) clearInterval(messageInterval);
                loadChatList();
            });
        }
    }

    // Start polling for chat list
    loadChatList();
    chatListInterval = setInterval(loadChatList, 5000);
</script>
@endsection

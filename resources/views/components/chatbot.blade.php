{{-- resources/views/components/chatbot.blade.php --}}
{{-- Include di layout utama: @include('components.chatbot') --}}

<style>
    #hijab-chat-widget * {
        box-sizing: border-box;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Floating Button */
    #hijab-chat-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #374151;
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(55, 65, 81, 0.3);
        z-index: 9999;
        transition: all 0.3s ease;
    }

    #hijab-chat-btn:hover {
        background: #1f2937;
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(55, 65, 81, 0.4);
    }

    #hijab-chat-btn svg { transition: transform 0.3s ease; }
    #hijab-chat-btn.open svg { transform: rotate(90deg); }

    /* Notif dot */
    #hijab-chat-btn::after {
        content: '';
        position: absolute;
        top: 4px; right: 4px;
        width: 8px; height: 8px;
        background: #f97316;
        border-radius: 50%;
        border: 2px solid white;
        animation: pulse-dot 2s infinite;
    }
    #hijab-chat-btn.open::after { display: none; }

    @keyframes pulse-dot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
    }

    /* Chat Window */
    #hijab-chat-window {
        position: fixed;
        bottom: 92px;
        right: 24px;
        width: 380px;
        max-height: 540px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        z-index: 9998;
        overflow: hidden;
        transform: scale(0.9) translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
        transform-origin: bottom right;
        border: 1px solid rgba(0,0,0,0.06);
    }

    #hijab-chat-window.open {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    /* Header */
    .hijab-chat-header {
        background: #374151;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .hijab-avatar {
        width: 42px; height: 42px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .hijab-chat-header h4 {
        margin: 0; font-size: 16px; font-weight: 600;
        letter-spacing: -0.3px;
    }

    .hijab-chat-header p {
        margin: 3px 0 0; font-size: 12px; opacity: 0.9;
        font-weight: 400;
    }

    .hijab-online-dot {
        width: 7px; height: 7px;
        background: #86efac;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: pulse-dot 2s infinite;
    }

    /* Messages area */
    #hijab-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #fafafa;
        scroll-behavior: smooth;
        min-height: 0;
    }

    #hijab-messages::-webkit-scrollbar { width: 5px; }
    #hijab-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 5px; }
    #hijab-messages::-webkit-scrollbar-track { background: transparent; }

    .hijab-msg {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.6;
        animation: msg-in 0.3s cubic-bezier(0.34,1.56,0.64,1);
        font-weight: 400;
    }

    @keyframes msg-in {
        from { opacity: 0; transform: translateY(8px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .hijab-msg.bot {
        background: white;
        color: #374151;
        border-bottom-left-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        align-self: flex-start;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .hijab-msg.bot strong { color: #111827; font-weight: 600; }
    .hijab-msg.bot em { font-style: italic; color: #4b5563; }
    .hijab-msg.bot ul { margin: 6px 0; padding-left: 18px; }
    .hijab-msg.bot li { margin-bottom: 4px; line-height: 1.5; }

    .hijab-msg.admin-reply {
        background: #e0f2fe;
        color: #0c4a6e;
        border-bottom-left-radius: 6px;
        align-self: flex-start;
        border: 1px solid #bae6fd;
    }

    .hijab-msg.user {
        background: #374151;
        color: white;
        border-bottom-right-radius: 6px;
        align-self: flex-end;
        box-shadow: 0 2px 8px rgba(55, 65, 81, 0.25);
    }

    /* Product cards inside bot message */
    .hijab-product-cards {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
        max-height: 380px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .hijab-product-cards::-webkit-scrollbar { width: 4px; }
    .hijab-product-cards::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    .hijab-product-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .hijab-product-card:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .hijab-product-card img {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
        background: #e5e7eb;
    }

    .hijab-product-card .info {
        flex: 1;
        min-width: 0;
    }

    .hijab-product-card .info .name {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hijab-product-card .info .meta {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }

    .hijab-product-card .info .price {
        font-size: 13px;
        font-weight: 700;
        color: #059669;
        margin-top: 3px;
    }

    .hijab-product-card .info .price .discount {
        font-size: 10px;
        color: #dc2626;
        margin-left: 4px;
        font-weight: 600;
    }

    .hijab-product-card .arrow {
        color: #9ca3af;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* Typing indicator */
    .hijab-typing {
        display: flex; gap: 5px; align-items: center;
        padding: 14px 18px;
        background: white;
        border-radius: 16px;
        border-bottom-left-radius: 6px;
        align-self: flex-start;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
    }

    .hijab-typing span {
        width: 6px; height: 6px;
        background: #9ca3af;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .hijab-typing span:nth-child(2) { animation-delay: 0.2s; }
    .hijab-typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30% { transform: translateY(-6px); opacity: 1; }
    }

    /* Quick replies */
    .hijab-quick-replies {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 12px 20px 12px;
        background: #fafafa;
        border-top: 1px solid rgba(0,0,0,0.04);
        flex-shrink: 0;
    }

    .hijab-quick-btn {
        background: white;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 12px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all 0.2s ease;
        font-weight: 500;
        text-align: center;
        line-height: 1.4;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hijab-quick-btn:hover {
        background: #374151;
        color: white;
        border-color: #374151;
        transform: translateY(-1px);
    }

    /* Input area */
    .hijab-chat-input {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding: 14px 16px 4px;
        border-top: 1px solid #e5e7eb;
        background: white;
        gap: 10px;
        position: relative;
    }
    .hijab-char-count {
        width: 100%;
        text-align: right;
        font-size: 10px;
        color: #9ca3af;
        padding: 0 4px;
        margin-top: -4px;
        line-height: 1;
    }

    #hijab-input {
        flex: 1;
        border: 1.5px solid #e5e7eb;
        border-radius: 24px;
        padding: 11px 16px;
        font-size: 14px;
        outline: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #374151;
        transition: all 0.2s ease;
        resize: none;
        background: #fafafa;
    }

    #hijab-input:focus {
        border-color: #374151;
        background: white;
        box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
    }

    #hijab-send-btn {
        width: 42px; height: 42px;
        background: #374151;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    #hijab-send-btn:hover {
        background: #4b5563;
        transform: scale(1.08);
    }
    #hijab-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        background: #d1d5db;
    }

    @media (max-width: 768px) {
        #hijab-chat-window {
            width: calc(100vw - 24px);
            right: 12px;
            bottom: calc(76px + env(safe-area-inset-bottom) + 64px);
            max-height: calc(100vh - 160px - env(safe-area-inset-bottom));
        }
        #hijab-chat-btn { 
            right: 12px; 
            bottom: calc(76px + env(safe-area-inset-bottom)); 
        }
    }

    /* Dark mode support */
    [data-theme="dark"] #hijab-chat-window {
        background: #27272a;
        border-color: #3f3f46;
    }

    [data-theme="dark"] #hijab-messages {
        background: #18181b;
    }

    [data-theme="dark"] .hijab-msg.bot {
        background: #3f3f46;
        color: #fafafa;
        border-color: #52525b;
    }

    [data-theme="dark"] .hijab-msg.bot strong { color: #ffffff; }
    [data-theme="dark"] .hijab-msg.bot em { color: #d4d4d8; }
    [data-theme="dark"] .hijab-msg.bot ul { color: #fafafa; }

    [data-theme="dark"] .hijab-msg.admin-reply {
        background: #1e3a8a;
        color: #eff6ff;
        border-color: #1e40af;
    }

    [data-theme="dark"] .hijab-product-card {
        background: #3f3f46;
        border-color: #52525b;
    }
    [data-theme="dark"] .hijab-product-card:hover {
        background: #52525b;
        border-color: #6b7280;
    }
    [data-theme="dark"] .hijab-product-card .info .name { color: #fafafa; }
    [data-theme="dark"] .hijab-product-card .info .meta { color: #a1a1aa; }
    [data-theme="dark"] .hijab-product-card .info .price { color: #34d399; }
    [data-theme="dark"] .hijab-product-card .arrow { color: #a1a1aa; }
    [data-theme="dark"] .hijab-product-cards::-webkit-scrollbar-thumb { background: #52525b; }

    [data-theme="dark"] .hijab-quick-replies {
        background: #18181b;
        border-top-color: #3f3f46;
    }

    [data-theme="dark"] .hijab-quick-btn {
        background: #3f3f46;
        color: #fafafa;
        border-color: #52525b;
    }

    [data-theme="dark"] .hijab-quick-btn:hover {
        background: #6b7280;
        border-color: #6b7280;
    }

    [data-theme="dark"] .hijab-chat-input {
        background: #27272a;
        border-color: #3f3f46;
    }

    [data-theme="dark"] #hijab-input {
        background: #3f3f46;
        color: #fafafa;
        border-color: #52525b;
    }

    [data-theme="dark"] #hijab-input:focus {
        border-color: #6b7280;
        background: #27272a;
    }

    [data-theme="dark"] .hijab-typing {
        background: #3f3f46;
        border-color: #52525b;
    }
    [data-theme="dark"] .hijab-char-count {
        color: #71717a;
    }
</style>

<div id="hijab-chat-widget">
    {{-- Floating button --}}
    <button id="hijab-chat-btn" onclick="hijabChatToggle()" aria-label="Buka chat">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </button>

    {{-- Chat window --}}
    <div id="hijab-chat-window">
        {{-- Header --}}
        <div class="hijab-chat-header">
            <div class="hijab-avatar flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-white">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
            <div>
                <h4>Customer Service</h4>
                <p><span class="hijab-online-dot"></span>Online</p>
            </div>
        </div>

        {{-- Messages --}}
        <div id="hijab-messages">
            <div class="hijab-msg bot">
                Halo! 👋 Selamat datang di <strong>Hijab</strong>!<br>
                Saya Hijab, siap bantu kamu temukan perlengkapan hijab yang paling cocok. Mau belanja atau cuma tanya-tanya dulu, silakan ya! 🏓<br><br>
                <em>💡 Ketik <strong>"admin"</strong> kapan saja untuk terhubung dengan tim Customer Service kami.</em>
            </div>
        </div>

        {{-- Quick replies --}}
        <div class="hijab-quick-replies" id="hijab-quick">
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Saya baru mau mulai main hijab, hijab apa yang cocok?')">Hijab untuk pemula</button>
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Rekomendasi hijab intermediate yang bagus')">Hijab intermediate</button>
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Ada sepatu hijab yang recommended?')">Sepatu hijab</button>
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Aksesori hijab apa saja yang perlu dibeli?')">Aksesori wajib</button>
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Cara order dan pembayaran gimana?')">Cara order & bayar</button>
            <button class="hijab-quick-btn" onclick="hijabSendQuick('Ada promo atau voucher hari ini?')">Promo & voucher</button>
        </div>

        {{-- Input --}}
        <div class="hijab-chat-input">
            <input
                type="text"
                id="hijab-input"
                placeholder="Ketik pesan..."
                onkeydown="if(event.key==='Enter') hijabSendMessage()"
                maxlength="500"
            />
            <button id="hijab-send-btn" onclick="hijabSendMessage()" aria-label="Kirim">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
            <span id="hijab-char-count" class="hijab-char-count">0/500</span>
        </div>
    </div>
</div>

<script>
    let hijabChatOpen   = false;
    let hijabHistory    = [];
    let hijabIsLoading  = false;
    let hijabLastSent   = 0;
    
    // Live chat states
    let isLiveChat = false;
    let liveChatSessionId = null;
    let lastLiveChatId = 0;
    let liveChatInterval = null;

    function hijabChatToggle() {
        hijabChatOpen = !hijabChatOpen;
        document.getElementById('hijab-chat-window').classList.toggle('open', hijabChatOpen);
        document.getElementById('hijab-chat-btn').classList.toggle('open', hijabChatOpen);
    }

    function hijabFormatText(text) {
        if (!text) return '';

        // Escape HTML dulu supaya aman
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Bold: **text** -> <strong>text</strong>
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

        // Italic: *text* -> <em>text</em> (hanya yang tersisa, bukan **)
        html = html.replace(/(^|\s|[\(])\*(.+?)\*(?![*\w])/g, '$1<em>$2</em>');

        // List dengan tanda strip (-) atau bintang (*) di awal baris -> jadi bullet list rapi
        const lines = html.split('\n');
        let inList = false;
        let resultLines = [];
        for (const line of lines) {
            const trimmed = line.trim();
            // Cek apakah baris ini adalah list item (diawali - atau * atau 1. )
            const listMatch = trimmed.match(/^(?:[-*•]|\d+\.)\s+(.+)$/);
            if (listMatch) {
                if (!inList) {
                    resultLines.push('<ul style="margin:4px 0;padding-left:16px;list-style:disc;">');
                    inList = true;
                }
                resultLines.push('<li style="margin-bottom:2px;">' + listMatch[1] + '</li>');
            } else {
                if (inList) {
                    resultLines.push('</ul>');
                    inList = false;
                }
                resultLines.push(line);
            }
        }
        if (inList) resultLines.push('</ul>');
        html = resultLines.join('\n');

        // Bersihkan tanda * yang tersisa (single atau double asterisk yang tidak terpasang)
        html = html.replace(/\*{1,2}/g, '');

        // Ganti newline jadi <br> (kecuali yang sudah di dalam tag <ul>)
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    function hijabAppendMsg(text, role, products) {
        const container = document.getElementById('hijab-messages');
        const div = document.createElement('div');
        div.className = `hijab-msg ${role}`;

        // Hapus baris REKOMENDASI: dari tampilan
        const cleanText = text.replace(/REKOMENDASI:.*/gi, '').trim();
        div.innerHTML = hijabFormatText(cleanText);

        // Jika ada produk rekomendasi, tambahkan cards
        if (products && products.length > 0) {
            const cardsDiv = document.createElement('div');
            cardsDiv.className = 'hijab-product-cards';
            products.forEach(p => {
                const card = document.createElement('div');
                card.className = 'hijab-product-card';
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    window.open(p.url, '_blank');
                });
                card.innerHTML = `
                    <img src="${p.image}" alt="${p.name}" loading="lazy" onerror="this.src='/images/logo.png'">
                    <div class="info">
                        <div class="name">${p.name}</div>
                        <div class="meta">${p.brand || ''} ${p.level ? '· ' + p.level : ''}</div>
                        <div class="price">${p.price}${p.discount ? '<span class="discount">-' + p.discount + '</span>' : ''}</div>
                    </div>
                    <div class="arrow"><i class="fas fa-chevron-right"></i></div>
                `;
                cardsDiv.appendChild(card);
            });
            div.appendChild(cardsDiv);
        }

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        return div;
    }

    function hijabShowTyping() {
        const container = document.getElementById('hijab-messages');
        const div = document.createElement('div');
        div.className = 'hijab-typing';
        div.id = 'hijab-typing';
        div.innerHTML = '<span></span><span></span><span></span>';
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function hijabHideTyping() {
        const el = document.getElementById('hijab-typing');
        if (el) el.remove();
    }

    function hijabSendQuick(text) {
        document.getElementById('hijab-input').value = text;
        // Sembunyikan quick replies setelah pertama dipakai
        document.getElementById('hijab-quick').style.display = 'none';
        hijabSendMessage();
    }

    async function hijabSendMessage() {
        const input = document.getElementById('hijab-input');
        const text  = input.value.trim();
        if (!text || hijabIsLoading) return;

        const now = Date.now();
        const cooldown = 2000;
        if (now - hijabLastSent < cooldown) {
            const sisa = Math.ceil((cooldown - (now - hijabLastSent)) / 1000);
            hijabAppendMsg('Tunggu ' + sisa + ' detik lagi ya... ⏳', 'bot');
            return;
        }
        hijabLastSent = now;

        input.value = '';
        document.getElementById('hijab-char-count').textContent = '0/500';
        hijabIsLoading = true;
        document.getElementById('hijab-send-btn').disabled = true;
        document.getElementById('hijab-quick').style.display = 'none';

        hijabAppendMsg(text, 'user');
        hijabShowTyping();

        try {
            if (isLiveChat && liveChatSessionId) {
                const res = await fetch(`/live-chat/${liveChatSessionId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: text }),
                });
                
                hijabHideTyping();
                
                if (!res.ok) {
                    hijabAppendMsg('Gagal mengirim pesan ke admin.', 'bot');
                } else {
                    const data = await res.json();
                    if (data.id > lastLiveChatId) lastLiveChatId = data.id;
                }
            } else {
                const res = await fetch('{{ route("chatbot.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: text, history: hijabHistory }),
                });

                const contentType = res.headers.get('content-type') || '';
                let data = {};
                let rawText = '';

                if (contentType.includes('application/json')) {
                    data = await res.json();
                } else {
                    rawText = await res.text();
                    console.error('Chatbot: server returned non-JSON', res.status, rawText.substring(0, 500));
                }

                hijabHideTyping();

                if (!res.ok) {
                    if (res.status === 419) {
                        hijabAppendMsg('Sesi habis, refresh halaman dulu ya! 🔄', 'bot');
                    } else if (data.error) {
                        hijabAppendMsg(data.error, 'bot');
                    } else {
                        hijabAppendMsg('Server sibuk (error ' + res.status + '). Coba lagi ya! 😅', 'bot');
                    }
                    hijabIsLoading = false;
                    document.getElementById('hijab-send-btn').disabled = false;
                    input.focus();
                    return;
                }

                const reply = data.reply || data.error || 'Maaf, terjadi kesalahan.';
                hijabAppendMsg(reply, 'bot', data.products || []);

                if (data.transfer_to_admin) {
                    isLiveChat = true;
                    liveChatSessionId = data.session_id;
                    startLiveChatPolling();
                }

                // Simpan ke history (hanya teks, tanpa cards)
                hijabHistory.push({ role: 'user', text });
                hijabHistory.push({ role: 'model', text: reply });
                if (hijabHistory.length > 20) hijabHistory = hijabHistory.slice(-20);
            }

        } catch (e) {
            hijabHideTyping();
            console.error('Chatbot fetch error:', e);
            if (e.name === 'TypeError') {
                hijabAppendMsg('Maaf, koneksi bermasalah. Periksa internet & coba lagi ya! 😅', 'bot');
            } else {
                hijabAppendMsg('Maaf, ada masalah teknis. Coba lagi ya! �️', 'bot');
            }
        }

        hijabIsLoading = false;
        document.getElementById('hijab-send-btn').disabled = false;
        input.focus();
    }

    // Character counter
    document.getElementById('hijab-input').addEventListener('input', function() {
        const len = this.value.length;
        const max = this.getAttribute('maxlength');
        document.getElementById('hijab-char-count').textContent = len + '/' + max;
    });

    function startLiveChatPolling() {
        if (liveChatInterval) clearInterval(liveChatInterval);
        liveChatInterval = setInterval(async () => {
            if (!liveChatSessionId || !isLiveChat) return;
            try {
                const res = await fetch(`/live-chat/${liveChatSessionId}/poll?last_id=${lastLiveChatId}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            if (msg.id > lastLiveChatId) {
                                lastLiveChatId = msg.id;
                                if (msg.sender === 'admin') {
                                    hijabAppendMsg(msg.text, 'admin-reply');
                                }
                            }
                        });
                    }
                    if (data.status === 'closed') {
                        hijabAppendMsg('Sesi obrolan telah ditutup oleh admin.', 'bot');
                        isLiveChat = false;
                        clearInterval(liveChatInterval);
                    }
                }
            } catch (e) {
                console.error('Polling error', e);
            }
        }, 2000);
    }
</script>

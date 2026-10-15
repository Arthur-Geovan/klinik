@extends('layout.app')

@section('content')
<style>
    .chatbot-shell { max-width: 900px; }
    .chatbot-header { background: linear-gradient(135deg, #236b8e, #2aa79b); color: #fff; }
    .chat-window { height: min(58vh, 540px); overflow-y: auto; background: #f6f9fb; }
    .chat-message { max-width: 78%; border-radius: 1rem; padding: .8rem 1rem; line-height: 1.5; }
    .chat-message.bot { background: #fff; border: 1px solid #e1e9ee; }
    .chat-message.user { background: #236b8e; color: #fff; }
    .chat-time { font-size: .72rem; opacity: .65; }
</style>

<div class="container-fluid chatbot-shell">
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="chatbot-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-robot fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-1">Tanya AI Klinik</h3>
                    <p class="mb-0 opacity-75">Asisten informasi untuk membantu persiapan kunjungan Anda.</p>
                </div>
            </div>
        </div>

        <div id="chatWindow" class="chat-window p-3 p-md-4" aria-live="polite">
            <div class="d-flex mb-3">
                <div class="chat-message bot">
                    <div>Halo, {{ Auth::user()->name }}. Saya dapat membantu informasi tentang reservasi, jadwal dokter, dan poli.</div>
                    <div class="chat-time mt-2">Asisten AI Klinik</div>
                </div>
            </div>
        </div>

        <div class="p-3 border-top bg-white">
            <div class="alert alert-warning py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                Jawaban ini bukan diagnosis. Untuk keadaan darurat, segera hubungi 112 atau datang ke IGD terdekat.
            </div>
            <form id="chatForm" class="d-flex gap-2">
                @csrf
                <label for="chatMessage" class="visually-hidden">Tulis pertanyaan</label>
                <input id="chatMessage" name="message" class="form-control" maxlength="1000" autocomplete="off" placeholder="Tulis pertanyaan Anda..." required>
                <button id="chatSubmit" class="btn btn-primary px-3" type="submit" aria-label="Kirim pertanyaan" title="Kirim pertanyaan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatMessage');
    const chatWindow = document.getElementById('chatWindow');
    const chatSubmit = document.getElementById('chatSubmit');
    const chatHistoryKey = @json('chatbot-history-' . Auth::id());
    const maxChatMessages = 50;

    function saveChatHistory() {
        const messages = Array.from(chatWindow.querySelectorAll('.chat-message')).map((message) => ({
            type: message.classList.contains('user') ? 'user' : 'bot',
            text: message.firstElementChild.textContent,
        }));

        try {
            sessionStorage.setItem(chatHistoryKey, JSON.stringify(messages.slice(-maxChatMessages)));
        } catch {}
    }

    function addMessage(text, type, persist = true) {
        const wrapper = document.createElement('div');
        wrapper.className = `d-flex mb-3 ${type === 'user' ? 'justify-content-end' : ''}`;
        const message = document.createElement('div');
        message.className = `chat-message ${type}`;
        message.innerHTML = `<div></div><div class="chat-time mt-2">${type === 'user' ? 'Anda' : 'Asisten AI Klinik'}</div>`;
        message.firstElementChild.textContent = text;
        wrapper.appendChild(message);
        chatWindow.appendChild(wrapper);
        if (persist) saveChatHistory();
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    try {
        const savedMessages = JSON.parse(sessionStorage.getItem(chatHistoryKey) || '[]');
        if (Array.isArray(savedMessages) && savedMessages.length > 0) {
            chatWindow.replaceChildren();
            savedMessages.slice(-maxChatMessages).forEach((message) => {
                if (message && typeof message.text === 'string' && ['user', 'bot'].includes(message.type)) {
                    addMessage(message.text, message.type, false);
                }
            });
        }
    } catch {
        try {
            sessionStorage.removeItem(chatHistoryKey);
        } catch {}
    }

    chatForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        chatInput.value = '';
        chatInput.disabled = true;
        chatSubmit.disabled = true;

        try {
            const response = await fetch('{{ route('chatbot.message') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ message }),
            });
            const data = await response.json();
            addMessage(data.reply || data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
        } catch (error) {
            addMessage('Koneksi sedang bermasalah. Silakan coba lagi atau gunakan menu Reservasi.', 'bot');
        } finally {
            chatInput.disabled = false;
            chatSubmit.disabled = false;
            chatInput.focus();
        }
    });
</script>
@endsection
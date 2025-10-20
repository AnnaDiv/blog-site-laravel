<x-layout>
<script>
// put this at the top of conversations page
window.conversationsReady = new Promise(res => {
    const oldFetch = window.fetch;
    window.fetch = function(...args) {
        return oldFetch.apply(this, args).then(r => {
            if (args[0] === '/conversations') res(); // list is now painted
            return r;
        });
    };
});
</script>
<style>
.flex.h-screen { min-height: 0; }

#right-pane {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

#message-box {
    flex: 1 1 0;
    overflow-y: auto;
}

#conv-aside {
    overflow-y: auto;
}
</style>
<div class="flex antialiased border border-solid" style="height:90vh">

    <!-- 1) LEFT – conversation list -->
    <aside class="w-1/3 lg:w-1/4 bg-blue-100 border-r overflow-y-auto" id="conv-aside">
        <h2 class="px-4 py-3 font-bold text-gray-700 text-center">Conversations</h2>
        <br>
        <ul>
            @forelse($conversations as $conv)
                <li style="display:flex">
                    @php
                        $other = $conv->users->firstWhere('id', '!=', auth()->id());
                    @endphp
                    {{-- clickable row --}}
                    <button
                        class="w-full text-left px-4 py-3 bg-blue-300 rounded-lg hover:bg-blue-400 hover:text-gray-900 hover:font-bold focus:outline-none focus:bg-blue-200 transition flex items-center space-x-2"
                        onclick="openInRightPanel({{ $conv->id }}, {{ json_encode($other->nickname) }})"
                    >
                        Conversation with {{ $other->nickname }}
                        @if($conv->unread)
                            <span class="w-2 h-2 bg-blue-500 rounded-full ml-auto"></span>
                        @endif
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        onclick="startThenOpen({{ $other->id }}, '{{ $other->nickname }}')">
                        Bubble <i class="fa fa-comment" aria-hidden="true"></i>
                    </button>
                </li>
            @empty
                <li class="px-4 py-3 text-blue-500 text-center font-bold">You have no conversations. Start one, meet some new friends!</li>
            @endforelse
        </ul>
    </aside>

    <!-- 2) RIGHT – message pane -->
    <main class="flex-1 flex flex-col bg-white" id="right-pane">
        {{-- empty state --}}
        <div id="right-empty" class="m-auto text-gray-400 text-center font-bold">
            Choose a conversation to start messaging
        </div>

        {{-- actual chat (hidden by default) --}}
        <div id="right-chat" class="flex-1 flex flex-col hidden">
            <header class="px-5 py-3 border-b font-semibold text-gray-800">
                Conversation <span id="right-header-id"></span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        onclick="startThenOpen({{ $other->id }}, '{{ $other->nickname }}')">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                </button>
            </header>

            <div id="message-box" class="flex-1 overflow-y-auto p-4 space-y-3"></div>

            <form id="right-panel-form" class="p-4 border-t"
                onsubmit="sendMessageChat(document.getElementById('right-panel-input').value); return false;">
                <div class="flex space-x-2">
                    <input id="right-panel-input"
                        type="text"
                        placeholder="Type a message…"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Send</button>
                </div>
            </form>
        </div>
    </main>
</div>
<script>
    const message_text = document.getElementById('message-box').textContent;
    const input_dm = document.querySelector('#right-panel-input');
</script>
<script>
(() => {
    const id   = sessionStorage.getItem('openConv');
    const nick = sessionStorage.getItem('openNick');
    if (!id) return;
    sessionStorage.removeItem('openConv');
    sessionStorage.removeItem('openNick');

    /* listen first */
    window.addEventListener('conversationsReady', () => openInRightPanel(id, nick), { once: true });
})();
</script>
</x-layout>

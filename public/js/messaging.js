function $fresh(sel){ return document.querySelector(sel); }

/* ----------  minimise toggle  ---------- */
window.toggleChat = function(){
    const o = document.getElementById('dm-overlay');
    o.style.display = o.style.display === 'flex' ? 'none' : 'flex';
    document.getElementById('dm-chevron').classList.toggle('open', o.style.display === 'flex');
};

/* ----------  TOKEN HANDLING  ---------- */
async function fetchToken(){
    if(authToken) return authToken;
    const res = await fetch(`/api/token/jwt`, {
        credentials:'include',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    if(!res.ok || !res.headers.get('content-type')?.includes('json')){
        console.error('JWT endpoint returned HTML – not logged in?', res.status);
        throw new Error('JWT fetch failed');
    }
    const json = await res.json();
    authToken = json.token;
    return authToken;
}

/* ----------  AJAX HELPER  ---------- */
async function api(url, opts={}){
    const token = await fetchToken();
    const res   = await fetch(url, {
        ...opts,
        headers:{
            'Authorization': `Bearer ${token}`,
            'Content-Type' : 'application/json',
            'Accept'       : 'application/json',
            'X-CSRF-TOKEN' : CSRF,
            ...opts.headers
        }
    });
    if(!res.ok || !res.headers.get('content-type')?.includes('json')){
        console.error('API returned HTML instead of JSON', res.status, await res.text());
        throw new Error('API error');
    }
    return res.json();
}

/* open / close */
window.openDM = async function(convId, recipientNickname){
const bar   = document.querySelector('#dm-bar');
    const panel = document.querySelector('#dm-overlay');
    const name  = document.querySelector('#dm-bar-name');
    const msgBox= document.querySelector('#dm-messages');

    bar.style.display   = 'flex';
    name.textContent    = `Chat with ${recipientNickname}`;
    currentConv = convId;
    panel.style.display = 'flex';

    /* =====  DEBUG  ===== */
    console.log('panel exists:', !!panel);
    console.log('msgBox exists:', !!msgBox);
    console.log('convId:', convId);

    // 1.  hard-load once
    const {data} = await api(`${API}/messages/${currentConv}`);
    console.log('messages returned:', data);

    // 2.  render manually
    renderMessages(data, msgBox);

    // 3.  start poll
    pollTimer = setInterval(() => loadMessages(msgBox), POLL_MS);
};

window.closeDM = function(){
    $('#dm-overlay').style.display = 'none';
    clearInterval(pollTimer);
    msgBox.innerHTML = '';
    currentConv = null;
};

async function loadMessages(box){
if (!currentConv) return;
    const panel = document.querySelector('#dm-overlay');
    if (!panel || panel.style.display === 'none') return; // panel absent/minimised → skip

    const {data} = await api(`${API}/messages/${currentConv}`);
    renderMessages(data, box);
}

async function sendMessage(text){
    await api(`${API}/messages/${currentConv}`, {
        method: 'POST',
        body: JSON.stringify({body:text})
    });
    input.value = '';
    await loadMessages();
}

function renderMessages(list, box){
    box.innerHTML = '';
    list.forEach(m=>{
        const div = document.createElement('div');
        div.className = 'dm-message ' + (m.sender_id === mine ? 'own' : '');
        div.innerHTML = `<span class="body">${m.body}</span>`;
        box.appendChild(div);
    });
    box.scrollTop = box.scrollHeight;
}

$('#dm-form').addEventListener('submit', e => {
    e.preventDefault();
    const txt = input.value.trim();
    if(txt) sendMessage(txt);
});

/* ----------  CONVERSATION BOOTSTRAP  ---------- */
async function startThenOpen(receiverId, receiverNickname){
    try{
        const {conversation_id} = await api(`${API}/messages/start`, {
            method: 'POST',
            body: JSON.stringify({receiver_id: receiverId})
        });
        console.log('conversation_id from server', conversation_id);
        console.log('reciever_id from server', receiverNickname);
        openDM(conversation_id, receiverNickname);
    }catch(e){ console.error(e); }
}

/* ----------  persist ---------- */
window.addEventListener('beforeunload', () => {
    if (currentConv) {
        sessionStorage.setItem('dm_conv',  currentConv);
        sessionStorage.setItem('dm_nick', $('#dm-bar-name').dataset.nick || $('#dm-bar-name').textContent.replace('Chat with ',''));
        sessionStorage.setItem('dm_open', $('#dm-overlay').style.display === 'flex');
    }
});

/* ----------  restore ---------- */
document.addEventListener('DOMContentLoaded', () => {
    const conv = sessionStorage.getItem('dm_conv');
    const nick = sessionStorage.getItem('dm_nick');
    const open = sessionStorage.getItem('dm_open') === 'true';

    if (conv && nick) {
        $('#dm-bar').style.display   = 'flex';
        $('#dm-bar-name').textContent = `Chat with ${nick}`;

        // always fetch/render, but control panel visibility
        window.openDM(conv, nick);          // this now does steps 1-3
        if (!open) {                        // was minimised → shut panel
            $('#dm-overlay').style.display = 'none';
            clearInterval(pollTimer);       // stop poll while minimised
        }

        ['dm_conv','dm_nick','dm_open'].forEach(k=>sessionStorage.removeItem(k));
    }
});

/* ----------  close chat completely  ---------- */
window.closeChat = function(e){
    e.stopPropagation();
    // hide everything
    $('#dm-overlay').style.display = 'none';
    $('#dm-bar').style.display     = 'none';
    // clear text and data
    $('#dm-bar-name').textContent  = '';
    $('#dm-bar-name').dataset.nick = '';
    // stop polling and reset
    clearInterval(pollTimer);
    msgBox.innerHTML = '';
    currentConv = null;
    // wipe session so it doesn’t come back on next page
    sessionStorage.removeItem('dm_conv');
    sessionStorage.removeItem('dm_nick');
    sessionStorage.removeItem('dm_open');
};

function bindForm(){
    const form = document.querySelector('#dm-form');
    if (!form) return;
    form.replaceWith(form.cloneNode(true)); // remove old listeners
    form.addEventListener('submit', e => {
        e.preventDefault();
        const txt = document.querySelector('#dm-input').value.trim();
        if (txt) sendMessage(txt);
    });
}
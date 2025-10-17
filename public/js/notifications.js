document.addEventListener('DOMContentLoaded', function () {
    const dropdown  = document.getElementById('dropdownList');
    const toggleBtn = document.getElementById('dropdownToggle');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!dropdown || !toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    fetch('/notifications')
        .then(res => res.json())
        .then(data => {
            dropdown.innerHTML = '';

            if (!Array.isArray(data[0]) || data[0].length === 0) {
                dropdown.innerHTML = '<li>No notifications</li>';
                toggleBtn.classList.remove('has-unread');
                return;
            }

            let hasUnread = false;

            data[0].forEach(item => {
                const li = document.createElement('li');
                const a  = document.createElement('a');
                li.className = 'notification';

                const tmp   = new DOMParser().parseFromString(item.link, 'text/html');
                const aOld  = tmp.querySelector('a');
                const href  = aOld?.getAttribute('href') ?? '#';
                const conv  = aOld?.dataset.conv  ?? '';
                const nick  = aOld?.dataset.nick  ?? '';

                a.href      = href;
                a.textContent = item.content;

                if (item.used == 1) {
                    li.classList.add('notification-read');
                    a.classList.add('read-link');
                } else {
                    li.classList.add('notification-unread');
                    a.classList.add('unread-link');
                    hasUnread = true;
                }

                if (conv && nick) {
                    a.addEventListener('click', () => {
                        sessionStorage.setItem('openConv', conv);
                        sessionStorage.setItem('openNick', nick);
                        if (item.used == 0) {
                            fetch('/notifications', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ notification_id: item.id })
                            }).catch(() => {});
                        }
                    });
                } else {
                    a.addEventListener('click', () => {
                        if (item.used == 0) {
                            fetch('/notifications', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ notification_id: item.id })
                            }).catch(() => {});
                        }
                    });
                }

                li.appendChild(a);
                dropdown.appendChild(li);
            });

            toggleBtn.classList.toggle('has-unread', hasUnread);

            /* ---- NEW: fire event only after list is painted ---- */
            if (window.location.pathname === '/conversations' &&
                data[0].some(n => n.link.includes('data-conv'))) {
                window.dispatchEvent(new CustomEvent('conversationsReady'));
            }
        })
        .catch(err => {
            dropdown.innerHTML = '<li>Error loading notifications</li>';
            console.error(err);
        });
});
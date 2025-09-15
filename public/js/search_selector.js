document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('search_q');

    let clickedButtonTarget = null;

    // Track which button was clicked
    document.querySelectorAll('#searchForm button[type="submit"]').forEach(btn => {
        btn.addEventListener('click', function () {
            clickedButtonTarget = this.getAttribute('data-target'); // "posts" or "users"
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Stop default form action

        const query = encodeURIComponent(input.value.trim());

        if (!query || !clickedButtonTarget) {
            return; // prevent empty queries or unclicked button
        }

        let targetURL = '';

        if (clickedButtonTarget === 'posts') {
            targetURL = `${window.searchRoutes.posts}?search_q=${query}`;
        } else if (clickedButtonTarget === 'users') {
            targetURL = `${window.searchRoutes.users}?search_q=${query}`;
        }

        window.location.href = targetURL;
    });
});
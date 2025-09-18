function fetchLikes() {
  fetch(`/like/${postId}`)
    .then(res => res.json())
    .then(data => {
      if (!Array.isArray(data) || data.length < 2) {
        console.error('Unexpected data format:', data);
        return;
      }

      userLiked = parseInt(data[0] ?? 0);
      const totalLikes = data[1] ?? 0;

      if (likeCountDisplay) {
        likeCountDisplay.textContent = `${totalLikes} likes`;
      }

      if (likeImg) {
        likeImg.src = userLiked === 1
          ? "/storage/post/likeheart.png"
          : "/storage/post/nolikeheart.png";
      }
    })
    .catch(err => console.error('Failed to fetch likes:', err));
}

if (likeToggleButton) {
  likeToggleButton.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('/like/toggle', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        post_id: postId,
        post_owner: postOwner
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        userLiked = data.liked;

        if (likeImg) {
          likeImg.src = userLiked === 1
            ? "/storage/post/likeheart.png"
            : "/storage/post/nolikeheart.png";
        }

        // Update like count visually without re-fetching
        if (likeCountDisplay) {
          const currentCount = parseInt(likeCountDisplay.textContent) || 0;
          likeCountDisplay.textContent = `${currentCount + (userLiked === 1 ? 1 : -1)} likes`;
        }
      } else {
        alert(data.error || 'Failed to toggle like');
      }
    })
    .catch(err => console.error('Like toggle failed:', err));
  });
}

fetchLikes();
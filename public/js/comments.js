document.addEventListener('DOMContentLoaded', () => {  
  
  function fetchComments() {
    fetch(`/comment/${postId}`)
      .then(res => res.json())
      .then(data => {
        console.log('Comments:', data);
        list.innerHTML = '';

        if (data.length === 0) {
          list.innerHTML = '<p>No comments yet.</p>';
          return;
        }

        data.forEach(comment => {
          const div = document.createElement('div');
          div.className = 'comment';
          div.id = 'comment' + comment.id;

          div.innerHTML = `
            <div class="comment-body">
              <a href="#${comment.user_id}_profile">
                <strong>${comment.user_nickname}</strong>
              </a>: ${comment.content}
              <br><small>${new Date(comment.time).toLocaleString()}</small>
            </div>
          `;

          if (comment.user_id == userId) {
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-comment';
            deleteBtn.dataset.id = comment.id;
            deleteBtn.textContent = '🗑️';
            div.appendChild(deleteBtn);
          }

          list.appendChild(div);
        });

        // Bind delete buttons
        document.querySelectorAll('.delete-comment').forEach(btn => {
          btn.addEventListener('click', function () {
            const commentId = this.dataset.id;
            fetch('/comment/remove', {
              method: 'POST',
              headers: { 
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: new URLSearchParams({
                comment_id: commentId,
                post_id: postId
              })
            })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  fetchComments();
                } else {
                  alert(data.error || 'Failed to delete comment');
                }
              });
          });
        });
      });
  }

  if (form && input && list) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      fetch('/comment/add', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: new URLSearchParams({
          comment: input.value,
          post_id: postId,
          post_owner: postOwner
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            input.value = '';
            fetchComments();
          } else {
            alert(data.error || 'Failed to post comment');
          }
        });
    });

  } else {
    console.warn('Comments: Missing form, input, or list element');
  }
  fetchComments();

});
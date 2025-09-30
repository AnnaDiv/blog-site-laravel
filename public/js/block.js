function fetchBlock() {
  fetch('/block?profileUser=' + profileUserNickname + '&blockingUser=' + currentUserNickname
  )
    .then(res => res.text())
    .then(text => {
      console.log('GET raw response:', text);

      try {
        const data = JSON.parse(text);
        console.log('GET parsed JSON:', data);

        if (!Array.isArray(data) || data.length < 1) {
          console.error('Unexpected data format:', data);
          return;
        }

        const isBlocked = Boolean(data[0]);

        const BlockImg = document.querySelector('#block-toggle img');
        const FollowImg = document.querySelector('#follow-toggle img');

        BlockImg.src = isBlocked ? BlockedImgUrl : UnblockedImgUrl;
        if (isBlocked) {
            FollowImg.src = NotFollowImgUrl;
        }
      } catch (err) {
        console.error('JSON parse failed! Raw response was:', text);
      }
    });
}

if (BlockToggleButton) {
  BlockToggleButton.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('/block', {
      method: 'POST',
      headers: {         
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest", 
      },
      body: JSON.stringify({
        blockingUser: currentUserNickname,
        profileUser: profileUserNickname,
      }),
    })
      .then(res => res.text())
      .then(text => {
        console.log('POST raw response:', text);

        try {
          const data = JSON.parse(text);
          console.log('POST parsed JSON:', data);

          if (data.success) {
            if (data.remove) {
                const count = parseInt(FollowCountDisplay.textContent, 10);
                if (!isNaN(count) && count > 0) {
                    FollowCountDisplay.textContent = count - 1;
                }
            }
            fetchBlock();
          } else {
            alert(data.error || 'Failed to toggle block');
          }
        } catch (err) {
          console.error('JSON parse failed! Raw response was:', text);
        }
      });
  });
}

fetchBlock();
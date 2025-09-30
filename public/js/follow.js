function fetchFollows() {
  fetch("/follow?profileUser=" + profileUserNickname + "&follower=" + currentUserNickname, {
    headers: { "X-Requested-With": "XMLHttpRequest" }
  })
    .then(res => res.json())
    .then(data => {
      console.log('GET parsed JSON:', data);

      if (!Array.isArray(data) || data.length < 2) {
        console.error('Unexpected data format:', data);
        return;
      }

      userFollows = Boolean(data[0]);
      const totalFollows = data[1];

      document.getElementById('follow-count').textContent =
        `${totalFollows} follows`;

      const FollowImg = document.querySelector('#follow-toggle img');
      const BlockImg = document.querySelector('#block-toggle img');

      FollowImg.src = userFollows ? FollowImgUrl : NotFollowImgUrl;
      if (userFollows) {
        BlockImg.src = UnblockedImgUrl;
      }
    })
    .catch(err => console.error("GET request failed:", err));
}

if (FollowToggleButton) {
  FollowToggleButton.addEventListener("click", function (e) {
    e.preventDefault();

    fetch("/follow", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        follower: currentUserNickname,
        profileUser: profileUserNickname,
      }),
    })
      .then(res => res.json())
      .then(data => {
        console.log('POST parsed JSON:', data);
        if (data.success) {
          fetchFollows();
        } else {
          alert(data.error || "Failed to toggle follow");
        }
      })
      .catch(err => console.error("POST request failed:", err));
  });
}

fetchFollows();
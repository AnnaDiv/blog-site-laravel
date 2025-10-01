document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.getElementById('image');
    if (!imageInput || !userID) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('user_id', userID);

        const xhr = new XMLHttpRequest();

        // Show loading UI
        spinner.style.display = 'flex';
        progressBarWrapper.style.display = 'block';

        // Track upload progress
        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressBar.style.width = `${percent}%`;
            }
        });

        // Handle response
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                spinner.style.display = 'none';
                progressBarWrapper.style.display = 'none';
                progressBar.style.width = '0%';

                let data;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch {
                    alert('Server returned invalid response.');
                    imageInput.value = '';
                    return;
                }

                if (xhr.status === 200 && data.success && data.image_url) {
                    previewImage.src = data.image_url;
                } else {
                    alert(data.error || `Upload failed: ${xhr.status}`);
                    imageInput.value = '';
                }
            }
        };

        xhr.open('POST', '/image/preview', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // ensures Laravel sees AJAX
        xhr.send(formData);
    });
});
<x-layout>
<div class="profile-form-container">
    <form method="POST" enctype="multipart/form-data" action="{{ route('profile.update', $user) }}">
        @csrf

        @method('PUT')
        <div class="profile-up">
            <!-- Profile Image Section -->
            <div class="profile-image-up">
                <div class="image-preview-wrapper">
                    @if ($user->image_folder)
                        <img id="post_image" class="profile-image" src="{{ asset('storage/' . $user->image_folder) }}"/>
                    @else
                        <img id="post_image" class="profile-image" src="{{ asset('storage/user/alt/blank.jpeg') }}"/>
                    @endif
                    <label for="image" class="form-label">Change Profile Image</label>
                    <input type="file" name="image" id="image" class="form-input-file"/>

                    <!-- Spinner -->
                    <div class="image-spinner" id="image-spinner">
                        <div class="spinner"></div>
                    </div>

                    <!-- Progress bar -->
                    <div id="upload-progress-bar"><div class="bar"></div></div>
                </div>


            </div>

            <!-- Profile Info Section -->
            <div class="profile-info-up">
                <div class="form-group">
                    <label for="nickname" class="form-label">Nickname</label>
                    <input type="text" name="nickname" id="nickname" class="form-input"
                        value="{{ old('nickname') ?? $user->nickname }}"/>
                </div>

                <div class="form-group">
                    <label for="motto" class="form-label">Motto</label>
                    <textarea name="motto" id="motto" class="form-textarea">{{ old('motto') ?? $user->motto }}</textarea>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-input"
                        value="{{ old('email') ?? $user->email }}"/>
                </div>

                <div class="form-group password-section">
                    <h3 class="form-subtitle">Change Password</h3>
                    <label for="old_pass" class="form-label">Old Password</label>
                    <input type="password" name="old_pass" id="old_pass" class="form-input"/>

                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="password" id="password" class="form-input"/>

                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input"/>

                    <label class="form-checkbox">
                        <input type="checkbox" onclick="showPass()"/> Show Passwords
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <input type="submit" name="submit" value="Update" class="btn-submit"/>
        </div>
    </form>
</div>
</x-layout>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const userID = @json(auth()->user()->id ?? null);
const spinner = document.getElementById('image-spinner');
const progressBarWrapper = document.getElementById('upload-progress-bar');
const progressBar = progressBarWrapper.querySelector('.bar');
const imageInput = document.getElementById('image');
const previewImage = document.querySelector('#post_image');
</script>
<script>
function showPass() {
  var old = document.getElementById("old_pass");
  var newp = document.getElementById("password");
  var newpc = document.getElementById("password_confirmation");
  old.type = old.type === "password" ? "text" : "password";
  newp.type = newp.type === "password" ? "text" : "password";
  newpc.type = newpc.type === "password" ? "text" : "password";
}
const userID = @json(auth()->user()->id ?? null);
</script>
<script src="{{ asset('js/imageCreator.js') }}"></script>
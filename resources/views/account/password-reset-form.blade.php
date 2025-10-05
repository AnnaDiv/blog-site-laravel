<x-layout>
<div class="login-container">
    <form method="POST" class="login-form" action="{{ route('password.reset.submit') }}">
            
        @csrf

        <input type="hidden" name="token" value="{{ $token }}"/>

        <input type="hidden" name="email" value="{{ $email }}"/>

        <div>Change Password:
            <label for="password">New Password</label>
            <input type="password" name="password" id="password"/>
        <br>
            <label for="password_confirmation">New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" />

            <div class="form-options">
                <label>
                    <input type="checkbox" onclick="showPass()"> Show Passwords
                </label>
            </div>
        </div>

        <input type="submit" name="submit" value="Submit"/>
    </form>
</div>
</x-layout>

<script>
function showPass() {
  var old = document.getElementById("password");
  var newp = document.getElementById("password_confirmation");
  old.type = old.type === "password" ? "text" : "password";
  newp.type = newp.type === "password" ? "text" : "password";
}
</script>
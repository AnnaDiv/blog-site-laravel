<x-layout>
<div class="login-container">
    <form class="login-form" method="POST" action="{{route('login.auth')}}">
        @csrf
        <h2><strong>Login to your account</strong></h2>

        <x-inputs.text name="email" id="email" label="Email" placeholder="you@example.com" :required="true"/>

        <x-inputs.text type="password" name="password" id="password" label="Password" placeholder="••••••••" :required="true"/>

        <div class="form-options">
            <label>
                <input type="checkbox" onclick="showPass()"> Show Password
            </label>
        </div>

        <input class="login-btn" type="submit" name="submit" value="Login"/>
    </form>

    <div class="login-footer">
    <p>Don't have an account?
        <a href="{{route('register')}}">
            <button class="secondary-btn">Create Account</button>
        </a>
    </p>
    <p>Need help?
        <a href="{{ route('account.help') }}">
            <button class="secondary-btn">Account Help</button>
        </a>
    </p>
    </div>
</div>

<script>
function showPass() {
  var old = document.getElementById("password");
  old.type = old.type === "password" ? "text" : "password";
}
</script>
</x-layout>
<x-layout>
<style>
    .login-container{
        max-width: 40% !important;
        min-width: 20% !important;
    }
    .login-form{
        display: flex;
    }
</style>
<div class="login-container">
    <h3>Provide your email to get a verification link to change your password:</h3>
    <br>
    <form method="POST" class="login-form" action="{{ route('password.token') }}">
        
        @csrf

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="you@example.com" required/>

        <input type="submit" name="submit" value="Send Email"/>
    </form>
</div>
</x-layout>
<x-layout>

<div class="login-container">
    <form method="POST" class="login-form" action="{{route('register.store')}}">
        @csrf
        <h2><strong>Create an account</strong></h2>

        <x-inputs.text name="email" id="email" label="Email" :required="true" value="{{old('email')}}"/>

        <x-inputs.text name="nickname" id="nickname" label="Username" :required="true" value="{{old('nickname')}}"/>

        <x-inputs.text name="motto" id="motto" label="Motto" value="{{old('motto')}}"/>

        <x-inputs.text id="password" name="password" type="password" placeholder="Password" label="Password"/>
        <x-inputs.text id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm Password" label="Confirm Password"/>
        
        <div class="form-options">
            <label>
                <input type="checkbox" onclick="showPass()"> Show Passwords
            </label>
        </div>
        <input type="submit" name="submit" value="Create"/>
    </form>

</div>

<style>
#fail_p{
    visibility: hidden;
}
</style>
<script>
function showPass() {
  var old_pass = document.getElementById("password");
  var new_pass = document.getElementById("password_confirmation");
  old_pass.type = old_pass.type === "password" ? "text" : "password";
  new_pass.type = new_pass.type === "password" ? "text" : "password";
}
</script>
</x-layout>
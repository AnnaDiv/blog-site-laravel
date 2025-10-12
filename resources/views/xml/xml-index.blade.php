<x-layout>
    <div class="simple-text">
        <h2 class="item-title">XML feeds</h2><br><br>
        <ol style="list-style-type: disc; padding-left: 1.2rem;">
            <li>Feed for site: <a class="button" href="{{ route('xml.all.posts') }}">XML Feed <i class="fa fa-user-image"></i></a></li><br>
            <li>Feed for singular user: <a class="button" href="{{ route('xml.user.posts', ['user_nickname' => 'John']) }}">XML user: John, feed <i class="fa fa-user"></i></a></li>
        </ol>
    </div>
</x-layout>
@props(['user'])

<tr class="user-item @if ($user->status == 'inactive') user-item-red @endif">
    <td>{{ $user->id }}</td>

    <td>
        <div class="user-item-img">
            <a href="{{ route('profile.all', $user->nickname) }}">
                @if($user->image_folder)
                    <img class="item_image" src="{{ asset('storage/' . $user->image_folder) }}" alt="User image">
                @else
                    <img class="item_image" src="{{ asset('storage/user/alt/blank.jpeg') }}" alt="User image">
                @endif
            </a>
        </div>
    </td>
    <td>
        <a href="{{ route('profile.all', $user->nickname) }}">
            <h4 class="post-title">{{ $user->nickname }}</h4>
        </a>
    </td>
    <td>
        <p class="post-description">@if ($user->motto)Exists: <br> Motto: {{$user->motto}} @else Doesnt Exist: Null @endif</p>
    </td>
    <td>
        <div class="post-categories">
            Posts: {{ $user->posts_count }}<br>
            Comments: {{ $user->comments_count }}<br>
            Likes: {{ $user->likes_count }}
        </div>
    </td>

    <td>
        <a href="{{ route('profile.all', $user->nickname) }}"><button>View</button></a>

        <form method="POST" action="{{ route('profile.edit.admin', $user->nickname) }}">
            @csrf
            @method('PUT')
            <button>Edit</button>
        </form>

        @if ($user->status == 'active')
            <form method="POST" action="{{ route('admin.user.ban', $user->nickname) }}">
                @csrf
                @method('PUT')
                <button>Ban/Suspend</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.user.activate', $user->nickname) }}">
                @csrf
                @method('PUT')
                <button>Re-instate User</button>
            </form>
        @endif
        
        <form method="POST" action="{{ route('admin.user.permDelete', $user->nickname) }}">
            @csrf
            @method('DELETE')
            <button>Perma Delete</button>
        </form>
    </td>
</tr>
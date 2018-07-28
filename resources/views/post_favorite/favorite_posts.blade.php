@if (count($microposts) > 0)
<ul class="media-list">
@foreach ($microposts as $micropost)
    <li class="media">
        <div class="media-left">
            <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
        </div>
        <div class="media-body">
            <div>
                {{ $micropost->content }}
            </div>
            <!--<div>
                <p>{!! link_to_route('users.show', 'View profile', ['id' => $user->id]) !!}</p>
            </div>-->
        </div>
    </li>
@endforeach
</ul>
{!! $microposts->render() !!}
@endif
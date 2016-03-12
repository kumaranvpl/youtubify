<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">

        <title>{{ $model->name }} - {{ $settings->get('siteName') }}</title>

        <meta name="google" content="notranslate">

        <meta itemprop="name" content="{{ $model->name }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="{{ $type  }}">
        <meta name="twitter:title" content="{{ $model->name }} - {{ $settings->get('siteName') }}">
        <meta name="twitter:url" content="{{ Request::url() }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ $model->name }} - {{ $settings->get('siteName') }}" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:site_name" content="{{ $settings->get('siteName') }}" />

        @if ($type === 'artist')
            <meta property="og:description" content="{{ trans('app.listenTo') }} {{ $model->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta property="og:type" content="music.musician" />
            <meta property="og:image" content="{{ $model->image_large  }}">
            <meta name="twitter:description" content="{{ trans('app.listenTo') }} {{ $model->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta name="twitter:image" content="{{ $model->image_large  }}">
            <meta itemprop="image" content="{{ $model->image_large }}">
            <meta itemprop="description" content="{{ trans('app.listenTo') }} {{ $model->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}">
            <meta property="og:image:width" content="1000">
            <meta property="og:image:height" content="667">
        @elseif ($type === 'album')
            <meta property="og:description" content="{{ $model->name }}, album by {{ $model->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta property="og:image" content="{{ $model->image  }}">
            <meta name="twitter:image" content="{{ $model->image  }}">
            <meta name="twitter:description" content="{{ $model->name }}, album by {{ $model->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta itemprop="description" content="{{ $model->name }}, album by {{ $model->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta itemprop="image" content="{{ $model->image }}">
            <meta property="og:image:width" content="300">
            <meta property="og:image:height" content="300">
        @elseif ($type === 'playlist')
            <meta property="og:description" content="{{ $model->name }}, playlist by {{ $model->users()->wherePivot('owner', 1)->first()->getNameOrEmail() }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta name="twitter:description" content="{{ $model->name }}, playlist by {{ $model->users()->wherePivot('owner', 1)->first()->getNameOrEmail() }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta itemprop="description" content="{{ $model->name }}, playlist by {{ $model->users()->wherePivot('owner', 1)->first()->getNameOrEmail() }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta property="og:type" content="music.playlist" />
            <meta property="og:image" content="{{ ! $model->tracks->isEmpty() ? $model->tracks->first()->album->image : url().'/assets/images/album-no-image.png' }}">
            <meta name="twitter:image" content="{{ ! $model->tracks->isEmpty() ? $model->tracks->first()->album->image : url().'/assets/images/album-no-image.png' }}">
            <meta itemprop="image" content="{{ ! $model->tracks->isEmpty() ? $model->tracks->first()->album->image : url().'/assets/images/album-no-image.png' }}">
            <meta property="music:song_count" content="{{ count($model->tracks) }}">
            <meta property="og:image:width" content="300">
            <meta property="og:image:height" content="300">

            @foreach($model->tracks as $index => $track)
                <meta property="music:song" content="{{ url().'/track/'.$track->id }}">
                <meta property="music:song:track" content="{{ $index }}">
            @endforeach
        @elseif ($type === 'track')
            <meta property="og:description" content="{{ $model->name }}, a song by {{ $model->album->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta name="twitter:description" content="{{ $model->name }}, a song by {{ $model->album->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta itemprop="description" content="{{ $model->name }}, a song by {{ $model->album->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}" />
            <meta property="og:type" content="music.song" />
            <meta property="og:image:url" content="{{ $model->album->image }}" />
            <meta name="twitter:image" content="{{ $model->album->image }}" />
            <meta property="music:duration" content="{{ $model->duration }}">
            <meta itemprop="image" content="{{ $model->album->image }}">
            <meta property="og:image:width" content="300">
            <meta property="og:image:height" content="300">
        @endif

        @if ($type !== 'album' && $type !== 'track')
            <meta property="og:updated_time" content="{{ $model->updated_at->timestamp }}" />
        @endif
    </head>

    <body>
        @if ($type === 'album')
            <h2>{{ $model->artist->name  }}</h2>
        @endif

        @if ($type === 'artist')
            <p>{{ trans('app.listenTo') }} {{ $model->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}</p>
        @elseif ($type === 'album')
            <p>{{ $model->name }}, album by {{ $model->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}</p>
        @elseif ($type === 'playlist')
            <p>{{ $model->name }}, playlist by {{ $model->users()->wherePivot('owner', 1)->first()->getNameOrEmail() }} {{ trans('app.on') }} {{ $settings->get('siteName') }}</p>
        @elseif ($type === 'track')
            <p>{{ $model->name }}, a song by {{ $model->album->artist->name }} {{ trans('app.on') }} {{ $settings->get('siteName') }}</p>
        @endif

        <a href="{{ Request::url() }}">
            @if ($type === 'playlist')
                <img src="{{ ! $model->tracks->isEmpty() ? $model->tracks->first()->album->image : url().'/assets/images/album-no-image.png' }}" alt="{{ $model->name }}">
            @elseif ($type === 'track')
                <img src="{{ $model->album->image  }}" alt="{{ $model->name }}">
            @else
                <img src="{{ $type === 'artist' ? $model->image_large : $model->image  }}" alt="{{ $model->name }}">
            @endif
        </a>

        @if($type === 'artist')
            @foreach($model->albums as $album)
                <h3><a href="{{ url().'/album/'.($album->artist ? urlencode($album->artist->name).'/'.urlencode($album->name) : urlencode($album->name)) }}">{{ $album->name }}</a> - {{ $album->release_date }}</h3>

                <ul>
                    @foreach($album->tracks as $track)
                        <li><a href="{{ url().'/track/'.$track->id  }}">{{ $track->name }} - {{ $album->name }} - {{ $model->name }}</a></li>
                    @endforeach
                </ul>
            @endforeach

        @elseif($type === 'album')
            <h3><a href="{{ url().'/album/'.($model->artist ? urlencode($model->artist->name).'/'.urlencode($model->name) : urlencode($model->name)) }}">{{ $model->name }}</a> - {{ $model->release_date }}</h3>
            <ul>
                @foreach($model->tracks as $track)
                    <li>{{ $track->name }} - {{ $model->name }} - {{ $model->artist ? $model->artist->name : 'various artists' }}</li>
                @endforeach
            </ul>
        @endif
    </body>
</html>

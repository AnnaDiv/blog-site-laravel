<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="{{ asset('storage/xml/feed-style-browse.xsl') }}"?>

<pins xmlns:media="http://search.yahoo.com/mrss/">
    <metadata>
        <site>zaranna</site>
        <loc>{{ url('/browse') }}</loc>
        <description>Latest pins from our community</description>
        <generated>{{ date('c') }}</generated>
        <count>{{ count($posts) }}</count>
    </metadata>
    
    @foreach ($posts as $post)
        <pin>
            <id>{{ $post->id }}</id>
            <title>{{ $post->title }}</title>
            <description><![CDATA[{{ $post->content }} ]]></description>
            <loc>{{ url('/post/view/' . $post->id) }}</loc>
            <created>{{ $post->time }}</created>
            
            <image>
                <url>{!! asset('storage/' . $post->image_folder) !!}</url>
            </image>
            
            <author>
                <name>{{ $post->user_nickname }}</name>
                <profile>{{ url('/profile/' . $post->user_nickname) }}</profile>
            </author>
            
            <stats>
                <likes>{{ count($post->likes) }}</likes>
                <comments>{{ count($post->comments) }}</comments>
            </stats>
            
            @if ($post->categories)
                <categories>
                    @foreach ($post->categories as $category)
                        @if($category->title !== 'None' && $category->title !== 'none')
                            <category>{{$category->title }}</category>
                        @endif
                    @endforeach
                </categories>
            @endif
        </pin>
    @endforeach

    <pagination>
        <current-page>{{ $posts->currentPage() }}</current-page>
        <total-pages>{{ $posts->lastPage() }}</total-pages>
        <per-page>{{ $posts->perPage() }}</per-page>
        <total-items>{{ $posts->total() }}</total-items>

        @if ($posts->previousPageUrl())
            <prev>{{ $posts->previousPageUrl() }}</prev>
        @endif

        @if ($posts->hasMorePages())
            <next>{{ $posts->nextPageUrl() }}</next>
        @endif
    </pagination>
</pins>

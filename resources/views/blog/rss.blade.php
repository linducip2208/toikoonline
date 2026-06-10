<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ $title }}</title>
    <link>{{ $link }}</link>
    <description>{{ $description }}</description>
    <language>{{ $language }}</language>
    <lastBuildDate>{{ $lastBuildDate }}</lastBuildDate>
    <atom:link href="{{ route('blog.rss') }}" rel="self" type="application/rss+xml"/>
    @foreach($posts as $post)
    <item>
        <title>{{ $post->title }}</title>
        <link>{{ route('blog.show', $post->slug) }}</link>
        <guid>{{ route('blog.show', $post->slug) }}</guid>
        <pubDate>{{ $post->published_at?->toRfc2822String() ?? $post->created_at->toRfc2822String() }}</pubDate>
        <description><![CDATA[{{ $post->short_description ?? Str::limit(strip_tags($post->content), 300) }}]]></description>
        @if($post->featured_image)
        <enclosure url="{{ $post->featured_image }}" type="image/jpeg"/>
        @endif
        <category>{{ $post->category?->name ?? '' }}</category>
    </item>
    @endforeach
</channel>
</rss>

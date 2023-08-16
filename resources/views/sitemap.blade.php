<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    @foreach($metas as $meta)
    <url>
        <loc> {{ $meta['loc'] }} </loc>
        <lastmod> {{ $meta['lastmod'] }} </lastmod>
        <priority> {{ $meta['priority'] }} </priority>
    </url>
    @endforeach
</urlset>
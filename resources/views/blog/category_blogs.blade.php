<ul class="list-unstyled">
	@foreach($categories as $category)
		@if($category->blogs->count())
			<li class="list-item text-gray fw-bold h4"> {{ $category->title }} </li>
			<ul class="list-unstyled my-3">
				@foreach($category->blogs as $article)
					<li class="list-item py-2 ms-4">
						<a href="{{ resolveRoute('blog.article',['id' => $article->id, 'slug' => $article->slug]) }}" class="h4">
							{{ $article->title }}
						</a>
					</li>
				@endforeach
			</ul>
		@endif
		@if($category->child_categories->count())
			@include('blog.category_blogs',['categories' => $category->child_categories])
		@endif
	@endforeach
</ul>
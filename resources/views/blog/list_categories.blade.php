<ul class="list-unstyled">
	@foreach($categories as $category)
	<li class="list-item">
		<a class="common-link" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
	</li>
	@if($category->child_categories->count())
		@include('blog.list_categories',['categories' => $category->child_categories])
	@endif
	@endforeach
</ul>
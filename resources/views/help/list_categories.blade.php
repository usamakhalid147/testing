<ul class="list-unstyled">
	@foreach($categories as $category)
	<li class="list-item">
		<a class="common-link" href="{{ resolveRoute('help.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
	</li>
	@if($category->child_categories->count())
		@include('help.list_categories',['categories' => $category->child_categories])
	@endif
	@endforeach
</ul>
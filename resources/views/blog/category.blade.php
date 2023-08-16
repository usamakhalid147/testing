@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container pt-4">
		<div class="row pb-2 pb-md-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a class="common-link" href="{{ resolveRoute('blog') }}"> @lang('messages.blog_center') </a>
					</li>
					@foreach($blog_categories as $category)
					<li class="breadcrumb-item {{ $loop->last ? 'active' : ''}}">
						@if($loop->last)
							{{ $category->title }}
						@else
							<a class="common-link" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
						@endif
					</li>
					@endforeach
				</ol>
			</nav>
		</div>
		<div class="row pb-2 pb-md-4">
			<div class="col-lg-3 d-lg-block d-md-none">
				<ul class="list-unstyled blog-category-list">
					@foreach($child_categories as $category)
						@if($category->blogs->count() || $category->child_categories->count())
						<li class="list-item">
							<a class="common-link {{ $category->id == $blog_category->id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
						</li>
						@endif
						@if($category->child_categories->count())
							<ul class="list-unstyled ps-4">
								@foreach($category->child_categories as $category)
								@if($category->blogs->count())
								<li class="list-item">
									<a class="common-link {{ $category->id == $blog_category->id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
								</li>
								@endif
								@endforeach
							</ul>
						@endif
					@endforeach
				</ul>
			</div>
			<div class="col-lg-8 col-md-10">
				<div class="blog-question">
					<h3 class="text-gray h1"> {{ $blog_category->title }} </h3>
					<h6 class="text-muted"> {{ $blog_category->description }} </h6>
				</div>
				<div class="blog-answer">
					<ul class="list-unstyled my-3 ms-4">
					@foreach($blog_category->blogs as $article)
						<li class="list-item py-2">
							<a href="{{ resolveRoute('blog.article',['id' => $article->id, 'slug' => $article->slug]) }}" class="h4">
								{{ $article->title }}
							</a>
						</li>
					@endforeach
					</ul>
					@include('blog.category_blogs',['categories' => $blog_category->child_categories])
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
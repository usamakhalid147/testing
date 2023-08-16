@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container">
		<div class="row pb-2 pb-md-4">
			<h2 class="fw-bolder text-black mt-2"> @lang('messages.blog_center') </h2>
			@foreach($popular_categories->chunk(2) as $categories)
			<div class="row g-3">
				@foreach($categories as $category)
				<div class="col-md-6">
					<div class="blog-header hover-border card">
						<a class="card-horizontal" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}">
							<div class="w-100 me-2">
								<img class="img blog-header-image" src="{{ $category->image_src }}">
							</div>
							<div class="card-body mt-4">
								<h4 class="card-titled-block h3 fw-bolder text-gray">{{ $category->title }}</h4>
								<p class="card-text text-muted"> {{ $category->description }} </p>
							</div>
						</a>
					</div>
				</div>
				@endforeach
			</div>
			@endforeach
		</div>
		<div class="row pt-2 pt-md-4">
			<h3 class="h3 fw-bolder"> @lang('messages.popular_blogs') </h3>
			<div class="row mb-3">
				@foreach($popular_blogs as $blog)
				<div class="col-md-3 mb-2">
					<div class="blog-article card hover-underline h-100">
						<a href="{{ resolveRoute('blog.article',['id' => $blog->id, 'slug' => $blog->slug]) }}" class="card-body d-flex align-items-start flex-column justify-content-between">
							<h4 class="card-title mb-1 text-truncate-2" title="{{ $blog->title }}"> {{ $blog->title }} </h4>
							<div class="small text-muted"> {{ $blog->published_at }} </div>
							<p class="text-truncate-3"> {{ $blog->short_answer }} </p>
							<button class="btn btn-sm btn-primary align-self-end"> @lang('messages.read_more') </button>
						</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<div class="row pt-2 pt-md-4">
			<h3 class="h3 fw-bolder"> @lang('messages.latest_blogs') </h3>
			<div class="row mb-3">
				@foreach($latest_blogs as $blog)
				<div class="col-md-3 mb-2">
					<div class="blog-article card hover-underline h-100">
						<a href="{{ resolveRoute('blog.article',['id' => $blog->id, 'slug' => $blog->slug]) }}" class="card-body d-flex align-items-start flex-column justify-content-between">
							<h4 class="card-title mb-1 text-truncate-2" title="{{ $blog->title }}"> {{ $blog->title }} </h4>
							<div class="small text-muted"> {{ $blog->published_at }} </div>
							<p class="text-truncate-3"> {{ $blog->short_answer }} </p>
							<button class="btn btn-sm btn-primary align-self-end"> @lang('messages.read_more') </button>
						</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<div class="row pt-2 pt-md-4">
			<div class="row mb-4">
			@foreach($other_categories as $category)
				@if($category->blogs->count())
					<div class="col-md-3 mb-4">
						<div class="articles">
							<h4 class="article-header text-truncate-4"> {{ $category->title }} </h4>
							<div class="article-links">
								@foreach($category->blogs->take(5) as $article)
								<a href="{{ resolveRoute('blog.article',['id' => $article->id, 'slug' => $article->slug]) }}" class="hover-underline">
									<h6 class="mb-3  text-truncate-2" title="{{ $article->title }}"> {{ $article->title }} </h6>
								</a>
								@endforeach
							</div>
						</div>
					</div>
				@endif
			@endforeach
			</div>
		</div>
	</div>
</main>
@endsection
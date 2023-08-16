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
					@foreach($blog_category as $category)
					<li class="breadcrumb-item">
						<a class="common-link" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
					</li>
					@endforeach
					<li class="breadcrumb-item active">
						{{ $blog->title }}
					</li>
				</ol>
			</nav>
		</div>
		<div class="row pb-2 pb-md-4">
			<div class="col-lg-3 d-none d-lg-block">
				<ul class="list-unstyled blog-category-list">
					@foreach($child_categories as $category)
						<li class="list-item">
							<a class="common-link {{ $category->id == $blog->category_id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
						</li>
						@if($category->child_categories->count())
							<ul class="list-unstyled ps-4">
								@foreach($category->child_categories as $category)
								<li class="list-item">
									<a class="common-link {{ $category->id == $blog->category_id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('blog.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
								</li>
								@endforeach
							</ul>
						@endif
					@endforeach
				</ul>
			</div>
			<div class="col-lg-9 col-md-12 card px-4">
				<div class="blog-question my-4 py-2">
					<h3 class="text-black"> {!! $blog->title !!} </h3>
				</div>
				<div class="blog-answer overflow-hidden">
					{!! $blog->content !!}
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container pt-4">
		<div class="row pb-2 pb-md-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a class="common-link" href="{{ resolveRoute('help') }}"> @lang('messages.help_center') </a>
					</li>
					@foreach($help_category as $category)
					<li class="breadcrumb-item">
						<a class="common-link" href="{{ resolveRoute('help.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
					</li>
					@endforeach
					<li class="breadcrumb-item active">
						{{ $help->title }}
					</li>
				</ol>
			</nav>
		</div>
		<div class="row pb-2 pb-md-4">
			<div class="col-lg-3 d-none d-lg-block">
				<ul class="list-unstyled help-category-list">
					@foreach($child_categories as $category)
						<li class="list-item">
							<a class="common-link {{ $category->id == $help->category_id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('help.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
						</li>
						@if($category->child_categories->count())
							<ul class="list-unstyled ps-4">
								@foreach($category->child_categories as $category)
								<li class="list-item">
									<a class="common-link {{ $category->id == $help->category_id ? 'active fw-bold' : '' }}" href="{{ resolveRoute('help.category',['slug' => $category->slug]) }}"> {{ $category->title }} </a>
								</li>
								@endforeach
							</ul>
						@endif
					@endforeach
				</ul>
			</div>
			<div class="col-lg-9 col-md-12 card px-4">
				<div class="help-question my-4 py-2">
					<h3 class="text-black"> {!! $help->title !!} </h3>
				</div>
				<div class="help-answer overflow-hidden">
					{!! $help->content !!}
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container py-4">
		<div class="mb-4 d-flex align-items-center">
			<h1 class="fw-bold"> @lang('messages.saved') </h1>
			<button class="ms-auto px-4 btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWishlistModal">
				@lang('messages.create_list')
			</button>
		</div>
		<div class="wishlist-container row g-2 g-md-4 mb-2" :class="{'loading' :isLoading }">
			<div class="col-md-4" v-for="list in wishlists">
				<a :href="list.target_link" class="card shadow-card">
				<div class="card-body card-img-top p-0">
					<img v-lazy="list.thumbnail" class="wishlist-image rounded img-fluid mx-auto d-block"/>
				</div>
				<div class="card-footer">
					<h3 class="text-black"> @{{ list.name }} </h3>
					<div class="d-flex">
						<p class="badge lh-sm mb-0 text-muted bg-dark bg-opacity-10 me-2" v-if="list.list_count > 0"> @{{ list.list_count }} @lang('messages.hotels') </p>
						<p class="text-muted mb-0" v-if="list.list_count == 0"> @lang('messages.nothing_saved_yet') </p>
					</div>
				</div>
				</a>
			</div>
			<div class="pt-5 no-wishlists d-none" v-show="wishlists.length == 0 && !isLoading">
				<p class="h5"> @lang('messages.no_wishlist_created') </p>
			</div>
		</div>
	</div>
</main>
@endsection
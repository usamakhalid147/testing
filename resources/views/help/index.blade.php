@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="faq-search-wrap mb-5 py-5 bg-light text-center">
					<div class="container">
						<h1 class="fw-bold text-black"> @lang('messages.frequently_asked_questions') </h1>
						<div class="d-flex justify-content-center mt-5">
							<div class="input-group w-50 mb-3">
								<input type="text" class="form-control form-control-lg" placeholder="Search" aria-label="Search" aria-describedby="search-faq" v-model="search_text" v-on:keyup="searchFilter();">
								<span class="input-group-text" id="search-faq" v-on:click="searchFilter();"><i class="icon icon-arrow-right"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container mt-sm-60 mt-30">
			<div class="row" v-show="!show_result">
				<div class="col-xl-4">
					<div class="card">
						<h4 class="card-header"> @lang('messages.categories') </h4>
						<div class="card-body">
							<ul class="list-group list-group-flush">
								<li class="list-group-item d-flex align-items-center" v-for="category in help_categories">
									<a href="javascript:;" class="common-link" v-on:click="chooseCategory(category)">
										@{{ category.title }}
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-xl-8">
					<div class="card" v-if="currentCategory != 0">
						<h4 class="card-header"> @{{ help_category.title }} </h4>
						<div class="card-body">
							<div class="help-child-categories" v-if="help_category.has_child">
								<ul class="list-group list-group-flush">
									<li class="list-group-item d-flex align-items-center" v-for="category in help_category.child_categories">
										<a href="javascript:;" class="common-link" v-on:click="chooseCategory(category)">
											@{{ category.title }}
										</a>
									</li>
								</ul>
							</div>
							<div class="accordion accordion-flush" id="categoryHelps" v-if="help_category.helps.length > 0">
								<div class="accordion-item mb-3 bg-light border-0" v-for="help in help_category.helps">
									<h2 class="accordion-header">
										<button class="accordion-button collapsed" type="button" v-on:click="toggleHelp(help.id)">
											<i class="icon icon-description"></i>
											<span class="ms-2"> @{{ help.title }} </span>
										</button>
									</h2>
									<div :id="'#category-help-'+help.id" class="accordion-collapse collapse" data-bs-parent="#categoryHelps">
										<div class="accordion-body" v-html="help.content"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card" v-show="currentCategory == 0">
						<h4 class="card-header"> @lang('messages.recommended_questions') </h4>
						<div class="card-body">
							<div class="accordion accordion-flush" id="recommendedHelps">
								@foreach($recommended_helps as $help)
									<div class="accordion-item mb-3 bg-light border-0">
										<h2 class="accordion-header">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#recommended-help-{{ $help->id }}" aria-expanded="false" aria-controls="recommended-help-{{ $help->id }}">
												<i class="icon icon-description"></i>
												<span class="ms-2"> {{ $help->title }} </span>
											</button>
										</h2>
										<div id="recommended-help-{{ $help->id }}" class="accordion-collapse collapse" data-bs-parent="#recommendedHelps">
											<div class="accordion-body">
												{!! $help->content !!}
											</div>
										</div>
									</div>
									<hr/>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row" v-show="show_result">
				<div class="col-xl-12" :class="{'loading' : isLoading}">
					<div class="card">
						<h4 class="card-header"> @lang('messages.results') </h4>
						<div class="card-body">
							<div class="accordion accordion-flush" id="categoryHelps" v-if="help_category.helps.length > 0">
								<div class="accordion-item mb-3 bg-light border-0" v-for="help in help_category.helps">
									<h2 class="accordion-header">
										<button class="accordion-button collapsed" type="button" v-on:click="toggleHelp(help.id)">
											<i class="icon icon-description"></i>
											<span class="ms-2"> @{{ help.title }} </span>
										</button>
									</h2>
									<div :id="'#category-help-'+help.id" class="accordion-collapse collapse" data-bs-parent="#categoryHelps">
										<div class="accordion-body" v-html="help.content"></div>
									</div>
								</div>
							</div>
							<div v-else>
								<span class="text-danger" v-if="error_messages != ''">@{{ error_messages }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		"help_categories" => $help_categories,
	]) !!};
</script>
@endpush
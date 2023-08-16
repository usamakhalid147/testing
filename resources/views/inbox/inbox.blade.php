@extends('layouts.app')
@section('content')
<main id="inbox" role="main" class="main-container pt-4">
	<div class="container">
		<div class="d-flex align-items-center flex-wrap">
			<div class="ms-md-auto col-md-3 col-sm-12 mt-3 mt-md-0 col-12">
				<select class="form-select" class="inbox_filter" name="inbox_filter" v-model="inbox_filter" v-on:change="updateMessages()">
					<option value="all">
						@lang('messages.all_messages')
					</option>
					<option value="starred">
						@lang('messages.starred')
					</option>
					<option value="unread">
						@lang('messages.unread')
					</option>
					<option value="reservations">
						@lang('messages.reservations')
					</option>
					<option value="archive">
						@lang('messages.archived')
					</option>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 inbox-container" :class="{'loading': isLoading}">
				<inbox-thread :messages="messages.data" @inbox_status_updated="updateMessageStatus($event)"  :translations="translations">
				</inbox-thread>
				<pagination :pagination="messages" @paginate="getMessages()" v-show="messages.data.length">
				</pagination>
				<div class="d-flex align-items-center justify-content-center" style="min-height: 150px" :class="{'d-none': messages.data.length > 0 && !isContentLoading && !isLoading}">
					<p class="h5"> @lang('messages.no_new_messages') </p>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		"translations" => $message_text,
	]) !!};
</script>
@endpush
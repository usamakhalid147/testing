@extends('layouts.app')
@section('content')
<main role="main" class="main-container static-pages">
	<div class="container">
		<div class="py-4 text-wrap">
			{!! $content !!}
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	$( document ).ready(function() {
		let isMobile = "{{ session('is_mobile') }}";
		if(isMobile) {
			$('a').attr('href' , 'javascript:void(0)');
		}
	});
</script>
@endpush
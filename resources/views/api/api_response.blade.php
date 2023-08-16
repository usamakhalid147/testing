<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript">
		let return_response = '{!! json_encode($return_response) !!}';
        document.addEventListener('DOMContentLoaded', () => {
	        MessageInvoker.postMessage(return_response);
        });
    </script>
</head>
<body>
<div id="response" style="display:none;">
	{!! json_encode($return_response) !!}
</div>
</body>
</html>
<tr style="padding:0;vertical-align:top;text-align:left">
	<td style="word-wrap:break-word;vertical-align:top;color:#6f6f6f;font-family:'Cereal',Helvetica,Arial,sans-serif;font-weight:normal;padding:0;margin:0;text-align:left;font-size:16px;line-height:19px;border-collapse:collapse!important">
		<div>
			<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0;width:100%;display:table">
				<tbody>
					<tr style="padding:0;vertical-align:top;">
						<th style="font-family:'Cereal',Helvetica,Arial,sans-serif;" >
							<a href="{{ $site_url }}">
								<img alt="{{ $site_name }}" src="{{ $site_logo }}" style="outline:none;text-decoration:none;width:auto;max-width:100%;clear:both;display:block;border:none;padding-bottom:16px;padding-top:25px;max-height:50px;margin: 0 auto;">
							</a>
						</th>
					</tr>
				</tbody>
			</table>
		</div>
	</td>
</tr>
<tr>
	<td align="center">
		@if($header_title != '')
		<h1 style="font-size: 36px;line-height: 40px;margin: 10px 0;"> {{ $header_title }} </h1>
		@endif
		<p style="margin-top: 0;"> {!! $header_subtitle ?? '' !!} </p>
	</td>
</tr>
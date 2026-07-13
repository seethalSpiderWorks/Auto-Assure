<div style="margin-left: {{ $indent ?? 40 }}px; margin-bottom: 10px;">
	<span style="font-weight: 600;">{{ $optionCounter }}. {{ $option->options_name }}</span>
	<div class="mt-2">
		@foreach(['1' => 'Pass', '2' => 'Fail', '3' => 'N/A'] as $val => $label)
			<label class="me-3">
				<input type="radio" name="option_{{ $option->options_id }}" value="{{ $val }}" class="me-1">
				{{ $label }}
			</label>
		@endforeach
	</div>

	@if(!empty($option->options_name_one))
		@php $subPoints = explode('|', $option->options_name_one); @endphp
		<div style="margin-left: 20px; margin-top: 5px;">
			@foreach($subPoints as $index => $subPoint)
				<div style="margin-bottom: 5px;">
					<span>{{ $roman[$index] ?? ($index+1) }}. {{ trim($subPoint) }}</span>
					<span class="ms-2">
						@foreach(['1' => 'Pass', '2' => 'Fail', '3' => 'N/A'] as $val => $label)
							<label class="me-2">
								<input type="radio" name="option_{{ $option->options_id }}_sub_{{ $index }}" value="{{ $val }}" class="me-1"> {{ $label }}
							</label>
						@endforeach
					</span>
				</div>
			@endforeach
		</div>
	@endif
</div>

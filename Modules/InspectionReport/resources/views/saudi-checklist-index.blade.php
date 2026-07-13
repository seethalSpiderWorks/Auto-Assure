<?php 

// categories
$cat = DB::table('watheeq_inspection_category')
    ->select('cat_id', 'cat_name', 'cat_date')
    ->where('cat_status', 0)
    ->get(); 
	
// subcategories
$subcat = DB::table('watheeq_inspection_subcategory')
    ->select('subcat_id', 'subcat_cat_id', 'subcat_name', 'subcat_date')
    ->where('subcat_status', 0)
    ->get();

// third-level categories
$thirdcat = DB::table('watheeq_inspection_thirdcat')
    ->select('thirdcat_id', 'thirdcat_cat_id', 'thirdcat_subcat_id', 'thirdcat_name', 'thirdcat_date')
    ->where('thirdcat_status', 0)
    ->get();

// fourth-level categories (if needed)
$fourthcat = DB::table('watheeq_inspection_fourthcat')
    ->select('fourthcat_id', 'fourthcat_cat_id','fourthcat_subcat_id','fourthcat_thirdcat_id','fourthcat_name','fourthcat_date')
    ->where('fourthcat_status', 0)
    ->get();

// options
$options = DB::table('watheeq_inspection_options')
    ->select('options_id', 'options_cat_id', 'options_subcat_id', 'options_thirdcat_id', 'options_fourthcat_id', 'options_name', 'options_name_one', 'options_date')
    ->where('options_status', 0)
    ->get();
?>


<!-- Saudi Checklist Section -->

<div class="tab-pane fade" id="saudi-checklist" role="tabpanel" aria-labelledby="saudi-checklist-tab">  
	<form method="post" id="saudi-checklistForm">
		<input type='hidden' name='_token' value='{{csrf_token()}}'>
		<input type="hidden" class="edit_id" name="edit_id" > <!-- Edit Report id -->

		@csrf

		@php 
			$letters = range('a','z');
			$roman = ['i','ii','iii','iv','v','vi','vii','viii','ix','x'];
			$pageCounter = 1;
			$optionPerPage = 29; // maximum options per page
			$currentOptionCount = 0;
		@endphp
	
		<div id="pagination-top" class="text-center mb-3"></div> 
		<div id="progress" class="mb-3 text-center fw-bold"></div>

		@foreach($cat as $category)
			@php
				$catSubcats = $subcat->filter(fn($s) => $s->subcat_cat_id == $category->cat_id);
			@endphp

			{{-- Start new page --}}
			<div class="checklist-page" data-page="{{ $pageCounter }}">
			  <h5>{{ $pageCounter }}. {{ $category->cat_name }}</h5>
			@php $subCounter = 1; @endphp

			@if($catSubcats->count() > 0)
				@foreach($catSubcats as $subcategory)
					<h6 class="ms-2">{{ $category->cat_id }}.{{ $subCounter }} {{ $subcategory->subcat_name }}</h6>

					@php
						$thirds = $thirdcat->filter(fn($t) => 
							$t->thirdcat_cat_id == $category->cat_id && 
							$t->thirdcat_subcat_id == $subcategory->subcat_id
						);
					@endphp

					@if($thirds->count() > 0)
						@php $thirdCounter = 1; @endphp
						@foreach($thirds as $third)
							<strong class="ms-3">{{ $category->cat_id }}.{{ $subCounter }}.{{ $thirdCounter }} {{ $third->thirdcat_name }}</strong><br>

							@php
								$fourths = $fourthcat->filter(fn($f) => 
									$f->fourthcat_cat_id == $category->cat_id &&
									$f->fourthcat_subcat_id == $subcategory->subcat_id &&
									$f->fourthcat_thirdcat_id == $third->thirdcat_id
								);
							@endphp

							@if($fourths->count() > 0)
								@php $fourthCounter = 1; @endphp
								@foreach($fourths as $fourth)
									<strong class="ms-4">{{ $category->cat_id }}.{{ $subCounter }}.{{ $thirdCounter }}.{{ $fourthCounter }} {{ $fourth->fourthcat_name }}</strong><br>

									@php $optionCounter = 'a'; @endphp
									@foreach($options->filter(fn($o) => 
										$o->options_cat_id == $category->cat_id &&
										$o->options_subcat_id == $subcategory->subcat_id &&
										$o->options_thirdcat_id == $third->thirdcat_id &&
										$o->options_fourthcat_id == $fourth->fourthcat_id
									) as $option)
										@include('inspectionreport::saudi-checklist-options', [
											'option' => $option, 
											'optionCounter' => $optionCounter, 
											'roman' => $roman, 
											'indent' => 60
										])
										@php 
											$optionCounter++; 
											$currentOptionCount++;
											if($currentOptionCount >= $optionPerPage) {
												$pageCounter++;
												$currentOptionCount = 0;
												echo '</div><div class="checklist-page" data-page="'.$pageCounter.'">';
											}
										@endphp
									@endforeach
									@php $fourthCounter++; @endphp
								@endforeach
							@else
								@php $optionCounter = 'a'; @endphp
								@foreach($options->filter(fn($o) => 
									$o->options_cat_id == $category->cat_id &&
									$o->options_subcat_id == $subcategory->subcat_id &&
									($o->options_thirdcat_id == $third->thirdcat_id || $o->options_thirdcat_id == 0) &&
									($o->options_fourthcat_id == 0 || is_null($o->options_fourthcat_id))
								) as $option)
									@include('inspectionreport::saudi-checklist-options', [
										'option' => $option, 
										'optionCounter' => $optionCounter, 
										'roman' => $roman, 
										'indent' => 40
									])
									@php 
										$optionCounter++;
										$currentOptionCount++;
										if($currentOptionCount >= $optionPerPage) {
											$pageCounter++;
											$currentOptionCount = 0;
											echo '</div><div class="checklist-page" data-page="'.$pageCounter.'">';
										}
									@endphp
								@endforeach
							@endif

							@php $thirdCounter++; @endphp
						@endforeach
					@else
						@php $optionCounter = 'a'; @endphp
						@foreach($options->filter(fn($o) => 
							$o->options_cat_id == $category->cat_id &&
							$o->options_subcat_id == $subcategory->subcat_id &&
							($o->options_thirdcat_id == 0 || is_null($o->options_thirdcat_id)) &&
							($o->options_fourthcat_id == 0 || is_null($o->options_fourthcat_id))
						) as $option)
							@include('inspectionreport::saudi-checklist-options', [
								'option' => $option, 
								'optionCounter' => $optionCounter, 
								'roman' => $roman, 
								'indent' => 40
							])
							@php 
								$optionCounter++;
								$currentOptionCount++;
								if($currentOptionCount >= $optionPerPage) {
									$pageCounter++;
									$currentOptionCount = 0;
									echo '</div><div class="checklist-page" data-page="'.$pageCounter.'">';
								}
							@endphp
						@endforeach
					@endif
					@php $subCounter++; @endphp
				@endforeach
			@else
				@php $optionLetterIndex = 0; @endphp
				@foreach($options->filter(fn($o) => 
					$o->options_cat_id == $category->cat_id &&
					($o->options_subcat_id == 0 || is_null($o->options_subcat_id)) &&
					($o->options_thirdcat_id == 0 || is_null($o->options_thirdcat_id)) &&
					($o->options_fourthcat_id == 0 || is_null($o->options_fourthcat_id))
				) as $option)
					<div style="margin-left: 30px; margin-bottom: 10px;">
						<span style="font-weight: 600;">{{ $letters[$optionLetterIndex] ?? chr(97 + $optionLetterIndex) }}. {{ $option->options_name }}</span>
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
													<input type="radio" name="option_{{ $option->options_id }}_sub_{{ $index }}" value="{{ $val }}" class="me-1">
													{{ $label }}
												</label>
											@endforeach
										</span>
									</div>
								@endforeach
							</div>
						@endif
					</div>
					@php 
						$optionLetterIndex++;
						$currentOptionCount++;
						if($currentOptionCount >= $optionPerPage) {
							$pageCounter++;
							$currentOptionCount = 0;
							echo '</div><div class="checklist-page" data-page="'.$pageCounter.'">';
						}
					@endphp
				@endforeach
			@endif
			</div> {{-- END checklist-page --}}
			@php $pageCounter++; @endphp
		@endforeach

		<div class="mt-4 text-center">
			<div id="pagination-bottom" class="mb-2"></div> <!-- BOTTOM PAGINATION -->
			<button type="button" id="prevPage" class="btn btn-secondary me-2">Previous</button>
			<button type="button" id="nextPage" class="btn btn-primary">Next</button>
			<button type="submit" class="btn btn-success" style="display:none;">Submit Checklist</button>
		</div>
	
		<div class="mb-3">
			<a href="#" onclick="backToTab('saudi-checklistForm','vehicle')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
			
			<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/checklist-store")}}', 'saudi-checklistForm', 'add-modal', 'inspectionReportDataTable','',true,'summary')" class="btn btn-info btn-block" value="Save & Next"> 
		</div>
	<!--</div>-->


	</form> 
</div>  

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pages     = document.querySelectorAll('.checklist-page');
    const nextBtn   = document.getElementById('nextPage');
    const prevBtn   = document.getElementById('prevPage');
    const submitBtn = document.querySelector('button[type="submit"]');
    const progress  = document.getElementById('progress');

    // Top and bottom pagination containers
    const paginationTop    = document.getElementById('pagination-top');
    const paginationBottom = document.getElementById('pagination');

    let currentPage = 0;

    function renderPagination() {
        [paginationTop, paginationBottom].forEach(pagination => {
            if (!pagination) return;
            pagination.innerHTML = '';
            const totalPages = pages.length;
            const maxVisible = 3; // Show 3 numbers at a time

            let start = Math.max(0, currentPage - 1);
            let end = Math.min(totalPages, start + maxVisible);
            if (end - start < maxVisible) start = Math.max(0, end - maxVisible);

            // First page
            if (start > 0) {
                pagination.appendChild(createPageButton(0));
                if (start > 1) {
                    const dots = document.createElement('span');
                    dots.innerText = ' … ';
                    pagination.appendChild(dots);
                }
            }

            // Middle pages
            for (let i = start; i < end; i++) {
                pagination.appendChild(createPageButton(i));
            }

            // Last page
            if (end < totalPages) {
                if (end < totalPages - 1) {
                    const dots = document.createElement('span');
                    dots.innerText = ' … ';
                    pagination.appendChild(dots);
                }
                pagination.appendChild(createPageButton(totalPages - 1));
            }
        });
    }

    function createPageButton(i) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerText = i + 1;
        btn.className = i === currentPage ? 'btn btn-primary me-1' : 'btn btn-outline-primary me-1';
        btn.addEventListener('click', () => {
            currentPage = i;
            showPage(currentPage);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        return btn;
    }

    function showPage(index) {
        pages.forEach((page, i) => {
            page.style.display = i === index ? 'block' : 'none';
        });

        prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = index === pages.length - 1 ? 'none' : 'inline-block';
        if (submitBtn) submitBtn.style.display = index === pages.length - 1 ? 'inline-block' : 'none';
        if (progress) progress.innerText = `Page ${index + 1} of ${pages.length}`;

        renderPagination();
    }

    nextBtn.addEventListener('click', () => {
        if (currentPage < pages.length - 1) {
            currentPage++;
            showPage(currentPage);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    showPage(currentPage);
});
</script> 
<span style="font-size:11px;">
		<?php 
			if($id == 1)
			{
				$cDate = date('m-d-Y');
				echo "From ".$cDate;
			}
		   else 
		   {
			   
			   echo "From ".date('d-m-Y',strtotime($startDateString))." to ". date('d-m-Y',strtotime($endDateString));
		   }
		
		?>
	
</span>
@if(count($course) > 0)
@foreach($course as $value)

	<div class="row align-items-center g-0 mt-3">
		<div class="col-sm-3">
			<p class="text-truncate mt-1 mb-0" title="{{ $value->course_name}}"><i class="mdi mdi-circle-medium text-{{ $value->course_color_name }} me-2"></i> {{ $value->course_name}}</p>
		</div>

		<?php 
		$totLead = $totalLead->totCount;
		$privilege = Auth::user()->previlage;
		$centre = session('application_branch');
		$date = date('Y-m-d');
		$leadCourse = $percentage = 0;
		

		$courseLead = DB::table('tbl_lead')
			->select(DB::raw('COUNT(CASE WHEN lead_date_on >="'.$startDateString.'" AND lead_date_on <="'.$endDateString.'" THEN 1 ELSE null END) courseCount'))
			->where('lead_status',0)
			->where('lead_course_id',$value->course_id);	
		if($privilege != 2)
		{
			$courseLead->where('lead_branch_id',$centre);
		}
		$courseLead=$courseLead->first();
		if($courseLead) { 
			$leadCourse = $courseLead->courseCount;
		}
		if($totLead != 0 && $totLead != null)
		{
			$percentage = ($leadCourse /$totLead)*100;
		}


		?>

		<div class="col-sm-9">
			<div class="progress mt-1" style="height: 6px;">
				<div  class="progress-bar progress-bar bg-{{ $value->course_color_name }}" role="progressbar"
					 style="width: {{$percentage}}%" aria-valuenow="52" aria-valuemin="0"
					 aria-valuemax="{{ $percentage }}"  data-toggle="tooltip" data-placement="top" data-content="{{ $percentage }}%" title="{{ $percentage }}%">
				</div>
			</div>
		</div>
</div> <!-- end row-->

	@endforeach
@endif
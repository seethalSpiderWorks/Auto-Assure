<style>
.tick 
{
    display: inline-flex;
    width: 20px;
    height: 20px;
    background-image: url(../crm/img/icons/check.svg);
    background-size: contain;
}

.tick.not {
    background-image: url(../crm/img/icons/close-red.svg);
}
</style>

<!--<h5> Inspection Report View</h5>-->

<table class="table tbl-u-boarderd">
	
	<tr>
	    <h4> Basic Details </h4>
		<th class="mdl-td-c"> Reference no </th> <th>:</th>
		<td><?= $data->report_reference_no; ?></td>
		<td class="mdl-td"></td>
	</tr>

	<tr> 
		<th class="mdl-td-c"> Name </th> <th>:</th>
		<td><?=$data->report_client_name;?></td>
		<td class="mdl-td"></td>
		<?php 
		if($data->report_client_name_ar) { ?>
			<th class="mdl-td-c"> Name in Arabic  </th> <th>:</th>
			<td><?= $data->report_client_name_ar; ?></td>
			<td class="mdl-td"></td>
			<?php 
		} ?>
	</tr>

	<tr> 
		<th class="mdl-td-c"> Date of Inspection </th> <th>:</th>
		<td><?= date("d-m-Y", strtotime($data->report_date_of_inspection));?></td>
		<td class="mdl-td"></td>
		<?php 
		if($data->report_vehicle_plate_no) { ?>
			<th class="mdl-td-c"> Plate Number </th> <th>:</th>
			<td> {{$data->report_vehicle_plate_no}} </td>
			<td class="mdl-td"></td>
			<?php 
		} ?>
	</tr> 

	<?php 
	$report_unique_id = $data->report_unique_id;
	$report_random_id = $data->report_unique_id_random;
	//$unique_id =  MD5($report_unique_id);   
	if($report_random_id != null) 
	{ ?>
		<tr> 
    		<th class="mdl-td-c"> English Link </th> <th>:</th>
			<td><a href="https://auto-assure.com/report/inspection/<?php echo $report_random_id;?>" target="_blank">
    		    https://auto-assure.com/report/inspection/<?php echo $report_random_id;?> </a> 
			</td>
			<td class="mdl-td"></td>
				<!---- Arabic Link ----><!-- display:none -->
			<th class="mdl-td-c"> Arabic Link </th> <th>:</th>
			<td><a href="https://auto-assure.com/report/inspection-ar/<?php echo $report_random_id;?>" target="_blank"> 
			    https://auto-assure.com/report/inspection-ar/<?php echo $report_random_id;?>  </a>
			</td>
    	</tr> 
		<?php
	} ?>
</table>

<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>

<table class="table tbl-u-boarderd">
	<tr><h4> Vehicle Info </h4>
		<th class="mdl-td-c"> Title </th> <th>:</th>
		<td><?= $data->vehicle_info_title; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Model Year </th> <th>:</th>
		<td><?=$data->vehicle_info_model_year;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Manufacturing Year </th> <th>:</th>
		<td><?= $data->vehicle_info_manuf_year; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Chassis Number </th> <th>:</th>
		<td><?=$data->vehicle_info_chassis_no;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c">Odometer</th> <th>:</th>
		<td><?= $data->vehicle_info_odometer; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Condition</th> <th>:</th>
		<td><?php 
			if($data->vehicle_info_condition == 1)
			{  
				echo "Used"; 
			}
			else if($data->vehicle_info_condition == 2)
			{ 
				echo "New"; 
			} 
			else{
			}?>
		</td>
	</tr>
</table>

<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
 
<!--------- Additional Specs --------->
<table class="table tbl-u-boarderd">
	<tr><h4> Additional Specs </h4>
		<th class="mdl-td-c"> Region </th> <th>:</th>
		<td><?= $data->add_spec_region; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Exterior Color </th> <th>:</th>
		<td><?=$data->exte_color_name;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Interior Color </th> <th>:</th>
		<td><?= $data->inte_color_name; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Gearbox </th> <th>:</th>
		<td><?=$data->gearbox_type_name;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Fuel Type </th> <th>:</th>
		<td><?= $data->fuel_type_name; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Steering Side </th> <th>:</th>
		<td><?=$data->steering_side_name;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Cylinders </th> <th>:</th>
		<td><?= $data->add_spec_cylinders; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Engine Size </th> <th>:</th>
		<td><?=$data->add_spec_engine_size;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Keys </th> <th>:</th>
		<td><?= $data->add_spec_keys; ?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c"> Doors </th> <th>:</th>
		<td><?=$data->add_spec_doors;?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Seats </th> <th>:</th>
		<td><?= $data->add_spec_seats; ?></td>
	</tr>
</table>

<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>

<!--------- Warranty / Services --------->
<table class="table tbl-u-boarderd">
	<tr><h4> Warranty / Services </h4>
		<th class="mdl-td-c"> With Service History </th> <th>:</th>
		<td><?= $data->war_service_history; ?></td>
	</tr>
	<tr> 
		<th class="mdl-td-c"> Last Service </th> <th>:</th>
		<td><?=$data->war_service_last;?></td>
		<td class="mdl-td"></td>
		<?php if($data->war_service_next){ ?>
		    <th class="mdl-td-c"> Next Service Due </th> <th>:</th>
		    <td><?= $data->war_service_next; ?></td>
		    <?php 
		} ?>
	</tr>
</table>
<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>

<!--------- Vehicle Overview --------->
<table class="table tbl-u-boarderd">
    <?php 
    if($data->overview_english) { ?>
    	<tr><h4> Vehicle Overview</h4>
    		<th class="mdl-td-c"> Overview in English </th> <th>:</th>
    		<td><?= $data->overview_english; ?></td>
    	</tr>
	<?php 
    }
	if($data->overview_arabic) { ?>
		<tr> 
			<th class="mdl-td-c"> Overview in Arabic </th> <th>:</th>
			<td><?=$data->overview_arabic;?></td>
		</tr> <?php 
	} ?>
</table>
<?php 
if($data->overview_english || $data->overview_arabic) { ?>
    <p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
    <?php 
} ?>
<!--------- Inspection Summary --------->
    @php
		$summary_types = DB::table('tbl_summary_type')
				->select('summary_type_id', 'summary_type_name', 'summary_type_name_ar')
				->where('summary_type_status', 0)
				->get();
	@endphp

    	<h4>Inspection Summary</h4>

		<table class="table tbl-u-boarderd" style="">
            <tbody>
                @foreach ($summary_types as $stype)
                    @php
                        $summary_items = DB::table('tbl_report_insp_summary')
                            ->select('insp_summary_desc', 'insp_summary_desc_ar')
                            ->where('insp_summary_status', 0)
                            ->where('insp_summary_report_id', $data->report_id)
                            ->where('insp_summary_type', $stype->summary_type_id)
                            ->get();
                    @endphp
        
                    @if ($summary_items->where('insp_summary_desc', '!=', '')->count() > 0)
                        <tr>
                            <th style="">{{ $stype->summary_type_name }}</th>
                        </tr>
        
                        @foreach ($summary_items as $summary)
                            @if (!empty($summary->insp_summary_desc))
                                <tr>
                                    <td>
                                        <b> • </b> {{ $summary->insp_summary_desc }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>

<!--------- Inspection Gallery Start --------->
<table class="table tbl-u-boarderd">
	<h4> Inspection Gallery </h4>
	<?php 
	$gallery_type = DB::table('tbl_gallery_type')
			->select('gallery_type_id','gallery_type_name')
			->where('gallery_type_status',0)
			->get();
	
	foreach($gallery_type as $gtype)
	{ 
		$insGall = DB::table('tbl_report_gallery')
			->select('gallery_image','gallery_image_type')
            ->where('gallery_report_id',$data->report_id)
            ->where('gallery_image_type',$gtype->gallery_type_id)
            ->where('gallery_status',0)
            ->first();  
		
		$insp_gallery = DB::table('tbl_report_gallery')
			->select('gallery_image','gallery_image_type','gallery_image_desc')
            ->where('gallery_report_id',$data->report_id)
            ->where('gallery_image_type',$gtype->gallery_type_id)
            ->where('gallery_status',0)
            ->get(); 
			
 		if($insGall) 
		{	?>
			<p style="margin-top:15px;"><b> <?php echo $gtype->gallery_type_name; ?> </b></p> <?php  
		}?>
		
		<div class="row"> <?php
			foreach($insp_gallery as $gallery)
			{ ?> 
				
				<div class="col-sm-2"> 
					<a href="https://auto-assure.com/crm/uploads/inspectionreport/gallery/<?php echo $gallery->gallery_image;?>" title="Image" target="_blank"><img style="width:150px;height:150px;" class="img-circle" src="https://auto-assure.com/crm/uploads/inspectionreport/gallery/<?php echo $gallery->gallery_image;?>" style="max-width:100%"> </a> &nbsp;
					
					<p> <?php echo $gallery->gallery_image_desc; ?> </p>
				</div>	<?php
			} ?> 
		</div> <?php
	}?>			 
	 	
</table>

<table class="table tbl-u-boarderd" style="display:none;">
	<!-- <h4> Inspection Gallery </h4> -->
	<?php 
	$insp_gallery = DB::table('tbl_report_gallery')
              ->select('gallery_image','gallery_image_type')
              ->where('gallery_report_id',$data->report_id)
              ->where('gallery_status',0)
              ->get();  
	foreach($insp_gallery as $gallery)
	{ ?>
		<!--<a href="https://auto-assure.com/crm/uploads/inspectionreport/gallery/<?php echo $gallery->gallery_image;?>" title="Image" target="_blank"><img style="width:100px;height:100px;" class="img-circle" src="https://auto-assure.com/crm/uploads/inspectionreport/gallery/<?php echo $gallery->gallery_image;?>" style="max-width:100%"></a> --> &nbsp; <?php
	}?>
</table>
<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
<!--------- Inspection Gallery End --------->


<!--------- Saudi Checklist Start --------->
<?php
$cat = DB::table('watheeq_inspection_category')
	->select('cat_id','cat_name')
	->where('cat_status', 0)
	->get();

$subcat = DB::table('watheeq_inspection_subcategory')
	->select('subcat_id', 'subcat_cat_id', 'subcat_name')
	->where('subcat_status', 0)
	->get();

$thirdcat  = DB::table('watheeq_inspection_thirdcat')
	->select('thirdcat_id','thirdcat_cat_id', 'thirdcat_subcat_id', 'thirdcat_name')
	->where('thirdcat_status', 0)
	->get();

$fourthcat = DB::table('watheeq_inspection_fourthcat')
	->select('fourthcat_id','fourthcat_cat_id', 'fourthcat_subcat_id', 'fourthcat_thirdcat_id', 'fourthcat_name')
	->where('fourthcat_status', 0)
	->get();

$options = DB::table('watheeq_inspection_options')
	->select('options_id','options_cat_id', 'options_subcat_id', 'options_thirdcat_id', 'options_fourthcat_id', 'options_name', 'options_name_one')
	->where('options_status', 0)
	->get();

$record = DB::table('watheeq_inspection_answers')
	->select('answers_id', 'answers_report_id', 'answers')
    //->where('answers_user_id', auth()->id())
    ->where('answers_report_id', $data->report_id)
    ->first();

$answers = json_decode($record->answers ?? '{}', true);
?>

@php 
$letters = range('a','z');
$pageCounter = 1;

// Helper: check if a category/subcategory/etc. has Pass/Fail options
function hasOptions($categoryId, $subcatId, $thirdcatId, $fourthcatId, $options, $answers) {
    return $options->filter(fn($o) =>
        $o->options_cat_id == $categoryId &&
        $o->options_subcat_id == $subcatId &&
        $o->options_thirdcat_id == $thirdcatId &&
        $o->options_fourthcat_id == $fourthcatId &&
        in_array($answers['option_'.$o->options_id] ?? 0, [1,2])
    )->count() > 0;
}

// Helper: tick icon span
function tickSpan($val) {
    return $val == 1
        ? '<span class="tick"></span>'      // Pass
        : '<span class="tick not"></span>'; // Fail
}
@endphp

<!-------- Header -------->
@php
    $answers = json_decode($record->answers ?? '{}', true);
    $hasAnyAnswers = collect($answers)
        ->filter(fn($v) => in_array($v, [1,2]))
        ->count() > 0;
@endphp

@if($hasAnyAnswers)
    <h4>Checklists</h4>
@endif
<!-------- Header -------->

@foreach($cat as $cIndex => $category)
    @php
        $catHasOptions = hasOptions($category->cat_id, 0, 0, 0, $options, $answers);

        $catSubcats = $subcat->filter(fn($s) => 
            $s->subcat_cat_id == $category->cat_id &&
            (hasOptions($category->cat_id, $s->subcat_id, 0, 0, $options, $answers) ||
             $thirdcat->filter(fn($t) => 
                 $t->thirdcat_cat_id == $category->cat_id &&
                 $t->thirdcat_subcat_id == $s->subcat_id &&
                 (hasOptions($category->cat_id, $s->subcat_id, $t->thirdcat_id, 0, $options, $answers) ||
                  $fourthcat->filter(fn($f) => 
                      $f->fourthcat_cat_id == $category->cat_id &&
                      $f->fourthcat_subcat_id == $s->subcat_id &&
                      $f->fourthcat_thirdcat_id == $t->thirdcat_id &&
                      hasOptions($category->cat_id, $s->subcat_id, $t->thirdcat_id, $f->fourthcat_id, $options, $answers)
                  )->count() > 0
                 )
             )->count() > 0
            )
        );
    @endphp

    @if($catHasOptions || $catSubcats->count() > 0)
        {{-- Top-level Category --}}
        <h5>{{ $pageCounter }}. {{ $category->cat_name }}</h5>

        {{-- Category-level options --}}
        @foreach($options->where('options_cat_id', $category->cat_id)
                        ->where('options_subcat_id', 0)
                        ->where('options_thirdcat_id', 0)
                        ->where('options_fourthcat_id', 0) as $o)
            @if(in_array($answers['option_'.$o->options_id] ?? 0, [1,2]))
                <div class="ms-3">
                    {!! tickSpan($answers['option_'.$o->options_id]) !!}
                    {{ $letters[$loop->index] ?? chr(97+$loop->index) }}. {{ $o->options_name }}
                </div>
            @endif
        @endforeach

        {{-- Subcategories --}}
        @foreach($catSubcats as $subcategory)
            @php
                $thirds = $thirdcat->filter(fn($t) => 
                    $t->thirdcat_cat_id == $category->cat_id &&
                    $t->thirdcat_subcat_id == $subcategory->subcat_id &&
                    (hasOptions($category->cat_id, $subcategory->subcat_id, $t->thirdcat_id, 0, $options, $answers) ||
                     $fourthcat->filter(fn($f) => 
                         $f->fourthcat_cat_id == $category->cat_id &&
                         $f->fourthcat_subcat_id == $subcategory->subcat_id &&
                         $f->fourthcat_thirdcat_id == $t->thirdcat_id &&
                         hasOptions($category->cat_id, $subcategory->subcat_id, $t->thirdcat_id, $f->fourthcat_id, $options, $answers)
                     )->count() > 0
                    )
                );
            @endphp

            @if(hasOptions($category->cat_id, $subcategory->subcat_id, 0, 0, $options, $answers) || $thirds->count() > 0)
                {{-- Subcategory title --}}
                <h6 class="ms-2">
                    {{ $pageCounter }}.{{ $loop->iteration }} {{ $subcategory->subcat_name }}
                </h6>

                {{-- Subcategory-level options --}}
                @foreach($options->where('options_cat_id', $category->cat_id)
                                ->where('options_subcat_id', $subcategory->subcat_id)
                                ->where('options_thirdcat_id', 0)
                                ->where('options_fourthcat_id', 0) as $o)
                    @if(in_array($answers['option_'.$o->options_id] ?? 0, [1,2]))
                        <div class="ms-3">
                            {!! tickSpan($answers['option_'.$o->options_id]) !!}
                            {{ $letters[$loop->index] ?? chr(97+$loop->index) }}. {{ $o->options_name }}
                        </div>
                    @endif
                @endforeach

                {{-- Third-level --}}
                @foreach($thirds as $third)
                    @php
                        $fourths = $fourthcat->filter(fn($f) => 
                            $f->fourthcat_cat_id == $category->cat_id &&
                            $f->fourthcat_subcat_id == $subcategory->subcat_id &&
                            $f->fourthcat_thirdcat_id == $third->thirdcat_id &&
                            hasOptions($category->cat_id, $subcategory->subcat_id, $third->thirdcat_id, $f->fourthcat_id, $options, $answers)
                        );
                    @endphp

                    @if(hasOptions($category->cat_id, $subcategory->subcat_id, $third->thirdcat_id, 0, $options, $answers) || $fourths->count() > 0)
                        {{-- Third-level title --}}
                        <strong class="ms-3">
                            {{ $pageCounter }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }} {{ $third->thirdcat_name }}
                        </strong><br>

                        {{-- Third-level options --}}
                        @foreach($options->where('options_cat_id', $category->cat_id)
                                        ->where('options_subcat_id', $subcategory->subcat_id)
                                        ->where('options_thirdcat_id', $third->thirdcat_id)
                                        ->where('options_fourthcat_id', 0) as $o)
                            @if(in_array($answers['option_'.$o->options_id] ?? 0, [1,2]))
                                <div class="ms-4">
                                    {!! tickSpan($answers['option_'.$o->options_id]) !!}
                                    {{ $letters[$loop->index] ?? chr(97+$loop->index) }}. {{ $o->options_name }}
                                </div>
                            @endif
                        @endforeach

                        {{-- Fourth-level --}}
                        @foreach($fourths as $fourth)
                            @php
                                $fourthOptions = $options->where('options_cat_id', $category->cat_id)
                                                         ->where('options_subcat_id', $subcategory->subcat_id)
                                                         ->where('options_thirdcat_id', $third->thirdcat_id)
                                                         ->where('options_fourthcat_id', $fourth->fourthcat_id)
                                                         ->filter(fn($o) => in_array($answers['option_'.$o->options_id] ?? 0, [1,2]));
                            @endphp
                            @if($fourthOptions->count() > 0)
                                <strong class="ms-4">
                                    {{ $pageCounter }}.{{ $loop->parent->parent->iteration }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }} {{ $fourth->fourthcat_name }}
                                </strong><br>
                                @foreach($fourthOptions as $o)
                                    <div class="ms-5">
                                        {!! tickSpan($answers['option_'.$o->options_id]) !!}
                                        {{ $letters[$loop->index] ?? chr(97+$loop->index) }}. {{ $o->options_name }}
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        @php $pageCounter++; @endphp
    @endif
@endforeach
<!--------- Saudi Checklist End --------->

<!--------- Vehicle Specifications --------->
<?php 
$vehiclespec = DB::table('tbl_report_vehicle_spec')
	->select('vehicle_spec_report_id','hill_start_assist','launch_control','child_safety_seats','rear_parking_sensors','anti_lock_brakes','ebd','alarm','front_airbags','side_airbags','traction_control_sys','anti_glare_rear_view','tire_pressure_monitor','cd_player','mp_player','aux_audio_system','winch','body_kit','lift_kit','leather_seats','rear_seat_enter_sys','parking_sensors','rear_view_camera','navigation','fire_extinguisher')
	->where('vehicle_spec_report_id',$data->report_id)
	->where('vehicle_spec_status',0)
	->first();   
	
$dataOne = DB::table('tbl_report_spec_check_one')
	->select('one_id','one_status','one_report_id','air_suspension','adaptive_air_suspension','differential_lock','paddle_shifters','tiptronic','hill_descent_assist','hill_start_assist','auto_hold','comfort_seats','sport_seats','sport_brakes','sport_suspension','sport_exhaust','lane_change','launch_control','child_safety_seats','front_view_camera','rear_view_camera','degree_camera','front_parking_sensors','rear_parking_sensors','lane_departure','anti_lock_brakes','ebd','alarm','front_airbags','side_airbags','traction_control_sys','park_assist','blind_spot_monitor','tire_pressure_monitor','anti_glare_rear_view','winch','body_kit_aaa','lift_kit_aaa','leather_seats_aaa','rear_seat_enter_sys_aaa','parking_sensors','rear_view_camera_aaa','navigation_aaa','fire_extinguisher')
	->where('one_report_id',$data->report_id)
	->where('one_status',0)
	->first();
	
$dataTwo = DB::table('tbl_report_spec_check_two')
	->select('two_id','two_status','two_report_id','digital_driver_display','cd_player','dvd_player','mp_player', 'sd_card_player','bluetooth_interface','premium_sound_system','aux_audio_system','usb','usb_c','touch_screen', 'rear_seat_enter_sys','wireless','ambient_lighting','apple_carplay','navigation','standard_ac','dual_climcont_ac','multi_climcont_ac','keyless_entry','keyless_start','power_steering','heads_up_display','cruise_control','adaptive_cruise_control','seat_cooling_front','seat_cooling_rear','seat_massage_front','seat_massage_rear','driver_memory_seat','passenger_memory_seat','power_driver_seats','power_passenger_seats','power_rear_seats','power_front_windows','power_rear_windows','power_trunk','power_locks','power_mirrors','power_folding_mirrors','sun_roof','panoramic_roof','cool_box','seat_heated_front','auto_park','remote_start_engine','soft_close_doors','adaptive_lights','night_vision','captain_rear_seats','leather_seats','leather_fabric','body_kit','lift_kit','front_spoiler','rear_spoiler','fog_light_front','roof_carrier','halogen_headlight','led_headlight','xenon_headlight','trailer_hook_coupling')
	->where('two_report_id',$data->report_id)
	->where('two_status',0)
	->first();  ?>
	
	
<?php
function hasValid($data, $fields) {
    foreach ($fields as $f) {
        if (isset($data->$f) && $data->$f != null && $data->$f != 3) {
            return true;
        }
    }
    return false;
}


?>
<!------------ Performance ------------->	
<?php
if($dataOne != null) 
{ 
    $performanceFields = [
        'air_suspension','adaptive_air_suspension','differential_lock','paddle_shifters',
        'tiptronic','hill_descent_assist','hill_start_assist','auto_hold','comfort_seats',
        'sport_seats','sport_brakes','sport_suspension','sport_exhaust','lane_change',
        'launch_control']; 
        
    $safetyFields = [
        'child_safety_seats','front_view_camera','rear_view_camera','degree_camera',
        'front_parking_sensors','rear_parking_sensors','lane_departure','anti_lock_brakes',
        'ebd','alarm','front_airbags','side_airbags','traction_control_sys','park_assist',
        'blind_spot_monitor','tire_pressure_monitor','anti_glare_rear_view'];   
        
    $featureFields = [
        'winch', 'body_kit_aaa', 'lift_kit_aaa', 'leather_seats_aaa', 'parking_sensors', 
        'rear_view_camera_aaa', 'navigation_aaa', 'fire_extinguisher', 'rear_seat_enter_sys_aaa'];
        
    $hasSpecs = hasValid($dataOne, $performanceFields) || hasValid($dataOne, $safetyFields) || hasValid($dataOne, $featureFields);  
?>
	<table class="table tbl-u-boarderd">
        <?php if ($hasSpecs) { ?>
	        <tr> <h4> Vehicle Specifications </h4> </tr>
        <?php } ?>
        
        <?php if (hasValid($dataOne, $performanceFields)) { ?>
		    <div class="row"> <h5> Performance </h5> </div>
		<?php } ?>
		<div class="row">
		    
		    <?php 
		    if($dataOne->air_suspension != null && $dataOne->air_suspension != 3)
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->air_suspension == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->air_suspension == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Air suspension
    				</div>
    			</div> <?php
		    }
		    if($dataOne->adaptive_air_suspension != null && $dataOne->adaptive_air_suspension != 3)
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->adaptive_air_suspension == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->adaptive_air_suspension == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Adaptive Air Suspension
    				</div>
    			</div> <?php
		    }
		    if($dataOne->differential_lock != null && $dataOne->differential_lock != 3)
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->differential_lock == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->differential_lock == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Differential lock
    				</div>
    			</div> <?php 
		    }
		    
		    if($dataOne->paddle_shifters != null && $dataOne->paddle_shifters != 3) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->paddle_shifters == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->paddle_shifters == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Paddle shifters
    				</div>
    			</div>  <?php 
		    } 
		    
		    if($dataOne->tiptronic != null && $dataOne->tiptronic != 3) 
		    { ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->tiptronic == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->tiptronic == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Tiptronic
    				</div>
    			</div> <?php
		    }
		    
			if($dataOne->hill_descent_assist != null && $dataOne->hill_descent_assist != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->hill_descent_assist == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->hill_descent_assist == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Hill descent assist
    				</div>
    			</div>  <?php 
			} 
			
			if($dataOne->hill_start_assist != null && $dataOne->hill_start_assist != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->hill_start_assist == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->hill_start_assist == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Hill Start Assist 
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->auto_hold != null && $dataOne->auto_hold != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->auto_hold == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->auto_hold == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Auto hold
    				</div>
    			</div>  <?php 
			}
			
			if($dataOne->comfort_seats != null && $dataOne->comfort_seats != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->comfort_seats == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->comfort_seats == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Comfort seats
    				</div>
    			</div> <?php 
			}
			
			if($dataOne->sport_seats != null && $dataOne->sport_seats != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->sport_seats == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->sport_seats == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sport seats
    				</div>
    			</div>  <?php 
			} 
			
			if($dataOne->sport_brakes != null && $dataOne->sport_brakes != 3) 
			{  ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->sport_brakes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->sport_brakes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sport brakes
    				</div>
    			</div>  <?php 
			}
			
			if($dataOne->sport_suspension != null && $dataOne->sport_suspension != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->sport_suspension == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->sport_suspension == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sport suspension
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->sport_exhaust  != null && $dataOne->sport_exhaust  != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->sport_exhaust == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->sport_suspension == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sport exhaust
    				</div>
    			</div> <?php 
			}
			
			if($dataOne->lane_change != null && $dataOne->lane_change != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->lane_change == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->lane_change == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Lane change
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->launch_control != null && $dataOne->launch_control != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->launch_control == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->launch_control == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					}  ?> Launch Control
    				</div>
    			</div> <?php   
			} ?>
		</div>
 		  
		<!------------ Safety ------------>
		<?php if (hasValid($dataOne, $safetyFields)) { ?>
		<div class="row"><h5> Safety </h5></div>	
		<?php } ?>
		
		<div class="row"> 
 			<?php 
 			if($dataOne->child_safety_seats != null && $dataOne->child_safety_seats != 3)
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->child_safety_seats == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->child_safety_seats == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Child Safety Seats (ISOFIX)
    				</div>
    			</div>  <?php 
 			}
 			
 			if($dataOne->front_view_camera != null && $dataOne->front_view_camera != 3) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->front_view_camera == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->front_view_camera == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Front View Camera
    				</div>
    			</div> <?php 
 			} 
 			
 			if($dataOne->rear_view_camera != null && $dataOne->rear_view_camera != 3) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->rear_view_camera == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->rear_view_camera == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Rear View Camera
    				</div>
    			</div> <?php 
 			}
 			
 			if($dataOne->degree_camera != null && $dataOne->degree_camera != 3) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->degree_camera == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->degree_camera == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> 360 Degree Camera
    				</div>
    			</div> <?php 
 			} 
 			
 			if($dataOne->front_parking_sensors != null && $dataOne->front_parking_sensors != 3) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->front_parking_sensors == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->front_parking_sensors == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Front parking sensors
    				</div>
    			</div> <?php
 			} 
 			
 			if($dataOne->rear_parking_sensors != null && $dataOne->rear_parking_sensors != 3) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  <?php 
    					if($dataOne->rear_parking_sensors == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->rear_parking_sensors == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Rear Parking Sensors
    				</div>
    			</div> <?php 
 			} 
 			
 			if($dataOne->lane_departure != null && $dataOne->lane_departure != 3 ) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  <?php 
    					if($dataOne->lane_departure == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->lane_departure == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Lane departure
    				</div>
    			</div> <?php 
 			} 
 			
 			if($dataOne->anti_lock_brakes != null && $dataOne->anti_lock_brakes != 3 ) 
 			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  <?php 
    					if($dataOne->anti_lock_brakes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->anti_lock_brakes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Anti-Lock Brakes (ABS)
    				</div>
    			</div> <?php 
 			} 
 			
 		 	if($dataOne->anti_lock_brakes != null && $dataOne->anti_lock_brakes != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->ebd == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->ebd == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> EBD
    				</div>
    			</div> <?php 
			}
			
			if($dataOne->alarm != null && $dataOne->alarm != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->alarm == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->alarm == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Alarm
    				</div>
    			</div> <?php 
			} 
			
		    if($dataOne->front_airbags != null && $dataOne->front_airbags != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->front_airbags == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->front_airbags == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Front Airbags
    				</div>
    			</div> <?php 
			}  
			
			if($dataOne->side_airbags != null && $dataOne->side_airbags != 3)
		    { ?>
			<div class="col-sm-2">
				<div class="mb-3">  <?php 
					if($dataOne->side_airbags == 1)
					{ ?>
						<span class="tick"></span> <?php 
					} 
					elseif($dataOne->side_airbags == 2)
					{ ?>
						<span class="tick not"></span> <?php
					} ?> Side Airbags
				</div>
			</div> <?php 
			} 
			
			if($dataOne->traction_control_sys != null && $dataOne->traction_control_sys != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->traction_control_sys == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->traction_control_sys == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?>Traction Control System
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->park_assist != null && $dataOne->park_assist != 3)
			{?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->park_assist == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->park_assist == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Park assist
    				</div>
    			</div> <?php 
			}   
			
			if($dataOne->blind_spot_monitor != null && $dataOne->blind_spot_monitor != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->blind_spot_monitor == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->blind_spot_monitor == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					}  ?> Blind spot monitor
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->tire_pressure_monitor != null && $dataOne->tire_pressure_monitor != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataOne->tire_pressure_monitor == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->tire_pressure_monitor == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?>Tire Pressure Monitor
    				</div>
    			</div> <?php 
			} 
			
			if($dataOne->anti_glare_rear_view != null && $dataOne->anti_glare_rear_view != 3 ) 
			{ ?>
    			<div class="col-sm-3">
    				<div class="mb-3"> 
    					<?php 
    					if($dataOne->anti_glare_rear_view == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataOne->anti_glare_rear_view == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?>Anti Glare Rear View Mirror
    				</div>
    			</div> <?php 
			} ?>
			
		</div> <?php 
} 
if($dataTwo != null) 
{
    $entertainmentFields = [
    'digital_driver_display','cd_player','dvd_player','mp_player','sd_card_player',
    'bluetooth_interface','premium_sound_system','aux_audio_system','usb','usb_c',
    'touch_screen','rear_seat_enter_sys','wireless','ambient_lighting','apple_carplay',
    'navigation','standard_ac','dual_climcont_ac','multi_climcont_ac','keyless_entry',
    'keyless_start','power_steering','heads_up_display','cruise_control',
    'adaptive_cruise_control','seat_cooling_front','seat_cooling_rear',
    'seat_massage_front','seat_massage_rear','driver_memory_seat','passenger_memory_seat',
    'power_driver_seats','power_passenger_seats','power_rear_seats','power_front_windows',
    'power_rear_windows','power_trunk','power_locks','power_mirrors',
    'power_folding_mirrors','sun_roof','panoramic_roof','cool_box','seat_heated_front',
    'auto_park','remote_start_engine','soft_close_doors','adaptive_lights','night_vision',
    'captain_rear_seats','leather_seats','leather_fabric','body_kit','lift_kit',
    'front_spoiler','rear_spoiler','fog_light_front','roof_carrier','halogen_headlight',
    'led_headlight','xenon_headlight','trailer_hook_coupling'];
?>	
		<!------------ Interior - Entertainment ------------>
		<?php if (hasValid($dataTwo, $entertainmentFields)) { ?>
		    <div class="row"><h5> Interior - Entertainment </h5></div>	
		<?php } ?>
		
		<div class="row">  	
		    <?php 
		    if($dataTwo->digital_driver_display != null && $dataTwo->digital_driver_display != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  <?php 
    					if($dataTwo->digital_driver_display == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataTwo->digital_driver_display == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Digital Driver Display
    				</div>
    			</div> <?php 
			}
			
            if($dataTwo->cd_player != null && $dataTwo->cd_player != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataTwo->cd_player == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataTwo->cd_player == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> CD Player
    				</div>
    			</div> <?php 
			} 
			
			if($dataTwo->dvd_player != null && $dataTwo->dvd_player != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataTwo->dvd_player == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataTwo->dvd_player == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> DVD Player
    				</div>
    			</div> <?php 
			} 
			
			if($dataTwo->mp_player != null && $dataTwo->mp_player != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataTwo->mp_player == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataTwo->mp_player == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> MP3 Player
    				</div>
    			</div> <?php 
			}  
			
			if($dataTwo->sd_card_player != null && $dataTwo->sd_card_player != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> <?php 
    					if($dataTwo->sd_card_player == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($dataTwo->sd_card_player == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> SD card player
    				</div>
    			</div> <?php  
			} 
			if($dataTwo->bluetooth_interface != null && $dataTwo->bluetooth_interface != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->bluetooth_interface == 1)
						{ ?>
							<span class="tick"></span> Bluetooth interface <?php 
						} 
						elseif($dataTwo->bluetooth_interface == 2)
						{ ?>
							<span class="tick not"></span> Bluetooth interface <?php
						} ?> 
					</div>
				</div> <?php 
			}   
			if($dataTwo->premium_sound_system != null && $dataTwo->premium_sound_system != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->premium_sound_system == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->premium_sound_system == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Premium sound system
					</div>
				</div>  <?php 
			}  
			if($dataTwo->aux_audio_system != null && $dataTwo->aux_audio_system != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3">  <?php 
						if($dataTwo->aux_audio_system == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->aux_audio_system == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> AUX Audio System
					</div>
				</div> <?php 
			}   
            if($dataTwo->usb != null && $dataTwo->usb != 3)
            { ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->usb == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->usb == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> USB
					</div>
				</div> <?php 
			} 
			if($dataTwo->usb_c != null && $dataTwo->usb_c != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->usb_c == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->usb_c == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> USB-C
					</div>
				</div> <?php 
			}
			if($dataTwo->touch_screen != null && $dataTwo->touch_screen != 3 )  
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->touch_screen == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->touch_screen == 2)
						{ ?>
							<span class="tick not"></span> <?php
						}?> Touch screen
					</div>
				</div> <?php  
			} 
			if($dataTwo->rear_seat_enter_sys != null && $dataTwo->rear_seat_enter_sys != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->rear_seat_enter_sys == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->rear_seat_enter_sys == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Rear seat entertainment system
					</div>
				</div> <?php 
			} 
			if($dataTwo->wireless != null && $dataTwo->wireless != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->wireless == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->wireless == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Wireless
					</div>
				</div> <?php 
			}  
			if($dataTwo->ambient_lighting != null && $dataTwo->ambient_lighting != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->ambient_lighting == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->ambient_lighting == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Ambient lighting
					</div>
				</div> <?php 
			}
			if($dataTwo->apple_carplay != null && $dataTwo->apple_carplay != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->apple_carplay == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->apple_carplay == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Apple carplay
					</div>
				</div> <?php
			}
			if($dataTwo->navigation != null && $dataTwo->navigation != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->navigation == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->navigation == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Navigation
					</div>
				</div> <?php 
			} 
			if($dataTwo->standard_ac != null && $dataTwo->standard_ac != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->standard_ac == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->standard_ac == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Standard A/C
					</div>
				</div> <?php
			} 
			if($dataTwo->dual_climcont_ac != null && $dataTwo->dual_climcont_ac != 3)
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->dual_climcont_ac == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->dual_climcont_ac == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Dual-Zone climcont A/C
					</div>
				</div> <?php
			}   
			if($dataTwo->multi_climcont_ac != null && $dataTwo->multi_climcont_ac != 3)  
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->multi_climcont_ac == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->multi_climcont_ac == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Multi-Zone Climate Ctrl AC
					</div>
				</div> <?php 
			}   
            if($dataTwo->keyless_entry != null && $dataTwo->keyless_entry != 3 ) 
            { ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->keyless_entry == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->keyless_entry == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Keyless Entry
					</div>
				</div> <?php 
            } 
            if($dataTwo->keyless_start != null && $dataTwo->keyless_start != 3) 
            { ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->keyless_start == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->keyless_start == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Keyless Start    
					</div>
				</div> <?php 
            }  
			if($dataTwo->power_steering != null && $dataTwo->power_steering != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_steering == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_steering == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power steering
					</div>
				</div> <?php 
			} 
			if($dataTwo->heads_up_display != null && $dataTwo->heads_up_display != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->heads_up_display == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->heads_up_display == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Heads up display
					</div>
				</div> <?php 
			}   
			if($dataTwo->cruise_control != null && $dataTwo->cruise_control != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->cruise_control == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->cruise_control == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Cruise control
					</div>
				</div> <?php 
			}   
	        if($dataTwo->adaptive_cruise_control != null && $dataTwo->adaptive_cruise_control != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->adaptive_cruise_control == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->adaptive_cruise_control == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Adaptive cruise control
					</div>
				</div> <?php 
	        }   
 		    if($dataTwo->seat_cooling_front != null && $dataTwo->seat_cooling_front != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->seat_cooling_front == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->seat_cooling_front == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Seat cooling front 
					</div>
				</div> <?php 
 		    } 
            if($dataTwo->seat_cooling_rear != null && $dataTwo->seat_cooling_rear != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->seat_cooling_rear == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->seat_cooling_rear == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Seat cooling Rear 
					</div>
				</div> <?php 
            } 
			if($dataTwo->seat_massage_front != null && $dataTwo->seat_massage_front != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->seat_massage_front == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->seat_massage_front == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Seat Massage Front
					</div>
				</div> <?php 
			}
			if($dataTwo->seat_massage_rear != null && $dataTwo->seat_massage_rear != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->seat_massage_rear == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->seat_massage_rear == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Seat Massage Rear
					</div>
				</div> <?php 
			} 
			if($dataTwo->driver_memory_seat != null && $dataTwo->driver_memory_seat != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->driver_memory_seat == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->driver_memory_seat == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Driver Memory Seat
					</div>
				</div> <?php 
			}  
			if($dataTwo->passenger_memory_seat != null && $dataTwo->passenger_memory_seat != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->passenger_memory_seat == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->passenger_memory_seat == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Passenger Memory Seat
					</div>
				</div>  <?php 
			}   
            if($dataTwo->power_driver_seats != null && $dataTwo->power_driver_seats != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_driver_seats == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_driver_seats == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Driver Seats
					</div>
				</div> <?php 
            } 
			if($dataTwo->power_passenger_seats != null && $dataTwo->power_passenger_seats != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_passenger_seats == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_passenger_seats == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Passenger Seats
					</div>
				</div> <?php 
			} 
			if($dataTwo->power_rear_seats != null && $dataTwo->power_rear_seats != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_rear_seats == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_rear_seats == 2)
						{ ?>
							<span class="tick not"></span> <?php
						}  ?> Power Rear Seats
					</div>
				</div> <?php 
			} 
            if($dataTwo->power_front_windows != null && $dataTwo->power_front_windows != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_front_windows == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_front_windows == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Front Windows
					</div>
				</div> <?php 
            } 
            if($dataTwo->power_rear_windows != null && $dataTwo->power_rear_windows != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_rear_windows == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_rear_windows == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Rear Windows
					</div>
				</div> <?php 
            } 
			if($dataTwo->power_trunk != null && $dataTwo->power_trunk != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_trunk == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_trunk == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Trunk
					</div>
				</div> <?php 
			} 
			if($dataTwo->power_locks != null && $dataTwo->power_locks != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_locks == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_locks == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Locks
					</div>
				</div> <?php 
			} 
			if($dataTwo->power_mirrors != null && $dataTwo->power_mirrors != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_mirrors == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_mirrors == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Mirrors
					</div>
				</div> <?php 
			}  
            if($dataTwo->power_folding_mirrors != null && $dataTwo->power_folding_mirrors != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->power_folding_mirrors == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->power_folding_mirrors == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Power Folding Mirrors
					</div>
				</div> <?php 
            } 
			if($dataTwo->sun_roof != null && $dataTwo->sun_roof != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->sun_roof == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->sun_roof == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Sun Roof
					</div>
				</div> <?php 
			} 
			if($dataTwo->panoramic_roof != null && $dataTwo->panoramic_roof != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->panoramic_roof == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->panoramic_roof == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Panoramic Roof
					</div>
				</div> <?php 
			}     
			if($dataTwo->cool_box != null && $dataTwo->cool_box != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->cool_box == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->cool_box == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Cool Box
					</div>
				</div> <?php 
			}   
			if($dataTwo->seat_heated_front != null && $dataTwo->seat_heated_front != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->seat_heated_front == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->seat_heated_front == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Seat Heated Front
					</div>
				</div> <?php 
			} 
			if($dataTwo->auto_park != null && $dataTwo->auto_park != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->auto_park == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->auto_park == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Auto Park
					</div>
				</div> <?php 
			} 
            if($dataTwo->remote_start_engine != null && $dataTwo->remote_start_engine != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->remote_start_engine == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->remote_start_engine == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Remote Start Engine
					</div>
				</div> <?php 
            }  
			if($dataTwo->soft_close_doors != null && $dataTwo->soft_close_doors != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->soft_close_doors == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->soft_close_doors == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Soft Close Doors
					</div>
				</div> <?php 
			} 
			if($dataTwo->adaptive_lights != null && $dataTwo->adaptive_lights != 3) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->adaptive_lights == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->adaptive_lights == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Adaptive lights
					</div>
				</div> <?php 
			} 
			if($dataTwo->night_vision != null && $dataTwo->night_vision != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->night_vision == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->night_vision == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Night vision
					</div>
				</div> <?php 
			}  
			if($dataTwo->captain_rear_seats != null && $dataTwo->captain_rear_seats != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->captain_rear_seats == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->captain_rear_seats == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Captain Rear Seats
					</div>
				</div> <?php 
			} 
            if($dataTwo->leather_seats != null && $dataTwo->leather_seats != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->leather_seats == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->leather_seats == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Leather Seats
					</div>
				</div> <?php 
            } 
			if($dataTwo->leather_fabric != null && $dataTwo->leather_fabric != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->leather_fabric == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->leather_fabric == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Leather Fabric
					</div>
				</div> <?php 
			} 
			if($dataTwo->body_kit != null && $dataTwo->body_kit != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->body_kit == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->body_kit == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Body Kit
					</div>
				</div> <?php 
			} 
			if($dataTwo->lift_kit != null && $dataTwo->lift_kit != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->lift_kit == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->lift_kit == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Lift Kit
					</div>
				</div> <?php 
			} 
            if($dataTwo->front_spoiler != null && $dataTwo->front_spoiler != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->front_spoiler == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->front_spoiler == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Front Spoiler
					</div>
				</div> <?php 
            } 
            if($dataTwo->rear_spoiler != null && $dataTwo->rear_spoiler != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->rear_spoiler == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->rear_spoiler == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Rear Spoiler
					</div>
				</div> <?php 
            } 
            if($dataTwo->fog_light_front != null && $dataTwo->fog_light_front != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->fog_light_front == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->fog_light_front == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Fog Light Front
					</div>
				</div> <?php 
            } 
            if($dataTwo->roof_carrier != null && $dataTwo->roof_carrier != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->roof_carrier == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->roof_carrier == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Roof Carrier
					</div>
				</div> <?php 
            } 
			if($dataTwo->halogen_headlight != null && $dataTwo->halogen_headlight != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->halogen_headlight == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->halogen_headlight == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Halogen Headlight
					</div>
				</div> <?php 
			} 
			if($dataTwo->led_headlight != null && $dataTwo->led_headlight != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->led_headlight == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->led_headlight == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Led Headlight
					</div>
				</div> <?php 
			} 
            if($dataTwo->xenon_headlight != null && $dataTwo->xenon_headlight != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->xenon_headlight == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->xenon_headlight == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Xenon Headlight
					</div>
				</div> <?php 
            } 
			if($dataTwo->trailer_hook_coupling != null && $dataTwo->trailer_hook_coupling != 3) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataTwo->trailer_hook_coupling == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataTwo->trailer_hook_coupling == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Trailer Hook Coupling
					</div>
				</div> <?php
			} ?>
		</div>
		<?php
} 
if($dataOne != null)
{   
    ?>
		<!------------ Aftermarket Added Accessories ------------>
		<?php if (hasValid($dataOne, $featureFields)) { ?>
		    <div class="row"><h5> Aftermarket Added Accessories </h5></div>	
		<?php } ?>
		<div class="row">
		    <?php
		    if($dataOne->winch != null && $dataOne->winch != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"><?php 
						if($dataOne->winch == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->winch == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Winch
					</div>
				</div> <?php 
		    }  
			if($dataOne->body_kit_aaa != null && $dataOne->body_kit_aaa != 3 )  
			{?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->body_kit_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->body_kit_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Body Kit
					</div>
				</div> <?php 
			}
			if($dataOne->lift_kit_aaa != null && $dataOne->lift_kit_aaa != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->lift_kit_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->lift_kit_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Lift Kit
					</div>
				</div> <?php 
			}
			if($dataOne->leather_seats_aaa != null && $dataOne->leather_seats_aaa != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->leather_seats_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->leather_seats_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Leather Seats
					</div>
				</div> <?php 
			}  
			if($dataOne->parking_sensors != null && $dataOne->parking_sensors != 3 ) 
			{ ?> 
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->parking_sensors == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->parking_sensors == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Parking Sensors
					</div>
				</div> <?php 
			} 
			if($dataOne->rear_view_camera_aaa != null && $dataOne->rear_view_camera_aaa != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->rear_view_camera_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->rear_view_camera_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Rear View Camera
					</div>
				</div> <?php 
			} 
			if($dataOne->navigation_aaa != null && $dataOne->navigation_aaa != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> <?php 
						if($dataOne->navigation_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->navigation_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Navigation
					</div>
				</div> <?php 
			}
			if($dataOne->fire_extinguisher != null && $dataOne->fire_extinguisher != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"><?php 
						if($dataOne->fire_extinguisher == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->fire_extinguisher == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Fire extinguisher
					</div> 
				</div> <?php 
			} 
			if($dataOne->rear_seat_enter_sys_aaa != null && $dataOne->rear_seat_enter_sys_aaa != 3 ) 
			{ ?>
				<div class="col-sm-3">
					<div class="mb-3"> <?php 
						if($dataOne->rear_seat_enter_sys_aaa == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($dataOne->rear_seat_enter_sys_aaa == 2)
						{ ?>
							<span class="tick not"></span> <?php
						}  ?> Rear Seat Entertainment System
					</div>
				</div> <?php 
			} ?>
		</div>
 
	</table>
	
	<?php if ($hasSpecs) { ?>
	    <p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
	    <?php
	}
} ?>


<!--------- Inspection Checklist --------->
<?php 
$checklist = DB::table('tbl_report_insp_checklist')
		->select('insp_checklist_id','glass','door_locks_operation','fuel_filler_cover_petrol', 'general_body_condition','seat_belts','headliner','rearview_mirror','steering_wheel','gear_lever','sun_visor', 'pillar_trim','armrest_console','spare_tyre','front_left_tyre','back_right_tyre','front_right_tyre', 'back_left_tyre','coolant_level','coolant_leaks','steering_fluid','brake_master_booster', 'evidence_overheating','coolant_conditions','radiator_cap','radiator_fan','gear_selector','gear_shifting', 'transmission_mount','gear_noise','fluid_level_oil_leak','door_locks','central_locking', 'ignitionlock_startingsys','instrument_panel','headlights','sidelights_runlights','rear_lights', 'indicator_hazardlights','boot_tailgate_lock','reverse_lights','fog_lights','multimedia','ac_control_cooling', 'steering_ball_joints','brakes_lines','subframe','power_steering_rack','wheels_hubs_bearings','dampers_bushes','evidencefloor_chassis','engine_performance','gearbox_operation','clutch_operation','steering_operation', 'brake_operation','hand_parking_brake','drive_train','instru_control_func')
		->where('insp_checklist_report_id',$data->report_id)
		->where('insp_checklist_status',0)
		->first(); 
		
$checklistOne = DB::table('tbl_report_insp_check_one')
	->select('one_checklist_id','door_locks_operation','fuel_filler_cover_petrol','glass','molding','bumper_grills','front_bumper','rear_bumper','front_left_headlights','front_right_headlights','rear_left_tail_lights', 'rear_right_tail_lights','general_body_condition','seat_belts','headliner','rearview_mirror','steering_wheel','gear_lever','sun_visor','pillar_trim','armrest_console','floor_mats_carpets','trunk_liner','dashboard','glove_compartment','seats','door_trims','ac_grills','sunroof_shade_liner','spare_tyre','front_left_tyre','back_right_tyre','front_right_tyre','back_left_tyre','coolant_level','coolant_leaks','steering_fluid', 'brake_master_booster','evidence_overheating','coolant_conditions','radiator_cap','radiator_fan','fender_liner','hoses_pipes','cable_harnes_connector','power_steer_fluidlevel','engine_oil_level','external_engine_leaks','engine_mounts','turbo_supercharger','fuel_pump_pipes','cold_starting','fast_idle','noise_level','excess_smoke','inlet_manifold','outlet_manifold','exhaust_pipes','silencer','head_shield_mounting','joints_couplings','engine_underside_leak','catalytic_converter','engine_shield')
	->where('one_checklist_report_id',$data->report_id)
	->where('one_checklist_status',0)
	->first();

$checklistTwo = DB::table('tbl_report_insp_check_two')
	->select('two_checklist_id','gear_selector','gear_shifting','transmission_mount','gear_noise','fluid_level_oil_leak','door_locks','central_locking','ignitionlock_startsys','instrument_panel','headlights','sidelights_runlights','rear_lights','indicator_hazardlights','boot_tailgate_lock','reverse_lights','fog_lights','multimedia','ac_control_cooling','side_mirror','auxiliary_lights','panel_lights','horn','window_operation','sunroof_operation','wipers_jet_washers','keys_remote_controls','warning_lights','number_plate_light','steering_ball_joints','brakes_lines','subframe','power_steering_rack','wheels_hubs_bearings','dampers_bushes','evidencefloor_chassis','engine_performance','gearbox_operation','clutch_operation','steering_operation','brake_operation','hand_parking_brake','drive_train','instru_control_func','suspension_noise','shock_absorber','road_holding_stability','nois')
	->where('two_checklist_report_id',$data->report_id)
	->where('two_checklist_status',0)
	->first();
		
if($checklistOne != null)
{ 
    $exteriorFields = [
        'door_locks_operation', 'fuel_filler_cover_petrol', 'glass', 'molding', 
        'bumper_grills', 'front_bumper', 'rear_bumper', 'front_left_headlights',
        'front_right_headlights', 'rear_left_tail_lights', 'rear_right_tail_lights', 
        'general_body_condition' ];
        
    $interiorFieldsCheck = [
        'seat_belts', 'headliner', 'rearview_mirror', 'steering_wheel', 'gear_lever',
        'sun_visor', 'pillar_trim', 'armrest_console', 'floor_mats_carpets', 'trunk_liner',
        'dashboard', 'seats', 'door_trims', 'ac_grills','sunroof_shade_liner'];
        
    $tyreFields = ['spare_tyre', 'front_left_tyre', 'front_right_tyre', 'back_left_tyre', 'back_right_tyre'];
    
    $engineFields = [
        'coolant_level', 'coolant_leaks', 'steering_fluid', 'brake_master_booster',
        'evidence_overheating', 'coolant_conditions', 'radiator_cap', 'radiator_fan',
        'fender_liner', 'hoses_pipes', 'cable_harnes_connector', 'power_steer_fluidlevel',
        'engine_oil_level', 'external_engine_leaks', 'engine_mounts', 'turbo_supercharger',
        'fuel_pump_pipes', 'cold_starting', 'fast_idle', 'noise_level', 'excess_smoke',
        'inlet_manifold', 'outlet_manifold', 'exhaust_pipes', 'silencer', 'head_shield_mounting', 
        'joints_couplings', 'engine_underside_leak', 'catalytic_converter', 'engine_shield'];
    
    $hasInCheck = hasValid($checklistOne, $exteriorFields) || hasValid($checklistOne, $interiorFieldsCheck) || 
                  hasValid($checklistOne, $tyreFields) || hasValid($checklistOne, $engineFields); 
    ?>
		
	<table class="table tbl-u-boarderd">
    
    <?php if ($hasInCheck) { ?>
	    <tr> <h4> Inspection Checklist </h4> </tr>
	    <?php 
    } ?>
		
		<!------------ Exterior ------------->	
		<?php if (hasValid($checklistOne, $exteriorFields)) { ?>
		    <div class="row"> <h5> Exterior </h5> </div>
		<?php } ?>
		<div class="row">
		    <?php 
			if($checklistOne->door_locks_operation != null && $checklistOne->door_locks_operation != 3 )
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->door_locks_operation == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->door_locks_operation == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Door locks / operation
					</div>
				</div> <?php 
			}  
    		if($checklistOne->fuel_filler_cover_petrol != null && $checklistOne->fuel_filler_cover_petrol != 3 )
    	    { ?>	
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->fuel_filler_cover_petrol == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->fuel_filler_cover_petrol == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Fuel filler cover / Petrol
					</div>
				</div> <?php 
    	    }
			if($checklistOne->glass != null && $checklistOne->glass != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->glass == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->glass == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Glass
					</div>
				</div> <?php 
			} 
			if($checklistOne->molding != null && $checklistOne->molding != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->molding == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->molding == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Molding
					</div>
				</div> <?php 
			} 
			if($checklistOne->bumper_grills != null && $checklistOne->bumper_grills != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->bumper_grills == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->bumper_grills == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Bumper Gills
					</div>
				</div> <?php 
			} 
			if($checklistOne->front_bumper != null && $checklistOne->front_bumper != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->front_bumper == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->front_bumper == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Front Bumper
					</div>
				</div> <?php 
			} 
			if($checklistOne->rear_bumper != null && $checklistOne->rear_bumper != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->rear_bumper == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->rear_bumper == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> Rear Bumper
					</div>
				</div> <?php 
			} 
			if($checklistOne->front_left_headlights != null && $checklistOne->front_left_headlights != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->front_left_headlights == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->front_left_headlights == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?>  Front left headlights
					</div>
				</div> <?php 
			} 
			if($checklistOne->front_right_headlights != null && $checklistOne->front_right_headlights != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->front_right_headlights == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->front_right_headlights == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?>  Front right headlights
					</div>
				</div>  <?php 
			}   
			if($checklistOne->rear_left_tail_lights != null && $checklistOne->rear_left_tail_lights != 3 )  
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->rear_left_tail_lights == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->rear_left_tail_lights == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?>  Rear left tail lights
					</div>
				</div> <?php 
			} 
			if($checklistOne->rear_right_tail_lights != null && $checklistOne->rear_right_tail_lights != 3 ) 
			{ ?>
				<div class="col-sm-2">
					<div class="mb-3"> 
						<?php 
						if($checklistOne->rear_right_tail_lights == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->rear_right_tail_lights == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?>  Rear right tail lights
					</div>
				</div> <?php 
			} 
			if($checklistOne->general_body_condition != null && $checklistOne->general_body_condition != 3 ) 
			{ ?>
				<div class="col-sm-2"> 
					<div class="mb-3"> 
						<?php 
						if($checklistOne->general_body_condition == 1)
						{ ?>
							<span class="tick"></span> <?php 
						} 
						elseif($checklistOne->general_body_condition == 2)
						{ ?>
							<span class="tick not"></span> <?php
						} ?> General body condition
					</div>
				</div> <?php 
			} ?>
			
		</div>
		<!------------ Interior ------------->		  
		<?php if (hasValid($checklistOne, $interiorFieldsCheck)) { ?>
		    <div class="row"> <h5> Interior </h5> </div>
		<?php } ?>
		
		<div class="row">
		    <?php 
		    if($checklistOne->seat_belts != null && $checklistOne->seat_belts != 3 )
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->seat_belts == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->seat_belts == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Seat belts
    				</div>
    			</div> <?php 
		    }  
			if($checklistOne->headliner != null && $checklistOne->headliner != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->headliner == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->headliner == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Headliner
    				</div>
    			</div> <?php 
			} 
			if($checklistOne->rearview_mirror != null && $checklistOne->rearview_mirror != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->rearview_mirror == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->rearview_mirror == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Rearview mirror
    				</div>
    			</div> <?php 
			} 
			if($checklistOne->steering_wheel != null && $checklistOne->steering_wheel != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->steering_wheel == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->steering_wheel == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Steering wheel
    				</div>
    			</div> <?php 
			}  
			if($checklistOne->gear_lever != null && $checklistOne->gear_lever != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->gear_lever == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->gear_lever == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Gear lever
    				</div>
    			</div> <?php 
			}  
			if($checklistOne->sun_visor != null && $checklistOne->sun_visor != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->sun_visor == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->sun_visor == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sun visor
    				</div>
    			</div> <?php 
			} 
			if($checklistOne->pillar_trim != null && $checklistOne->pillar_trim != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->pillar_trim == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->pillar_trim == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Pillar trim
    				</div>
    			</div> <?php 
			} 
			if($checklistOne->armrest_console != null && $checklistOne->armrest_console != 3) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->armrest_console == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->armrest_console == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Armrest console
    				</div>
    			</div> <?php 
			}  
			if($checklistOne->floor_mats_carpets != null && $checklistOne->floor_mats_carpets != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->floor_mats_carpets == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->floor_mats_carpets == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Floor mats carpets
    				</div>
    			</div><?php 
			} 
			if($checklistOne->trunk_liner != null && $checklistOne->trunk_liner != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->trunk_liner == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->trunk_liner == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Trunk liner
    				</div>
    			</div> <?php
			}
			if($checklistOne->dashboard != null && $checklistOne->dashboard != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->dashboard == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->dashboard == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Dashboard
    				</div>
    			</div> <?php
			}
			if($checklistOne->seats != null && $checklistOne->seats != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->seats == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->seats == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Seats
    				</div>
    			</div> <?php
			}
			if($checklistOne->door_trims != null && $checklistOne->door_trims != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->door_trims == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->door_trims == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Door trims
    				</div>
    			</div> <?php
			}
			if($checklistOne->ac_grills != null && $checklistOne->ac_grills != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->ac_grills == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->ac_grills == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> A/C Grills
    				</div>
    			</div> <?php 
			}
			if($checklistOne->sunroof_shade_liner != null && $checklistOne->sunroof_shade_liner != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->sunroof_shade_liner == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->sunroof_shade_liner == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sunroof shade liner
    				</div>
    			</div> <?php
			} ?>
			
		</div>
			
		<!------------ Tyre ------------->	
		<?php if (hasValid($checklistOne, $tyreFields)) { ?>
		    <div class="row"><h5> Tyre </h5></div>
		<?php } ?>
		<div class="row">	
		    <?php
		    if($checklistOne->spare_tyre != null && $checklistOne->spare_tyre != 3 ) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->spare_tyre == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->spare_tyre == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Spare Tyre
    				</div>
    			</div> <?php
		    }
			if($checklistOne->front_left_tyre != null && $checklistOne->front_left_tyre != 3 )
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->front_left_tyre == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->front_left_tyre == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Front Left Tyre
    				</div>
    			</div> <?php
			} 
			if($checklistOne->back_right_tyre != null && $checklistOne->back_right_tyre != 3)
			{?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->back_right_tyre == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->back_right_tyre == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Back Right Tyre
    				</div>
    			</div><?php
			} 
			if($checklistOne->front_right_tyre != null && $checklistOne->front_right_tyre != 3)
			{  ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->front_right_tyre == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->front_right_tyre == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Front Right Tyre
    				</div>
    			</div> <?php
			} 
			if($checklistOne->back_left_tyre != null && $checklistOne->back_left_tyre != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->back_left_tyre == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->back_left_tyre == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Back Left Tyre
    				</div>
    			</div> <?php 
			}?>
		</div>
		<!------------ Engine ------------->
		<?php if (hasValid($checklistOne, $engineFields)) { ?> 
		    <div class="row"> <h5> Engine </h5> </div>
		<?php } ?>
		<div class="row">
		    <?php 
			if($checklistOne->coolant_level != null && $checklistOne->coolant_level != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->coolant_level == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->coolant_level == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Coolant level
    				</div>
    			</div> <?php
			}
			if($checklistOne->coolant_leaks != null && $checklistOne->coolant_leaks != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->coolant_leaks == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->coolant_leaks == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Coolant leaks
    				</div>
    			</div> <?php
			}
			if($checklistOne->steering_fluid != null && $checklistOne->steering_fluid != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->steering_fluid == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->steering_fluid == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Steering Fluid
    				</div>
    			</div> <?php 
			}
			if($checklistOne->brake_master_booster != null && $checklistOne->brake_master_booster != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->brake_master_booster == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->brake_master_booster == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					}
    					else
    					{
    					} ?> Brake master and booster
    				</div>
    			</div> <?php
			}
			if($checklistOne->evidence_overheating != null && $checklistOne->evidence_overheating != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->evidence_overheating == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->evidence_overheating == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Evidence of overheating
    				</div>
    			</div> <?php
			}
		    if($checklistOne->coolant_conditions != null && $checklistOne->coolant_conditions != 3 )
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->coolant_conditions == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->coolant_conditions == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Coolant Conditions
    				</div>
    			</div> <?php
		    } 
			if($checklistOne->radiator_cap != null && $checklistOne->radiator_cap != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->radiator_cap == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->radiator_cap == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Radiator cap
    				</div>
    			</div> <?php
			}
			if($checklistOne->radiator_fan != null && $checklistOne->radiator_fan != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->radiator_fan == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->radiator_fan == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Radiator fan
    				</div>
    			</div> <?php
			}
			if($checklistOne->fender_liner != null && $checklistOne->fender_liner != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->fender_liner == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->fender_liner == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Fender liner
    				</div>
    			</div> <?php
			}
			if($checklistOne->hoses_pipes != null && $checklistOne->hoses_pipes != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->hoses_pipes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->hoses_pipes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Hoses pipes
    				</div>
    			</div> <?php
			}
			if($checklistOne->cable_harnes_connector != null && $checklistOne->cable_harnes_connector != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->cable_harnes_connector == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->cable_harnes_connector == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Cables, harnes & connectors
    				</div>
    			</div> <?php
			}
			if($checklistOne->power_steer_fluidlevel != null && $checklistOne->power_steer_fluidlevel != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->power_steer_fluidlevel == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->power_steer_fluidlevel == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Power steering fluid level
    				</div>
    			</div> <?php 
			} 
			if($checklistOne->engine_oil_level != null && $checklistOne->engine_oil_level != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->engine_oil_level == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->engine_oil_level == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Engine oil level
    				</div>
    			</div> <?php
			} 
			if($checklistOne->external_engine_leaks != null && $checklistOne->external_engine_leaks != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->external_engine_leaks == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->external_engine_leaks == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					}
    					else
    					{
    					} ?> External engine leaks
    				</div>
    			</div> <?php  
			}
			if($checklistOne->engine_mounts != null && $checklistOne->engine_mounts != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->engine_mounts == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->engine_mounts == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Engine mounts
    				</div>
    			</div> <?php
			}
			if($checklistOne->turbo_supercharger != null && $checklistOne->turbo_supercharger != 3 )
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->turbo_supercharger == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->turbo_supercharger == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Turbo/ Supercharger
    				</div>
    			</div> <?php
			}
			if($checklistOne->fuel_pump_pipes != null && $checklistOne->fuel_pump_pipes != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  
    					<?php 
    					if($checklistOne->fuel_pump_pipes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->fuel_pump_pipes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Fuel pump & pipes
    				</div>
    			</div> <?php
			}
			if($checklistOne->cold_starting != null && $checklistOne->cold_starting != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->cold_starting == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->cold_starting == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Cold starting
    				</div>
    			</div> <?php
			}  
			if($checklistOne->fast_idle != null && $checklistOne->fast_idle != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->fast_idle == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->fast_idle == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Fast idle when the engine cold
    				</div>
    			</div> <?php
			} 
			if($checklistOne->noise_level != null && $checklistOne->noise_level != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->noise_level == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->noise_level == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Noise level when the engine cold
    				</div>
    			</div> <?php 
			}  
			if($checklistOne->excess_smoke != null && $checklistOne->excess_smoke != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  
    					<?php 
    					if($checklistOne->excess_smoke == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->excess_smoke == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Excess Smoke(minor/major)
    				</div>
    			</div> <?php 
			}
			if($checklistOne->inlet_manifold != null && $checklistOne->inlet_manifold != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->inlet_manifold == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->inlet_manifold == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Inlet manifold
    				</div>
    			</div> <?php 
			}
			if($checklistOne->outlet_manifold != null && $checklistOne->outlet_manifold != 3 )
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->outlet_manifold == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->outlet_manifold == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Outlet manifold
    				</div>
    			</div> <?php
			}
			if($checklistOne->exhaust_pipes != null && $checklistOne->exhaust_pipes != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->exhaust_pipes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->exhaust_pipes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Exhaust Pipes
    				</div>
    			</div> <?php
			}
			if($checklistOne->silencer != null && $checklistOne->silencer != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->silencer == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->silencer == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Silencer
    				</div>
    			</div> <?php
			}
			if($checklistOne->head_shield_mounting != null && $checklistOne->head_shield_mounting != 3) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->head_shield_mounting == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->head_shield_mounting == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Head shields & mountings
    				</div>
    			</div> <?php
			}
			if($checklistOne->joints_couplings != null && $checklistOne->joints_couplings != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  
    					<?php 
    					if($checklistOne->joints_couplings == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->joints_couplings == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Joints & couplings
    				</div>
    			</div> <?php
			}
			if($checklistOne->engine_underside_leak != null && $checklistOne->engine_underside_leak != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">  
    					<?php 
    					if($checklistOne->engine_underside_leak == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->engine_underside_leak == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Engine underside leaks
    				</div>
    			</div>  <?php
			} 
			if($checklistOne->catalytic_converter != null && $checklistOne->catalytic_converter != 3)
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->catalytic_converter == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->catalytic_converter == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Catalytic converter
    				</div>
    			</div>  <?php  
			} 
			if($checklistOne->engine_shield != null && $checklistOne->engine_shield != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistOne->engine_shield == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistOne->engine_shield == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Engine shield
    				</div>
    			</div> <?php
			} ?>
			
		</div> <?php
}
if($checklistTwo != null)
{  
    $transmissionFields = [
        'gear_selector', 'gear_shifting', 'transmission_mount', 'gear_noise', 'fluid_level_oil_leak'];
        
    $electricalFields = [
        'door_locks', 'central_locking', 'ignitionlock_startsys', 'instrument_panel',
        'headlights', 'sidelights_runlights', 'rear_lights', 'indicator_hazardlights',
        'boot_tailgate_lock', 'reverse_lights', 'fog_lights', 'multimedia',
        'ac_control_cooling', 'side_mirror', 'auxiliary_lights', 'panel_lights', 
        'horn', 'window_operation', 'sunroof_operation', 'wipers_jet_washers',
        'keys_remote_controls', 'warning_lights', 'number_plate_light']; 
        
    $underbodyFields = [
        'steering_ball_joints', 'brakes_lines', 'subframe', 'power_steering_rack',
        'wheels_hubs_bearings', 'dampers_bushes', 'evidencefloor_chassis' ];    
    
    $testdriveFields = [
        'engine_performance', 'gearbox_operation', 'clutch_operation', 'steering_operation',
        'brake_operation', 'hand_parking_brake', 'drive_train', 'instru_control_func',
        'suspension_noise', 'shock_absorber', 'road_holding_stability', 'nois'];  
            
    ?>
		<!------------ Transmission - Checklisttwo ------------->
		<?php if (hasValid($checklistTwo, $transmissionFields)) { ?> 
		    <div class="row"><h5> Transmission </h5></div>
		<?php } ?>
		<div class="row">	
		    <?php   
			if($checklistTwo->gear_selector != null && $checklistTwo->gear_selector != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php   
    					if($checklistTwo->gear_selector == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->gear_selector == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Gear selector
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->gear_shifting != null && $checklistTwo->gear_shifting != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->gear_shifting != null ) 
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->gear_shifting == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Gear shifting
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->transmission_mount != null && $checklistTwo->transmission_mount != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->transmission_mount == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->transmission_mount == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Transmission Mount (Gear Mount)
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->gear_noise != null && $checklistTwo->gear_noise != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->gear_noise == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->gear_noise == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Gear noise
    				</div>
    			</div> <?php
			}
			if($checklistTwo->fluid_level_oil_leak != null && $checklistTwo->fluid_level_oil_leak != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->fluid_level_oil_leak == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->fluid_level_oil_leak == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Fluid Level & Oil Leak
    				</div>
    			</div><?php  
			} ?>
		</div> 
			 
		<!------------ Electrical ------------->		  
		<?php if (hasValid($checklistTwo, $electricalFields)) { ?> 
		    <div class="row"><h5> Electrical </h5></div>
		<?php } ?>
		
		<div class="row">
		    <?php 
			if($checklistTwo->door_locks != null && $checklistTwo->door_locks != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->door_locks == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->door_locks == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Door locks (which side)
    				</div>
    			</div> <?php 
			}  
			if($checklistTwo->central_locking != null && $checklistTwo->central_locking != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->central_locking == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->central_locking == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Central Locking
    				</div>
    			</div> <?php
			}
			if($checklistTwo->ignitionlock_startsys != null && $checklistTwo->ignitionlock_startsys != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->ignitionlock_startsys == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->ignitionlock_startsys == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Ignition lock / Starting system
    				</div>
    			</div> <?php
			}
			if($checklistTwo->instrument_panel != null && $checklistTwo->instrument_panel != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->instrument_panel == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->instrument_panel == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Instrument panel
    				</div>
    			</div> <?php
			}
			if($checklistTwo->headlights != null && $checklistTwo->headlights != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->headlights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->headlights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Headlights
    				</div>
    			</div>  <?php
			}
		    if($checklistTwo->sidelights_runlights != null && $checklistTwo->sidelights_runlights != 3 ) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->sidelights_runlights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->sidelights_runlights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sidelights / Running lights
    				</div>
    			</div> <?php
		    }
			if($checklistTwo->rear_lights != null && $checklistTwo->rear_lights != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->rear_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->rear_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Rear lights
    				</div>
    			</div> <?php
			}
			if($checklistTwo->indicator_hazardlights != null && $checklistTwo->indicator_hazardlights != 3)
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->indicator_hazardlights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->indicator_hazardlights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Indicator / Hazard lights
    				</div>
    			</div> <?php  
			} 
			if($checklistTwo->boot_tailgate_lock != null && $checklistTwo->boot_tailgate_lock != 3)
			{  ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->boot_tailgate_lock == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->boot_tailgate_lock == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Boot / Tailgate lock
    				</div>
    			</div> <?php
			}
			if($checklistTwo->reverse_lights != null && $checklistTwo->reverse_lights != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->reverse_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->reverse_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Reverse lights
    				</div>
    			</div> <?php
			}
	        if($checklistTwo->fog_lights != null && $checklistTwo->fog_lights != 3 )
	        { ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->fog_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->fog_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Fog lights
    				</div>
    			</div> <?php 
	        } 
			if($checklistTwo->multimedia != null && $checklistTwo->multimedia != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->multimedia == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->multimedia == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Multimedia
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->ac_control_cooling != null && $checklistTwo->ac_control_cooling != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->ac_control_cooling == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->ac_control_cooling == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> A/C Control & Cooling
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->side_mirror != null && $checklistTwo->side_mirror != 3)
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->side_mirror == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->side_mirror == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Side mirror
    				</div>
    			</div> <?php    
			} 
			if($checklistTwo->auxiliary_lights != null && $checklistTwo->auxiliary_lights != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->auxiliary_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->auxiliary_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Auxiliary lights
    				</div>
    			</div>  <?php
			} 
			if($checklistTwo->panel_lights != null && $checklistTwo->panel_lights != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->panel_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->panel_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Panel lights
    				</div>
    			</div> <?php
			}
			if($checklistTwo->horn != null && $checklistTwo->horn != 3) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->horn == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->horn == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Horn
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->window_operation != null && $checklistTwo->window_operation != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->window_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->window_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Window operation
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->sunroof_operation != null && $checklistTwo->sunroof_operation != 3 )
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->sunroof_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->sunroof_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Sunroof operation
    				</div>
    			</div> <?php  
			}  
			if($checklistTwo->wipers_jet_washers != null && $checklistTwo->wipers_jet_washers != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->wipers_jet_washers == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->wipers_jet_washers == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Wipers jet washers
    				</div>
    			</div> <?php  
			} 
			if($checklistTwo->keys_remote_controls != null && $checklistTwo->keys_remote_controls != 3 ) 
			{?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->keys_remote_controls == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->keys_remote_controls == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Keys remote controls
    				</div>
    			</div> <?php 
			} 
			if($checklistTwo->warning_lights != null && $checklistTwo->warning_lights != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->warning_lights == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->warning_lights == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Warning lights
    				</div>
    			</div> <?php 
			} 
			if($checklistTwo->number_plate_light != null && $checklistTwo->number_plate_light != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->number_plate_light == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->number_plate_light == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Number plate light
    				</div>
    			</div> <?php  
			} ?>
		</div>
			 
		<!------------ Underbody ------------->	
		<?php if (hasValid($checklistTwo, $underbodyFields)) { ?>  
		    <div class="row"><h5> Underbody </h5></div>
		<?php } ?>
		<div class="row"> 
		    
		    <?php
		    if($checklistTwo->steering_ball_joints != null && $checklistTwo->steering_ball_joints != 3) 
		    { ?>
     			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->steering_ball_joints == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->steering_ball_joints == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Steering joints and ball joints
    				</div>
			    </div> <?php
		    } 
			if($checklistTwo->brakes_lines != null && $checklistTwo->brakes_lines != 3) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->brakes_lines == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->brakes_lines == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Brakes lines
    				</div>
    			</div>  <?php
			}  
			if($checklistTwo->subframe != null && $checklistTwo->subframe != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->subframe == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->subframe == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Subframe
    				</div>
    			</div> <?php 
			} 
			if($checklistTwo->power_steering_rack != null && $checklistTwo->power_steering_rack != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->power_steering_rack == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->power_steering_rack == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Power steering/ Steering rack
    				</div>
    			</div> <?php 
			}  
			if($checklistTwo->wheels_hubs_bearings != null && $checklistTwo->wheels_hubs_bearings != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->wheels_hubs_bearings == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->wheels_hubs_bearings == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Wheels, hubs, and bearings
    				</div>
    			</div>  <?php   
			}  
		    if($checklistTwo->dampers_bushes != null && $checklistTwo->dampers_bushes != 3 ) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->dampers_bushes == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->dampers_bushes == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Dampers and bushes
    				</div>
    			</div>  <?php 
		    }
		    if($checklistTwo->evidencefloor_chassis != null && $checklistTwo->evidencefloor_chassis != 3 ) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->evidencefloor_chassis == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->evidencefloor_chassis == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Evidence of floor/chassis corrosion
    				</div>
    			</div>  <?php
		    } ?>
		</div>
			 
		<!------------ Test Drive ------------->
		<?php if (hasValid($checklistTwo, $testdriveFields)) { ?>   
		    <div class="row"><h5> Test Drive </h5></div>
		<?php } ?>
		
		<div class="row">
		    <?php
		    if($checklistTwo->engine_performance != null && $checklistTwo->engine_performance != 3 ) 
		    { ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->engine_performance == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->engine_performance == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Engine - Performance
    				</div>
    			</div> <?php
		    } 
		    if($checklistTwo->gearbox_operation != null && $checklistTwo->gearbox_operation != 3 ) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->gearbox_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->gearbox_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Gearbox operation
    				</div>
    			</div> <?php
		    }  
			if($checklistTwo->clutch_operation != null && $checklistTwo->clutch_operation != 3)
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3"> 
    					<?php 
    					if($checklistTwo->clutch_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->clutch_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Clutch operation
    				</div>
    			</div> <?php
			}
			if($checklistTwo->steering_operation != null && $checklistTwo->steering_operation != 3) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->steering_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->steering_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Steering operation
    				</div>
    			</div> <?php
			}
			if($checklistTwo->brake_operation != null && $checklistTwo->brake_operation != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->brake_operation == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->brake_operation == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Brake operation
    				</div>
    			</div>  <?php
			} 
		    if($checklistTwo->hand_parking_brake != null && $checklistTwo->hand_parking_brake != 3) 
		    { ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->hand_parking_brake == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->hand_parking_brake == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Hand brake/ Parking brake
    				</div>
    			</div> <?php
		    }  
			if($checklistTwo->drive_train != null && $checklistTwo->drive_train != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->drive_train == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->drive_train == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Drive Train (4WD,2WD, AWD)
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->instru_control_func != null && $checklistTwo->instru_control_func != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->instru_control_func == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->instru_control_func == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Instruments and controls functioning
    				</div>
    			</div> <?php
			} 
			if($checklistTwo->suspension_noise != null && $checklistTwo->suspension_noise != 3 ) 
			{ ?> 
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->suspension_noise == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->suspension_noise == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Suspension noise
    				</div>
			</div> <?php
			}  
			if($checklistTwo->shock_absorber != null && $checklistTwo->shock_absorber != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->shock_absorber == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->shock_absorber == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Shock absorber
    				</div>
    			</div> <?php
			}  
			if($checklistTwo->road_holding_stability != null && $checklistTwo->road_holding_stability != 3) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->road_holding_stability == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->road_holding_stability == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Road holding stability
    				</div>
    			</div> <?php
			}  
			if($checklistTwo->nois != null && $checklistTwo->nois != 3 ) 
			{ ?>
    			<div class="col-sm-2">
    				<div class="mb-3">
    					<?php 
    					if($checklistTwo->nois == 1)
    					{ ?>
    						<span class="tick"></span> <?php 
    					} 
    					elseif($checklistTwo->nois == 2)
    					{ ?>
    						<span class="tick not"></span> <?php
    					} ?> Nois
    				</div>
    			</div> <?php
			} ?>
		</div>
			 				
	</table>
	<?php 
	if ($hasInCheck) { ?>
	    <p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
	    <?php
	}
} ?>
<!--------------- Checklist end --------------->

<!--------------- DAMAGES START --------------->	
	<?php   
	$damages = DB::table('tbl_report_damages')
		->select('damage_image')
		->where('damage_report_id',$data->report_id)
		->where('damage_status',0)
		->orderby('damage_id','desc')
		->first();  ?>
		
	<table class="table tbl-u-boarderd">
		<?php 
		if($damages)
		{ ?>
		     <h4> Damages </h4>
			 <a href="https://auto-assure.com/crm/uploads/inspectionreport/damages/<?php echo $damages->damage_image;?>" title="Image" target="_blank"><img style="width:250px;height:250px;" class="img-circle" src="https://auto-assure.com/crm/uploads/inspectionreport/damages/<?php echo $damages->damage_image;?>" style="max-width:100%"></a> &nbsp;
			 <?php
		}?>
	</table>	
<!------------------------------>	
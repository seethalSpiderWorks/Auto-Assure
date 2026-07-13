<!-- set alerts/messages/warnings on popup -->
<table class="table tbl-u-boarderd">
	<tr>
		<th class="mdl-td-c">Added Date</th>
		<th>:</th>
		<td><?= date("d-m-Y", strtotime($data->created_at));?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Company UNQ ID</th>
		<th>:</th>
		<td><?=$data->company_unq_id;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Company Name</th>
		<th>:</th>
		<td><?=$data->company_name;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Company Code</th>
		<th>:</th>
		<td><?= $data->company_code;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Contact Person</th>
		<th>:</th>
		<td><?=$data->company_person ;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Mobile</th>
		<th>:</th>
		<td><?= $data->company_mob;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Landline</th>
		<th>:</th>
		<td><?=$data->company_lan;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Email</th>
		<th>:</th>
		<td><?= $data->company_email;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Address</th>
		<th>:</th>
		<td><?=$data->company_address;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Country</th>
		<th>:</th>
		<td><?= $data->country_name;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">State</th>
		<th>:</th>
		<td><?=$data->state_name;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">City</th>
		<th>:</th>
		<td><?= $data->city_name;?></td>
	</tr>
	
	<tr>
		<?php $company_logo = $data->company_logo;?>
		<th class="mdl-td-c"> Logo</th>
		<th>:</th>
		<?php if($company_logo != null) { ?>
		<td><img src="{{url('/')}}<?php echo $company_logo; ?>" style="width:200px;"></td>
		<?php } ?>
    </tr>

	
</table>

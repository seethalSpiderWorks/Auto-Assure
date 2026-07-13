<!-- set alerts/messages/warnings on popup -->
<table class="table tbl-u-boarderd">
	<tr>
		<th class="mdl-td-c">Added Date</th>
		<th>:</th>
		<td><?= date("d-m-Y", strtotime($data->created_at));?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Branch UNQ ID</th>
		<th>:</th>
		<td><?=$data->branch_unqid;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Branch Name</th>
		<th>:</th>
		<td><?=$data->branch_name;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Branch Code</th>
		<th>:</th>
		<td><?= $data->branch_shortcode;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Contact Person</th>
		<th>:</th>
		<td><?=$data->branch_person ;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Mobile</th>
		<th>:</th>
		<td><?= $data->branch_mob;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Landline</th>
		<th>:</th>
		<td><?=$data->branch_land;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Email</th>
		<th>:</th>
		<td><?= $data->branch_email;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Address</th>
		<th>:</th>
		<td><?=$data->branch_address;?></td>
		<td class="mdl-td"></td>
		<!-- <th class="mdl-td-c">Country</th>
		<th>:</th>
		<td> </td>  -->
	</tr>
	<tr>
		<th class="mdl-td-c">State</th>
		<th>:</th>
		<td><?=$data->branch_state;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">District</th>
		<th>:</th>
		<td><?= $data->branch_district;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">Latitude</th>
		<th>:</th>
		<td><?=$data->branch_latitude;?></td>
		<td class="mdl-td"></td>
		<th class="mdl-td-c">Longitude</th>
		<th>:</th>
		<td><?= $data->branch_longitude;?></td>
	</tr>
	<tr>
		<th class="mdl-td-c">GST</th>
		<th>:</th>
		<td><?=$data->branch_gstin;?></td>
	</tr>
</table>

<br>
<div class="card">
    <div class="card-body">
        <h4 class="card-title"><b>Basic Details</b></h4>
        <div class="row table-responsive">
            <table  class="table table-bordered dt-responsive nowrap" style="min-width:955px" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="text-align:center">Date</th>
						<th style="text-align:center">Time</th>
                        <th style="text-align:center">Next Followup</th>
                        <th>Comments</th>
                        <th>Staff</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1;  ?>
                    @foreach($data as $value)
                        <tr>
                            <td style="text-align:center"><?php echo $i;?></td>
                            <td style="text-align:center">{{date("d-m-Y",strtotime($value->followup_date))}}</td>
                            <td style="text-align:center">{{date("h:i a",strtotime($value->followup_on))}} </td>
                            <td style="text-align:center"> <?php 
                                if($value->next_followup_date !='')
                                {
                                  echo  date("d-m-Y",strtotime($value->next_followup_date));
                                }
                                else
                                {
                                    echo  "";
                                }
                                ?>
                            </td>
 
                            <td> {{$value->followup_remarks}} </td>

                            <td>
                                <?php
                                $user = DB::table('users')
                                    ->select('name', 'lname')
                                    ->where('id',$value->followup_created)
                                    ->first();
                                    
                                if($user)
                                {
                                    echo $user->name." ".$user->lname;
                                }
                                else
                                {
                                    echo  '';
                                } ?>
                            </td>

                            <td>
                                <?php
                                $users = DB::table('users')
                                    ->select('name', 'lname')
                                    ->where('id',$value->followup_assigned_users_id)
                                    ->first();
                                    
                                if($users)
                                {
                                    echo $users->name." ".$users->lname;
                                }
                                else
                                {
                                    echo '';
                                } ?>
                            </td>

                            <td>
                                {{$value->followup_current_status}}
                            </td>
                        </tr>

                        <?php $i++; ?>
                    @endforeach
 
                </tbody>
            </table>
        </div>
        @if($data !='')
            @if(count($data))
            <p >Showing <?php echo count($data);?> entries</p>
            @else
            <p >Showing <?php echo "0";?> entries</p>
            @endif
        @else
            <p>Showing <?php echo "0";?> entries</p>
        @endif
    </div>
</div>

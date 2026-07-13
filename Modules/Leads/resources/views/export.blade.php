<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        table { border-collapse:collapse; }
        th, td { border:1px solid #000000; padding:4px 6px; mso-number-format:"\@"; }
        th { background-color:#87AFC6; font-weight:bold; text-align:left; }
    </style>
</head>
<body>
    <table id="visiterDataTable" border="1">
        <thead>
            <tr>
                <th class="sno">#</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Mobile</th>
				<th>Package</th>
                <th>Source</th>
                <th>Staff</th>
                <th>Current Status</th>
                <th>Last Comment</th>
                <th>Lead Date</th>
                <th>Assign Date</th>
                <th>Last Status Update Date</th>
            </tr>
        </thead>
        <tbody>
            <?php   
            foreach($data as $key=>$row)
            { 
                $key++; ?>
                <tr>
                    <td><?php echo $key;?></td>
                    <td><?php echo $row->breg_fname;?></td>
                    <td><?php echo $row->breg_email;?></td>
                    <td><?php echo $row->breg_mob;?></td>
    			    <td><?php echo $row->lead_pack_name;?></td>
                    <td><?php echo $row->source_name; ?></td>
                    <td>
                        <?php  
                        $user = DB::table('users')->where('id',$row->lead_assigned_users)->first();
                            if($user!="")
                            {
                                echo $user->name." ".$user->lname;
                            } 
                            else
                            {
                                $follow = DB::table('tbl_lead_followup')
                                    ->select('followup_assigned_users_id','followup_current_status','followup_created')
                                    ->where('followup_reg_id',$row->lead_reg_id)
                                    ->where('followup_status',0)
                                    ->where(function ($query) {
                                        $query->where('followup_current_status',"Reassign")
                                            ->orWhere('followup_current_status',"Assign");
                                        })
                                    ->latest()
                                    ->first();
                               
                                if($follow !="")
                                {
                                    if($follow ->followup_assigned_users_id !="")
                                    {
                                        $user = DB::table('users')->where('id',$follow ->followup_assigned_users_id)->first();
                                        if($user)
                                        {
                                           echo $user->name." ".$user->lname;
                                        }
                                        else
                                        {
                                            echo  '';
                                        }
                                    }
                                }
                                else
                                {
                                    $follow = DB::table('tbl_lead_followup')
                                            ->select('followup_assigned_users_id','followup_current_status','followup_created')
                                            ->where('followup_reg_id',$row->lead_reg_id)
                                            ->where('followup_status',0)    
                                            ->latest()
                                            ->first(); 
                                        
                                    if($follow !="")
                                    {
                                        if($follow ->followup_created !="")
                                        {
                                            $user = DB::table('users')->where('id',$follow ->followup_created)->first();
                                            if($user)
                                            {
                                                echo $user->name." ".$user->lname;
                                            }
                                            else
                                            {
                                                echo  '';
                                            }
                                        }
                                    } 
                                    else
                                    {
                                        echo  '';
                                    }
                                        
                                }
                            }   ?>
                    </td>
                    <td>
                        <?php
                        $follow = DB::table('tbl_lead_followup')
                                ->select('followup_current_status')
                                ->where('followup_reg_id',$row->lead_reg_id)
                                ->where('followup_status',0)
                                ->latest()
                                ->first();
                                    
                        if($follow)
                        {
                            echo $follow->followup_current_status;
                        }
                        else
                        {
                            echo '';
                        }
                             ?>
                    </td>
                    <td>
                        <?php
                        $follow = DB::table('tbl_lead_followup')
                                ->select('followup_remarks')
                                ->where('followup_reg_id',$row->lead_reg_id)
                                ->where('followup_status',0)
                                ->latest()
                                ->first();
                                            
                        if($follow !='')
                        {
                            if($follow->followup_remarks !='')
                            {
                                echo $follow->followup_remarks;
                            }
                            else
                            {
                                echo '';
                            }
                        }
                        else
                        {
                            echo '';
                        }  ?>
                    </td>
                    <td><?php echo date("d/m/Y",strtotime($row->lead_date));?></td>
                    <td><?php 
                        $leaddate = DB::table('tbl_lead_followup')
                            ->select('followup_date')
                            ->where('followup_reg_id',$row->lead_reg_id)
                            ->where('followup_status',0)
                            ->where(function ($query) 
                                {
                                    $query->where('followup_current_status',"Reassign")
                                        ->orWhere('followup_current_status',"Assign");
                                })
                            ->latest()
                            ->first();
                        
                        if($leaddate)
                        {
                            echo (date("d-m-Y",strtotime($leaddate->followup_date)));
                        }
                        else
                        {
                             echo '';
                        }  ?>
                    </td>
                    <td><?php 
                        $leaddate = DB::table('tbl_lead_followup')
                                ->select('followup_date')
                                ->where('followup_reg_id',$row->lead_reg_id)
                                ->where('followup_status',0)
                                ->latest()
                                ->first();
                                
                        if($leaddate)
                        {
                            echo (date("d/m/Y",strtotime($leaddate->followup_date)));
                        }
                        else
                        {
                            echo '';
                        } ?>
                    </td>
             
                </tr> <?php 
            }  // print_r("hi"); die(); ?>
        
        </tbody>
    </table>
</body>
</html>
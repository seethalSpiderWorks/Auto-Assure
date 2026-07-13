	<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!--h4 class="card-title">Added Company</h4>
                    <p class="card-title-desc"></p-->
				    <div class="table-responsive">	
					    <input type="hidden" name="choosen_privilege" id="choosen_privilege" value="<?php echo $choosen_privilege;?>">
				        <table  id="privilegeDataTable" class="mytable table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="sno" width="18%">Main Menu</th>
                                    <th width="18%">Sub Menu</th>
        						        <?php 
        					            foreach($optionsDisplay as $options){ ?>	   
        						            <th width="<?php echo 30/count($optionsDisplay)?>%"><?php echo $options->option_name; ?> 
        							        <input type="checkbox" class="select_all" name="select_all<?php  echo $options->id; ?>" data-selall_pri_id="<?php  echo $choosen_privilege; ?>" data-op_id="<?php  echo $options->id; ?>" ></th>
        							        <?php    
        						        } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 				   
            				    $maninmenus_array = json_decode($optionsMenudata['alloted_mainmenus']);
            				    $submenus_array = json_decode($optionsMenudata['alloted_submenus']);
            				    if(empty($maninmenus_array)){ $maninmenus_array = array(); }
            					if(empty($submenus_array)){ $submenus_array = array(); }
				   
        				        $main_array = array();
        				        foreach($mainDisplay as $main){					  
        					    if(count($main->Submenus) != 0){
            					    foreach($main->Submenus as $sub) { ?>						   
                					    <tr>
                					        <?php 
                					        if(!in_array($main->id,$main_array)){  ?>
                					            <td <?php if(count($main->Submenus) > 0){ echo "rowspan='".count($main->Submenus)."'";} ?>>					  
                					                <!--<input type="checkbox" class="allocate_main_menu" name="main_data" <?php if(in_array($main->id,$maninmenus_array)){ echo "checked"; }  ?> value="<?php  echo $main->id; ?>">&nbsp -->
                  					                <?php echo $main->main_menuname; ?>
                					            </td>
                					            <?php
                					                array_push($main_array,$main->id);					  
                					        } ?>
                					        
                					        <td>
                					            <input type="checkbox" class="allocate_sub_menu" data-submenu_id="<?php  echo $sub->sub_id; ?>" <?php if(in_array($sub->sub_id,$submenus_array)){ echo "checked"; }  ?> name="sub_data" value="<?php  echo $sub->sub_id; ?>">&nbsp
                					            <?php echo $sub->sub_name;  ?>
                					        </td>
    					  
                    					    <?php 
                                            $set_array = $optionchoosendata->where('opset_privilege','=',$choosen_privilege)->where('opset_main_id','=',$main->id)->where('opset_sub_id','=',$sub->sub_id)->first();
                    					    if($set_array){ $options_set = json_decode($set_array['opset_options']); }else{ $options_set = array();  }	
                    					  
                    					    foreach($optionsDisplay as $options){ ?>	   
                    						    <td style="text-align: center;">
                    						        <input type="checkbox" class="allocate_option_menu" data-optionpriv_id="<?php  echo $choosen_privilege; ?>" data-optionmain_id="<?php  echo $main->id; ?>" data-optionsub_id="<?php  echo $sub->sub_id; ?>" name="option_data<?php  echo $options->id; ?>" value="<?php  echo $options->id; ?>" <?php if(in_array($options->id,$options_set)){ echo "checked"; }  ?>>
                    						    </td>
                    							<?php    
                    						} ?>
    					                </tr> 	
    					  					   
    					                <?php
    					            }
					            }
					            else{ ?>
                                    <tr>
                					    <td>
                					        <!-- <input type="checkbox" class="allocate_main_menu" name="main_data" <?php if(in_array($main->id,$maninmenus_array)){ echo "checked"; }  ?> value="<?php  echo $main->id; ?>"> &nbsp-->					   
                					        <?php  echo $main->main_menuname; ?></td>
                					        <td>
                					            <input type="checkbox" class="allocate_main_menu" name="main_data" <?php if(in_array($main->id,$maninmenus_array)){ echo "checked"; }  ?> value="<?php  echo $main->id; ?>">&nbsp
                					            <?php  echo $main->main_menuname; ?>
                					        </td>
                					    <?php 
                					    $set_array = $optionchoosendata->where('opset_privilege','=',$choosen_privilege)->where('opset_main_id','=',$main->id)->where('opset_sub_id','=','0')->first();
                					    if($set_array){ $options_set = json_decode($set_array['opset_options']); }else{ $options_set = array();  }	
                					  
                					    foreach($optionsDisplay as $options){ ?>	   
                    						<td style="text-align: center;">
                    						    <input type="checkbox" class="allocate_option_menu" name="option_data<?php  echo $options->id; ?>"  data-optionpriv_id="<?php  echo $choosen_privilege; ?>" data-optionmain_id="<?php  echo $main->id; ?>" data-optionsub_id="0"  value="<?php  echo $options->id; ?>" <?php if(in_array($options->id,$options_set)){ echo "checked"; }  ?>>
                    						</td>
							                <?php    
						                } ?>
					                </tr>   						   
						            <?php 						
					            } ?>			   
				 
				                <?php 
				            } ?>				   
                        </tbody>
                    </table>
				</div>                          
        
            </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
	
<script type="text/javascript">
    <?php 
    foreach($optionsDisplay as $options){ ?>
        if($('[name="option_data<?php  echo $options->id; ?>"]:checked').length == $('[name="option_data<?php  echo $options->id; ?>"]').length){ 
            $(":checkbox[name='select_all<?php  echo $options->id; ?>']").attr("checked", true);
        }
        <?php
    } ?>
</script>	  

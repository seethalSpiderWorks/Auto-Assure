   <ul>
                                                    @foreach($permissions as $key=>$row) 
                                                   <li class="checkboxLi" id="id_{{ $key }}">
                                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" >
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                     <input type="checkbox" class="parentP" id="checkbox_id_{{ $key }}"/>
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $row['main_id']; ?>" class="">
                                                        <span> {{ $row['display_name']}}</span>
                                                    </a>
                                                </h4>
                                            </div>
                                           
                                                <div id="collapse<?php echo $row['main_id']; ?>" class="panel-collapse collapse">
                                                    <div class="panel-body" id="checker_menu<?php echo $row['main_id'];?>">
                                                         @foreach($row['child_permissions'] as $childKey=>$childRow)  
                                                        <ul class="pa rplist">
                                                             <li class="childP">
                                                                @if($childRow['menu_id'])
                                                                <input type="checkbox" checked="checked" name="permissions[]" class="permission" value="{{ $childRow['sub_id']}}" /><span> {{ $childRow['display_name']}}</span>                                                
                                                                @else
                                                                <input type="checkbox" name="permissions[]" class="permission" value="{{ $childRow['sub_id']}}" /><span> {{ $childRow['display_name']}}</span>                                                
                                                                @endif
                                                            </li>
                                                        </ul>
                                                        @endforeach
                                                    </div>
                                                </div>
                                               
                                        </div>
                                    </div>
                                </li>
                                     @endforeach
                                 </ul>

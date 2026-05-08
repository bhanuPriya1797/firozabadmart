@component('admin.layouts.main')

@slot('title')
Activity Details - {{ config('app.name') }}
@endslot

<?php

$user_id = (isset($activity->user_id))?$activity->user_id:'';
//$row_id = (isset($activity->row_id))?$activity->row_id:'';
$function_name = (isset($activity->function_name))?$activity->function_name:'';
$action_table = (isset($activity->action_table))?$activity->action_table:'';
$action_type = (isset($activity->action_type))?$activity->action_type:'';
$action_description = (isset($activity->action_description))?$activity->action_description:'';
//$description = (isset($activity->description))?$activity->description:'';
$ip_address = (isset($activity->ip_address))?$activity->ip_address:'';

?>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Activity ({{ $action_type }})</h4>
    </div>
  
    <div class="card">
        <table width="1100" border="0" align="center" cellpadding="0" cellspacing="0" class="mainsec" class="table-responsive">
            <tr>
                <td width="806" valign="top" class="innersec">
                    <table cellspacing="1" class="table table-bordered" cellpadding="0" border="0" width="100%"> 
                        <tr>
                            <td><b>User Name : </b></td>
                            <td>{{$activity->activityLogadmin->name}}</td>
                        </tr>

                        <!-- <tr>
                            <td><b>Row Id: </b></td>
                            <td>{{$activity->row_id}}</td>
                        </tr> -->
                        
                        <tr>
                            <td><b>Last Action link: </b></td>
                            <td style="word-break: break-all; overflow-wrap: break-word; max-width: 100%;">
                                <a href="javascript:void(0);">{{ $function_name }}</a>
                            </td>
                        </tr>

                        <tr>
                            <td><b>Action perform On Table: </b></td>
                            <td>{{$activity->action_table}}</td>
                        </tr>

                        <tr>
                            <td><b>Action Type: </b></td>
                            <td>{{$activity->action_type}}</td>
                        </tr>

                        <tr>
                            <td><b>Action Description: </b></td>
                            <td>{{$activity->action_description}}</td>
                        </tr>

                        <!-- <tr>
                            <td><b>Description: </b></td>
                            <td>{!! $activity->description !!}</td>
                        </tr> -->

                        <tr>
                            <td><b>IP Address: </b></td>
                            <td>{{$activity->ip_address}}</td>
                        </tr>

                        <tr>
                            <td><b>Action Date: </b></td>
                            <td>{{ CustomHelper::DateFormat($activity->created_at, 'd/m/Y') }}</td>
                        </tr>
                   </table>
                </td>
            </tr>
        </table> 
    </div>
</div>
@slot('footerBlock')
@endslot

@endcomponent
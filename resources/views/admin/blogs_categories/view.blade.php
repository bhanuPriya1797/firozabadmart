@component('admin.layouts.main')

@slot('title')
Admin -  - {{ config('app.name') }}
@endslot

<?php

$name = (isset($category->name))?$category->name:'';
$meta_title = (isset($category->meta_title))?$category->meta_title:'';
$meta_keyword = (isset($category->meta_keyword))?$category->meta_keyword:'';
$meta_description = (isset($category->meta_description))?$category->meta_description:'';
$status = (isset($category->status))?$category->status:0;
$sort_order = (isset($category->sort_order))?$category->sort_order:0;
$created_at = (isset($category->created_at))?$category->created_at:0;

?>
<div class="centersec">
<div class="bgcolor viewsec">

    @include('snippets.errors')
    @include('snippets.flash')

    <div class="alert_msg"></div>

<table width="1100" border="0" align="center" cellpadding="0" cellspacing="0" class="mainsec" class="table-responsive">

<tr>
    <td width="806" valign="top" class="innersec">
        <table cellspacing="1" class="table table-bordered" cellpadding="0" border="0" width="100%">
        <h2>Blog Category Detail</h2>   
                <tr>
                  <td><b>Name : </b></td>
                  <td>{{$category->name}}</td>
                </tr>

                <tr>
                    <td><b>No Of Blog: </b></td>
                    <td>{{$category->blogs()->count()}}</td>
                </tr>
                <tr>
                    <td><b>Meta Title: </b></td>
                    <td>{{$category->meta_title}}</td>
                </tr>

                <tr>
                    <td><b>Meta Keyword: </b></td>
                    <td>{{$category->meta_keyword}}</td>
                </tr>
                <tr>
                    <td><b>Meta Description: </b></td>
                    <td>{{$category->meta_description}}</td>
                </tr>



                 <tr>
                    <td><b>Status: </b></td>
                    <td>{{ CustomHelper::getStatusStr($category->status) }}</td>
                </tr>

                <tr>
                    <td><b>Sort Order: </b></td>
                    <td>{{$category->sort_order}}</td>
                </tr>

                <tr>
                    <td><b>Date Created: </b></td>
                    <td>{{CustomHelper::DateFormat($category->created_at, 'd/m/Y')}}</td>
                </tr></table>    
        </td>
    </tr>
</table>
</div>
</div>
@slot('bottomBlock')
@endslot

@endcomponent
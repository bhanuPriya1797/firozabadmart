<?php
$link_type = (isset($link_type)) ? $link_type : '';
$page_id = (isset($page_id)) ? $page_id : '';
$pages = (isset($pages)) ? $pages : collect();
?>

@if(!empty($link_type) && $pages->count() > 0)
    <div class="form-floating">
        <select name="page_id" class="form-select" id="page_id">
            <option value="">Select a {{ ucfirst($link_type) }}...</option>
            @foreach($pages as $page)
                @php
                    $url = '';
                    $selected = '';
                    
                    switch($link_type) {
                        case 'cms':
                            $url = $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'service':
                            $url = 'services/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'portfolio':
                            $url = 'portfolios/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'blog':
                            $url = 'blog/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'news':
                            $url = 'news/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'event':
                            $url = 'events/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                        case 'category':
                            $url = 'category/' . $page->slug;
                            $selected = ($page->id == $page_id) ? 'selected' : '';
                            break;
                    }
                @endphp
                <option value="{{ $page->id }}" data-url="{{ $url }}" {{ $selected }}>
                    {{ $page->title }}
                </option>
            @endforeach
        </select>
        <label for="page_id">Select {{ ucfirst($link_type) }}</label>
    </div>
@else
    <div class="form-floating">
        <select name="page_id" class="form-select" id="page_id" disabled>
            <option value="">No {{ ucfirst($link_type) }} available</option>
        </select>
        <label for="page_id">Select {{ ucfirst($link_type) }}</label>
    </div>
    <div class="alert alert-info mt-2">
        <i class="ti tabler-info-circle me-2"></i>
        No {{ $link_type }} items found. Please create some {{ $link_type }} items first.
    </div>
@endif

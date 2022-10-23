{{--
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@if ((isset($res_item_filter)) && ($res_item_filter))
<div class="mobile-filter">
    <center>
        <div class="dropdown mobile-filter-dropdown is-inline-block" id="filter-type">
            <div class="dropdown-trigger is-pointer" onclick="window.vue.toggleDropdown2(document.getElementById('filter-type'));">
                {{ __('app.select_type') }}&nbsp;<i class="fas fa-chevron-down is-pointer"></i>
            </div>
            <div class="dropdown-menu" role="menu">
                <div class="dropdown-content">
                    @foreach (\App\Models\TypeModel::getTypes() as $item)
                        <a class="dropdown-item" href="{{ url('/') }}?type={{ $item->slug }}">{{ $item->type }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="control has-icons-right is-inline-block">
            <input class="input is-border-rounded is-input-navbar" type="text" placeholder="{{ __('app.search_item') }}" value="@if (isset($_GET['text_search'])) {{ $_GET['text_search'] }} @endif" onkeypress="if (event.which === 13) location.href='{{ url('/') }}?text_search=' + this.value;">

            <span class="icon is-small is-right">
                <i class="fas fa-search"></i>
            </span>
        </div>
    </center>
</div>
@endif
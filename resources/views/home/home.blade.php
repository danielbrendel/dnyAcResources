{{--
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_view')

@section('title', '')

@section('content')
    <div class="container">
        <div class="columns">
            <div class="column is-1"></div>

            <div class="column is-10">
                <div id="item-content"></div>
            </div>

            <div class="column is-1"></div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        window.paginate = null;
        window.filterType = '_all_';
        window.filterText = null;
        window.filterTag = null;

        @if (isset($_GET['type']))
            window.filterType = '{{ $_GET['type'] }}';
        @endif

        @if (isset($_GET['text_search']))
            window.filterText = '{{ $_GET['text_search'] }}';
        @endif

        @if (isset($_GET['tag']))
            window.filterTag = '{{ $_GET['tag'] }}';
        @endif

        window.queryItems = function() {
            let content = document.getElementById('item-content');

            content.innerHTML += '<div id="spinner"><center><i class="fa fa-spinner fa-spin"></i></center></div>';

            let loadmore = document.getElementById('loadmore');
            if (loadmore) {
                loadmore.remove();
            }

            window.vue.ajaxRequest('post', '{{ url('/item/query') }}', {
                paginate: window.paginate,
                type: window.filterType,
                text_search: window.filterText,
                tag: window.filterTag
            },
            function(response) {
                if (response.code == 200) {
                    response.data.forEach(function(elem, index) {
                        let html = window.vue.renderItem(elem);

                        content.innerHTML += html;
                    });

                    if (response.data.length > 0) {
                        window.paginate = response.data[response.data.length - 1].id;
                    }

                    let spinner = document.getElementById('spinner');
                    if (spinner) {
                        spinner.remove();
                    }

                    if (response.data.length === 0) {
                        content.innerHTML += '<div><br/><center>{{ __('app.no_more_items') }}</center></div>';
                    } else {
                        content.innerHTML += '<div id="loadmore"><center><br/><i class="fas fa-plus is-pointer" onclick="window.queryItems();"></i></center></div>';
                    }
                } else {
                    console.error(response.msg);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.queryItems();
        });
    </script>
@endsection
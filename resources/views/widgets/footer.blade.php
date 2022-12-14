{{--
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<div class="footer">
    <div class="columns">
        <div class="column is-4 hide-on-small-device"></div>

        <div class="column is-4">
            <div class="footer-frame">
                <div class="footer-content">
                    &copy; {{ date('Y') }} by {{ env('APP_AUTHOR') }} | <span><a href="{{ url('/imprint') }}">{{ __('app.imprint') }}</a></span>&nbsp;&bull;&nbsp;<span><a href="{{ url('/tos') }}">{{ __('app.terms_of_service') }}</a></span> | <span><a href="{{ env('APP_LINK_GITHUB') }}" title="GitHub"><i class="fab fa-github"></i></a></span>&nbsp;&nbsp;&nbsp;<span><a href="{{ env('APP_LINK_TWITTER') }}" title="Twitter"><i class="fab fa-twitter"></i></a></span>
                </div>
            </div>
        </div>

        <div class="column is-4 hide-on-small-device"></div>
    </div>
</div>
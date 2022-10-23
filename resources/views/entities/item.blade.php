{{--
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_view')

@section('title', ' - ' . $item->name)

@section('content')
    <div class="container">
        <div class="columns">
            <div class="column is-2"></div>

            <div class="column is-8">
                <div class="resource-item-full">
                    <div class="resource-item-full-image" style="background-image: url('{{ asset('gfx/logos/' . $item->logo) }}')"></div>

                    <div class="resource-item-full-about">
                        <div class="resource-item-full-about-title">
                            {{ $item->name }}

                            @auth
                                @if (($user->admin) || ($user->id == $item->userId))
                                    <div class="is-inline-block is-pointer" title="{{ __('app.edit_item') }}" onclick="location.href = '{{ url('/item/' . $item->id . '/edit') }}';"><i class="far fa-edit"></i></div>
                                    <div class="is-inline-block is-pointer" title="{{ __('app.delete_item') }}" onclick="window.vue.deleteItem({{ $item->id }});"><i class="fas fa-times"></i></div>
                                @endif

                                @if ($user->admin)
                                    <div class="is-inline-block is-pointer" title="{{ __('app.lock_item') }}" onclick="location.href = '{{ url('/admin/entity/lock/?id=' . $item->id . '&type=ENT_ITEM') }}';"><i class="fas fa-lock"></i></div>
                                @endif
                            @endauth
                        </div>

                        <div class="resource-item-full-about-hint">{{ $item->summary }}</div>
                        <div class="resource-item-full-about-tags">
                            @foreach ($item->tags as $tag)
                                @if (strlen($tag) > 0)
                                    <span><a href="{{ url('/') }}?tag={{ $tag }}">#{{ $tag }}</a>&nbsp;</span>
                                @endif
                            @endforeach
                        </div>

                        <div class="resource-item-full-preview-mobile">
                            <a href="{{ asset('gfx/logos/' . $item->logo) }}"><img src="{{ asset('gfx/logos/' . $item->logo) }}" alt="Preview"/></a>
                        </div>
                    </div>

                    <div class="resource-item-full-download">
                        <div><a class="button is-success" href="{{ $item->download }}"><i class="fas fa-download"></i>&nbsp;{{ __('app.item_download') }}</a></div>
                        @if (!env('APP_ALLOW_DL_HOSTING'))
                        <div><a class="is-color-grey" href="{{ $item->download }}">{{ $item->download }}</a></div>
                        @endif
                    </div>

                    <div class="resource-item-full-user">
                        {{ __('app.item_creator', ['creator' => $item->creator]) }} &bull; {!! __('app.item_submitted_by', ['user' => $item->userData->username, 'url' => url('/user/' . $item->userData->username)]) !!}
                    </div>

                    <div class="resource-item-full-description is-wrap">{{ $item->description }}</div>

                    @if (isset($item->github->html_url))
                    <div class="resource-item-full-github">
                        @include('widgets.github', ['github' => $item->github])
                    </div>
                    @endif

                    <div class="resource-item-full-links">
                        @if (!isset($item->github->html_url))
                            @if ((is_string($item->github)) && (strlen($item->github) > 0))
                                <div class="resource-item-full-links-github">
                                    <i class="fab fa-github"></i>&nbsp;<a href="https://github.com/{{ $item->github }}">{{ $item->github }}</a>
                                </div>
                            @endif
                        @endif

                        @if (($item->twitter !== null) && (is_string($item->twitter)) && (strlen($item->twitter) > 0))
                        <div class="resource-item-full-links-twitter">
                            <i class="fab fa-twitter"></i>&nbsp;<a href="https://twitter.com/{{ $item->twitter }}">{{ $item->twitter }}</a>
                        </div>
                        @endif

                        @if (($item->website !== null) && (is_string($item->website)) && (strlen($item->website) > 0))
                        <div class="resource-item-full-links-homepage">
                            <i class="fas fa-globe"></i>&nbsp;<a href="{{ $item->website }}">{{ $item->website }}</a>
                        </div>
                        @endif
                    </div>

                    <div class="resource-item-full-stats">
                        <div class="resource-item-full-stats-stars">
                            @for ($i = 0; $i < $item->avg_stars; $i++)
                                <span class="review-star-color"><i class="fas fa-star"></i></span>
                            @endfor

                            @if ($item->avg_stars < 5)
                                @for ($j = $item->avg_stars; $j < 5; $j++)
                                    <span class="review-star-color"><i class="far fa-star"></i></span>
                                @endfor
                            @endif

                            {{ __('app.review_count', ['count' => $item->review_count]) }}
                        </div>

                        <div class="resource-item-full-stats-views">
                            <i class="far fa-eye"></i>&nbsp;{{ $item->views }}
                        </div>
                    </div>
                </div>

                @auth
                    @if ((!$user->admin) && ($user->id !== $item->userId))
                        <div class="resource-item-full-report">
                            <a href="javascript:void(0);" onclick="window.vue.reportItem({{ $item->id }});">{{ __('app.report') }}</a>
                        </div>
                    @endif
                @endauth

                <div class="reviews">
                    <div class="reviews-hint">
                        <div class="is-inline-block">{{ __('app.reviews') }}&nbsp;</div>
                    
                        <div class="is-inline-block">
                            @for ($i = 0; $i < 5; $i++)
                                @if ($i < $item->avg_stars)
                                    <span class="review-star-color"><i class="fas fa-star"></i></span>
                                @else
                                    <span class="review-star-color"><i class="far fa-star"></i></span>
                                @endif
                            @endfor
                        </div>
                    </div>

                    @auth
                        @if ($item->user_review === null)
                        <div class="reviews-write">
                            <div class="reviews-write-title">{{ __('app.write_review') }}</div>

                            <form method="POST" action="{{ url('/item/' . $item->id . '/review/send') }}">
                                @csrf

                                <div class="field">
                                    <div class="control">
                                        <textarea class="textarea" name="content" placeholder="{{ __('app.review_content_placeholder') }}"></textarea>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <span class="review-star-color is-pointer" onclick="window.vue.setRating(1);"><i id="review_rating_star_1" class="far fa-star"></i></span>
                                        <span class="review-star-color is-pointer" onclick="window.vue.setRating(2);"><i id="review_rating_star_2" class="far fa-star"></i></span>
                                        <span class="review-star-color is-pointer" onclick="window.vue.setRating(3);"><i id="review_rating_star_3" class="far fa-star"></i></span>
                                        <span class="review-star-color is-pointer" onclick="window.vue.setRating(4);"><i id="review_rating_star_4" class="far fa-star"></i></span>
                                        <span class="review-star-color is-pointer" onclick="window.vue.setRating(5);"><i id="review_rating_star_5" class="far fa-star"></i></span>

                                        <input type="hidden" name="rating" id="rating" value="0">
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <input type="submit" class="button is-link" value="{{ __('app.submit_review') }}">
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                            <div class="reviews-write">
                                <div class="reviews-write-title">{{ __('app.already_reviewed') }}</div>
                            </div>
                        @endif
                    @endauth

                    <div class="reviews-content" id="review-content"></div>
                </div>

                @if (count($others) > 0)
                <div class="random-resources">
                    <div class="random-resources-hint">{{ __('app.random_items_hint') }}</div>

                    <div class="random-resources-items">
                        @foreach ($others as $item)
                        <div class="resource-item is-pointer" onclick="location.href = '{{ url('/view/' . $item->slug) }}';">
                            <div class="resource-item-image" style="background-image: url('{{ url('/gfx/logos/' . $item->logo) }}')"></div>

                            <div class="resource-item-about">
                                <div class="resource-item-about-title">{{ $item->name }}</div>
                                <div class="resource-item-about-hint">{{ $item->summary }}</div>

                                <div class="resource-item-about-tags">
                                    @foreach ($item->tags as $tag)
                                        @if (strlen($tag) > 0)
                                            <span><a href="{{ url('/') }}?tag={{ $tag }}">#{{ $tag }}</a>&nbsp;</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="resource-item-stats">
                                <div class="resource-item-stats-stars">
                                    @for ($i = 0; $i < $item->avg_stars; $i++)
                                        <span class="review-star-color"><i class="fas fa-star"></i></span>
                                    @endfor

                                    @if ($item->avg_stars < 5)
                                        @for ($j = $item->avg_stars; $j < 5; $j++)
                                            <span class="review-star-color"><i class="far fa-star"></i></span>
                                        @endfor
                                    @endif

                                    {{ __('app.review_count', ['count' => $item->review_count]) }}
                                </div>

                                <div class="resource-item-stats-views">
                                    <i class="far fa-eye"></i>&nbsp;{{ $item->views }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="column is-2"></div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        window.itemId = {{ $item->id }};
        window.paginate = null;
        window.userReviewShown = false;

        @auth
            window.userId = {{ $user->id }};
        @elseguest
            window.userId = 0;
        @endauth

        @auth
            @if ($user->admin)
                window.isAdmin = true;
            @else
                window.isAdmin = false;
            @endif
        @elseguest
            window.isAdmin = false;
        @endauth

        window.queryReviews = function() {
            let content = document.getElementById('review-content');

            content.innerHTML += '<div id="spinner"><center><i class="fa fa-spinner fa-spin"></i></center></div>';

            let loadmore = document.getElementById('loadmore');
            if (loadmore) {
                loadmore.remove();
            }

            window.vue.ajaxRequest('post', '{{ url('/item/query/reviews') }}', {
                itemId: window.itemId,
                paginate: window.paginate
            },
            function(response) {
                if (response.code == 200) {
                    if (!userReviewShown) {
                        if (response.user_review !== null) {
                            content.innerHTML += window.vue.renderReview(response.user_review, window.userId, window.isAdmin);
                        }

                        userReviewShown = true;
                    }

                    response.data.forEach(function(elem, index) {
                        if (response.user_review !== null) {
                            if (response.user_review.id === elem.id) {
                                return;
                            }
                        }

                        let html = window.vue.renderReview(elem, window.userId, window.isAdmin);

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
                        content.innerHTML += '<div id="loadmore"><center><br/><i class="fas fa-plus is-pointer" onclick="window.queryReviews();"></i></center></div>';
                    }
                } else {
                    console.error(response.msg);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.queryReviews();
        });
    </script>
@endsection

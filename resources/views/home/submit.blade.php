{{--
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_view')

@section('content')
    <div class="container">
        <div class="columns">
            <div class="column is-2"></div>

            <div class="column is-8">
                <div class="item-submit-title">{{ __('app.submit_item') }}</div>

                <div class="item-submit-form">
                    <form method="POST" action="{{ url('/submit') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label class="label">{{ __('app.item_name') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="name" placeholder="{{ __('app.item_name_placeholder') }}" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_logo') }}</label>
                            <div class="control">
                                <input class="input" type="file" name="logo" data-role="file" data-button-title="{{ __('app.select_logo') }}" required>
                            </div>
                            <p class="help">{{ __('app.item_logo_hint') }}</p>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_type') }}</label>
                            <div class="control">
                                <select class="input" name="type" required>
                                    <option value="" selected>{{ __('app.item_select_type') }}</option>

                                    @foreach (\App\Models\TypeModel::getTypes() as $item)
                                        <option value="{{ $item->id }}">{{ $item->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_input_creator') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="creator" placeholder="{{ __('app.item_creator_placeholder') }}" value="{{ old('creator') }}" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_summary') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="summary" placeholder="{{ __('app.item_summary_placeholder') }}" value="{{ old('summary') }}" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_description') }}</label>
                            <div class="control">
                                <textarea name="description" placeholder="{{ __('app.item_description_placeholder') }}" required>{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_tags') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="tags" placeholder="{{ __('app.item_tags_placeholder') }}" value="{{ old('tags') }}">
                            </div>
                            <p class="help">{{ __('app.item_tags_hint') }}</p>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_download') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="download" placeholder="{{ __('app.item_download_placeholder') }}" value="{{ old('download') }}" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_github') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="github" placeholder="{{ __('app.item_github_placeholder') }}" value="{{ old('github') }}" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_twitter') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="twitter" placeholder="{{ __('app.item_twitter_placeholder') }}" value="{{ old('twitter') }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_website') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="website" placeholder="{{ __('app.item_website_placeholder') }}" value="{{ old('website') }}">
                            </div>
                        </div>

                        <div class="field">
                            <input class="button is-info is-top-5" type="submit" value="{{ __('app.submit') }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="column is-2"></div>
        </div>
    </div>
@endsection

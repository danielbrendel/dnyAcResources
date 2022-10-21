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
                <div class="item-edit-title">{{ __('app.edit_item') }}</div>

                <div class="item-edit-form">
                    <form method="POST" action="{{ url('/item/' . $item->id . '/edit') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label class="label">{{ __('app.item_logo') }}</label>
                            <div class="control">
                                <input class="input" type="file" name="logo" data-role="file" data-button-title="{{ __('app.select_logo') }}">
                            </div>
                            <p class="help">{{ __('app.item_logo_hint') }}</p>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_type') }}</label>
                            <div class="control">
                                <select class="input" name="type">
                                    @foreach (\App\Models\TypeModel::getTypes() as $type)
                                        <option value="{{ $type->id }}" @if ($type->id === $item->typeId) {{ 'selected' }} @endif>{{ $type->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_input_creator') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="creator" placeholder="{{ __('app.item_creator_placeholder') }}" value="{{ $item->creator }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_summary') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="summary" placeholder="{{ __('app.item_summary_placeholder') }}" value="{{ $item->summary }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_description') }}</label>
                            <div class="control">
                                <textarea name="description" placeholder="{{ __('app.item_summary_placeholder') }}">{{ $item->description }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_tags') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="tags" placeholder="{{ __('app.item_tags_placeholder') }}" value="{{ $item->tags }}">
                            </div>
                            <p class="help">{{ __('app.item_tags_hint') }}</p>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_download') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="download" placeholder="{{ __('app.item_download_placeholder') }}" value="{{ $item->download }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_github') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="github" placeholder="{{ __('app.item_github_placeholder') }}" value="https://github.com/{{ $item->github }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_twitter') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="twitter" placeholder="{{ __('app.item_twitter_placeholder') }}" value="{{ $item->twitter }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.item_website') }}</label>
                            <div class="control">
                                <input class="input" type="text" name="website" placeholder="{{ __('app.item_website_placeholder') }}" value="{{ $item->website }}">
                            </div>
                        </div>

                        <div class="field">
                            <input class="button is-info is-top-5" type="submit" value="{{ __('app.save') }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="column is-2"></div>
        </div>
    </div>
@endsection

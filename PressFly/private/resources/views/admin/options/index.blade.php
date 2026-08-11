<?php
/**
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Option[] $options
 */

?>

@extends('layouts.admin')

@section('title', __('Settings'))

@section('content')
    @if (version_compare(PHP_VERSION, '8.0.0') < 0)
        <div class="alert alert-danger" role="alert">
                <?= __(
                "PressFly will work only on PHP <b>8.0.0</b> or higher starting from the next update so please ask your hosting company to upgrade the PHP version as soon as possible."
            ) ?>
        </div>
    @endif

    <form action="{{ route('admin.options.index') }}" method="post" enctype="multipart/form-data" id="form-settings"
          onSubmit="save_settings.disabled=true; save_settings.value='{{ __('Saving ...') }}'; return true;">
        @csrf

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link" href="#general" aria-controls="general" role="tab"
                                            data-toggle="tab"><?= __('General') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#language" aria-controls="language" role="tab"
                                            data-toggle="tab"><?= __('Language') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#earnings" aria-controls="earnings" role="tab"
                                            data-toggle="tab"><?= __('Earnings') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#protection" aria-controls="protection" role="tab"
                                            data-toggle="tab"><?= __('Protection') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#users" aria-controls="users" role="tab"
                                            data-toggle="tab"><?= __('Users') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#captcha" aria-controls="captcha" role="tab"
                                            data-toggle="tab"><?= __('Captcha') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#integration" aria-controls="integration" role="tab"
                                            data-toggle="tab"><?= __('Code Integration') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#social" aria-controls="Social Media" role="tab"
                                            data-toggle="tab"><?= __('Social Media Links') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#email" aria-controls="email" role="tab"
                                            data-toggle="tab"><?= __('Email') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#social_login" aria-controls="social_login"
                                            role="tab" data-toggle="tab"><?= __('Social Login') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#adlinkfly" aria-controls="adlinkfly"
                                            role="tab" data-toggle="tab"><?= __('AdLinkFly') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#cron" aria-controls="cron" role="tab"
                                            data-toggle="tab"><?= __('Cron') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#sms" aria-controls="sms" role="tab"
                                            data-toggle="tab"><?= __('SMS') ?></a></li>
                </ul>
            </div>
            <div class="card-body">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" id="general" class="tab-pane fade show active">
                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Site Name') ?></div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input type="text" name="Options[{{$settings['site_name']['id']}}][value]"
                                           value="{{ old("Options[{$settings['site_name']['id']}][value]", $settings['site_name']['value']) }}"
                                           class="form-control">
                                    <small class="form-text text-muted">{{ __('This is your site name.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('SEO Site Meta Title') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['site_meta_title']['id']}][value]",
                                    old("Options[{$settings['site_meta_title']['id']}][value]", $settings['site_meta_title']['value']),
                                    ['class' => 'form-control']) }}
                                <small
                                    class="form-text text-muted">{{ __('This is your site meta title. The recommended length is 50-60 characters.') }}</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Site Description') ?></div>
                            <div class="col-sm-10">
                                {{ Form::textarea("Options[{$settings['site_description']['id']}][value]",
                                    old("Options[{$settings['site_description']['id']}][value]", $settings['site_description']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('SEO Site Keywords') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['site_keywords']['id']}][value]",
                                    old("Options[{$settings['site_keywords']['id']}][value]", $settings['site_keywords']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Site Share Image') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['site_share_image']['id']}][value]",
                                    old("Options[{$settings['site_share_image']['id']}][value]", $settings['site_share_image']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Time Zone') ?></div>
                            <div class="col-sm-10">
                                @php $DateTimeZone = \DateTimeZone::listIdentifiers(DateTimeZone::ALL); @endphp
                                {{ Form::select("Options[{$settings['timezone']['id']}][value]", array_combine($DateTimeZone, $DateTimeZone),
                                    old("Options[{$settings['timezone']['id']}][value]", $settings['timezone']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Datetime Format') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['datetime_format']['id']}][value]",
                                    old("Options[{$settings['datetime_format']['id']}][value]", $settings['datetime_format']['value']),
                                    ['class' => 'form-control']) }}
                                <small class="form-text text-muted">
                                    <a href="https://carbon.nesbot.com/docs/#iso-format-available-replacements"
                                       target="_blank">
                                        {{ __('Documentation on date and time formatting') }}
                                    </a>
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable SSL Integration') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['ssl_enable']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['ssl_enable']['id']}][value]", $settings['ssl_enable']['value']),
                                    ['class' => 'form-control']) }}
                                <small class="form-text text-muted">
                                    {{ __('You should install SSL into your website before enable SSL integration. For more information about SSL, please ask your hosting company.') }}
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Display Cookie Notification Bar') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['cookie_notification_bar']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['cookie_notification_bar']['id']}][value]", $settings['cookie_notification_bar']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Allowed Upload file types') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['upload_filetypes']['id']}][value]",
                                    old("Options[{$settings['upload_filetypes']['id']}][value]", $settings['upload_filetypes']['value']),
                                    ['class' => 'form-control']) }}
                                <small
                                    class="form-text text-muted">{{ __('Allowed file types. Separate types by comma.') }}</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Max upload file size') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['fileupload_max']['id']}][value]",
                                    old("Options[{$settings['fileupload_max']['id']}][value]", $settings['fileupload_max']['value']),
                                    ['class' => 'form-control', 'min' => 0]) }}
                                <small
                                    class="form-text text-muted">{{ __('Size in KB. Note 1MB equal to 1024KB') }}</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Listing meta data') ?></div>
                            <div class="col-sm-10">
                                <input type="hidden" name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                       value="0">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                                       value="hits" {{ in_array('hits', $settings['listing_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Hits <small calss="text-muted">(Don't select "Paid views")</small>
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                                       value="paid_views" {{ in_array('paid_views', $settings['listing_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Paid views <small calss="text-muted">(Don't select "Hits")</small>
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                                       value="author" {{ in_array('author', $settings['listing_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Author
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                                       value="published_date" {{ in_array('published_date', $settings['listing_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Published date
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['listing_meta_data']['id'] }}][value][]"
                                                       value="modified_date" {{ in_array('modified_date', $settings['listing_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Modified date
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Article meta data') ?></div>
                            <div class="col-sm-10">
                                <input type="hidden" name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                       value="0">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                                       value="hits" {{ in_array('hits', $settings['article_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Hits <small class="text-muted">(Don't select "Paid views")</small>
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                                       value="paid_views" {{ in_array('paid_views', $settings['article_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Paid views <small calss="text-muted">(Don't select "Hits")</small>
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                                       value="author" {{ in_array('author', $settings['article_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Author
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                                       value="published_date" {{ in_array('published_date', $settings['article_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Published date
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <label class="form-check-label mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="Options[{{ $settings['article_meta_data']['id'] }}][value][]"
                                                       value="modified_date" {{ in_array('modified_date', $settings['article_meta_data']['value'] ?? []) ? 'checked' : '' }}>
                                                Modified date
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <h3>{{ __('Default Pages') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Write and Get Paid Page') }}</div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['write_paid_page']['id']}][value]", $pages,
                                    old("Options[{$settings['write_paid_page']['id']}][value]", $settings['write_paid_page']['value']),
                                    ['placeholder' => '', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Privacy Page') }}</div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['privacy_page']['id']}][value]", $pages,
                                    old("Options[{$settings['privacy_page']['id']}][value]", $settings['privacy_page']['value']),
                                    ['placeholder' => '', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Terms of Use Page') }}</div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['terms_page']['id']}][value]", $pages,
                                    old("Options[{$settings['terms_page']['id']}][value]", $settings['terms_page']['value']),
                                    ['placeholder' => '', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3>{{ __('Mailchimp Newsletter') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Mailchimp API Key') ?></div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input type="text" name="Options[{{$settings['mailchimp_api_key']['id']}}][value]"
                                           value="{{ old("Options[{$settings['mailchimp_api_key']['id']}][value]", $settings['mailchimp_api_key']['value']) }}"
                                           class="form-control">
                                    <small class="form-text text-muted">
                                        {{ __('To get the API key, follow this article') }}
                                        <a target="_blank"
                                           href="https://mailchimp.com/help/about-api-keys/#Find_or_generate_your_API_key">https://mailchimp.com/help/about-api-keys/#Find_or_generate_your_API_key</a>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Mailchimp Audience List Id') ?></div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input type="text" name="Options[{{$settings['mailchimp_list_id']['id']}}][value]"
                                           value="{{ old("Options[{$settings['mailchimp_list_id']['id']}][value]", $settings['mailchimp_list_id']['value']) }}"
                                           class="form-control">
                                    <small class="form-text text-muted">
                                        {{ __('To get the audience list id, follow this article') }}
                                        <a target="_blank" href="https://mailchimp.com/help/find-audience-id/">https://mailchimp.com/help/find-audience-id/</a>
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="language" class="tab-pane fade">

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Language') }}</div>
                            <div class="col-sm-10">
                                <?php
                                $files = \File::glob(resource_path('lang') . "/*.json");
                                $langs = [];
                                foreach ($files as $file) {
                                    $name = pathinfo($file, PATHINFO_FILENAME);
                                    $langs[$name] = $name;
                                }
                                ?>
                                {{ Form::select("Options[{$settings['language']['id']}][value]", $langs,
                                    old("Options[{$settings['language']['id']}][value]", $settings['language']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Language Direction') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['language_direction']['id']}][value]", ['ltr' => __('LTR'), 'rtl' => __('RTL')],
                                    old("Options[{$settings['language_direction']['id']}][value]", $settings['language_direction']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="earnings" class="tab-pane fade">
                        <!--
                    <div class="form-group row">
                        <div class="col-sm-2"><?= __('Enable Pay Per View(PPV)') ?></div>
                        <div class="col-sm-10">
                            {{ Form::select("Options[{$settings['enable_ppv']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                old("Options[{$settings['enable_ppv']['id']}][value]", $settings['enable_ppv']['value']),
                                ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2"><?= __('Enable Pay Per Article(PPA)') ?></div>
                        <div class="col-sm-10">
                            {{ Form::select("Options[{$settings['enable_ppa']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                old("Options[{$settings['enable_ppa']['id']}][value]", $settings['enable_ppa']['value']),
                                ['class' => 'form-control']) }}
                        </div>
                    </div>
-->

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Author Earnings') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['enable_author_earnings']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['enable_author_earnings']['id']}][value]", $settings['enable_author_earnings']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>


                        <div class="form-group row">
                            <div
                                class="col-sm-2"><?= __(
                                    'How many first days after publication to generate earnings?'
                                ) ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['paid_days']['id']}][value]",
                                    old("Options[{$settings['paid_days']['id']}][value]", $settings['paid_days']['value']),
                                    ['class' => 'form-control', 'min' => 0, 'step' => 1, 'max' => 9999999]) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Paid Views Per Day') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['paid_views_day']['id']}][value]",
                                    old("Options[{$settings['paid_views_day']['id']}][value]", $settings['paid_views_day']['value']),
                                    ['class' => 'form-control', 'min' => 1, 'step' => 1]) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Force Disable Adblock') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['force_disable_adblock']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['force_disable_adblock']['id']}][value]", $settings['force_disable_adblock']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3><?= __('Currency Settings') ?></h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Currency Code') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['currency_code']['id']}][value]",
                                    old("Options[{$settings['currency_code']['id']}][value]", $settings['currency_code']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Currency Symbol') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['currency_symbol']['id']}][value]",
                                    old("Options[{$settings['currency_symbol']['id']}][value]", $settings['currency_symbol']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Currency Symbol Position') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['currency_position']['id']}][value]", ['before' => __('Before Price'), 'after' => __('After Price')],
                                    old("Options[{$settings['currency_position']['id']}][value]", $settings['currency_position']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Price Number of Decimals') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['price_decimals']['id']}][value]",
                                    old("Options[{$settings['price_decimals']['id']}][value]", $settings['price_decimals']['value']),
                                    ['class' => 'form-control', 'step' => 1, 'min' => 0, 'max' => 9]) }}
                            </div>
                        </div>


                        <h3><?= __('Referral Settings') ?></h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Referrals') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['enable_referrals']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['enable_referrals']['id']}][value]", $settings['enable_referrals']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="row conditional"
                             data-condition="Options[<?= $settings['enable_referrals']['id'] ?>][value] === '1'">
                            <div class="col-sm-2"><?= __('Referral Percentage') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['referral_percentage']['id']}][value]",
                                    old("Options[{$settings['referral_percentage']['id']}][value]", $settings['referral_percentage']['value']),
                                    ['class' => 'form-control']) }}
                                <small
                                    class="form-text text-muted">{{  __('Enter the referral earning percentage. Ex. 20') }}</small>
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="protection" class="tab-pane fade">
                        <style>
                            .proxy_service {
                                /*margin-bottom: 15px;*/
                            }

                            .proxy_service label {
                                display: block;
                                font-weight: bold;
                            }

                            .proxy_service label span {
                                font-weight: normal;
                            }
                        </style>

                        <div class="alert alert-danger">
                            {{ __('It is highly recommended to use a paid proxy/VPN service detection like IsProxyIP.com to protect your ads and prevent scammers from gaining earnings from your website.') }}
                        </div>

                        <div class="form-group row proxy_service">
                            <div class="col-sm-2"><?= __('Proxy/VPN Service Detection') ?></div>
                            <div class="col-sm-10">
                                <label>
                                    <input type="radio" name="Options[{{  $settings['proxy_service']['id'] }}][value]"
                                           value="disabled"
                                        {!! (old("Options[{$settings['proxy_service']['id']}][value]", $settings['proxy_service']['value']) === 'disabled') ? 'checked="checked"' : '' !!}
                                    > Disabled<span> {{ __('(Not recommended)') }}</span>
                                </label>
                                <label>
                                    <input type="radio" name="Options[{{  $settings['proxy_service']['id'] }}][value]"
                                           value="free"
                                        {!! (old("Options[{$settings['proxy_service']['id']}][value]", $settings['proxy_service']['value']) === 'free') ? 'checked="checked"' : '' !!}
                                    > Free<span> {{ __('(Not recommended)') }}</span>
                                </label>
                                <label>
                                    <input type="radio" name="Options[{{  $settings['proxy_service']['id'] }}][value]"
                                           value="isproxyip"
                                        {!! (old("Options[{$settings['proxy_service']['id']}][value]", $settings['proxy_service']['value']) === 'isproxyip') ? 'checked="checked"' : '' !!}
                                    >
                                    IsProxyIP.com<span> {{ __('(Highly recommended) - Detects Public Proxies, VPN, TOR, Hosting Data Centers, Web Proxies &amp; Bad Search Engine Robots.') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row conditional"
                             data-condition="Options[<?= $settings['proxy_service']['id'] ?>][value] === 'isproxyip'">
                            <div class="col-sm-2"><?= __('IsProxyIP API Key') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['isproxyip_key']['id']}][value]",
                                    old("Options[{$settings['isproxyip_key']['id']}][value]", $settings['isproxyip_key']['value']),
                                    ['class' => 'form-control']) }}

                                <div class="help-block mt-2 alert alert-info">
                                    <?= __('To get an API key you need to register at') ?>
                                    <a href="https://isproxyip.com/pricing"
                                       target="_blank">https://isproxyip.com/pricing</a>
                                </div>
                            </div>
                        </div>

                        <h3><?= __('reCAPTCHA v3 Settings') ?></h3>

                        <div class="alert alert-danger">
                            <?= __(
                                'reCAPTCHA v3 detects abusive traffic on your website without user friction. It returns a score for each request you send to reCAPTCHA and gives you more flexibility to fight against spam and abuse in your website. Learn more from here'
                            ) ?>
                            <a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank"
                               rel="nofollow noreferrer noopener">https://www.google.com/recaptcha/intro/v3.html</a>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('reCAPTCHA v3 Site key') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['recaptcha_v3_site_key']['id']}][value]",
                                    old("Options[{$settings['recaptcha_v3_site_key']['id']}][value]", $settings['recaptcha_v3_site_key']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('reCAPTCHA v3 Secret key') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['recaptcha_v3_secret_key']['id']}][value]",
                                    old("Options[{$settings['recaptcha_v3_secret_key']['id']}][value]", $settings['recaptcha_v3_secret_key']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('reCAPTCHA v3 Score') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['recaptcha_v3_score']['id']}][value]",
                                    old("Options[{$settings['recaptcha_v3_score']['id']}][value]", $settings['recaptcha_v3_score']['value']),
                                    ['class' => 'form-control', 'step' => '0.1', 'min' => '0', 'max' => '1']) }}
                                <small class="form-text text-muted"></small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Protect the article earnings with reCAPTCHA v3') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['recaptcha_v3_article']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['recaptcha_v3_article']['id']}][value]", $settings['recaptcha_v3_article']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Ads Protector') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['ads_protector']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['ads_protector']['id']}][value]", $settings['ads_protector']['value']),
                                    ['class' => 'form-control']) }}

                                <div class="help-block mt-2 alert alert-info">
                                    <?= __(
                                        'Check the visitors with reCAPTCHA v3 score and against proxy/VPN. Make sure to add the correct reCAPTCHA v3 keys.'
                                    ) ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="users" class="tab-pane fade">

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Close Registration') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['close_registration']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['close_registration']['id']}][value]", $settings['close_registration']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Signup Bonus') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['signup_bonus']['id']}][value]",
                                    old("Options[{$settings['signup_bonus']['id']}][value]", $settings['signup_bonus']['value']),
                                    ['class' => 'form-control', 'min' => 0, 'step' => 'any']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Account Activation by Email') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['account_activate_email']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['account_activate_email']['id']}][value]", $settings['account_activate_email']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Reserved Usernames') ?></div>
                            <div class="col-sm-10">
                                {{ Form::textarea("Options[{$settings['reserved_usernames']['id']}][value]",
                                    old("Options[{$settings['reserved_usernames']['id']}][value]", $settings['reserved_usernames']['value']),
                                    ['class' => 'form-control']) }}
                                <small class="form-text text-muted">
                                    {{ __('Separate by comma, no spaces.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" id="captcha" class="tab-pane fade">

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Captcha') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['captcha']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                    old("Options[{$settings['captcha']['id']}][value]", $settings['captcha']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="conditional"
                             data-condition="Options[<?= $settings['captcha']['id'] ?>][value] === '1'">

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Captcha Type') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['captcha_type']['id']}][value]",
                                        [
                                            'recaptcha_v2_checkbox' => __('reCAPTCHA v2 Checkbox'),
                                            'recaptcha_v2_invisible' => __('reCAPTCHA v2 Invisible'),
                                            'hcaptcha_checkbox' => __('hCaptcha Checkbox'),
                                            'solvemedia' => __('Solve Media'),
                                        ],
                                        old("Options[{$settings['captcha_type']['id']}][value]", $settings['captcha_type']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="conditional"
                                 data-condition="Options[<?= $settings['captcha_type']['id'] ?>][value] === 'recaptcha_v2_checkbox'">

                                <legend><?= __('reCAPTCHA v2 Checkbox Settings') ?></legend>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('reCAPTCHA v2 Checkbox Site key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['recaptcha_v2_checkbox_site_key']['id']}][value]",
                                            old("Options[{$settings['recaptcha_v2_checkbox_site_key']['id']}][value]", $settings['recaptcha_v2_checkbox_site_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('reCAPTCHA v2 Checkbox Secret key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['recaptcha_v2_checkbox_secret_key']['id']}][value]",
                                            old("Options[{$settings['recaptcha_v2_checkbox_secret_key']['id']}][value]", $settings['recaptcha_v2_checkbox_secret_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="conditional"
                                 data-condition="Options[<?= $settings['captcha_type']['id'] ?>][value] === 'recaptcha_v2_invisible'">

                                <legend><?= __('reCAPTCHA v2 Invisible Settings') ?></legend>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('reCAPTCHA v2 Invisible Site key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['recaptcha_v2_invisible_site_key']['id']}][value]",
                                            old("Options[{$settings['recaptcha_v2_invisible_site_key']['id']}][value]", $settings['recaptcha_v2_invisible_site_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('reCAPTCHA v2 Invisible Secret key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['recaptcha_v2_invisible_secret_key']['id']}][value]",
                                            old("Options[{$settings['recaptcha_v2_invisible_secret_key']['id']}][value]", $settings['recaptcha_v2_invisible_secret_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="conditional"
                                 data-condition="Options[<?= $settings['captcha_type']['id'] ?>][value] === 'hcaptcha_checkbox'">

                                <legend><?= __('hCaptcha Checkbox Settings') ?></legend>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('hCaptcha Checkbox Site key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['hcaptcha_checkbox_site_key']['id']}][value]",
                                            old("Options[{$settings['hcaptcha_checkbox_site_key']['id']}][value]", $settings['hcaptcha_checkbox_site_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('hCaptcha Checkbox Secret key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['hcaptcha_checkbox_secret_key']['id']}][value]",
                                            old("Options[{$settings['hcaptcha_checkbox_secret_key']['id']}][value]", $settings['hcaptcha_checkbox_secret_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="conditional"
                                 data-condition="Options[<?= $settings['captcha_type']['id'] ?>][value] === 'solvemedia'">

                                <legend><?= __('Solve Media Settings') ?></legend>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('Solve Media Challenge Key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['solvemedia_challenge_key']['id']}][value]",
                                            old("Options[{$settings['solvemedia_challenge_key']['id']}][value]", $settings['solvemedia_challenge_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('Solve Media Verification Key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['solvemedia_verification_key']['id']}][value]",
                                            old("Options[{$settings['solvemedia_verification_key']['id']}][value]", $settings['solvemedia_verification_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2"><?= __('Solve Media Authentication Hash Key') ?></div>
                                    <div class="col-sm-10">
                                        {{ Form::text("Options[{$settings['solvemedia_authentication_key']['id']}][value]",
                                            old("Options[{$settings['solvemedia_authentication_key']['id']}][value]", $settings['solvemedia_authentication_key']['value']),
                                            ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Enable on Login Form') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['captcha_login']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                        old("Options[{$settings['captcha_login']['id']}][value]", $settings['captcha_login']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Enable on Register Form') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['captcha_register']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                        old("Options[{$settings['captcha_register']['id']}][value]", $settings['captcha_register']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Enable on Forgot Password Form') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['captcha_forgot_password']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                        old("Options[{$settings['captcha_forgot_password']['id']}][value]", $settings['captcha_forgot_password']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Enable on Contact Form') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['captcha_contact']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                        old("Options[{$settings['captcha_contact']['id']}][value]", $settings['captcha_contact']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div role="tabpanel" id="integration" class="tab-pane fade">

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Add code between <head> & </head> of the frontend') }}</div>
                            <div class="col-sm-10">
                                {{ Form::textarea("Options[{$settings['frontend_head_code']['id']}][value]",
                                    old("Options[{$settings['frontend_head_code']['id']}][value]", $settings['frontend_head_code']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">{{ __('Add code before </body> of the frontend') }}</div>
                            <div class="col-sm-10">
                                {{ Form::textarea("Options[{$settings['frontend_footer_code']['id']}][value]",
                                    old("Options[{$settings['frontend_footer_code']['id']}][value]", $settings['frontend_footer_code']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="social" class="tab-pane fade">

                        <p>We only support FontAwesome icons and you can find a list of all icons at
                            <a target="_blank" href="https://fontawesome.com/v5.15/icons?d=gallery&p=2&m=free">https://fontawesome.com/v5.15/icons?d=gallery&p=2&m=free</a>
                        </p>

                        <textarea id="template_social_link_{{ $settings['social_links']['id'] }}" style="display: none;"
                                  readonly><li class="ui-state-default list-group-item">
                                    <div class="form-inline">
                                        <i class="fas fa-exchange-alt fa-rotate-90"></i>

                                        <div class="form-group">
                                            <label>{{ __('Name') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{SOCIAL_KEY}][name]"
                                                   value="">
                                        </div>


                                        <div class="form-group">
                                            <label>{{ __('Icon') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{SOCIAL_KEY}][icon]"
                                                   value="">
                                        </div>

                                        <div class="form-group">
                                            <label>{{ __('URL') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{SOCIAL_KEY}][url]"
                                                   value="">
                                        </div>

                                        <a href="#" class="btn btn-danger btn btn-sm float-right social-delete">
                                            {{ __('Delete') }}
                                        </a>
                                    </div>
                                </li></textarea>

                        <a href="#" class="social-add btn btn-info">Add new item</a>

                        <ul id="sortable_social_links" class="list-group mb-3">
                            @foreach($settings['social_links']['value'] ?? [] as $key => $social_link)
                                <li class="ui-state-default list-group-item">
                                    <div class="form-inline">
                                        <i class="fas fa-exchange-alt fa-rotate-90"></i>

                                        <div class="form-group">
                                            <label>{{ __('Name') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{{ $key }}][name]"
                                                   value="{{ old("Options[{$settings['social_links']['id']}][value][$key][name]", $social_link['name']) }}">
                                        </div>


                                        <div class="form-group">
                                            <label>{{ __('Icon') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{{ $key }}][icon]"
                                                   value="{{ old("Options[{$settings['social_links']['id']}][value][$key][icon]", $social_link['icon']) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>{{ __('URL') }}</label>
                                            <input type="text" class="form-control mx-sm-3"
                                                   name="Options[{{ $settings['social_links']['id'] }}][value][{{ $key }}][url]"
                                                   value="{{ old("Options[{$settings['social_links']['id']}][value][$key][url]", $social_link['url']) }}">
                                        </div>

                                        <a href="#" class="btn btn-danger btn btn-sm float-right social-delete">
                                            {{ __('Delete') }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                    </div>

                    <div role="tabpanel" id="email" class="tab-pane fade">

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Admin Email') ?></div>
                            <div class="col-sm-10">
                                {{ Form::email("Options[{$settings['admin_email']['id']}][value]",
                                    old("Options[{$settings['admin_email']['id']}][value]", $settings['admin_email']['value']),
                                    ['class' => 'form-control']) }}
                                <small
                                    class="form-text text-muted">{{ __('The recipient email for the contact form and admin notifications.') }}</small>
                            </div>
                        </div>

                        <h3>{{ __('Admin Notifications') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('New Article Added') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_admin_new_article']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_admin_new_article']['id']}][value]", $settings['alert_admin_new_article']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('New Article Update Added') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_admin_update_article']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_admin_update_article']['id']}][value]", $settings['alert_admin_update_article']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('New User Registration') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_admin_new_user_register']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_admin_new_user_register']['id']}][value]", $settings['alert_admin_new_user_register']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('New Withdrawal Request') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_admin_new_withdrawal']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_admin_new_withdrawal']['id']}][value]", $settings['alert_admin_new_withdrawal']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3>{{ __('Member Notifications') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('New Article Approved') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_member_approved_new_article']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_member_approved_new_article']['id']}][value]", $settings['alert_member_approved_new_article']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Article Update Approved') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_member_approved_update_article']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_member_approved_update_article']['id']}][value]", $settings['alert_member_approved_update_article']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Approved Withdrawal Request') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_member_approved_withdraw']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_member_approved_withdraw']['id']}][value]", $settings['alert_member_approved_withdraw']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Completed Withdrawal Request') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_member_completed_withdraw']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_member_completed_withdraw']['id']}][value]", $settings['alert_member_completed_withdraw']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Canceled Withdrawal Request') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['alert_member_canceled_withdraw']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['alert_member_canceled_withdraw']['id']}][value]", $settings['alert_member_canceled_withdraw']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3>{{ __('Sending Email Settings') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('From Email') ?></div>
                            <div class="col-sm-10">
                                {{ Form::email("Options[{$settings['email_from']['id']}][value]",
                                    old("Options[{$settings['email_from']['id']}][value]", $settings['email_from']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Email Method') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['email_method']['id']}][value]",
                                    [
                                        'sendmail' => __('Sendmail'),
                                        'smtp' => __('SMTP'),
                                        'phpmailer-mail' => __('Mail Function - PHPMailer'),
                                        'phpmailer-smtp' => __('SMTP - PHPMailer'),
                                        'phpmailer-sendmail' => __('Sendmail - PHPMailer'),
                                    ],
                                    old("Options[{$settings['email_method']['id']}][value]", $settings['email_method']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="conditional"
                             data-condition="['smtp','phpmailer-smtp'].includes(Options[<?= $settings['email_method']['id'] ?>][value])">
                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('SMTP Connection Security') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['email_smtp_security']['id']}][value]", ['' => __('None'), 'ssl' => __('SSL'), 'tls' => __('TLS')],
                                        old("Options[{$settings['email_smtp_security']['id']}][value]", $settings['email_smtp_security']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('SMTP Outgoing Host') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::text("Options[{$settings['email_smtp_host']['id']}][value]",
                                        old("Options[{$settings['email_smtp_host']['id']}][value]", $settings['email_smtp_host']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('SMTP Outgoing Port') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::number("Options[{$settings['email_smtp_port']['id']}][value]",
                                        old("Options[{$settings['email_smtp_port']['id']}][value]", $settings['email_smtp_port']['value']),
                                        ['class' => 'form-control']) }}
                                    <small
                                        class="form-text text-muted">{{ __('Port value depends on the Connection Security type you set above None - port 25, SSL - port 465, TLS - port 587. these values maybe different between email providers.') }}</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('SMTP Username') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::text("Options[{$settings['email_smtp_username']['id']}][value]",
                                        old("Options[{$settings['email_smtp_username']['id']}][value]", $settings['email_smtp_username']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('SMTP Password') ?></div>
                                <div class="col-sm-10">
                                    <input type="password"
                                           name="Options[{{$settings['email_smtp_password']['id']}}][value]"
                                           class="form-control"
                                           value="{{ old("Options[{$settings['email_smtp_password']['id']}][value]", $settings['email_smtp_password']['value']) }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="social_login" class="tab-pane fade">

                        <h3>{{ __('Facebook Settings') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Facebook') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['social_login_facebook']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['social_login_facebook']['id']}][value]", $settings['social_login_facebook']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('App Id') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_facebook_app_id']['id']}][value]",
                                    old("Options[{$settings['social_login_facebook_app_id']['id']}][value]", $settings['social_login_facebook_app_id']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('App Secret') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_facebook_app_secret']['id']}][value]",
                                    old("Options[{$settings['social_login_facebook_app_secret']['id']}][value]", $settings['social_login_facebook_app_secret']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3>{{ __('Twitter Settings') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Twitter') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['social_login_twitter']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['social_login_twitter']['id']}][value]", $settings['social_login_twitter']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Consumer Key (API Key)') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_twitter_api_key']['id']}][value]",
                                    old("Options[{$settings['social_login_twitter_api_key']['id']}][value]", $settings['social_login_twitter_api_key']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Consumer Secret (API Secret)') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_twitter_api_secret']['id']}][value]",
                                    old("Options[{$settings['social_login_twitter_api_secret']['id']}][value]", $settings['social_login_twitter_api_secret']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <h3>{{ __('Google Settings') }}</h3>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Enable Google') ?></div>
                            <div class="col-sm-10">
                                {{ Form::select("Options[{$settings['social_login_google']['id']}][value]", [0 => __('No'), 1 => __('Yes')],
                                    old("Options[{$settings['social_login_google']['id']}][value]", $settings['social_login_google']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Client ID') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_google_client_id']['id']}][value]",
                                    old("Options[{$settings['social_login_google_client_id']['id']}][value]", $settings['social_login_google_client_id']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Client Secret') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['social_login_google_client_secret']['id']}][value]",
                                    old("Options[{$settings['social_login_google_client_secret']['id']}][value]", $settings['social_login_google_client_secret']['value']),
                                    ['class' => 'form-control']) }}
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="adlinkfly" class="tab-pane fade">

                        <p>{{ __('Add the following settings on your AdLinkFly website') }}</p>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('PressFly Access URL') ?></div>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" readonly onfocus="javascript:this.select()"
                                       value="{{ route('api.adlinkfly.article.view') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('PressFly Secret Key') ?></div>
                            <div class="col-sm-10">
                                {{ Form::text("Options[{$settings['adlinkfly_secret_key']['id']}][value]",
                                    old("Options[{$settings['adlinkfly_secret_key']['id']}][value]", $settings['adlinkfly_secret_key']['value']),
                                    ['class' => 'form-control', 'onfocus' => 'javascript:this.select()', 'readonly' => true]) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2"><?= __('Counter') ?></div>
                            <div class="col-sm-10">
                                {{ Form::number("Options[{$settings['adlinkfly_counter']['id']}][value]",
                                    old("Options[{$settings['adlinkfly_counter']['id']}][value]", $settings['adlinkfly_counter']['value']),
                                    ['class' => 'form-control', 'min' => 0, 'step' => 1]) }}
                            </div>
                        </div>

                    </div>

                    <div role="tabpanel" id="cron" class="tab-pane fade">

                        <?php
                        // http://php.net/manual/en/features.commandline.options.php
                        // PHP Executable Path
                        $phpFinder = new \Symfony\Component\Process\PhpExecutableFinder();
                        $phpPath = $phpFinder->find() ?: 'php';
                        ?>
                        <div class="mb-1">{{ __('Cron Job Command') }}</div>
                        <code class="d-block mb-3" style="font-size: 75%;">
                            * * * * * {{ $phpPath }} -d 'register_argc_argv=on;' -d
                            'apc.enabled=0;' {{ base_path('artisan') }} schedule:run >> /dev/null 2>&1
                        </code>

                    </div>
                    
                    <div role="tabpanel" id="sms" class="tab-pane fade">
                        <fieldset>
                            <legend><?= __('SMS Verification Configuration') ?></legend>
                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Enable SMS Verification') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::select("Options[{$settings['sms_verification_enabled']['id']}][value]", [1 => __('Yes'), 0 => __('No')],
                                        old("Options[{$settings['sms_verification_enabled']['id']}][value]", $settings['sms_verification_enabled']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend><?= __('Revesms Configuration') ?></legend>
                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('API Key') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::text("Options[{$settings['sms_revesms_api_key']['id']}][value]",
                                        old("Options[{$settings['sms_revesms_api_key']['id']}][value]", $settings['sms_revesms_api_key']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Secret Key') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::text("Options[{$settings['sms_revesms_secret_key']['id']}][value]",
                                        old("Options[{$settings['sms_revesms_secret_key']['id']}][value]", $settings['sms_revesms_secret_key']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2"><?= __('Caller ID') ?></div>
                                <div class="col-sm-10">
                                    {{ Form::text("Options[{$settings['sms_revesms_caller_id']['id']}][value]",
                                        old("Options[{$settings['sms_revesms_caller_id']['id']}][value]", $settings['sms_revesms_caller_id']['value']),
                                        ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>

                <div class="form-group">
                    <input type="submit" name="save_settings" class="btn btn-primary" value="{{ __('Save') }}">
                </div>
            </div>
        </div>

    </form>

    <hr>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= __('Send Test SMS') ?></h3>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ route('admin.options.testSms') }}" class="form-inline">
                @csrf
                <div class="form-group">
                    <input type="tel" name="mobile" class="form-control" placeholder="{{ __('Enter Mobile Number') }}" required>
                </div>
                <button type="submit" class="btn btn-success ml-2">{{ __('Send Test SMS') }}</button>
            </form>
        </div>
    </div>

@endsection

@push('footer')
    <script>
        /**
         * Bootstrap 4: Keep selected tab on page refresh
         */
        // store the currently selected tab in the localStorage
        $('#form-settings a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var id = $(e.target).attr('href').substr(1);
            localStorage.setItem('settings_selectedTab', id);
        });

        // on load of the page: switch to the currently selected tab
        var selectedTab = localStorage.getItem('settings_selectedTab');

        if ($('#form-settings').length && selectedTab !== null) {
            $('#form-settings a[data-toggle="tab"][href="#' + selectedTab + '"]').tab('show');
        } else {
            $('#form-settings a[data-toggle="tab"]:first').tab('show');
        }

    </script>
@endpush

@push('footer')
    <style>
        .ui-sortable .list-group-item {
            cursor: grabbing;
        }

        .ui-sortable .list-group-item .form-inline > i {
            margin-right: 15px;
        }
    </style>
    <script>
        $(function () {
            $("#sortable_social_links").sortable({
                //placeholder: "ui-state-highlight",
                items: "> li",
                cursor: 'move',
                opacity: 0.6,
                update: function (event, ui) {
                }
            }).disableSelection();
        });

        $(document).on('click', '.social-add', function (e) {
            e.preventDefault();

            var count = $('#sortable_social_links > li.list-group-item').length;

            var template = $("#template_social_link_{{ $settings['social_links']['id'] }}").val().replaceAll('{SOCIAL_KEY}', count);

            $("#sortable").append(template);
        });

        $(document).on('click', '.social-delete', function (e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $(this).closest('li.list-group-item').remove();
            }
            e.returnValue = false;
            return false;
        });
    </script>
@endpush

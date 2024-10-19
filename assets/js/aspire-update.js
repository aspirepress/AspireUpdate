jQuery(document).ready(function () {
    new FieldRules();
    new ApiKey();
});

class ApiKey {
    constructor() {
        ApiKey.fetch_api_key.init();
    }

    static fetch_api_key = {
        init() {
            jQuery('#aspirepress-generate-api-key').click(function () {
                ApiKey.fetch_api_key.hide_error();
                ApiKey.fetch_api_key.get();
            });
            ApiKey.fetch_api_key.hide_error();
        },
        get() {
            let parameters = {
                "url": aspirepress.apikey_api_url,
                "type": "POST",
                "contentType": 'application/json',
                "data": JSON.stringify({
                    "domain": aspirepress.domain
                })
            };
            jQuery.ajax(parameters)
            .done(function (response) {
                jQuery('#aspirepress-generate-api-key').parent().find('#aspirepress-settings-field-api_key').val(response.apikey);
            })
            .fail(function (response) {
                if ((response.status === 400) || (response.status === 401)) {
                    ApiKey.fetch_api_key.show_error(response.responseJSON?.error);
                } else {
                    ApiKey.fetch_api_key.show_error('Unexpected Error: ' + response.status);
                }
            });
        },
        show_error(message) {
            jQuery('#aspirepress-generate-api-key').parent().find('.error').html(message).show();
        },
        hide_error() {
            jQuery('#aspirepress-generate-api-key').parent().find('.error').html('').hide();
        }
    }
}

class FieldRules {
    constructor() {
        FieldRules.check_enabled_rewrites.init();
        FieldRules.check_enabled_debug_mode.init();
    }

    static check_enabled_rewrites = {
        init() {
            jQuery('#aspirepress-settings-field-enable').change(function () {
                if (jQuery(this).is(':checked')) {
                    FieldRules.show_field('api_key');
                    FieldRules.make_required('api_key');
                    FieldRules.show_field('api_hosts');
                    FieldRules.check_enabled_rewrite_wporg_api.init();
                    FieldRules.check_enabled_rewrite_wporg_downlods_api.init();
                } else {
                    FieldRules.hide_field('api_key');
                    FieldRules.remove_required('api_key');
                    FieldRules.hide_field('api_hosts');
                }
            }).change();
        }
    }

    static check_enabled_rewrite_wporg_api = {
        init() {
            jQuery('#aspirepress-settings-field-rewrite_wporg_api').change(function () {
                if (jQuery(this).is(':checked')) {
                    FieldRules.show_field('api_url');
                    FieldRules.make_required('api_url');
                } else {
                    FieldRules.hide_field('api_url');
                    FieldRules.remove_required('api_url');
                }
            }).change();
        }
    }

    static check_enabled_rewrite_wporg_downlods_api = {
        init() {
            jQuery('#aspirepress-settings-field-rewrite_wporg_dl').change(function () {
                if (jQuery(this).is(':checked')) {
                    FieldRules.show_field('api_download_url');
                    FieldRules.make_required('api_download_url');
                } else {
                    FieldRules.hide_field('api_download_url');
                    FieldRules.remove_required('api_download_url');
                }
            }).change();
        }
    }

    static check_enabled_debug_mode = {
        init() {
            jQuery('#aspirepress-settings-field-enable_debug').change(function () {
                if (jQuery(this).is(':checked')) {
                    FieldRules.show_field('enable_debug_type');
                    FieldRules.show_field('disable_ssl_verification');
                } else {
                    FieldRules.hide_field('enable_debug_type');
                    FieldRules.hide_field('disable_ssl_verification');
                }
            }).change();
        }
    }

    static hide_field(id) {
        jQuery('.aspirepress-settings-field-wrapper-' + id).parent().parent().hide();
    }

    static show_field(id) {
        let field_row = jQuery('.aspirepress-settings-field-wrapper-' + id).parent().parent();
        field_row.show().addClass('glow-reveal');
        setTimeout(function () {
            field_row.removeClass('glow-reveal');
        }, 500);
    }

    static remove_required(id) {
        jQuery('#aspirepress-settings-field-' + id).prop('required', false);
    }

    static make_required(id) {
        jQuery('#aspirepress-settings-field-' + id).prop('required', true);
    }
}
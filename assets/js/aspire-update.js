jQuery(document).ready(function () {
    new FieldRules();
    new ApiHost();
});


class ApiHost {
    constructor() {
        ApiHost.host_selector.init();
        ApiHost.other_hosts.init();
        ApiHost.api_key.init();
    }

    static host_selector = {
        field: jQuery('#aspireupdate-settings-field-api_host'),
        init() {
            ApiHost.host_selector.field.change(function () {
                let selected_option = ApiHost.host_selector.field.find(":selected");
                if ('other' === selected_option.val()) {
                    ApiHost.other_hosts.show();
                } else {
                    ApiHost.other_hosts.hide();
                }

                if (ApiHost.host_selector.is_api_key_required()) {
                    ApiHost.api_key.make_required();
                } else {
                    ApiHost.api_key.remove_required();
                }

                if (ApiHost.host_selector.has_api_key_url()) {
                    ApiHost.api_key.show_action_button();
                } else {
                    ApiHost.api_key.hide_action_button();
                }
            }).change();
        },
        is_api_key_required() {
            let selected_option = ApiHost.host_selector.field.find(":selected");
            let require_api_key = selected_option.attr('data-require-api-key');
            if ('true' === require_api_key) {
                return true;
            }
            return false;
        },
        has_api_key_url() {
            let selected_option = ApiHost.host_selector.field.find(":selected");
            let api_url = selected_option.attr('data-api-key-url');
            if ('' !== api_url) {
                return true;
            }
            return false;
        },
        get_api_key_url() {
            let selected_option = ApiHost.host_selector.field.find(":selected");
            let api_url = selected_option.attr('data-api-key-url');
            if ('' !== api_url) {
                return api_url;
            }
            return '';
        },
    }

    static other_hosts = {
        field: jQuery('#aspireupdate-settings-field-api_host_other'),
        init() {
            ApiHost.other_hosts.field.on("blur", function () {
                let value = ApiHost.other_hosts.field.val();
                value = ApiHost.other_hosts.strip_protocol(value);
                value = ApiHost.other_hosts.strip_dangerous_characters(value);
                ApiHost.other_hosts.field.val(value);
            });
        },
        show() {
            ApiHost.other_hosts.field.parent().show();
            ApiHost.other_hosts.field.focus();
            ApiHost.other_hosts.make_required();
        },
        hide() {
            ApiHost.other_hosts.field.parent().hide();
            ApiHost.other_hosts.remove_required();
        },
        make_required() {
            ApiHost.other_hosts.field.prop('required', true);
        },
        remove_required() {
            ApiHost.other_hosts.field.prop('required', false);
        },
        strip_protocol(value) {
            const protocol_regex = /^(https?|ftp|sftp|smtp|ftps|file):\/\/|^www\./i;
            return value.replace(protocol_regex, '');
        },
        strip_dangerous_characters(value) {
            const dangerous_characters_regex = /[<>/"'&;]/g;
            return value.replace(dangerous_characters_regex, '');
        }
    }

    static api_key = {
        field: jQuery('#aspireupdate-settings-field-api_key'),
        action_button: jQuery('#aspireupdate-generate-api-key'),
        init() {
            ApiHost.api_key.action_button.click(function () {
                ApiHost.api_key.hide_error();
                ApiHost.api_key.get_api_key();
            });
            ApiHost.api_key.hide_error();
        },
        get_api_key() {
            let parameters = {
                "url": ApiHost.host_selector.get_api_key_url(),
                "type": "POST",
                "contentType": 'application/json',
                "data": JSON.stringify({
                    "domain": aspireupdate.domain
                })
            };
            jQuery.ajax(parameters)
                .done(function (response) {
                    ApiHost.api_key.field.val(response.apikey);
                })
                .fail(function (response) {
                    if ((response.status === 400) || (response.status === 401)) {
                        ApiHost.api_key.show_error(response.responseJSON?.error);
                    } else {
                        ApiHost.api_key.show_error('Unexpected Error: ' + response.status);
                    }
                });
        },
        show() {
            ApiHost.api_key.field.parent().parent().parent().show();
        },
        hide() {
            ApiHost.api_key.field.parent().parent().parent().hide();
        },
        show_action_button() {
            ApiHost.api_key.action_button.show();
        },
        hide_action_button() {
            ApiHost.api_key.action_button.hide();
        },
        make_required() {
            ApiHost.api_key.field.prop('required', true);
        },
        remove_required() {
            ApiHost.api_key.field.prop('required', false);
        },
        show_error(message) {
            ApiHost.api_key.field.parent().find('.error').html(message).show();
        },
        hide_error() {
            ApiHost.api_key.field.parent().find('.error').html('').hide();
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
            jQuery('#aspireupdate-settings-field-enable').change(function () {
                if (jQuery(this).is(':checked')) {
                    FieldRules.show_field('api_host');
                    FieldRules.show_field('api_key');
                } else {
                    FieldRules.hide_field('api_host');
                    FieldRules.hide_field('api_key');
                }
            }).change();
        }
    }

    static check_enabled_debug_mode = {
        init() {
            jQuery('#aspireupdate-settings-field-enable_debug').change(function () {
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
        jQuery('.aspireupdate-settings-field-wrapper-' + id).parent().parent().hide();
    }

    static show_field(id) {
        let field_row = jQuery('.aspireupdate-settings-field-wrapper-' + id).parent().parent();
        field_row.show().addClass('glow-reveal');
        setTimeout(function () {
            field_row.removeClass('glow-reveal');
        }, 500);
    }

    static remove_required(id) {
        jQuery('#aspireupdate-settings-field-' + id).prop('required', false);
    }

    static make_required(id) {
        jQuery('#aspireupdate-settings-field-' + id).prop('required', true);
    }
}
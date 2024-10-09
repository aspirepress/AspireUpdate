jQuery(document).ready(function () {
    new FieldRules();
});

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
                    FieldRules.show_field('api_host');
                    FieldRules.show_field('rewrite_wporg_api');
                    FieldRules.show_field('api_url');
                    FieldRules.show_field('rewrite_wporg_dl');
                    FieldRules.show_field('api_download_url');
                    FieldRules.check_enabled_rewrite_wporg_api.init();
                    FieldRules.check_enabled_rewrite_wporg_downlods_api.init();
                } else {
                    FieldRules.hide_field('api_key');
                    FieldRules.remove_required('api_key');
                    FieldRules.hide_field('api_host');
                    FieldRules.hide_field('rewrite_wporg_api');
                    FieldRules.hide_field('api_url');
                    FieldRules.hide_field('rewrite_wporg_dl');
                    FieldRules.hide_field('api_download_url');
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
                    FieldRules.show_field('exclude_debug_type');
                    FieldRules.show_field('debug_log_path');
                    FieldRules.show_field('disable_ssl_verification');
                    FieldRules.show_field('examine_responses');
                } else {
                    FieldRules.hide_field('enable_debug_type');
                    FieldRules.hide_field('exclude_debug_type');
                    FieldRules.hide_field('debug_log_path');
                    FieldRules.hide_field('disable_ssl_verification');
                    FieldRules.hide_field('examine_responses');
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
jQuery(document).ready(function($) {
    'use strict';

    // Media uploader
    var mediaUploader;

    $('#fab_upload_icon_button').on('click', function(e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Icon',
            button: {
                text: 'Choose Icon'
            },
            multiple: false
        });

        // When a file is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#fab_custom_icon').val(attachment.url);
            $('#fab_icon_preview').html('<img src="' + attachment.url + '" style="max-width: 50px; max-height: 50px;" />');
        });

        // Open the uploader dialog
        mediaUploader.open();
    });

    // Show/hide custom icon field based on icon type selection
    function toggleCustomIconField() {
        var iconType = $('#fab_icon_type').val();
        if (iconType === 'custom') {
            $('#fab_custom_icon_wrapper').show();
        } else {
            $('#fab_custom_icon_wrapper').hide();
        }
    }

    // Show/hide WhatsApp fields based on icon type and toggle
    function toggleWhatsAppFields() {
        var iconType = $('#fab_icon_type').val();
        var generateWhatsApp = $('#fab_generate_whatsapp').is(':checked');

        // Show/hide WhatsApp section
        if (iconType === 'whatsapp') {
            $('#fab_generate_whatsapp').closest('tr').show();
            
            if (generateWhatsApp) {
                // Hide target URL, show phone and message fields
                $('#fab_target_url').closest('tr').hide();
                $('#fab_phone_number').closest('tr').show();
                $('#fab_prefilled_message').closest('tr').show();
            } else {
                // Show target URL, hide phone and message fields
                $('#fab_target_url').closest('tr').show();
                $('#fab_phone_number').closest('tr').hide();
                $('#fab_prefilled_message').closest('tr').hide();
            }
        } else {
            // Hide all WhatsApp-specific fields
            $('#fab_generate_whatsapp').closest('tr').hide();
            $('#fab_phone_number').closest('tr').hide();
            $('#fab_prefilled_message').closest('tr').hide();
            $('#fab_target_url').closest('tr').show();
        }
    }

    // Initial state
    toggleCustomIconField();
    toggleWhatsAppFields();

    // Event listeners
    $('#fab_icon_type').on('change', function() {
        toggleCustomIconField();
        toggleWhatsAppFields();
    });

    $('#fab_generate_whatsapp').on('change', function() {
        toggleWhatsAppFields();
    });
});

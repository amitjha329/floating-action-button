/**
 * Floating Action Button - Frontend Logic
 * Handles the display and link generation for the floating action button
 */

(function($) {
    'use strict';

    /**
     * Initialize the floating action button
     */
    function initFloatingActionButton() {
        // Check if settings are available
        if (typeof fabSettings === 'undefined') {
            return;
        }

        var settings = fabSettings;
        
        // Validate icon type
        if (!settings.iconType) {
            return;
        }

        // Generate the button HTML
        var buttonHtml = generateButtonHtml(settings);
        
        // Append to body
        $('body').append(buttonHtml);
    }

    /**
     * Generate the button HTML
     */
    function generateButtonHtml(settings) {
        var iconClass = getIconClass(settings.iconType);
        var iconHtml = '';
        var targetUrl = generateTargetUrl(settings);
        var positionClass = 'fab-' + settings.iconPosition;

        // Generate icon HTML
        if (settings.iconType === 'custom' && settings.customIcon) {
            iconHtml = '<img src="' + escapeHtml(settings.customIcon) + '" alt="Action Button" class="fab-custom-icon" />';
        } else {
            iconHtml = '<i class="' + iconClass + '"></i>';
        }

        // Build the complete button HTML
        var html = '<div class="fab-container ' + positionClass + '">' +
                   '<a href="' + escapeHtml(targetUrl) + '" class="fab-button fab-icon-' + escapeHtml(settings.iconType) + '" target="_blank" rel="noopener noreferrer">' +
                   iconHtml +
                   '</a>' +
                   '</div>';

        return html;
    }

    /**
     * Get Font Awesome icon class based on icon type
     */
    function getIconClass(iconType) {
        var iconMap = {
            'whatsapp': 'fab fa-whatsapp',
            'facebook': 'fab fa-facebook-f',
            'phone': 'fas fa-phone',
            'email': 'fas fa-envelope',
            'telegram': 'fab fa-telegram-plane'
        };

        return iconMap[iconType] || 'fas fa-comments';
    }

    /**
     * Generate target URL based on settings
     */
    function generateTargetUrl(settings) {
        // Special handling for WhatsApp with auto-generated link
        if (settings.iconType === 'whatsapp' && settings.generateWhatsapp == '1') {
            return generateWhatsAppLink(settings.phoneNumber, settings.prefilledMessage);
        }

        // Return the standard target URL
        return settings.targetUrl || '#';
    }

    /**
     * Generate WhatsApp wa.me link
     */
    function generateWhatsAppLink(phoneNumber, message) {
        if (!phoneNumber) {
            return '#';
        }

        // Remove any spaces, dashes, or parentheses from phone number
        var cleanPhone = phoneNumber.replace(/[\s\-\(\)]/g, '');

        // Build the base URL
        var url = 'https://wa.me/' + cleanPhone;

        // Add message if provided
        if (message && message.trim() !== '') {
            url += '?text=' + encodeURIComponent(message);
        }

        return url;
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        initFloatingActionButton();
    });

})(jQuery);

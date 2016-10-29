/* global ajaxurl, adminpage */

// @todo:: prepare success/failure messages

var FeatureFlags = FeatureFlags || {};

document.addEventListener( 'DOMContentLoaded', function() {
    FeatureFlags.init();
});

/**
 * Start all of our event listeners and handle on-load tasks.
 */
FeatureFlags.init = function() {
    // Find any action links on current page and hook into click event.
    FeatureFlags.gatherToggles();
}

/**
 * Get all plugin action links and hook them into their click event.
 */
FeatureFlags.gatherToggles = function() {
    // Find all feature toggles on the page.
    var toggles = document.querySelectorAll( 'input.feature-toggle' ),
        quantity = toggles.length;

    console.log( toggles );

    // If we have valid links on this page, hook into the click event for each one.
    if ( 0 != quantity ) {
        for ( i = 0; i < quantity; i++ ) {
            FeatureFlags.hookEvents( toggles[i] );
        }
    }
}

/**
 * Hook click events for our AJAX actions.
 *
 * @param item
 */
FeatureFlags.hookEvents = function( item ) {
    item.addEventListener( 'change', function() {

        console.log( item );

        var flagID = item.attributes['data-flag-id'].value,
            action = ( item.hasAttribute( 'checked') ) ? 'disable' : 'enable';

        // Assemble data for AJAX calls.
        var data = {
            'action' : 'ff_' + action + '_flag',
            'flag_id' : flagID,
            'nonce' : featureFlags.nonce
        };

        // Handle AJAX calls.
        jQuery.post(
            ajaxurl,
            data
        ).done(function (response) {
            console.log( response );

            // Remove this action link as it's now longer needed.
            if ( true === response.success ) {
                // Toggle the checked attribute to make sure our action check parses correctly.
                if ( 'enable' === action ) {
                    item.setAttribute( 'checked', 'checked' );
                } else {
                    item.removeAttribute( 'checked' );
                }
            }
        });
    });
}

/**
 *
 * @returns {boolean}
 */
FeatureFlags.onFlagsPage = function() {
    return ( 'settings_page_feature_flags' === adminpage );
}
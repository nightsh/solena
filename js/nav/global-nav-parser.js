/**
* KDE-www Global Navigation
*
* @category Navigation parser
* @copyright (c) 2012 KDE Webteam
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

try {
    var valid = true;
    var list  = new Array();
    
    /* Check for jQuery, availability of sites */
    valid = valid ? (typeof(jQuery) != 'undefined') : false;
    valid = valid ? (typeof(_sites) != 'undefined' && _sites.length > 0) : false;
    
    if (valid) {
        jQuery(function() {
            /* Build a list */
            for (idx in _sites) {
                var name = _sites[idx][0];
                var url  = _sites[idx][1];

                /* Hide link for this domain */
                var current = window.location.toString().split( '/' )[2].toLowerCase();
                var site = url.split( '/' )[2].toLowerCase();

                if (current != site) {
                    /* We use an array for performance reasons */
                    list.push('<li class="global-nav-item">' +
                              '<a href="' + url + '">' + name + '</a>' +
                              '</li>');
                }
            }

            /* We don't override the existing links */
            if (jQuery('#global-nav').length > 0) {
                jQuery('#global-nav').append(list.join(''));
            }
        });
    }
} catch(e) { }
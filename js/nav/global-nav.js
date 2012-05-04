/**
* KDE-www Global Navigation
*
* @category Navigation file
* @copyright (c) 2012 KDE Webteam
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

var _sites       = new Array();
var _browserLang = document.documentElement.lang.toLowerCase();

/* All available languages and current language */
var _langs       = ['en-us'];
var _lang        = 'en-us';

/* Check if language is supported */
if (_browserLang.length > 0) {
    for (idx in _langs) {
        if (_langs[idx] == _browserLang) {
            _lang = _browserLang;
            break;
        }
    }
}

/* Add the links script */
var _links       = document.createElement('script');
    _links.type  = 'text/javascript';
    _links.src   = 'http://cdn.kde.org/nav/global-nav-links.' + _lang + '.js';

/* Add the parser script */
var _parser      = document.createElement('script');
    _parser.type = 'text/javascript';
    _parser.src  = 'http://cdn.kde.org/nav/global-nav-parser.js';

document.getElementsByTagName('head')[0].appendChild(_links);
document.getElementsByTagName('head')[0].appendChild(_parser);
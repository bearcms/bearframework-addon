<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return <<<'EOT'
var bearCMS=bearCMS||{};bearCMS.lightboxContent=bearCMS.lightboxContent||function(){var n=null,e=[];return{open:function(o,t){var c=void 0!==(t=void 0!==t?t:{}).spacing?t.spacing:"0px",s=void 0===t.closeOnEscKey||t.closeOnEscKey,i=void 0===t.showCloseButton||t.showCloseButton,l=void 0!==t.onOpen?t.onOpen:null,a=void 0!==t.cache&&t.cache;clientPackages.get("lightbox").then((function(t){var u=t.make({closeOnEscKey:s,showCloseButton:i});n=u;var r=function(n){u.open(n,{closeOnEscKey:s,showCloseButton:i,spacing:c,onOpen:l})};a&&void 0!==e[o]?r(e[o]):clientPackages.get("serverRequests").then((function(n){n.send("-bearcms-lightbox-content",{id:o}).then((function(n){r(n),a&&(e[o]=n)}))}))}))},close:function(){null!==n&&n.close()}}}();
EOT;

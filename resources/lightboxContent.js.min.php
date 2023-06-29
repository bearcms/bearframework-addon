<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return <<<'EOT'
var bearCMS=bearCMS||{};bearCMS.lightboxContent=bearCMS.lightboxContent||function(){var n=null,o=[];return{open:function(e,t){var c=void 0!==t.spacing?t.spacing:"0px",i=void 0===t.showCloseButton||t.showCloseButton,a=void 0!==t.onOpen?t.onOpen:null,s=void 0!==t.cache&&t.cache;clientPackages.get("lightbox").then((function(t){var l=t.make({showCloseButton:i});n=l;var u=function(n){l.open(n,{showCloseButton:i,spacing:c,onOpen:a})};s&&void 0!==o[e]?u(o[e]):clientPackages.get("serverRequests").then((function(n){n.send("-bearcms-lightbox-content",{id:e}).then((function(n){u(n),s&&(o[e]=n)}))}))}))},close:function(){null!==n&&n.close()}}}();
EOT;

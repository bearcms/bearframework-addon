<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return <<<'EOT'
var bearCMS=bearCMS||{};bearCMS.modalContent=bearCMS.modalContent||function(){var n=null,e=[],o=function(o,t){var c=void 0!==(t=void 0!==t?t:{}).spacing?t.spacing:"0px",s=void 0===t.closeOnEscKey||t.closeOnEscKey,i=void 0===t.showCloseButton||t.showCloseButton,a=void 0!==t.onOpen?t.onOpen:null,l=void 0!==t.cache&&t.cache;clientPackages.get("lightbox").then(function(t){var u=t.make({closeOnEscKey:s,showCloseButton:i});n=u;var r=function(n){u.open(n,{closeOnEscKey:s,showCloseButton:i,spacing:c,onOpen:a})};l&&void 0!==e[o]?r(e[o]):clientPackages.get("serverRequests").then(function(n){n.send("-bearcms-modal-content",{id:o}).then(function(n){r(n),l&&(e[o]=n)})})})};return{open:o,close:function(){null!==n&&n.close()},_openOncePerSession:function(n,e){var t,c="bwm-"+n;t=c,document.cookie.split("; ").find(n=>n.startsWith(t+"="))?.split("=")[1]||(o(n,e),document.cookie=c+"=1;path=/")}}}();
EOT;

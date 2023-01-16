<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return <<<'EOT'
var bearCMS=bearCMS||{};bearCMS.elementEvents=bearCMS.elementEvents||function(){if(void 0!==window.addEventListener&&void 0!==document.querySelectorAll&&"undefined"!=typeof IntersectionObserver){var e=[],t=[],n=[],r=function(e,t){var n=e.getAttribute("data-bearcms-event-"+t);if(null!==n&&""!==n)try{new Function(n).apply(e)}catch(e){}},a=["data-bearcms-event-load","data-bearcms-event-viewport-enter","data-bearcms-event-viewport-leave","data-bearcms-event-present"],o=a.length,i=new IntersectionObserver((function(e){for(var t in e){var a=e[t],o=a.target;a.isIntersecting?(-1===n.indexOf(o)&&(n.push(o),r(o,"present")),r(o,"viewport-enter")):-1!==n.indexOf(o)&&r(o,"viewport-leave")}})),d=function(){for(var n=0;n<o;n++)for(var d=a[n],v=document.querySelectorAll("["+d+"]"),c=0;c<v.length;c++){var s=v[c];-1===e.indexOf(s)&&(e.push(s),i.observe(s),-1===t.indexOf(s)&&(t.push(s),r(s,"load")))}},v=!1,c=function(){v||(v=!0,"undefined"!=typeof MutationObserver&&new MutationObserver((function(){d()})).observe(document.querySelector("body"),{childList:!0,subtree:!0}))};document.addEventListener("readystatechange",()=>{c(),d()}),"complete"===document.readyState&&(c(),d())}else d=function(){};return{run:d}}();
EOT;

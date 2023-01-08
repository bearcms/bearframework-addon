<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return <<<'EOT'
var bearCMS=bearCMS||{};bearCMS.lightboxContent=bearCMS.lightboxContent||{open:function(e){clientPackages.get("lightbox").then((function(n){var t=n.make();clientPackages.get("serverRequests").then((function(n){n.send("-bearcms-lightbox-content",{id:e}).then((function(e){console.log(e),t.open(e)}))}))}))}};
EOT;

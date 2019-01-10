<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Config;

$code = trim($component->code);
$renderMode = trim($component->renderMode);
if ($renderMode === '') {
    $renderMode = 'clean';
}

$htmlSandboxUrl = Config::$htmlSandboxUrl;

$addHTMLSandbox = false;
$content = '';
if ($code !== '') {
    if ($renderMode === 'clean' || $renderMode === 'default') {
        $content = '<div class="bearcms-html-element"><component src="data:base64,' . base64_encode($code) . '" /></div>';
    } else if ($renderMode === 'sandbox') {
        if (strlen($htmlSandboxUrl) > 0) {
            $addHTMLSandbox = true;
            $content = '<div class="bearcms-html-element" style="font-size:0;"><div data-html-sandbox="' . htmlentities($code) . '" data-html-sandbox-url="' . htmlentities($htmlSandboxUrl) . '"></div><script>htmlSandbox.run();</script></div>';
        }
    }
}
?><html><head><style>.bearcms-html-element{word-wrap:break-word;}</style><?php if ($addHTMLSandbox) { ?>        
            <script id="bearcms-html-element-html-sandbox">var htmlSandbox = "undefined" !== typeof htmlSandbox?htmlSandbox:function(){var e = [], f = function(c, b){"100%" !== c.style.width && (c.style.width = b.width + "px"); c.style.height = b.height + "px"}; window.addEventListener("message", function(c){if ("undefined" !== typeof c.data.htmlSandboxMessageType && "undefined" !== typeof c.data.id){var b = c.data.id, a = c.data.htmlSandboxMessageType; "initialized" === a?"undefined" !== typeof e[b] && (a = e[b][1], a.contentWindow.postMessage({htmlSandboxMessageType:"setHTML", html:e[b][2]}, "*")):"setHTMLDone" ===
                        a?"undefined" !== typeof e[b] && (a = e[b][1], f(a, c.data.data.size), a.style.opacity = "1"):"updateSize" === a && "undefined" !== typeof e[b] && (a = e[b][1], f(a, c.data.data.size))}}); return{run:function(){for (var c = document.querySelectorAll("[data-html-sandbox]"), b = 0; b < c.length; b++){var a = c[b]; a.style.overflow = "hidden"; var h = a.getAttribute("data-html-sandbox"), g = a.getAttribute("data-html-sandbox-url"); null === g && (g = ""); a.removeAttribute("data-html-sandbox"); a.removeAttribute("data-html-sandbox-url"); var f = window.getComputedStyle(a),
                        k = e.length, d = document.createElement("iframe"); e[k] = [a, d, h]; d.style.border = "0"; h = function(a){d.style.width = "100%"; d.style.height = "30px"; d.setAttribute("srcdoc", '<body style="margin:0;background-color:red;font-family:Arial;font-size:14px;line-height:30px;padding:0 10px;color:#fff;">' + a + "</body>")}; var l = document.createElement("a"); l.href = g; "" === g?h("Error! The data-html-sandbox-url attribute is empty."):document.location.host === l.host?h("Security warning! The URL in the data-html-sandbox-url attribute must be on different host than the current page."):
                        (d.setAttribute("src", g), "inline" === f.display?(a.style.display = "inline-block", d.style.maxWidth = "100%"):"block" === f.display && (d.style.width = "100%"), d.style.height = "0", d.style.opacity = "0", d.addEventListener("load", function(a, b){return function(){b.contentWindow.postMessage({htmlSandboxMessageType:"initialize", width:b.style.width, id:a}, "*")}}(k, d))); a.appendChild(d)}}}}();</script>
        <?php } ?></head>
    <body><?= $content ?></body>
</html>
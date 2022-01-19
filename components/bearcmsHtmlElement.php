<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Config;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$code = trim((string)$component->code);
$renderMode = trim((string)$component->renderMode);
if ($renderMode === '') {
    $renderMode = 'clean';
}

$currentUserExists = $app->bearCMS->currentUser->exists();
$disabled = $currentUserExists && $app->request->query->exists('disable-html-elements');

$addHTMLSandbox = false;
$content = '';
if ($code !== '') {
    if ($renderMode === 'clean' || $renderMode === 'default' || $disabled) {
        if ($isFullHtmlOutputType || $disabled) {
            $content .= '<div class="bearcms-html-element">';
        }
        if ($disabled) {
            $content .= '<div style="background-color:red;color:#fff;padding:10px 15px 9px 15px;border-radius:4px;line-height:25px;font-size:14px;font-family:Arial,sans-serif;">';
            $content .= __('bearcms.element.HTMLCodeTemporaryDisabled.title') . '<div style="font-size:11px;">' . __('bearcms.element.HTMLCodeTemporaryDisabled.description') . '</div>';
            $content .= '</div>';
        } else {
            $allowDefaultMode = Config::getVariable('internalHTMLAllowDefaultMode');
            $allowDefaultMode = $allowDefaultMode !== null ? (int)$allowDefaultMode : true;
            if (!$allowDefaultMode && $renderMode === 'default') {
                if ($currentUserExists) {
                    $content .= '<div style="background-color:red;color:#fff;padding:10px 15px 9px 15px;border-radius:4px;line-height:25px;font-size:14px;font-family:Arial,sans-serif;">';
                    $content .= __('bearcms.element.HTMLCodeUnavailableSecurityMode.title') . '<div style="font-size:11px;">' . __('bearcms.element.HTMLCodeUnavailableSecurityMode.description') . '</div>';
                    $content .= '</div>';
                } else {
                }
            } else {
                $content .= '<component src="data:base64,' . base64_encode($code) . '" />';
            }
        }
        if ($isFullHtmlOutputType || $disabled) {
            $content .= '</div>';
        }
    } else if ($renderMode === 'sandbox') {
        if ($isFullHtmlOutputType) {
            $htmlSandboxURL = Config::getVariable('htmlSandboxUrl');
            if ($htmlSandboxURL !== null && is_callable($htmlSandboxURL)) {
                $htmlSandboxURL = (string) call_user_func($htmlSandboxURL);
            } else {
                $htmlSandboxURL = '';
            }
            if (strlen($htmlSandboxURL) > 0) {
                $addHTMLSandbox = true;
                $content = '<div class="bearcms-html-element" style="font-size:0;"><div data-html-sandbox="' . htmlentities($code) . '" data-html-sandbox-url="' . htmlentities($htmlSandboxURL) . '"></div><script>htmlSandbox.run();</script></div>';
            }
        } else {
            // not supported
        }
    }
}
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>';
    echo '.bearcms-html-element{word-break:break-word;}';
    echo '.bearcms-html-element:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
    echo '</style>';
    if ($addHTMLSandbox) {
        echo '<script id="bearcms-html-element-html-sandbox">var htmlSandbox="undefined"!==typeof htmlSandbox?htmlSandbox:function(){var e=[],f=function(c,b){"100%"!==c.style.width&&(c.style.width=b.width+"px");c.style.height=b.height+"px"};window.addEventListener("message",function(c){if("undefined"!==typeof c.data.htmlSandboxMessageType&&"undefined"!==typeof c.data.id){var b=c.data.id,a=c.data.htmlSandboxMessageType;"initialized"===a?"undefined"!==typeof e[b]&&(a=e[b][1],a.contentWindow.postMessage({htmlSandboxMessageType:"setHTML",html:e[b][2]},"*")):"setHTMLDone"===
a?"undefined"!==typeof e[b]&&(a=e[b][1],f(a,c.data.data.size),a.style.opacity="1"):"updateSize"===a&&"undefined"!==typeof e[b]&&(a=e[b][1],f(a,c.data.data.size))}});return{run:function(){for(var c=document.querySelectorAll("[data-html-sandbox]"),b=0;b<c.length;b++){var a=c[b];a.style.overflow="hidden";var h=a.getAttribute("data-html-sandbox"),g=a.getAttribute("data-html-sandbox-url");null===g&&(g="");a.removeAttribute("data-html-sandbox");a.removeAttribute("data-html-sandbox-url");var f=window.getComputedStyle(a),
k=e.length,d=document.createElement("iframe");e[k]=[a,d,h];d.style.border="0";h=function(a){d.style.width="100%";d.style.height="30px";d.setAttribute("srcdoc",\'<body style="margin:0;background-color:red;font-family:Arial;font-size:14px;line-height:30px;padding:0 10px;color:#fff;">\'+a+"</body>")};var l=document.createElement("a");l.href=g;""===g?h("Error! The data-html-sandbox-url attribute is empty."):document.location.host===l.host?h("Security warning! The URL in the data-html-sandbox-url attribute must be on different host than the current page."):
(d.setAttribute("src",g),"inline"===f.display?(a.style.display="inline-block",d.style.maxWidth="100%"):"block"===f.display&&(d.style.width="100%"),d.style.height="0",d.style.opacity="0",d.addEventListener("load",function(a,b){return function(){b.contentWindow.postMessage({htmlSandboxMessageType:"initialize",width:b.style.width,id:a},"*")}}(k,d)));a.appendChild(d)}}}}();</script>';
    }
    echo '</head>';
}
echo '<body>';
echo $content;
echo '</body></html>';

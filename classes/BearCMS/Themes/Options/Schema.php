<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Options;

/**
 * 
 */
class Schema
{

    use \BearCMS\Internal\ThemesOptionsGroupSchemaTrait;

    /**
     * 
     * @param string $id
     * @param mixed $value
     * @return self
     */
    public function setValue(string $id, $value): self
    {
        $this->setValues([$id => $value]);
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return self
     */
    public function setValues(array $values): self
    {
        $valuesSetCount = 0;
        $valuesCount = sizeof($values);
        $walkOptions = function($options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Options\OptionSchema) {
                    if (isset($values[$option->id])) {
                        $option->details['value'] = $values[$option->id];
                        $valuesSetCount++;
                        if ($valuesSetCount === $valuesCount) {
                            return true;
                        }
                    }
                } elseif ($option instanceof \BearCMS\Themes\Options\GroupSchema) {
                    if ($walkOptions($option->getList())) {
                        return;
                    }
                }
            }
        };
        $walkOptions($this->options);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getHTML(): string
    {
        $cssRules = [];
        $cssCode = '';
        $walkOptions = function($options) use (&$cssRules, &$cssCode, &$walkOptions) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Options\OptionSchema) {
                    $value = isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value']) : $option->details['value']) : null;
                    $optionType = $option->type;
                    if ($optionType === 'cssCode') {
                        $cssCode .= $value;
                    } else {
                        if (isset($option->details['cssOutput'])) {
                            foreach ($option->details['cssOutput'] as $outputDefinition) {
                                if (is_array($outputDefinition)) {
                                    if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                        $selector = $outputDefinition[1];
                                        $selectorVariants = ['', '', ''];
                                        if ($optionType === 'css' || $optionType === 'cssText' || $optionType === 'cssTextShadow' || $optionType === 'cssBackground' || $optionType === 'cssPadding' || $optionType === 'cssMargin' || $optionType === 'cssBorder' || $optionType === 'cssRadius' || $optionType === 'cssShadow' || $optionType === 'cssSize' || $optionType === 'cssTextAlign') {
                                            $temp = isset($value[0]) ? json_decode($value, true) : [];
                                            if (is_array($temp)) {
                                                foreach ($temp as $key => $_value) {
                                                    $pseudo = substr($key, -6);
                                                    if ($pseudo === ':hover') {
                                                        $selectorVariants[1] .= substr($key, 0, -6) . ':' . $_value . ';';
                                                    } else if ($pseudo === 'active') { // optimization
                                                        if (substr($key, -7) === ':active') {
                                                            $selectorVariants[2] .= substr($key, 0, -7) . ':' . $_value . ';';
                                                        } else {
                                                            $selectorVariants[0] .= $key . ':' . $_value . ';';
                                                        }
                                                    } else {
                                                        $selectorVariants[0] .= $key . ':' . $_value . ';';
                                                    }
                                                }
                                            }
                                        }
                                        if ($selectorVariants[0] !== '') {
                                            if (!isset($cssRules[$selector])) {
                                                $cssRules[$selector] = '';
                                            }
                                            $cssRules[$selector] .= $selectorVariants[0];
                                        }
                                        if ($selectorVariants[1] !== '') {
                                            if (!isset($cssRules[$selector . ':hover'])) {
                                                $cssRules[$selector . ':hover'] = '';
                                            }
                                            $cssRules[$selector . ':hover'] .= $selectorVariants[1];
                                        }
                                        if ($selectorVariants[2] !== '') {
                                            if (!isset($cssRules[$selector . ':active'])) {
                                                $cssRules[$selector . ':active'] = '';
                                            }
                                            $cssRules[$selector . ':active'] .= $selectorVariants[2];
                                        }
                                    } elseif (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                                        $selector = $outputDefinition[1];
                                        if (!isset($cssRules[$selector])) {
                                            $cssRules[$selector] = '';
                                        }
                                        $cssRules[$selector] .= $outputDefinition[2];
                                    }
                                }
                            }
                        }
                    }
                } elseif ($option instanceof \BearCMS\Themes\Options\GroupSchema) {
                    $walkOptions($option->getList());
                }
            }
        };
        $walkOptions($this->options);
        $style = '';
        foreach ($cssRules as $key => $value) {
            $style .= $key . '{' . $value . '}';
        }
        $linkTags = [];
        $applyFontNames = function($text) use (&$linkTags) {
            $webSafeFonts = [
                'Arial' => 'Arial,Helvetica,sans-serif',
                'Arial Black' => '"Arial Black",Gadget,sans-serif',
                'Comic Sans' => '"Comic Sans MS",cursive,sans-serif',
                'Courier' => '"Courier New",Courier,monospace',
                'Georgia' => 'Georgia,serif',
                'Impact' => 'Impact,Charcoal,sans-serif',
                'Lucida' => '"Lucida Sans Unicode","Lucida Grande",sans-serif',
                'Lucida Console' => '"Lucida Console",Monaco,monospace',
                'Palatino' => '"Palatino Linotype","Book Antiqua",Palatino,serif',
                'Tahoma' => 'Tahoma,Geneva,sans-serif',
                'Times New Roman' => '"Times New Roman",Times,serif',
                'Trebuchet' => '"Trebuchet MS",Helvetica,sans-serif',
                'Verdana' => 'Verdana,Geneva,sans-serif'
            ];

            $matches = [];
            preg_match_all('/font\-family\:(.*?);/', $text, $matches);
            foreach ($matches[0] as $i => $match) {
                $fontName = $matches[1][$i];
                if (isset($webSafeFonts[$fontName])) {
                    $text = str_replace($match, 'font-family:' . $webSafeFonts[$fontName] . ';', $text);
                } elseif (strpos($fontName, 'googlefonts:') === 0) {
                    $googleFontName = substr($fontName, strlen('googlefonts:'));
                    $text = str_replace($match, 'font-family:\'' . $googleFontName . '\';', $text);
                    if (!isset($linkTags[$googleFontName])) {
                        $linkTags[$googleFontName] = '<link href="//fonts.googleapis.com/css?family=' . urlencode($googleFontName) . '" rel="stylesheet" type="text/css" />';
                    }
                }
            }
            return $text;
        };
        $style = $applyFontNames($style);
        $cssCode = trim($cssCode); // Positioned in different style tag just in case it's invalid
        if (!empty($linkTags) || $style !== '' || $cssCode !== '') {
            $html = '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
        } else {
            $html = '';
        }
        return $html;
    }

}

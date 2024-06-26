<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\ElementsHelper as ElementsHelper;
use BearCMS\Internal\Themes as InternalThemes;
use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\ImportExport\ImportContext;
use IvoPetkov\HTML5DOMDocument;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsTypes
{

    /**
     * 
     * @var string|null
     */
    static private $contextDir = null;

    /**
     * 
     * @param ElementType $type
     * @return void
     */
    public static function add(ElementType $type): void
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->contexts->get(__DIR__);
            self::$contextDir = $context->dir;
        }
        $name = $type->componentName;
        $app->components
            ->addAlias($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php')
            ->addTag($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php');
        ElementsHelper::$elementsTypeComponents[$name] = $type->type;
        ElementsHelper::$elementsTypeDefinitions[$name] = $type;
    }

    /**
     * 
     * @return void
     */
    public static function addDefault(): void
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->contexts->get(__DIR__);
            self::$contextDir = $context->dir;
        }

        $hasElements = Config::hasFeature('ELEMENTS');
        $hasThemes = Config::hasFeature('THEMES');

        if ($hasElements || Config::hasFeature('ELEMENTS_HEADING')) {
            $type = new ElementType('heading', 'bearcms-heading-element', self::$contextDir . '/components/bearcmsHeadingElement.php');
            $type->properties = [
                [
                    'id' => 'size',
                    'type' => 'string'
                ],
                [
                    'id' => 'text',
                    'type' => 'string'
                ],
                [
                    'id' => 'linkTargetID',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['heading'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $options->addOption($idPrefix . "HeadingCSS", "css", '', [
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-heading-element", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . " .bearcms-heading-element"],
                            ]
                        ]);
                        $options->addVisibility($idPrefix . "HeadingVisibility", $parentSelector);

                        $containerSelector = ":has(> .bearcms-heading-element)";
                        $groupContainer = $options->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "HeadingContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        $groupContainer->addVisibility($idPrefix . "HeadingContainerVisibility", $parentSelector . $containerSelector);
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Heading"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-heading-element"];

                        $groupLarge = $optionsGroup->addGroup(__("bearcms.themes.options.HeadingLarge"));
                        $groupLarge->addOption($idPrefix . "HeadingLargeCSS", "css", '', [
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-heading-element-large", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-heading-element-large"]
                            ]
                        ]);

                        $containerSelector = $defaultStyleSelector . ":has(> .bearcms-heading-element-large)";
                        $groupContainer = $groupLarge->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "HeadingLargeContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        $groupMedium = $optionsGroup->addGroup(__("bearcms.themes.options.HeadingMedium"));
                        $groupMedium->addOption($idPrefix . "HeadingMediumCSS", "css", '', [
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-heading-element-medium", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-heading-element-medium"]
                            ]
                        ]);

                        $containerSelector = $defaultStyleSelector . ":has(> .bearcms-heading-element-medium)";
                        $groupContainer = $groupMedium->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "HeadingMediumContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        $groupSmall = $optionsGroup->addGroup(__("bearcms.themes.options.HeadingSmall"));
                        $groupSmall->addOption($idPrefix . "HeadingSmallCSS", "css", '', [
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-heading-element-small", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-heading-element-small"]
                            ]
                        ]);

                        $containerSelector = $defaultStyleSelector . ":has(> .bearcms-heading-element-small)";
                        $groupContainer = $groupSmall->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "HeadingSmallContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_TEXT')) {
            $type = new ElementType('text', 'bearcms-text-element', self::$contextDir . '/components/bearcmsTextElement.php');
            $type->properties = [
                [
                    'id' => 'text',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['text'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Text"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-text-element"];
                    }
                    $optionsGroup->addOption($idPrefix . "TextCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-text-element", "box-sizing:border-box;"],
                            ["rule", $parentSelector . " .bearcms-text-element ul", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-text-element ol", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-text-element li", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-text-element p", "margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-text-element input", "margin:0;padding:0;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-text-element"]
                        ]
                    ]);

                    $groupLinks = $optionsGroup->addGroup(__("bearcms.themes.options.Links"));
                    $groupLinks->addOption($idPrefix . "TextLinkCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBackground"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-text-element a", "text-decoration:none;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-text-element a"]
                        ]
                    ]);

                    if ($isElementContext) {
                        $optionsGroup->addVisibility($idPrefix . "TextVisibility", $parentSelector);
                    }

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-text-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "TextContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "TextContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_LINK')) {
            $type = new ElementType('link', 'bearcms-link-element', self::$contextDir . '/components/bearcmsLinkElement.php');
            $type->properties = [
                [
                    'id' => 'url',
                    'type' => 'string'
                ],
                [
                    'id' => 'text',
                    'type' => 'string'
                ],
                [
                    'id' => 'title',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['link'] = ['v2', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Link"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-link-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "LinkCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-link-element", "text-decoration:none;box-sizing:border-box;display:inline-block;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-link-element"]
                        ]
                    ]);

                    if ($isElementContext) {
                        $optionsGroup->addVisibility($idPrefix . "LinkVisibility", $parentSelector);
                    }

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-link-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "LinkContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "LinkContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_IMAGE')) {
            $type = new ElementType('image', 'bearcms-image-element', self::$contextDir . '/components/bearcmsImageElement.php');
            $type->properties = [
                [
                    'id' => 'filename',
                    'type' => 'string'
                ],
                [
                    'id' => 'title',
                    'type' => 'string'
                ],
                [
                    'id' => 'alt',
                    'type' => 'string'
                ],
                [
                    'id' => 'onClick',
                    'type' => 'string'
                ],
                [
                    'id' => 'url',
                    'type' => 'string'
                ],
                [
                    'id' => 'fileWidth',
                    'type' => 'string'
                ],
                [
                    'id' => 'fileHeight',
                    'type' => 'string'
                ],
                [
                    'id' => 'width', // Deprecated on 14 August 2021
                    'type' => 'string'
                ],
                [
                    'id' => 'align', // Deprecated on 14 August 2021
                    'type' => 'string'
                ],
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            $type->onDelete = function (array $data): void {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                InternalData::deleteElementAsset($filename);
            };
            $type->onDuplicate = function (array $data): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::duplicateElementAsset($filename);
                }
                return $data;
            };
            $type->onExport = function (array $data, callable $add): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::exportElementAsset($filename, 'file', $add);
                }
                return $data;
            };
            $type->onImport = function (array $data, ImportContext $context): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::importElementAsset($filename, 'bearcms/files/image/', $context);
                }
                return $data;
            };
            $type->getUploadsSizeItems = function (array $data): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    return [InternalData::getFilenameDataKey($filename)];
                }
                return [];
            };
            $type->optimizeData = function (array $data): ?array {
                $app = App::get();
                $hasChange = false;
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $realFilenameWithOptions = InternalData::getRealFilename($filename);
                    $realFilenameWithoutOptions = InternalData::removeFilenameOptions($realFilenameWithOptions);
                    $shortFilenameWithOptions = InternalData::getShortFilename($realFilenameWithOptions);
                    if (strpos($realFilenameWithoutOptions, 'appdata://') === 0) {
                        if ($data['filename'] !== $shortFilenameWithOptions) {
                            $data['filename'] = $shortFilenameWithOptions;
                            $hasChange = true;
                        }
                        if (!isset($data['fileWidth']) || !isset($data['fileHeight'])) {
                            $details = $app->assets->getDetails($realFilenameWithoutOptions, ['width', 'height']);
                            $data['fileWidth'] = $details['width'] !== null ? $details['width'] : 0;
                            $data['fileHeight'] = $details['height'] !== null ? $details['height'] : 0;
                            $hasChange = true;
                        }
                    }
                }
                if ($hasChange) {
                    return $data;
                }
                return null;
            };
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['image'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Image"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-image-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "ImageCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-image-element", "overflow:hidden;box-sizing:border-box;"],
                            ["rule", $parentSelector . " .bearcms-image-element img", "border:0;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-image-element"]
                        ]
                    ]);
                    if ($isElementContext) {
                        $optionsGroup->addOption($idPrefix . "elementContainerCSS", "css", '', [
                            "cssTypes" => ["cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-image-element"]
                            ]
                        ]);
                        $optionsGroup->addVisibility($idPrefix . "ImageVisibility", $parentSelector);
                    }

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-image-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "ImageContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "ImageContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_IMAGE_GALLERY')) {
            $type = new ElementType('imageGallery', 'bearcms-image-gallery-element', self::$contextDir . '/components/bearcmsImageGalleryElement.php');
            $type->properties = [
                [
                    'id' => 'type',
                    'type' => 'string'
                ],
                [
                    'id' => 'columnsCount',
                    'type' => 'string'
                ],
                [
                    'id' => 'imageSize',
                    'type' => 'string'
                ],
                [
                    'id' => 'imageAspectRatio',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            $type->updateComponentFromData = function ($component, array $data) {
                if (isset($data['files']) && is_array($data['files'])) {
                    $innerHTML = '';
                    foreach ($data['files'] as $file) {
                        if (isset($file['filename'])) {
                            $innerHTML .= '<file '
                                . 'filename="' . htmlentities($file['filename']) . '" '
                                . 'fileWidth="' . (isset($file['width']) ? htmlentities($file['width']) : null) . '" '
                                . 'fileHeight="' . (isset($file['height']) ? htmlentities($file['height']) : null) . '"'
                                . 'title="' . (isset($file['title']) ? htmlentities($file['title']) : null) . '"'
                                . 'alt="' . (isset($file['alt']) ? htmlentities($file['alt']) : null) . '"'
                                . '/>';
                        }
                    }
                    $component->innerHTML = $innerHTML;
                }
                return $component;
            };
            $type->updateDataFromComponent = function ($component, array $data): array {
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $files = [];
                $filesElements = $domDocument->querySelectorAll('file');
                foreach ($filesElements as $fileElement) {
                    $file = ['filename' => $fileElement->getAttribute('filename')];
                    $width = (string)$fileElement->getAttribute('filewidth');
                    if (isset($width[0])) {
                        $file['width'] = $width[0];
                    }
                    $height = (string)$fileElement->getAttribute('fileheight');
                    if (isset($height[0])) {
                        $file['height'] = $height[0];
                    }
                    $files[] = $file;
                }
                $data['files'] = $files;
                return $data;
            };
            $type->onDelete = function (array $data): void {
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $file) {
                        $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                        InternalData::deleteElementAsset($filename);
                    }
                }
            };
            $type->onDuplicate = function (array $data): array {
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $index => $file) {
                        $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                        if ($filename !== '') {
                            $data['files'][$index]['filename'] = InternalData::duplicateElementAsset($filename);
                        }
                    }
                }
                return $data;
            };
            $type->onExport = function (array $data, callable $add): array {
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $index => $file) {
                        $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                        if ($filename !== '') {
                            $data['files'][$index]['filename'] = InternalData::exportElementAsset($filename, 'file' . ($index + 1), $add);
                        }
                    }
                }
                return $data;
            };
            $type->onImport = function (array $data, ImportContext $context): array {
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $index => $file) {
                        $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                        if ($filename !== '') {
                            $data['files'][$index]['filename'] = InternalData::importElementAsset($filename, 'bearcms/files/imagegallery/', $context);
                        }
                    }
                }
                return $data;
            };
            $type->getUploadsSizeItems = function (array $data): array {
                $result = [];
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $file) {
                        if (isset($file['filename'])) {
                            $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                            if (strlen($filename) > 0) {
                                $result[] = InternalData::getFilenameDataKey($filename);
                            }
                        }
                    }
                }
                return $result;
            };
            $type->optimizeData = function (array $data): ?array {
                $app = App::get();
                $hasChange = false;
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $index => $file) {
                        if (isset($file['filename'])) {
                            $filename = isset($file['filename']) ? (string)$file['filename'] : '';
                            if (strlen($filename) > 0) {
                                $realFilenameWithOptions = InternalData::getRealFilename($filename);
                                $realFilenameWithoutOptions = InternalData::removeFilenameOptions($realFilenameWithOptions);
                                $shortFilenameWithOptions = InternalData::getShortFilename($realFilenameWithOptions);
                                if (strpos($realFilenameWithoutOptions, 'appdata://') === 0) {
                                    if ($file['filename'] !== $shortFilenameWithOptions) {
                                        $file['filename'] = $shortFilenameWithOptions;
                                        $hasChange = true;
                                    }
                                    if (!isset($file['width']) || !isset($file['height'])) {
                                        $details = $app->assets->getDetails($realFilenameWithoutOptions, ['width', 'height']);
                                        $file['width'] = $details['width'] !== null ? $details['width'] : 0;
                                        $file['height'] = $details['height'] !== null ? $details['height'] : 0;
                                        $hasChange = true;
                                    }
                                    $data['files'][$index] = $file;
                                }
                            }
                        }
                    }
                }
                if ($hasChange) {
                    return $data;
                }
                return null;
            };
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['imageGallery'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Image gallery"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-image-gallery-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "ImageGalleryCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-image-gallery-element"]
                        ]
                    ]);

                    $groupImage = $optionsGroup->addGroup(__("bearcms.themes.options.Image"));
                    $groupImage->addOption($idPrefix . "ImageGalleryImageCSS", "css", '', [
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-image-gallery-element-image", "overflow:hidden;"],
                            ["rule", $parentSelector . " .bearcms-image-gallery-element-image img", "border:0;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-image-gallery-element .bearcms-image-gallery-element-image"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-image-gallery-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "ImageGalleryContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "ImageGalleryContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_VIDEO')) {
            $type = new ElementType('video', 'bearcms-video-element', self::$contextDir . '/components/bearcmsVideoElement.php');
            $type->properties = [
                [
                    'id' => 'url',
                    'type' => 'string'
                ],
                [
                    'id' => 'filename',
                    'type' => 'string'
                ],
                [
                    'id' => 'posterFilename',
                    'type' => 'string'
                ],
                [
                    'id' => 'posterWidth',
                    'type' => 'string'
                ],
                [
                    'id' => 'posterHeight',
                    'type' => 'string'
                ],
                [
                    'id' => 'autoplay',
                    'type' => 'bool'
                ],
                [
                    'id' => 'muted',
                    'type' => 'bool'
                ],
                [
                    'id' => 'loop',
                    'type' => 'bool'
                ],
                [
                    'id' => 'width',
                    'type' => 'string'
                ],
                [
                    'id' => 'align',
                    'type' => 'string'
                ],
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            $type->onDelete = function (array $data): void {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                InternalData::deleteElementAsset($filename);
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                InternalData::deleteElementAsset($posterFilename);
            };
            $type->onDuplicate = function (array $data): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::duplicateElementAsset($filename);
                }
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                if (strlen($posterFilename) > 0) {
                    $data['posterFilename'] = InternalData::duplicateElementAsset($posterFilename);
                }
                return $data;
            };
            $type->onExport = function (array $data, callable $add): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::exportElementAsset($filename, 'file', $add);
                }
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                if (strlen($posterFilename) > 0) {
                    $data['posterFilename'] = InternalData::exportElementAsset($posterFilename, 'file', $add);
                }
                return $data;
            };
            $type->onImport = function (array $data, ImportContext $context): array {
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $data['filename'] = InternalData::importElementAsset($filename, 'bearcms/files/video/', $context);
                }
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                if (strlen($posterFilename) > 0) {
                    $data['posterFilename'] = InternalData::importElementAsset($posterFilename, 'bearcms/files/videoposter/', $context);
                }
                return $data;
            };
            $type->getUploadsSizeItems = function (array $data): array {
                $keys = [];
                $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                if (strlen($filename) > 0) {
                    $keys[] = InternalData::getFilenameDataKey($filename);
                }
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                if (strlen($posterFilename) > 0) {
                    $keys[] = InternalData::getFilenameDataKey($posterFilename);
                }
                return $keys;
            };
            $type->optimizeData = function (array $data): ?array {
                $app = App::get();
                $hasChange = false;
                $posterFilename = isset($data['posterFilename']) ? (string)$data['posterFilename'] : '';
                if (strlen($posterFilename) > 0) {
                    $realFilenameWithOptions = InternalData::getRealFilename($posterFilename);
                    $realFilenameWithoutOptions = InternalData::removeFilenameOptions($realFilenameWithOptions);
                    $shortFilenameWithOptions = InternalData::getShortFilename($realFilenameWithOptions);
                    if (strpos($realFilenameWithoutOptions, 'appdata://') === 0) {
                        if ($data['posterFilename'] !== $shortFilenameWithOptions) {
                            $data['posterFilename'] = $shortFilenameWithOptions;
                            $hasChange = true;
                        }
                        if (!isset($data['posterWidth']) || !isset($data['posterHeight'])) {
                            $details = $app->assets->getDetails($realFilenameWithoutOptions, ['width', 'height']);
                            $data['posterWidth'] = $details['width'] !== null ? $details['width'] : 0;
                            $data['posterHeight'] = $details['height'] !== null ? $details['height'] : 0;
                            $hasChange = true;
                        }
                    }
                }
                if ($hasChange) {
                    return $data;
                }
                return null;
            };
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['video'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Video"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-video-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "VideoCSS", "css", '', [
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $defaultStyleSelector . "> .bearcms-video-element", "overflow:hidden;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-video-element"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-video-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "VideoContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "VideoContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_NAVIGATION')) {
            $type = new ElementType('navigation', 'bearcms-navigation-element', self::$contextDir . '/components/bearcmsNavigationElement.php');
            $type->properties = [
                [
                    'id' => 'source',
                    'type' => 'string'
                ],
                [
                    'id' => 'sourceParentPageID',
                    'type' => 'string'
                ],
                [
                    'id' => 'showHomeLink',
                    'type' => 'bool'
                ],
                [
                    'id' => 'showSearchButton',
                    'type' => 'bool'
                ],
                [
                    'id' => 'showStoreCartButton',
                    'type' => 'bool'
                ],
                [
                    'id' => 'homeLinkText',
                    'type' => 'string'
                ],
                [
                    'id' => 'itemsType',
                    'type' => 'string'
                ],
                [
                    'id' => 'items',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['navigation'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Navigation"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-navigation-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "NavigationCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBorder", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-navigation-element", "margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-navigation-element ul", "margin:0;padding:0;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-navigation-element"]
                        ]
                    ]);

                    $groupElements = $optionsGroup->addGroup(__("bearcms.themes.options.Elements"));
                    $groupElements->addOption($idPrefix . "NavigationItemLinkCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-navigation-element-item a", "text-decoration:none;"], // treat as text link // no max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-navigation-element .bearcms-navigation-element-item a"]
                        ]
                    ]);

                    $groupElementsContainer = $groupElements->addGroup(__("bearcms.themes.options.Container"));
                    $groupElementsContainer->addOption($idPrefix . "NavigationItemLinkContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-navigation-element-item", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-navigation-element .bearcms-navigation-element-item"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-navigation-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "NavigationContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "NavigationContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_HTML')) {
            $type = new ElementType('html', 'bearcms-html-element', self::$contextDir . '/components/bearcmsHtmlElement.php');
            $type->properties = [
                [
                    'id' => 'code',
                    'type' => 'string'
                ],
                [
                    'id' => 'originalCode',
                    'type' => 'string'
                ],
                [
                    'id' => 'renderMode',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['html'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.HTML code"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-html-element"];
                    }
                    $optionsGroup->addOption($idPrefix . "HtmlCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-html-element ul", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-html-element ol", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-html-element li", "list-style-position:inside;margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-html-element p", "margin:0;padding:0;"],
                            ["rule", $parentSelector . " .bearcms-html-element input", "margin:0;padding:0;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-html-element"]
                        ]
                    ]);

                    $groupLinks = $optionsGroup->addGroup(__("bearcms.themes.options.Links"));
                    $groupLinks->addOption($idPrefix . "HtmlLinkCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-html-element a", "text-decoration:none;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-html-element a"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-html-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "HtmlContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "HtmlContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_BLOG_POSTS')) {
            $type = new ElementType('blogPosts', 'bearcms-blog-posts-element', self::$contextDir . '/components/bearcmsBlogPostsElement.php');
            $type->properties = [
                [
                    'id' => 'source',
                    'type' => 'string'
                ],
                [
                    'id' => 'sourceCategoriesIDs',
                    'type' => 'string'
                ],
                [
                    'id' => 'type',
                    'type' => 'string'
                ],
                [
                    'id' => 'showDate',
                    'type' => 'bool'
                ],
                [
                    'id' => 'showSummaryReadMoreButton',
                    'type' => 'bool'
                ],
                [
                    'id' => 'limit',
                    'type' => 'int'
                ],
                [
                    'id' => 'showLoadMoreButton',
                    'type' => 'bool'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            $type->onExport = function (array $data, callable $add): array {
                if (isset($data['sourceCategoriesIDs'])) {
                    $data['sourceCategoriesIDs'] = '';
                }
                return $data;
            };
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['blogPosts'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Blog posts"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-blog-posts-element"];
                    }

                    $optionsGroup->addOption($idPrefix . "BlogPostsCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element"]
                        ]
                    ]);

                    $optionsGroup->addOption($idPrefix . "BlogPostsSpacing", "htmlUnit", __("bearcms.themes.options.Posts spacing"), [
                        "defaultValue" => "0",
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post:not(:first-child)", "margin-top:{value};"]
                        ],
                        "onHighlight" => [['cssSelector', $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post"]]
                    ]);

                    $groupPost = $optionsGroup->addGroup(__("bearcms.themes.options.Post"));
                    $groupPost->addOption($idPrefix . "BlogPostsPostCSS", "css", '', [
                        "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post"]
                        ]
                    ]);

                    $groupPostTitle = $groupPost->addGroup(__("bearcms.themes.options.Title"));
                    $groupPostTitle->addOption($idPrefix . "BlogPostsPostTitleCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-title", "text-decoration:none;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-title"]
                        ]
                    ]);

                    $groupPostTitleContainer = $groupPostTitle->addGroup(__("bearcms.themes.options.Container"));
                    $groupPostTitleContainer->addOption($idPrefix . "BlogPostsPostTitleContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-title-container", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-title-container"]
                        ]
                    ]);

                    $groupPostDate = $groupPost->addGroup(__("bearcms.themes.options.Date"));
                    $groupPostDate->addOption($idPrefix . "BlogPostsPostDateCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-date"]
                        ]
                    ]);

                    $groupPostDateContainer = $groupPostDate->addGroup(__("bearcms.themes.options.Container"));
                    $groupPostDateContainer->addOption($idPrefix . "BlogPostsPostDateContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-blog-posts-element-post-date-container", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-date-container"]
                        ]
                    ]);

                    $groupPostContent = $groupPost->addGroup(__("bearcms.themes.options.Content"));
                    $groupPostContent->addOption($idPrefix . "BlogPostsPostContentCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-blog-posts-element-post-content", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-post-content"]
                        ]
                    ]);

                    $groupShowMoreButton = $optionsGroup->addGroup(__('bearcms.themes.options.blogPosts.Show more button'));
                    $groupShowMoreButton->addOption($idPrefix . "BlogPostsShowMoreButtonCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-show-more-button"]
                        ]
                    ]);

                    $groupShowMoreButtonContainer = $groupShowMoreButton->addGroup(__("bearcms.themes.options.Container"));
                    $groupShowMoreButtonContainer->addOption($idPrefix . "BlogPostsShowMoreButtonContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button-container", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-blog-posts-element .bearcms-blog-posts-element-show-more-button-container"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-blog-posts-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "BlogPostsContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "BlogPostsContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_COMMENTS')) {
            $type = new ElementType('comments', 'bearcms-comments-element', self::$contextDir . '/components/bearcmsCommentsElement.php');
            $type->properties = [
                [
                    'id' => 'threadID',
                    'type' => 'string'
                ],
                [
                    'id' => 'count',
                    'type' => 'int'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            $type->onDelete = function (array $data): void {
                if (isset($data['threadID'])) {
                    InternalData\Comments::deleteThread($data['threadID']);
                }
            };
            $type->onDuplicate = function (array $data): array {
                if (isset($data['threadID'])) {
                    $newThreadID = InternalData\Comments::generateNewThreadID();
                    InternalData\Comments::copyThread($data['threadID'], $newThreadID);
                    $data['threadID'] = $newThreadID;
                }
                return $data;
            };
            $type->onExport = function (array $data, callable $add): array {
                if (isset($data['threadID'])) {
                    $data['threadID'] = '';
                }
                return $data;
            };
            $type->onImport = function (array $data, ImportContext $context): array {
                $data['threadID'] = InternalData\Comments::generateNewThreadID();
                return $data;
            };
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['comments'] = ['v1', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $optionsGroup = $options;
                        $defaultStyleSelector = '';
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Comments"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-comments-element"];
                    }
                    $optionsGroup->addOption($idPrefix . "CommentsCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element"]
                        ]
                    ]);

                    $groupComment = $optionsGroup->addGroup(__("bearcms.themes.options.comments.Comment"));
                    $groupComment->addOption($idPrefix . "CommentsCommentCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-comment", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment"]
                        ]
                    ]);

                    $groupCommentAuthorName = $groupComment->addGroup(__("bearcms.themes.options.comments.Author name"));
                    $groupCommentAuthorName->addOption($idPrefix . "CommentsAuthorNameCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment-author-name"]
                        ]
                    ]);

                    $groupCommentAuthorImage = $groupComment->addGroup(__("bearcms.themes.options.comments.Author image"));
                    $groupCommentAuthorImage->addOption($idPrefix . "CommentsAuthorImageCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-comment-author-image", "box-sizing:border-box;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment-author-image"]
                        ]
                    ]);

                    $groupCommentDate = $groupComment->addGroup(__("bearcms.themes.options.comments.Date"));
                    $groupCommentDate->addOption($idPrefix . "CommentsDateCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment-date"]
                        ]
                    ]);

                    $groupCommentText = $groupComment->addGroup(__("bearcms.themes.options.comments.Text"));
                    $groupCommentText->addOption($idPrefix . "CommentsTextCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment-text"]
                        ]
                    ]);

                    $groupCommentTextLinks = $groupComment->addGroup(__("bearcms.themes.options.comments.Text links"));
                    $groupCommentTextLinks->addOption($idPrefix . "CommentsTextLinksCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-comment-text a"]
                        ]
                    ]);

                    $groupTextInput = $optionsGroup->addGroup(__("bearcms.themes.options.comments.Text input"));
                    $groupTextInput->addOption($idPrefix . "CommentsTextInputCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-element-text-input", "box-sizing:border-box;border:0;margin:0;padding:0;"],
                            ["selector", $parentSelector  . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-element-text-input"]
                        ]
                    ]);

                    $groupSendButton = $optionsGroup->addGroup(__("bearcms.themes.options.comments.Send button"));
                    $groupSendButton->addOption($idPrefix . "CommentsSendButtonCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-element-send-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-element-send-button"]
                        ]
                    ]);

                    $groupSendButtonWaiting = $groupSendButton->addGroup(__("bearcms.themes.options.comments.Send button waiting"));
                    $groupSendButtonWaiting->addOption($idPrefix . "CommentsSendButtonWaitingCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-element-send-button-waiting", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-element-send-button-waiting"]
                        ]
                    ]);

                    $groupShowMoreButton = $optionsGroup->addGroup(__("bearcms.themes.options.comments.Show more button"));
                    $groupShowMoreButton->addOption($idPrefix . "CommentsShowMoreButtonCSS", "css", '', [
                        "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-show-more-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                            ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-show-more-button"]
                        ]
                    ]);

                    $groupShowMoreButtonContainer = $groupShowMoreButton->addGroup(__("bearcms.themes.options.Container"));
                    $groupShowMoreButtonContainer->addOption($idPrefix . "CommentsShowMoreButtonContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-comments-show-more-button-container", "box-sizing:border-box;"],
                            ["selector", $parentSelector  . $defaultStyleSelector . "> .bearcms-comments-element .bearcms-comments-show-more-button-container"]
                        ]
                    ]);

                    $containerSelector = $defaultStyleSelector . ":has(> .bearcms-comments-element)";
                    $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                    $groupContainer->addOption($idPrefix . "CommentsContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                        "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                        "cssOutput" => [
                            ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                            ["selector", $parentSelector . $containerSelector]
                        ]
                    ]);

                    if ($isElementContext) {
                        $groupContainer->addVisibility($idPrefix . "CommentsContainerVisibility", $parentSelector . $containerSelector);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_SEPARATOR')) {
            $type = new ElementType('separator', 'bearcms-separator-element', self::$contextDir . '/components/bearcmsSeparatorElement.php');
            $type->properties = [
                [
                    'id' => 'size',
                    'type' => 'string'
                ]
            ];
            $type->canStyle = true;
            $type->canImportExport = true;
            self::add($type);
            if ($hasThemes) {
                InternalThemes::$elementsOptions['separator'] = ['v2', function ($options, $idPrefix, $parentSelector, $context, $details) {
                    $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                    if ($isElementContext) {
                        $options->addOption($idPrefix . "SeparatorCSS", "css", '', [
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-separator-element"],
                            ]
                        ]);
                        $options->addVisibility($idPrefix . "SeparatorVisibility", $parentSelector);

                        $containerSelector = ":has(> .bearcms-separator-element)";
                        $groupContainer = $options->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "SeparatorContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        if ($isElementContext) {
                            $groupContainer->addVisibility($idPrefix . "SeparatorContainerVisibility", $parentSelector . $containerSelector);
                        }
                    } else {
                        $optionsGroup = $options->addGroup(__("bearcms.themes.options.Separator"));
                        $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"])';
                        $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-separator-element"];

                        $groupLarge = $optionsGroup->addGroup(__("bearcms.themes.options.Separator.Large"));
                        $groupLarge->addOption($idPrefix . "SeparatorLargeCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-separator-element-large"]
                            ],
                            "defaultValue" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"90%"}'
                        ]);

                        $containerSelector = ":has(> .bearcms-separator-element-large)";
                        $groupContainer = $groupLarge->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "SeparatorLargeContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        $groupMedium = $optionsGroup->addGroup(__("bearcms.themes.options.Separator.Medium"));
                        $groupMedium->addOption($idPrefix . "SeparatorMediumCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-separator-element-medium"]
                            ],
                            "defaultValue" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"60%"}'
                        ]);

                        $containerSelector = ":has(> .bearcms-separator-element-medium)";
                        $groupContainer = $groupMedium->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "SeparatorMediumContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);

                        $groupSmall = $optionsGroup->addGroup(__("bearcms.themes.options.Separator.Small"));
                        $groupSmall->addOption($idPrefix . "SeparatorSmallCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["selector", $parentSelector . $defaultStyleSelector . "> .bearcms-separator-element-small"]
                            ],
                            "defaultValue" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"30%"}'
                        ]);

                        $containerSelector = ":has(> .bearcms-separator-element-small)";
                        $groupContainer = $groupSmall->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "SeparatorSmallContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                            "cssOptions" => ["*/hoverState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                            "cssOutput" => [
                                ["rule", $parentSelector . $containerSelector, "box-sizing:border-box;"],
                                ["selector", $parentSelector . $containerSelector]
                            ]
                        ]);
                    }
                }];
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_COLUMNS')) {
            InternalThemes::$elementsOptions['columns'] = function ($options, $idPrefix, $parentSelector, $context, $details) {
                if ($context === InternalThemes::OPTIONS_CONTEXT_ELEMENT) {
                    $optionsGroup = $options;
                } else {
                    throw new \Exception('Not supported in other contexts!');
                }
                $defaultValue = ElementsDataHelper::getDefaultElementStyle('columns');
                $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('columns', true)['layout']['value'];
                $optionsGroup->addOption($idPrefix . "layout", "columnsLayout", '', [
                    "states" => [
                        ["type" => "size"],
                        ["type" => "screenSize"]
                    ],
                    "cssOutput" => [
                        ["selector", $parentSelector, '--bearcms-elements-spacing:{cssPropertyValue(spacing,inherit)};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-columns-widths:{cssPropertyValue(widths,' . $defaultLayoutValue['widths'] . ')};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-columns-direction:{cssPropertyValue(direction,' . $defaultLayoutValue['direction'] . ')};'],
                    ],
                    "defaultValue" => $defaultValue['layout'],
                    "onHighlight" => [['cssSelector', $parentSelector]]
                ]);
                $optionsGroup->addVisibility($idPrefix . "visibility", $parentSelector);
            };
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_FLOATING_BOX')) {
            InternalThemes::$elementsOptions['floatingBox'] = function ($options, $idPrefix, $parentSelector, $context, $details) {
                if ($context === InternalThemes::OPTIONS_CONTEXT_ELEMENT) {
                    $optionsGroup = $options;
                } else {
                    throw new \Exception('Not supported in other contexts!');
                }
                $defaultValue = ElementsDataHelper::getDefaultElementStyle('floatingBox');
                $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('floatingBox', true)['layout']['value'];
                $optionsGroup->addOption($idPrefix . "layout", "floatingBoxLayout", '', [
                    "states" => [
                        ["type" => "size"],
                        ["type" => "screenSize"]
                    ],
                    "cssOutput" => [
                        ["selector", $parentSelector, '--bearcms-elements-spacing:{cssPropertyValue(spacing,inherit)};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-floating-box-position:{cssPropertyValue(position,' . $defaultLayoutValue['position'] . ')};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-floating-box-width:{cssPropertyValue(width,' . $defaultLayoutValue['width'] . ')};'],
                    ],
                    "defaultValue" => $defaultValue['layout'],
                    "onHighlight" => [['cssSelector', $parentSelector]]
                ]);
                $optionsGroup->addVisibility($idPrefix . "visibility", $parentSelector);
            };
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_FLEXIBLE_BOX')) {
            InternalThemes::$elementsOptions['flexibleBox'] = function ($options, $idPrefix, $parentSelector, $context, $details) {
                if ($context === InternalThemes::OPTIONS_CONTEXT_ELEMENT) {
                    $optionsGroup = $options;
                } else {
                    throw new \Exception('Not supported in other contexts!');
                }
                $defaultValue = ElementsDataHelper::getDefaultElementStyle('flexibleBox');
                $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('flexibleBox', true)['layout']['value'];
                $optionsGroup->addOption($idPrefix . "layout", "flexibleBoxLayout", '', [
                    "states" => [
                        ["type" => "size"],
                        ["type" => "screenSize"]
                    ],
                    "cssOutput" => [
                        ["selector", $parentSelector, '--bearcms-elements-spacing:{cssPropertyValue(spacing,inherit)};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-flexible-box-direction:{cssPropertyValue(direction,' . $defaultLayoutValue['direction'] . ')};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-flexible-box-alignment:{cssPropertyValue(alignment,' . $defaultLayoutValue['alignment'] . ')};'],
                        ["selector", $parentSelector, '--css-to-attribute-data-bearcms-flexible-box-cross-alignment:{cssPropertyValue(cross-alignment,default)};'],
                    ],
                    "defaultValue" => $defaultValue['layout'],
                    "onHighlight" => [['cssSelector', $parentSelector]]
                ]);
                $optionsGroup->addOption($idPrefix . "css", "css", '', [
                    "cssTypes" => ["cssMargin", "cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/hoverState", "*/focusState", "*/activeState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["selector", $parentSelector]
                    ],
                ]);
                $optionsGroup->addVisibility($idPrefix . "visibility", $parentSelector);
                $optionsGroup->addOption($idPrefix . "code", "code", '', [
                    "states" => [
                        ["type" => "visibility"],
                    ],
                    "cssOutput" => [
                        ["selector", $parentSelector],
                    ]
                ]);
            };
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_SLIDER')) {
            InternalThemes::$elementsOptions['slider'] = ['v3', function ($options, $idPrefix, $parentSelector, $context, $details) {
                $isElementContext = $context === InternalThemes::OPTIONS_CONTEXT_ELEMENT;
                if ($isElementContext) {
                    $optionsGroup = $options;
                    $defaultStyleSelector = '';
                } else {
                    $optionsGroup = $options->addGroup(__("bearcms.themes.options.Slider"));
                    $defaultStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"]).bearcms-slider-element';
                    $optionsGroup->details['internalElementSelector'] = [$idPrefix, $parentSelector . " .bearcms-slider-element"];
                }
                $defaultValue = ElementsDataHelper::getDefaultElementStyle('slider');
                $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('slider', true)['layout']['value'];
                $optionsGroup->addOption($idPrefix . "layout", "sliderLayout", '', [
                    "states" => [
                        ["type" => "size"],
                        ["type" => "screenSize"],
                        ["type" => "pageType"]
                    ],
                    "cssOutput" => [
                        ["selector", $parentSelector . $defaultStyleSelector, '--css-to-attribute-data-bearcms-slider-direction:{cssPropertyValue(direction,' . $defaultLayoutValue['direction'] . ')};'],
                        ["selector", $parentSelector . $defaultStyleSelector, '--css-to-attribute-data-bearcms-slider-alignment:{cssPropertyValue(alignment,' . $defaultLayoutValue['alignment'] . ')};'],
                        ["selector", $parentSelector . $defaultStyleSelector, '--css-to-attribute-data-bearcms-slider-autoplay:{cssPropertyValue(autoplay)};'],
                        ["selector", $parentSelector . $defaultStyleSelector, '--css-to-attribute-data-bearcms-slider-infinite:{cssPropertyValue(infinite)};'],
                        ["selector", $parentSelector . $defaultStyleSelector, '--css-to-attribute-data-bearcms-slider-swipe:{cssPropertyValue(swipe)};'],
                        ["selector", $parentSelector . $defaultStyleSelector, '--bearcms-slider-element-speed:{cssPropertyValue(speed,' . $defaultLayoutValue['speed'] . ')};'],
                    ],
                    "defaultValue" => $defaultValue['layout'],
                    "onHighlight" => [['cssSelector', $parentSelector . $defaultStyleSelector]]
                ]);
                $optionsGroup->addOption($idPrefix . "css", "css", '', [
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"], // todo add "cssPadding" but find solution for the absolute div inside
                    "cssOptions" => ["*/hoverState", "*/focusState", "*/visibilityState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["selector", $parentSelector . $defaultStyleSelector]
                    ],
                ]);
                if ($isElementContext) {
                    $optionsGroup->addVisibility($idPrefix . "visibility", $parentSelector);
                }

                $optionSlideGroup = $optionsGroup->addGroup(__('bearcms.themes.options.slider.Slide'));
                $optionSlideGroup->addOption($idPrefix . "slideCSS", "css", '', [
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOptions" => ["*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState", "*/firstChildState", "*/lastChildState"],
                    "cssOutput" => [
                        ["rule", $parentSelector . ">div:first-child>*", "box-sizing:border-box;"],
                        ["selector", $parentSelector . $defaultStyleSelector . ">div:first-child>*"]
                    ]
                ]);

                $optionNextButtonGroup = $optionsGroup->addGroup(__('bearcms.themes.options.slider.Next button'));
                $optionNextButtonGroup->addOption($idPrefix . "nextButtonCSS", "css", '', [
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/hoverState", "*/focusState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["rule", $parentSelector . " [data-bearcms-slider-button-next]", "box-sizing:border-box;display:inline-block;"],
                        ["selector", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-button-next]"]
                    ],
                    "defaultValue" => $defaultValue['nextButton']
                ]);
                $optionNextButtonGroup->addVisibility($idPrefix . "nextButtonVisibility", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-button-next]", ['defaultValue' => $defaultValue['nextButtonVisibility']]);

                $optionPreviousButtonGroup = $optionsGroup->addGroup(__('bearcms.themes.options.slider.Previous button'));
                $optionPreviousButtonGroup->addOption($idPrefix . "previousButtonCSS", "css", '', [
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/hoverState", "*/focusState", "*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["rule", $parentSelector . " [data-bearcms-slider-button-previous]", "box-sizing:border-box;display:inline-block;"],
                        ["selector", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-button-previous]"]
                    ],
                    "defaultValue" => $defaultValue['previousButton']
                ]);
                $optionPreviousButtonGroup->addVisibility($idPrefix . "previousButtonVisibility", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-button-previous]", ['defaultValue' => $defaultValue['previousButtonVisibility']]);

                $optionIndicatorGroup = $optionsGroup->addGroup(__('bearcms.themes.options.slider.Indicators'));
                $optionIndicatorGroup->addOption($idPrefix . "indicatorCSS", "css", '', [
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState", "*/firstChildState", "*/lastChildState"],
                    "cssOutput" => [
                        ["selector", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-indicator]"]
                    ],
                    "defaultValue" => $defaultValue['indicator']
                ]);
                $optionIndicatorSelectedGroup = $optionIndicatorGroup->addGroup(__('bearcms.themes.options.slider.IndicatorSelected'));
                $optionIndicatorSelectedGroup->addOption($idPrefix . "indicatorSelectedCSS", "css", '', [
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["selector", $parentSelector . $defaultStyleSelector . " [data-bearcms-slider-indicator-selected]"]
                    ],
                    "defaultValue" => $defaultValue['indicatorSelected']
                ]);

                $optionIndicatorsContainerGroup = $optionIndicatorGroup->addGroup(__("bearcms.themes.options.Container"));
                $optionIndicatorsContainerGroup->addOption($idPrefix . "indicatorsContainerCSS", "css", '', [
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssTextAlign", "cssSize", "cssTransform"],
                    "cssOptions" => ["*/sizeState", "*/screenSizeState", "*/pageTypeState", "*/tagsState"],
                    "cssOutput" => [
                        ["rule", $parentSelector . ">div:nth-child(2) [data-bearcms-slider-indicators]", "box-sizing:border-box;"],
                        ["selector", $parentSelector . $defaultStyleSelector . ">div:nth-child(2) [data-bearcms-slider-indicators]"]
                    ],
                    "defaultValue" => $defaultValue['indicators']
                ]);
                $optionIndicatorsContainerGroup->addVisibility($idPrefix . "indicatorsContainerVisibility", $parentSelector . $defaultStyleSelector . ">div:nth-child(2) [data-bearcms-slider-indicators]", ['defaultValue' => $defaultValue['indicatorsVisibility']]);
            }];
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_CANVAS')) {
            $type = new ElementType('canvas', 'bearcms-canvas-element', self::$contextDir . '/components/bearcmsCanvasElement.php');
            $type->properties = [
                [
                    'id' => 'value',
                    'type' => 'string'
                ]
            ];
            $type->onDelete = function (array $data): void {
                if (isset($data['value'])) {
                    $files = CanvasElementHelper::getFilesInValue((string)$data['value']);
                    foreach ($files as $filename) {
                        InternalData::deleteElementAsset($filename);
                    }
                }
            };
            $type->onDuplicate = function (array $data): array {
                if (isset($data['value'])) {
                    $value = (string)$data['value'];
                    $files = CanvasElementHelper::getFilesInValue($value, true);
                    $filesToUpdate = [];
                    foreach ($files as $filename) {
                        $filesToUpdate[$filename] = InternalData::duplicateElementAsset($filename);
                    }
                    if (!empty($filesToUpdate)) {
                        $data['value'] = CanvasElementHelper::updateFilesInValue($value, $filesToUpdate);
                    }
                }
                return $data;
            };
            $type->onExport = function (array $data, callable $add): array {
                if (isset($data['value'])) {
                    $value = (string)$data['value'];
                    $files = CanvasElementHelper::getFilesInValue($value, true);
                    $filesToUpdate = [];
                    foreach ($files as $i => $filename) {
                        $filesToUpdate[$filename] = InternalData::exportElementAsset($filename, 'file' . ($i + 1), $add);
                    }
                    if (!empty($filesToUpdate)) {
                        $data['value'] = CanvasElementHelper::updateFilesInValue($value, $filesToUpdate);
                    }
                }
                return $data;
            };
            $type->onImport = function (array $data, ImportContext $context): array {
                if (isset($data['value'])) {
                    $value = (string)$data['value'];
                    $files = CanvasElementHelper::getFilesInValue($value, true);
                    $filesToUpdate = [];
                    foreach ($files as $filename) {
                        $filesToUpdate[$filename] = InternalData::importElementAsset($filename, 'bearcms/files/canvasstyleimage/', $context);
                    }
                    if (!empty($filesToUpdate)) {
                        $data['value'] = CanvasElementHelper::updateFilesInValue($value, $filesToUpdate);
                    }
                }
                return $data;
            };
            $type->getUploadsSizeItems = function (array $data): array {
                $result = [];
                if (isset($data['value'])) {
                    $files = CanvasElementHelper::getFilesInValue((string)$data['value']);
                    foreach ($files as $filename) {
                        $result[] = InternalData::getFilenameDataKey($filename);
                    }
                }
                return $result;
            };
            self::add($type);
        }
    }
}

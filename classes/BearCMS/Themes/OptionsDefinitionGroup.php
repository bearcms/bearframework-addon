<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

class OptionsDefinitionGroup
{

    protected $name = '';
    protected $description = '';
    protected $options = [];

    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function add($option)
    {
        $this->options[] = $option;
        return $this;
    }

    public function addGroup($name, $description = '')
    {
        $group = new \BearCMS\Themes\OptionsDefinitionGroup($name, $description);
        $this->add($group);
        return $group;
    }

    public function makeGroup($name, $description = '')
    {
        return new \BearCMS\Themes\OptionsDefinitionGroup($name, $description);
    }

    public function addElements($idPrefix, $parentSelector)
    {
        $this->options = array_merge($this->options, [
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Heading"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Large"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingLargeCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-large", "font-weight:normal;"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-large"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Medium"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingMediumCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-medium", "font-weight:normal;"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-medium"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Small"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingSmallCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-small", "font-weight:normal;"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-small"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Text"),
                "options" => [
                    [
                        "id" => $idPrefix . "TextCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-text-element"],
                            ["rule", $parentSelector . " .bearcms-text-element ul,ol,li", "list-style-position:inside;"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Links"),
                        "options" => [
                            [
                                "id" => $idPrefix . "TextLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", $parentSelector . " .bearcms-text-element a"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Link"),
                "options" => [
                    [
                        "id" => $idPrefix . "LinkCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-link-element", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                            ["selector", $parentSelector . " .bearcms-link-element"]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Image"),
                "options" => [
                    [
                        "id" => $idPrefix . "ImageCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-image-element", "overflow:hidden;"],
                            ["rule", $parentSelector . " .bearcms-image-element img", "border:0;"],
                            ["selector", $parentSelector . " .bearcms-image-element"]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Image gallery"),
                "options" => [
                    [
                        "id" => $idPrefix . "ImageGalleryCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-image-gallery-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Image"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ImageGalleryImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                    ["rule", $parentSelector . " .bearcms-image-gallery-element-image img", "border:0;"],
                                    ["selector", $parentSelector . " .bearcms-image-gallery-element-image"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Video"),
                "options" => [
                    [
                        "id" => $idPrefix . "VideoCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-video-element", "overflow:hidden;"],
                            ["selector", $parentSelector . " .bearcms-video-element"]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Navigation"),
                "options" => [
                    [
                        "id" => $idPrefix . "NavigationCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-navigation-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Elements"),
                        "options" => [
                            [
                                "id" => $idPrefix . "NavigationItemLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-navigation-element-item a", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                    ["selector", $parentSelector . " .bearcms-navigation-element-item a"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
//            [
//                "type" => "group",
//                "name" => __("bearcms.themes.options.Contact Form"),
//                "options" => [
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.contactForm.Email label"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ContactFormEmailLabelCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-contact-form-element-email-label", "display:block;"],
//                                    ["selector", $parentSelector . " .bearcms-contact-form-element-email-label"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.contactForm.Email input"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ContactFormEmailInputCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-contact-form-element-email", "box-sizing:border-box;border:0;"],
//                                    ["selector", $parentSelector . " .bearcms-contact-form-element-email"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.contactForm.Message label"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ContactFormMessageLabelCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-contact-form-element-message-label", "display:block;"],
//                                    ["selector", $parentSelector . " .bearcms-contact-form-element-message-label"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.contactForm.Message input"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ContactFormMessageInputCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-contact-form-element-message", "box-sizing:border-box;border:0;"],
//                                    ["selector", $parentSelector . " .bearcms-contact-form-element-message"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.contactForm.Send button"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ContactFormSendButtonCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-contact-form-element-send-button", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                    ["selector", $parentSelector . " .bearcms-contact-form-element-send-button"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.contactForm.Send button waiting"),
//                                "options" => [
//                                    [
//                                        "id" => $idPrefix . "ContactFormSendButtonWaitingCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", $parentSelector . " .bearcms-contact-form-element-send-button-waiting", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                            ["selector", $parentSelector . " .bearcms-contact-form-element-send-button-waiting"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ]
//                ]
//            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Comments"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.comments.Comment"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsCommentCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-comment", "overflow:hidden;"],
                                    ["selector", $parentSelector . " .bearcms-comments-comment"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Author name"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsAuthorNameCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-author-name", "display:inline-block;"],
                                            ["selector", $parentSelector . " .bearcms-comments-comment-author-name"]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Author image"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsAuthorImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-author-image", "display:inline-block;float:left;"],
                                            ["selector", $parentSelector . " .bearcms-comments-comment-author-image"]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Date"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsDateCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-date", "display:inline-block;float:right;"],
                                            ["selector", $parentSelector . " .bearcms-comments-comment-date"]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Text"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsTextCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-comments-comment-text"]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Text links"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsTextLinksCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-text a", "display:inline-block;"],
                                            ["selector", $parentSelector . " .bearcms-comments-comment-text a"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.comments.Text input"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsTextInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-element-text-input", "box-sizing:border-box;border:0;"],
                                    ["selector", $parentSelector . " .bearcms-comments-element-text-input"]
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.comments.Send button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsSendButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-element-send-button", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                    ["selector", $parentSelector . " .bearcms-comments-element-send-button"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Send button waiting"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsSendButtonWaitingCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-element-send-button-waiting", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                            ["selector", $parentSelector . " .bearcms-comments-element-send-button-waiting"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.comments.Show more button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsShowMoreButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-show-more-button", "display:inline-block;"],
                                    ["selector", $parentSelector . " .bearcms-comments-show-more-button"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.comments.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-comments-show-more-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
//            [
//                "type" => "group",
//                "name" => __("bearcms.themes.options.Forum posts"),
//                "options" => [
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPosts.Post"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ForumPostsPostCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["selector", $parentSelector . " .bearcms-forum-posts-post"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPosts.Title"),
//                                "options" => [
//                                    [
//                                        "id" => $idPrefix . "ForumPostsTitleCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["selector", $parentSelector . " .bearcms-forum-posts-post-title"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPosts.Replies count"),
//                                "options" => [
//                                    [
//                                        "id" => $idPrefix . "ForumPostsRepliesCountCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", $parentSelector . " .bearcms-forum-posts-post-replies-count", "display:inline-block;float:right;"],
//                                            ["selector", $parentSelector . " .bearcms-forum-posts-post-replies-count"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPosts.Show more button"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ForumPostsShowMoreButtonCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-forum-posts-show-more-button", "display:inline-block;"],
//                                    ["selector", $parentSelector . " .bearcms-forum-posts-show-more-button"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPosts.Container"),
//                                "options" => [
//                                    [
//                                        "id" => $idPrefix . "ForumPostsShowMoreButtonContainerCSS",
//                                        "type" => "css",
//                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                        "cssOutput" => [
//                                            ["selector", $parentSelector . " .bearcms-forum-posts-show-more-button-container"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPosts.New post bitton"),
//                        "options" => [
//                            [
//                                "id" => $idPrefix . "ForumPostsNewPostButtonCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", $parentSelector . " .bearcms-forum-posts-new-post-button", "display:inline-block;"],
//                                    ["selector", $parentSelector . " .bearcms-forum-posts-new-post-button"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPosts.Container"),
//                                "options" => [
//                                    [
//                                        "id" => $idPrefix . "ForumPostsShowMoreButtonContainerCSS",
//                                        "type" => "css",
//                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                        "cssOutput" => [
//                                            ["selector", $parentSelector . " .bearcms-forum-posts-new-post-button-container"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ]
//                ]
//            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.HTML code"),
                "options" => [
                    [
                        "id" => $idPrefix . "HtmlCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-html-element"],
                            ["rule", $parentSelector . " .bearcms-html-element ul,ol,li", "list-style-position:inside;"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Links"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HtmlLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-html-element a", "display:inline-block;"],
                                    ["selector", $parentSelector . " .bearcms-html-element a"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Blog posts"),
                "options" => [
                    [
                        "id" => $idPrefix . "BlogPostsCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-blog-posts-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Post"),
                        "options" => [
                            [
                                "id" => $idPrefix . "BlogPostsPostCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                                "cssOutput" => [
                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.Title"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostTitleCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title"]
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.options.Container"),
                                        "options" => [
                                            [
                                                "id" => $idPrefix . "BlogPostsPostTitleContainerCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title-container"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.Date"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostDateCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date"]
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.options.Container"),
                                        "options" => [
                                            [
                                                "id" => $idPrefix . "BlogPostsPostDateContainerCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date-container"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.Content"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostContentCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-content"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.blogPosts.Show more button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "BlogPostsShowMoreButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button", "display:inline-block;"],
                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.blogPosts.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        foreach (\BearCMS\Internal\Themes::$elementsOptions as $elementOption) {
            $defintion = call_user_func($elementOption, $idPrefix, $parentSelector);
            if (is_array($defintion)) {
                $this->options[] = $defintion;
            }
        }
        return $this;
    }

    public function addPages()
    {
        $this->options = array_merge($this->options, [
            [
                "type" => "group",
                "name" => __("bearcms.themes.options.Blog post page"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Title"),
                        "options" => [
                            [
                                "id" => "blogPostPageTitleCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-blogpost-page-title", "font-weight:normal;"],
                                    ["selector", ".bearcms-blogpost-page-title"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.Container"),
                                "options" => [
                                    [
                                        "id" => "blogPostPageTitleContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-blogpost-page-title-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Date"),
                        "options" => [
                            [
                                "id" => "blogPostPageDateVisibility",
                                "type" => "list",
                                "name" => __('bearcms.themes.options.Visibility'),
                                "values" => [
                                    [
                                        "value" => "1",
                                        "name" => __('bearcms.themes.options.Visible')
                                    ],
                                    [
                                        "value" => "0",
                                        "name" => __('bearcms.themes.options.Hidden')
                                    ]
                                ]
                            ],
                            [
                                "id" => "blogPostPageDateCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".bearcms-blogpost-page-date"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.options.Container"),
                                "options" => [
                                    [
                                        "id" => "blogPostPageDateContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-blogpost-page-date-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.options.Content"),
                        "options" => [
                            [
                                "id" => "blogPostPageContentCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", ".bearcms-blogpost-page-content"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
//            [
//                "type" => "group",
//                "name" => __("bearcms.themes.options.New forum post page"),
//                "options" => [
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.newForumPostPage.Title label"),
//                        "options" => [
//                            [
//                                "id" => "newForumPostPageTitleLabelCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-new-forum-post-page-title-label", "display:block;"],
//                                    ["selector", ".bearcms-new-forum-post-page-title-label"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.newForumPostPage.Title input"),
//                        "options" => [
//                            [
//                                "id" => "newForumPostPageTitleInputCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-new-forum-post-page-title", "box-sizing:border-box;border:0;"],
//                                    ["selector", ".bearcms-new-forum-post-page-title"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.newForumPostPage.Text label"),
//                        "options" => [
//                            [
//                                "id" => "newForumPostPageTextLabelCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-new-forum-post-page-text-label", "display:block;"],
//                                    ["selector", ".bearcms-new-forum-post-page-text-label"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.newForumPostPage.Text input"),
//                        "options" => [
//                            [
//                                "id" => "newForumPostPageTextInputCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-new-forum-post-page-text", "box-sizing:border-box;border:0;"],
//                                    ["selector", ".bearcms-new-forum-post-page-text"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.newForumPostPage.Send button"),
//                        "options" => [
//                            [
//                                "id" => "newForumPostPageSendButtonCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-new-forum-post-page-send-button", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                    ["selector", ".bearcms-new-forum-post-page-send-button"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.newForumPostPage.Send button waiting"),
//                                "options" => [
//                                    [
//                                        "id" => "newForumPostPageSendButtonWaitingCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-new-forum-post-page-send-button-waiting", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                            ["selector", ".bearcms-new-forum-post-page-send-button-waiting"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ]
//                ]
//            ],
//            [
//                "type" => "group",
//                "name" => __("bearcms.themes.options.Forum post page"),
//                "options" => [
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Title"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageTitleCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-forum-post-page-title", "font-weight:normal;"],
//                                    ["selector", ".bearcms-forum-post-page-title"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Container"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageTitleContainerCSS",
//                                        "type" => "css",
//                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                        "cssOutput" => [
//                                            ["selector", ".bearcms-forum-post-page-title-container"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Date"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageDateCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["selector", ".bearcms-forum-post-page-date"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Container"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageDateContainerCSS",
//                                        "type" => "css",
//                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                        "cssOutput" => [
//                                            ["selector", ".bearcms-forum-post-page-date-container"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Content"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageContentCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["selector", ".bearcms-forum-post-page-content"]
//                                ]
//                            ]
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Reply"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageReplyCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-forum-post-page-reply", "overflow:hidden;"],
//                                    ["selector", ".bearcms-forum-post-page-reply"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Author name"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageReplyAuthorNameCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-forum-post-page-reply-author-name", "display:inline-block;"],
//                                            ["selector", ".bearcms-forum-post-page-reply-author-name"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Author image"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageReplyAuthorImageCSS",
//                                        "type" => "css",
//                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-forum-post-page-reply-author-image", "display:inline-block;float:left;"],
//                                            ["selector", ".bearcms-forum-post-page-reply-author-image"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Date"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageReplyDateCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-forum-post-page-reply-date", "display:inline-block;float:right;"],
//                                            ["selector", ".bearcms-forum-post-page-reply-date"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Text"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageReplyTextCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["selector", ".bearcms-forum-post-page-reply-text"]
//                                        ]
//                                    ]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Text links"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageReplyTextLinksCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-forum-post-page-reply-text a", "display:inline-block;"],
//                                            ["selector", ".bearcms-forum-post-page-reply-text a"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Text input"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageTextInputCSS",
//                                "type" => "css",
//                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-forum-post-page-text", "box-sizing:border-box;border:0;"],
//                                    ["selector", ".bearcms-forum-post-page-text"]
//                                ]
//                            ],
//                        ]
//                    ],
//                    [
//                        "type" => "group",
//                        "name" => __("bearcms.themes.options.forumPostPage.Send button"),
//                        "options" => [
//                            [
//                                "id" => "forumPostPageSendButtonCSS",
//                                "type" => "css",
//                                "cssOutput" => [
//                                    ["rule", ".bearcms-forum-post-page-send-button", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                    ["selector", ".bearcms-forum-post-page-send-button"]
//                                ]
//                            ],
//                            [
//                                "type" => "group",
//                                "name" => __("bearcms.themes.options.forumPostPage.Send button waiting"),
//                                "options" => [
//                                    [
//                                        "id" => "forumPostPageSendButtonWaitingCSS",
//                                        "type" => "css",
//                                        "cssOutput" => [
//                                            ["rule", ".bearcms-forum-post-page-send-button-waiting", "display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
//                                            ["selector", ".bearcms-forum-post-page-send-button-waiting"]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ]
//                ]
//            ],
        ]);
        return $this;
    }

    public function addCustomCSS()
    {
        $this->options[] = [
            "id" => "customCSS",
            "type" => "cssCode",
            "name" => __("bearcms.themes.options.Custom CSS")
        ];
        return $this;
    }

    public function toArray()
    {
        $result = [
            "type" => "group",
            "name" => $this->name
        ];
        if (strlen($this->description) > 0) {
            $result['description'] = $this->description;
        }
        $result['options'] = [];
        foreach ($this->options as $option) {
            $result['options'][] = is_object($option) && method_exists($option, 'toArray') ? $option->toArray() : (is_array($option) ? $option : (array) $option);
        }
        return $result;
    }

    public function getCSSRules()
    {
        $cssRules = [];
        $walkOptions = function($options) use (&$cssRules, &$walkOptions) {
            foreach ($options as $option) {
                if (isset($option['id'])) {
                    if (isset($option['cssOutput'])) {
                        foreach ($option['cssOutput'] as $outputDefinition) {
                            if (is_array($outputDefinition)) {
                                if (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
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
                if (isset($option['options'])) {
                    $walkOptions($option['options']);
                }
            }
        };
        $walkOptions($this->options);
        return $cssRules;
    }

}

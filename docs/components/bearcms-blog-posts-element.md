# &lt;component src="bearcms-blog-posts-element" /&gt;

Renders a list of the published blog posts. The newer posts are shown first.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`type`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Different data list types are available:

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`summary` Shows title and the beginning of the blog post

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`full` Shows title and the whole blog post content

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`titles` Shows only titles

`showDate`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Whether a date is shown too. Available values: true and false

`limit`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Maximum number of blog posts to list

## Examples

Creates an editable block with a specific id and type

```html
<component src="bearcms-blog-posts-element" id="sidebar-element-1" editable="true" type="titles" />
```

Creates a not editable block that lists only the newest blog post.

```html
<component src="bearcms-blog-posts-element" type="full" limit="1" />
```
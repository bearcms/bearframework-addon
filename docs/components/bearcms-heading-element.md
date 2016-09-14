# &lt;component src="bearcms-heading-element" /&gt;

Renders different types of titles.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`text`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The content of the element

`size`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The size of the text. Available values: large, medium, small.

## Examples

Creates an editable block with a specific id and type.

```html
<component src="bearcms-heading-element" id="sidebar-element-1" editable="true" text="Products" />
```

Creates a not editable block that shows a small title.

```html
<component src="bearcms-heading-element" text="Products" size="small" />
```
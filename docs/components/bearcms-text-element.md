# &lt;component src="bearcms-text-element" /&gt;

Renders text.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will no be editable if there is no logged in user, regardless of the attribute value.

`text`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The text inside this element.

## Examples

Creates an editable block with a specific id.

```html
<component src="bearcms-text-element" id="sidebar-element-1" editable="true" text="Once upon a time ..." />
```

Creates a not editable block renders HTML code.

```html
<component src="bearcms-text-element" text="Once upon a time ..." />
```
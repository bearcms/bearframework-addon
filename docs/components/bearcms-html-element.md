# &lt;component src="bearcms-html-element" /&gt;

Renders HTML code.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`code`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code inside this element.

## Examples

Creates an editable block with a specific id.

```html
<component src="bearcms-html-element" id="sidebar-element-1" editable="true" code="<strong>Some html code</strong>" />
```

Creates a not editable block renders HTML code.

```html
<component src="bearcms-html-element" code="<strong>Some html code</strong>" />
```
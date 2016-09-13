# &lt;component src="bearcms-link-element" /&gt;

Renders a link.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will no be editable if there is no logged in user, regardless of the attribute value.

`url`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The url of the link.

`text`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The text of the link.

`title`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML title attribute value.

## Examples

Creates an editable link block with a specific id, url and text

```html
<component src="bearcms-link-element" id="sidebar-element-1" editable="true" url="https://bearframework.com" text="Bear Framework" />
```

Creates a not editable link.

```html
<component src="bearcms-link-element" url="https://bearframework.com" text="Bear Framework" />
```
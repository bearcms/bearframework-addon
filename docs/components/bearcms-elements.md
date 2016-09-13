# &lt;component src="bearcms-elements" /&gt;

An elements container. The administrators use the UI to manage the elements inside.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the elements block.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the block editable or not. Available values: true and false. The block will no be editable if there is no logged in user, regardless of the attribute value.

`group`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Groups blocks so that elements can be moved inside blocks of same group.

`width`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The width of the block. Available values: 100%, 500px, 50rem, etc.

`spacing`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The space between elements in the block. Available values: 1rem, 15px, etc.

`class`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML class attribute value

## Examples

Creates an editable block with a specific id and spacing

```html
<component src="bearcms-elements" id="sidebar" editable="true" spacing="10px" />
```
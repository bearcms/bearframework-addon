# &lt;component src="bearcms-image-element" /&gt;

Renders an image.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`onClick`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Specifies what happens when the image is clicks

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`none` Nothing will happen

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`fullscreen` A fullscreen image will be opened

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`openUrl` A new page will open. This url must be set in the `url` attribute.

`class`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML class attribute value

`filename`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The filename of the image

`loadingBackground`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The type of background shown while the image is loading. Available values: none and checkered.

`title`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML title attribute value

`url`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The url to open of the onClick attribute is set to 'openUrl'.

## Examples

Creates an editable image block with a specific id and filename.

```html
<component src="bearcms-image-element" id="sidebar-element-1" editable="true" filename="app:assets/file1.jpg" onClick="fullscreen" />
```

Creates a not editable block that renders a link image

```html
<component src="bearcms-image-element" filename="app:assets/file1.jpg" onClick="openUrl" url="https://bearframework.com/" />
```
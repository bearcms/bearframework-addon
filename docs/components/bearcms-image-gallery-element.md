# &lt;component src="bearcms-image-gallery-element" /&gt;

Renders an image gallery.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`columnsCount`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of columns in the gallery grid.

`imageSize`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Columns count depends on the specified image size, if the `columnsCount` attribute is not specified. Available values: tiny, small, medium, large, huge

`imageAspectRatio`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The proportional relationship between the width and the height of every image. It is useful for cropping and resizing the images. Example values: 1:1, 1:2, 1.5:1, etc.

`spacing`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The spacing between the images. Example values: 10px, 1rem, etc.

## Examples

Creates an editable block with a specific id and fixed columns count

```html
<component src="bearcms-image-gallery-element" id="sidebar-element-1" editable="true" columnsCount="3">
    <file filename="app:assets/file1.jpg"/>
    <file filename="app:assets/file2.jpg"/>
    <file filename="app:assets/file3.jpg"/>
</component>
```

Creates a not editable block that renders some square images.

```html
<component src="bearcms-image-gallery-element" imageAspectRatio="1:1">
    <file filename="app:assets/file1.jpg"/>
    <file filename="app:assets/file2.jpg"/>
    <file filename="app:assets/file3.jpg"/>
</component>
```
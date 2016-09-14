# &lt;component src="bearcms-video-element" /&gt;

Renders video.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`url`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The url of the video to show. It must be provided by services like YouTube, Vimeo, etc.

`filename`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A filename of the local video file. Available value formats: /real/path/to/the/video/file.mp4, app:assets/file.mp4 (if the file is in the app folder), addon:vendor1/addon1/assets/file.mp4 (if the file is in an addon folder).

## Examples

Embeds a video from YouTube and makes the element editable.

```html
<component src="bearcms-video-element" id="sidebar-element-1" editable="true" url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />
```

Renders a local video file.

```html
<component src="bearcms-video-element" filename="app:assets/file1.mp4" />
```
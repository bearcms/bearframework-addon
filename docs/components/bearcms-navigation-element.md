# &lt;component src="bearcms-navigation-element" /&gt;

Renders a navigation menu containing pages created in the CMS.

## Attributes

`id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An identifier for the element. It's required only if the element is editable.

`editable`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makes the element editable or not. Available values: true or false. The element will be not editable if there is no logged in user, regardless of the attribute value.

`type`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Different data types are available:

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`top` Shows only the top level pages created in the CMS.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`children` Renders a list of specific page public children.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`tree` Renders a full tree containing all public pages.

`menuType`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Specifies the look of the navigation

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`horizontal-down` Creates horizontal navigation with drop down submenus.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`list-vertical` Creates a vertical list.

`selectedPath`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The currently selected page path. This will add special CSS class names to links pointing to pages in this path.

`showHomeLink`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Whether a link to the home page is shown alongside other pages. Available values: true and false.

`homeLinkText`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The text of the home button.

`pageID`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Specifies the parent page when the `type` attribute value is set to 'children'.

`class`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML class attribute value.

## Examples

Creates a horizontal navigation containing all pages.

```html
<component src="bearcms-navigation-element" type="tree" menuType="horizontal-down" />
```

Creates a vertical list of all top pages.

```html
<component src="bearcms-navigation-element" type="top" menuType="list-vertical" />
```
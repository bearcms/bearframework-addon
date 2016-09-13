# BearCMS\CurrentTemplateOptions
Data structure with array access containing all template options

## Methods

```php
public __construct ( array $data )
```

The constructor

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$data`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void offsetSet ( strint $offset , mixed $value )
```

Cannot modify template options

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$offset`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$value`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public boolean offsetExists ( string $offset )
```

Checks whether a option is set

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$offset`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if the option is set, FALSE otherwise

```php
public void offsetUnset ( strint $offset )
```

Cannot modify template options

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$offset`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public mixed offsetGet ( string $offset )
```

Returns the value of the option specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$offset`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The value of the option specified or null

```php
public void rewind ( void )
```

Iterator helper method

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void current ( void )
```

Iterator helper method

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void key ( void )
```

Iterator helper method

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void next ( void )
```

Iterator helper method

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void valid ( void )
```

Iterator helper method

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.
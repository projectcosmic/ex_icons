# External-use icons

This module helps with integrating [external-use icons](https://css-tricks.com/svg-sprites-use-better-icon-fonts/)
into Drupal.

## Setup

Once downloaded and installed, set the path to the icons sprite sheet using the
settings form at `/admin/config/media/ex-icons-settings`.

## Contents

### Theme element

A themable called `ex_icon` can be used as the `#theme` key to render any icon
from the sprite sheet. Use `#id` key to specify the icon.

```php
<?php
$render = [
  '#theme' => 'ex_icon',
  '#id' => 'arrow',
  '#attributes' => [
    'title' => t('Show more'),
    'width' => 25,
  ],
];
```

If a `title` attribute is set, then the SVG element will be given a `role` of
`img`, otherwise it will be `presentation`. If only one dimension attribute is
set, the the other will be calculated automatically from the source icon's
`viewBox` attribute.

### Twig function

Similar format as the theme element for use inline in twig templates. First
argument is the icon ID and the second is a hash of any attributes (optional).

```twig
{{ ex_icon('shopping-cart', { height: 20 }) }}
```

### Form element

An icon selection form element to allow picking of an icon in the sprite sheet
graphically. Currently limited to only selecting one value.

```php
$form['icon'] = [
  '#type' => 'ex_icon_select',
  '#title' => $this->t('Accompanying icon'),
  '#default_value' => $this->getSetting('icon'),
];
```

### Field API integration

A field type, widget and formatter to select and  display an icon using the
things mentioned above.

### Icons Manager service

A service with getters of icon data.

Data is cached for efficiency but can be cleared independently with [Drush](https://www.drush.org/):

```
$ drush cc ex-icons
```
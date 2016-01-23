ui-kit Symfony Bundle
======

A Symfony project created on January 16, 2016, 12:49 pm.

Install UIkit
=============

Add this to your composer.json file

```
"require": {
        ...
        "uikit/uikit": "2.23.*",
        ...
    },
```
And composer update

Add Uikit to the layout
=======================

Assetic installation
========================


`$ composer require symfony/assetic-bundle`


In your config.yml

```
assetic:
    assets:
        uikit_css:
            inputs:
                - %kernel.root_dir%/../vendor/uikit/uikit/themes/almost-flat/uikit-customizer.less
            filters:
                - less
                - cssrewrite
            output: css/uikit.css
        uikit_js:
            inputs:
                - %kernel.root_dir%/../vendor/uikit/uikit/docs/js/uikit.min.js
                - %kernel.root_dir%/../vendor/uikit/uikit/src/js/components/datepicker.js
                - %kernel.root_dir%/../vendor/uikit/uikit/src/js/components/sticky.js
                - %kernel.root_dir%/../vendor/uikit/uikit/src/js/components/accordion.js
                - %kernel.root_dir%/../vendor/uikit/uikit/src/js/components/tooltip.js
            output: js/uikit.min.js
        jquery_js:
            inputs:
                - %kernel.root_dir%/../vendor/components/jquery/jquery.min.js
            output: js/jquery.min.js
```
`php bin/console assetic:dump`

Twig templates
===============

## Layout Modifiers

You can specify either 'horizontal' or 'stacked' layout modifiers in your twig form

`{{ uikit_set_layout_modifier("horizontal") }}`
or
`{{ form(form, {layout_modifier: "horizontal"}) }}`

## Use Icons

add the icon name as an attr options (sans the 'uk-icon-'):
```php
->add('text_icon_calendar', "text", array("attr" => array("icon" => "calendar", "placeholder" =>  "Date")))
```
or
```php
->add('text_icon_clock', "text", array("attr" => array("icon" => "clock-o", "placeholder" =>  "Date")))
```


TODO:  
Configure which javascript modules you want you want to use in config.yml, 
finish templating, 
add errors,
tests!
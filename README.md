## VcoZfAuthAcl - Zend Framework 2 auth and acl module. 

## Installation
### Composer
 * Install [Composer](http://getcomposer.org/doc/00-intro.md)
 * Install the module using Composer into your application's vendor directory. Add the following line to your `composer.json`.

 ```json
 {
    "require": {
        "vcomedia/vco-zf-auth-acl": "dev-master"
    }
 }
```
 * Execute ```composer update```
 * Enable the module in your ZF2 `application.config.php` file.

 ```php
 return array(
     'modules' => array(
         'VcoZfAuthAcl'
     )
 );
 ```
 * Copy and paste the `vco-zf-auth-acl/config/module.vco-zf-auth-acl.local.php.dist` file to your `config/autoload` folder and customize it with your configuration settings. Make sure to remove `.dist` from your file. Your `module.vco-zf-auth-acl.local.php` might look something like the following:

  ```php
 <?php

 return array(
     'VcoZfAuthAcl' => array(
     )
 );
  ```

## Options

## Notes

## License
The MIT License (MIT)

Copyright (c) 2015 Vahag Dudukgian

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

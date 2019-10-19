## Installation

Install via composer:

```bash
$ composer require zendframework/zend-mvc-plugin-prg
```

If you are using the [zend-component-installer](https://docs.zendframework.com/zend-component-installer/),
you're done!

If not, you will need to add the component as a module to your
application. Add the entry `'Zend\Mvc\Plugin\Prg'` to
your list of modules in your application configuration (typically
one of `config/application.config.php` or `config/modules.config.php`).

## Usage

When a user sends a POST request (e.g. after submitting a form), their
browser will try to protect them from sending the POST again, breaking
the back button, causing browser warnings and pop-ups, and sometimes
reposting the form. Instead, when receiving a POST, we should store the
data in a session container and redirect the user to a GET request.

This plugin can be invoked with two arguments:

- `$redirect`, a string containing the redirect location,
  which can either be a named route or a URL, based on the contents of
  the second parameter.

- `$redirectToUrl`, a boolean that when set to
  `true`, causes the first parameter to be treated as a URL
  instead of a route name (this is required when redirecting to a URL
  instead of a route). This argument defaults to `false`.

When no arguments are provided, the current matched route is used.

### Example Usage

```php
// Pass in the route/url you want to redirect to after the POST
$prg = $this->prg('/user/register', true);

if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
    // Returned a response to redirect us.
    return $prg;
}

if ($prg === false) {
    // This wasn't a POST request, but there were no params in the flash
    // messenger; this is probably the first time the form was loaded.
    return ['form' => $myForm];
}

// $prg is an array containing the POST params from the previous request
$form->setData($prg);

// ... your form processing code here
```

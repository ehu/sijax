Sijax stands for "Simple ajax" and provides just that.
It's a simple php/jquery library providing easy ajax integration on both client and server side. It let's you register callable functions/methods, processing incoming data and dispatch calls.

This repository is a fork of [spantaleev/Sijax](http://github.com/spantaleev/sijax) with the following changes:
- The dependency on the `Core_Loader` is removed (you should load manually or use your own autoloader)
- Removed the `Suggest` and `Upload` plugins (rather use jquery plugins with sijax backend, see examples)
- The comet plugin is kept as is
- The [json2.js](http://github.com/douglascrockford/JSON-js/blob/master/json2.js) is updated and minified
- New examples (work in progress)
- Supports multiple server side uri's
- Added some helpers both client side and server side

There are sample files in `examples/` that demonstrate how it can be used.

## How does it work? ##

Sijax lets you register any function (simple functions, public class method, object method, closure) to be called from the client (browser) using javascript like this:

    Sijax.request('myFunction', 'uri', ['argument 1', 15.84]);

Where `myFunction` is the server side function to call, `uri` is the url to the server side script containing the function and `['argument 1', 15.84]` is the arguments to pass to `myFunction`.

Ajax support is provided by [jQuery](http://jquery.com/) at the low-level. Sijax only handles dispatching the correct registered function on the server, and interpreting the response.

A registered function may be referred to as response function. It gets triggered with a javascript call, and receives a `Response object` as its first argument. By calling different methods on the `Response object` the response function talks back to the browser.
Here's how the myFunction implementation on server side might look on the PHP side:

    function myFunction(SijaxResponse $objResponse, $message, $double) {
        $objResponse->alert('Argument 1: ' . $message);
    }

Once the response function exits, the `queued commands` (like `alert()`, or any other method called on the response object) would be send to the browser. `alert()` shows the default javascript alert window in the browser.

## Client side helpers ##

- `Sijax::sijaxRequest($method, $url, $args = array())` - returns a Sijax.request javascript function
- `Sijax::sijaxFunction($method, $url, $selector, $param = array(), $script = NULL, $event = 'click', $dom = 'document') - generates an event handler for $selector
- `Sijax::sijaxFunctionReBind($method, $url, $selector, $args = array())` - generates a new event handler for $selector
- `Sijax::sijaxGetFormValues($selector)` - returns a Sijax.getFormValues(selector)
- `Sijax::sijaxFunctionFormValues($method, $url, $selector, $form_selector, $script = NULL, $event = 'click', $dom = 'document') - binds Sijax.getFormValues(selector) to an event with jquery.on
- `Sijax::sijaxPopulatePostData($sijax_rq, $args)` - adds post data to the Sijax.request (useful when using Sijax request as data source in jquery plugins)

These helpers can also be utilized on server side with the $objResponse->script() response since they are generating javascript

## Server side helpers ##

- `Sijax::cleanBuffer()` - recursive deleting of all output buffers
- `Sijax::stopProcessing()` - a wrapper around `exit()`

## Available response functions ##

- `alert($message)` - shows the alert message
- `html($selector, $html)` - sets the given `$html` to all elements matching the jQuery selector `$selector`
- `htmlAppend($selector, $html)` - same as `html()`, but appends html instead of setting the new html
- `htmlPrepend($selector, $html)` - same as `html()`, but prepends html instead of setting the new html
- `attr($selector, $property, $value)` - changes the `$property` to `$value` for all elements matching the jQuery selector `$selector`
- `attrAppend($selector, $property, $value)` - same as `attr()`, but appends to the property value, instead of setting a new value
- `attrPrepend($selector, $property, $value)` - same as `attr()`, but prepends to the property value, instead of setting a new value
- `css($selector, $property, $value)` - changes the style `$property` to `$value` for all elements matching the jQuery selector `$selector`
- `script($javascript)` - executes the given `$javascript` code
- `remove($selector)` - removes all DOM elements matching the selector
- `redirect($url)` - redirects the browser to the given `$url`
- `call($function, $argumentsArray)` - calls a javascript function named `$function`, passing the given arguments to it

Here's an example on how to use some of them:

	/**
	* Server side function
	*/
	
    function myFunction(SijaxResponse $objResponse, $message, $double) {
        //Supposing we have: `<div id="message-container"></div>`
        $objResponse->html('#message-container', $message);

        //Supposing we have: `<input type="text" id="total-sum" />`
        $objResponse->attr('#total-sum', 'value', $double * 4);

		//Javascript alert
        $objResponse->alert('Sum was calculated!');

        //Let's remove all DIVs and the input box now
        $objResponse->remove('div')->remove('#total-sum');

        $objResponse->alert('Redirecting you..');

        //Let's redirect the user away
        $objResponse->redirect('http://github.com/');
    }

## Dependencies ##

JSON is used for passing messages around, so you'll need `json_encode()` on the server. Which means **PHP >= 5.2.0** is required.

JSON is also needed (for encoding messages) in the browser, so browsers having no native JSON support (like IE <= 7) need to load the additional JSON library (3kB).

Sijax will detect such browsers and load the library for them, provided you have pointed to it like so:

    Sijax::setJsonUri('{URI TO json2.js}');

The `json2.js` file is also hosted with this project, and can be found in the `src/js/` directory.

Browsers that do have native JSON support, won't need to load this additional resource.

## Known limitations ##

- Requires jQuery - since most projects probably already use jQuery, this may not be a problem
- Only supports utf-8
- Requires JSON - an additional 3kB library has to be loaded (automatically) for IE <= 7
- Does not handle magic quotes - if you have that enabled, you'll have to do your own $_POST processing
- Probably not as extensible and configurable as Xajax or other php ajax frameworks


## Comet streaming ##

Comet streaming is supportet via the comet plugin. `See examples/comet/` for more details. 

This is a very simple implementation (using a hidden iframe), and it works in all major browsers and that's probably all that's needed for simple streaming usage.

## Note on multiple requests ##

In php sessions are written to files. If you start a session with `session_start()` the script will block the session file until it returns or you explicit call `session_write_close()`.

So if you are using multiple sijax requests (jquery.ajax()) the first reqest will block the others until it returns, the second request will block the rest until it returns etc. The response will be as if it was sequential. This is a php limitation, not Sijax or jquery.

The use of php `session_` functions are important if you need multiple requests on the same page and if you are doing comet streaming.

## Examples ##

Example 1: Collection of simple Sijax requests using pure javascript and using the `Sijax::sijaxRequest` and `Sijax::sijaxFunction`

Example 2: Simple Sijax chat demonstrating how to fetch form values with `Sijax::getFormValues`

Example 3: Comet streaming with cancel, also demonstrates the importance of controlling the php session 

<p align="center"><a href="http://www.hostnet.nl" target="_blank">
    <img width="400" src="https://www.hostnet.nl/images/hostnet.svg">
</a></p>


The form handlers are designed to enhance the developer experience (DX) when
working with Symfony forms. It makes the controllers simpler by moving the form
success and failure flows to a separate class.

```php
class YourController extends Controller
{
    public function formAction(Request $request, MyEntityUser $user)
    {
        $handler = $this->get('hostnet.form_handler.factory')->create(MyFormHandler::class);

        if (($response = $handler->handle($request, new MyFormData())) instanceof RedirectResponse) {
            return $response;
        }

        // regular or in-valid flow
        return $this->render->renderView('/your/form.html.twig', [
            'form' => $handler->getForm()->createView()
        ]);
    }
}
```

By extracting the success - and if available, the failure - flows, you reduce
the amount of code in your controllers, which in turn, achieves slim
controllers. The definition of a controller is according to Symfony: _["a PHP 
function you create that reads information from the Symfony's Request object 
and creates and returns a Response object"](http://symfony.com/doc/current/controller.html)_. 

Installation
------------
 * Read the [installation guide](https://github.com/hostnet/form-handler-bundle/wiki/Installation)
   when using composer.
 * Get started with the [quick start](https://github.com/hostnet/form-handler-bundle/wiki/Quick-start).

Documentation
-------------
 * You can find the full documentation on our [github wiki pages](https://github.com/hostnet/form-handler-bundle/wiki).
 * If you are migrating from 1.1 to 1.2 or 2.x, check the [migration guide](https://github.com/hostnet/form-handler-bundle/wiki/Migration-towards-2.x).
 * The [legacy documentation for 1.1](https://github.com/hostnet/form-handler-bundle/wiki/Legacy-Readme)
   is still available but upgrading is recommended.

License
-------------
The `hostnet/form-handler-bundle` is licensed under the [MIT License](https://github.com/hostnet/form-handler-bundle/blob/master/LICENSE), meaning you can reuse the code within proprietary software provided that all copies of the licensed software include a copy of the MIT License terms and the copyright notice.

Get in touch
------------
 * Our primary contact channel is via IRC: [freenode.net#symfony](http://webchat.freenode.net/?channels=%23hostnet).
 * We are available on the [symfony-devs](https://slackinvite.me/to/symfony-devs)
   slack server in [#hostnet-form-handlers](https://symfony-devs.slack.com/messages/C3SJH42QP).
 * Or via our email: opensource@hostnet.nl.

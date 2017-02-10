form-handler-bundle
===================
> For the legacy handlers, see [LEGACY.md](LEGACY.md). For migration help, see [MIGRATION.md](MIGRATION.md).

The form handlers are designed to enhance the developer experience (DX) when working with Symfony forms. It makes the controllers simpler by moving the form success and failure flows to a separate class.

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

By extracting the success - and if available, the failure - flows, you reduce the amount of code in your controllers, which in turn, achieves slim controllers. The definition of a controller is according to Symfony: _["a PHP function you create that reads information from the Symfony's Request object and creates and returns a Response object"](http://symfony.com/doc/current/controller.html)_. 

# Installation

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require hostnet/form-handler-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle(),
            // ...
        ];
    }
}
```

# Preparations for 2.0

The current release is a preparation between the `1.x` and `2.x` versions. To ease the migration, you can already use the new structure as planned for `2.x`, while still using the old setup. Version `1.2` is the latest `1.x` feature release and we will not be patching bugs for the old `1.x` methods. Version `2.0` will have the same feature set as `1.2`, except for the legacy: `Hostnet\Component\Form\*`. Before you can use `2.0`, you will have to update your code as explaining in [the migration guide](MIGRATION.md).

If you which to read the readme for the legacy form handlers, you can find it in [LEGACY.md](LEGACY.md).

# Usage
> If you are familiar with the Symfony form component, these examples will seem very familiar. This is by design, the form handlers are designed to be symmetrical to the form types in the way they are used and configured.

In order to use the form handler, simply create a service that contains your form information. A simple example would be:
```php
<?php
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class MyFormHandler implements HandlerTypeInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /** {@inheritdoc} */
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(MyFormType::class);

        $config->onSuccess(function(MyFormData $data, FormInterface $form, Request $request) {
            // do something with the form data, like setting some data in the user
            $data->getUser()->setUsername($data->getUsername());
    
            // ...
            
            return new RedirectResponse($this->router->generate('my-route'));
        });
        // Also a failure branch can be added using:
        // $config->onFailure(/* ... */);
    }
}
```

>*Note*: Handlers are stateless. Which means that if you need to pass additional data (like the user) to the handler, you need to set this in the data object. For more information see [the definition of a Data Transfer Object (DTO)](https://en.wikipedia.org/wiki/Data_transfer_object).


Then create a service and tag it with form.handler
```yaml
my_form.handler:
    class: MyFormHandler
    arguments:
        - "@router"
    tags:
        - { name: form.handler }
```

And in your controller you can use the handler like:
```php
<?php
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MyController
{
    private $handler_factory;

    public function __construct(HandlerFactoryInterface $handler_factory)
    {
        $this->handler_factory = $handler_factory;
    }

    /**
     * @Route("/your-route", name="route-name")
     * @Template()
    */
    public function formAction(Request $request, MyEntityUser $user)
    {
        $handler = $this->handler_factory->create(MyFormHandler::class);
        $data    = new MyFormData();
        $data->setUser($user);
        
        if (($response = $handler->handle($request, $data)) instanceof RedirectResponse) {
            return $response;
        }
        
        // regular or in-valid flow
        return [
            'form' => $handler->getForm()->createView()
        ];
    }
}
```

The factory will retrieve the correct handler by the class name. This means that you do not have to inject the service yourself.

>*Note*: You cannot have multiple form handler services that use the same class, since the factory cannot find the correct service for it.

# Advanced features
It is also possible to use the `ActionSubscriberInterface` to subscribe to the success and failure flows instead of using callables. This uses the subscriber interface.

```php
<?php
use Hostnet\Component\FormHandler\ActionSubscriberInterface;
use Hostnet\Component\FormHandler\HandlerActions;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class MyFormHandler implements HandlerTypeInterface, ActionSubscriberInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /** {@inheritdoc} */
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(MyFormType::class);
        $config->registerActionSubscriber($this);
    }

    /** {@inheritdoc} */
    public function getSubscribedActions()
    {
        return [
            HandlerActions::SUCCESS => 'onSuccess',
            HandlerActions::FAILURE => 'onFailure',
        ];
    }

    public function onSuccess(MyFormData $data, FormInterface $form, Request $request)
    {
        // do something with the form data, like setting some data in the user
        $data->getUser()->setUsername($data->getUsername());

        // ...
        
        return new RedirectResponse($this->router->generate('my-route'));
    }

    public function onFailure(MyFormData $data, FormInterface $form, Request $request)
    {
        $request->getSession()->getFlashBag()->add('error', 'Something was wrong');
    }
}
```
This can help making your classes easier to unit test, but it provides no additional features.

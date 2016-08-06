form-handler-bundle
===================
The form handler bundle is designed give extra support on the [form handler component](https://github.com/hostnet/form-handler-component). This includes predefined services like the `SimpleFormProvider` and a handy `ParamConverter` to easily inject your form handler into a controller.

This bundle wraps itself around the `form-handler-component` which in turn, wraps itself around a form. It provides a small interface that you can use to handle the forms by sending through the `Request` and `FormHandlerInterface`. This interface requires you to implement a few methods. By adding 2 more optional interfaces; `FormFailureHandlerInterface` and `FormSuccessHandlerInterface`, you can move your success and failure branches away from the controller and into the form handler.

To reduce the amount of code needed there is also an `AbstractFormHandler` which provides most of the common code found in all form handlers. This implements the `FormHandlerInterface`, so you only need to implement the methods for your form.

Moving away your success and failure branches will cause the following:
 - Less dependencies in your controller
 - Re-usable code
 - Makes it easier to unit-test the results
 - A handler is "allowed" to have an entity manager as opposing to your service (because you don't want to randomly call flush, it's expensive).

In the examples provided below, you can see a controller and a handler that uses a parameter converter. If the pre-provided `SimpleFormProvider` isn't enough, you can always implement your own variant by using the `FormProviderInterface`, both found in the component (the service definition itself is in the bundle).

# Installation

## Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require hostnet/form-handler-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Step 2: Enable the Bundle

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
        $bundles = array(
            // ...

            new Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle()
        );

        // ...
    }

    // ...
}
```

# Usage

In order to use the form handler, simply create a service that contains your form information. A simple example would be:
```php
use Hostnet\Component\Form\AbstractFormHandler;
use Hostnet\Component\Form\FormFailureHandlerInterface;
use Hostnet\Component\Form\FormSuccesHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class MyFormHandler extends AbstractFormHandler implements FormSuccesHandlerInterface, FormFailureHandlerInterface
{
    private $data;
    private $router;
    private $user;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->data   = new MyFormData();
    }

    /** {@inheritdoc} */
    public function getType()
    {
        return MyFormType::class;
    }

    /** {@inheritdoc} */
    public function getData()
    {
        return $this->data;
    }
    
    public function setUser(MyEntityUser $user)
    {
        $this->user = $user;
    }

    /** {@inheritdoc} */
    public function onSuccess(Request $request)
    {
        // do something with the form data, like setting some data in the user
        $user->setUsername($this->data->getUsername());

        // ...
        return new RedirectResponse($this->router->generate("my-route"));
    }

    /** {@inheritdoc} */
    public function onFailure(Request $request)
    {
        // log the failed form post, or create a custom redirect.
    }
}
```

>*Note*: Implementing the `FormSuccesHandlerInterface` and `FormFailureHandlerInterface` is optional and in most cases you will not need the `FormFailureHandlerInterface` since you will want to render the page again but with the form errors.


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
class MyController
{
    private $form_provider;

    /**
     * @param SimpleFormProvider $form_provider form_handler.provider.simple 
     */
    public function __construct(SimpleFormProvider $form_provider)
    {
        $this->form_provider = $form_provider;
    }

    /**
     * @Route("/your-route", name="route-name")
     * @Template()
    */
    public function formAction(
        Request       $request,
        MyFormHandler $handler,
        MyEntityUser  $user
    ) {
        // you have 100% control over you handler, so you can
        // create setters that allow you to "inject" more things
        $handler->setUser($user);
        if (($response = $this->form_provider->handle($request, $handler)) instanceof RedirectResponse) {
            return $response;
        }
        // regular or in-valid flow
        return [
            'form' => $handler->getForm()->createView()
        ];
    }
}
```

The parameter converter will fetch the correct service with the class based on the tagged services.

>*Note*: You cannot have multiple form handler services that use the same class, since the parameter converter cannot find the correct service for it.

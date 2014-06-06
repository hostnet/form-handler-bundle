form-handler-bundle
===================
The form handler bundle is designed give extra support on the [form handler component](https://github.com/hostnet/form-handler-component). This includes predefined services like the ```SimpleFormProvider``` and a handy ParamCoverter to easily inject your form handler into a controller.

This bundle wraps itself around the form-handler-component which in turn, wraps itself around a form. It provides a small interface that you can use to handle the forms by sending through the ```Request``` and ```FormInformationInterface```. This interface requires you to implement a few methods. By adding 2 more optional interfaces; ```FormFailureHandlerInterface``` and ```FormSuccessHandlerInterface```, you can move your success and failure branches away.

Moving away your success and failure branches will cause the following:
 - Less dependencies in your controller
 - Re-usable code
 - Makes it easier to unit-test the results
 - A handler is "allowed" to have an entity manager as opposing to your service (because you don't want to randomly call flush, it's expensive).
 
In the examples provided below, you can see a controller and a handler that uses a parameter converter. If the pre-provided ```SimpleFormProvider``` isn't enough, you can always implement your own variant by using the ```FormProviderInterface```, both found in the component (the service definition itself is in the bundle).

# Installation

In your composer.json
```json
{
    "require" : {
        "hostnet/form-handler-bundle" : "master@dev"
    }
}
```
>*Note*: Recommended is to use the current stable tag.

Then add the bundle in your AppKernel:
```php
    $bundles = [
        // ...
        new Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle()
    ];
```

# Usage

In order to use the form handler, simply create a simple service that contains your form information. A simple example would be.

```php
use Hostnet\Component\Form\FormInformationInterface;
use Hostnet\Component\Form\FormSuccesHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class MyFormInformation implements FormInformationInterface, FormSuccesHandlerInterface
{
    private $data;
    private $user;
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->data   = new MyFormData();
        $this->router = $router;
    }

    public function setUser(MyEntityUser $user)
    {
        $this->user = $user;
    }

    public function getType()
    {
        return 'my_form'; // form.type alias, as tagged in your services.yml
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function getOptions()
    {
        return [];
    }
    
    public function getForm()
    {
        return $this->form;
    }
    
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    public function onSuccess()
    {
        // do something with the form data, like setting some data in the user
        $user->setUsername($this->data->getUsername());
        
        // ...
        return new RedirectResponse($this->router->generate("my-route"));
    }
}
```
Then create a service and tag it with form.handler
```yaml
my_form.handler:
    class: MyFormInformation
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
        Request $request, 
        MyFormInformation $handler,
        MyEntityUser $user
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

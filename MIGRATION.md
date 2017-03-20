Migration guide
===============
If you are planning on migrating to the new form handler structure, this guide will help to migrate a bit smoother. First of all, what has changed?

* A new namespace; `Hostnet\Component\FormHandler` which contains the new structure.
* Introduction of the `HandlerFactory` for creating handlers instead of `SimpleFormProvider` for dealing with handlers.
* Handlers are now stateless.
* Ability to typehint the form data properly.
* (Soft) dependency on the `SensioFrameworkExtraBundle` has been removed since you no longer need the parameter converter.

## Migration
### DTO
First of all, because the handlers are now stateless it means you can no longer set additional data. This means that you first have to refactor the handlers to use the data to pass additional fields.

So an an example the before case:
```php
<?php
// MyFormData.php
class MyFormData
{
    private $username;
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
}
```
```php
<?php
// MyFormHandler.php
class MyFormHandler extends AbstractFormHandler implements FormSuccessHandlerInterface
{
    private $data;
    private $user;

    public function __construct()
    {
        $this->data = new MyFormData();
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
        $this->user->setUsername($this->data->getUsername());

        // ...
    }
}
```
After
```php
<?php
// MyFormData.php
class MyFormData
{
    private $username;
    private $user;
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(MyEntityUser $user)
    {
        $this->user = $user;
    }
}
```
```php
<?php
// MyFormHandler.php
class MyFormHandler extends AbstractFormHandler implements FormSuccessHandlerInterface
{
    private $data;

    public function __construct()
    {
        $this->data = new MyFormData();
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

    /** {@inheritdoc} */
    public function onSuccess(Request $request)
    {
        // do something with the form data, like setting some data in the user
        $this->data->getUser()->setUsername($this->data->getUsername());

        // ...
    }
}
```
You can see that the setter is gone in the Handler and is moved to the data object.

### Controller BC layer
To aid in the migration process, the `HandlerFactory` can also deal with the "old" form handler classes. This means it is possible to first refactor your controller, before refactoring the handlers.

So using the orginal example, without changing the handler. The before case would look like:
```php
<?php
class MyController
{
    private $form_provider;

    public function __construct(SimpleFormProvider $form_provider)
    {
        $this->form_provider = $form_provider;
    }

    /**
     * @Route("/your-route", name="route-name")
     * @Template()
    */
    public function formAction(Request $request, MyEntityUser $user, MyFormHandler $handler)
    {
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
And after:
```php
<?php
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
You can see that the `SimpleFormProvider` is replaced with the `HandlerFactoryInterface`. Furthermore, the handler is created using the factory instead of injected as an action argument. Finally, the data object is created in the action since it is no longer stored in the handler.

Your controller is now in a format that is compatible with the new handler. All that is left is to refactor the handler. 

> Note: A deprecation notice is triggered in the Symfony toolbar when using "old" handlers in combination with the handler factory. This allows you to easily recognize when you are still using the old handler.

### Handler itself
The final step is to refactor the handler itself. This step is relatively easy if you use the `ActionSubscriberInterface` since it will be in almost the same format.

The before case:
```php
<?php
class MyFormHandler extends AbstractFormHandler implements FormSuccessHandlerInterface
{
    private $data;

    public function __construct()
    {
        $this->data = new MyFormData();
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

    /** {@inheritdoc} */
    public function onSuccess(Request $request)
    {
        // do something with the form data, like setting some data in the user
        $this->data->getUser()->setUsername($this->data->getUsername());

        // ...
    }
}
```
And after the refactor:
```php
<?php
class MyFormHandler implements HandlerTypeInterface, ActionSubscriberInterface
{
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(MyFormType::class);
        $config->registerActionSubscriber($this);
    }

    public function getSubscribedActions()
    {
        return [HandlerActions::SUCCESS => 'onSuccess'];
    }
    
    /** {@inheritdoc} */
    public function onSuccess(MyFormData $data, FormInterface $form, Request $request)
    {
        // do something with the form data, like setting some data in the user
        $data->getUser()->setUsername($data->getUsername());

        // ...
    }
}
```
The handler is now fully up-to-date with the new structure.

> Note: Using the `ActionSubscriberInterface` is not mandatory, you can also set an onSuccess in the `::configure()` method using a callable.

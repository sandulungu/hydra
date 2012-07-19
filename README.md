Hydra
=====

The cozy RESTfull PHP5.3 micro-framework.

* [Homepage](http://z7.github.com/hydra)
* [Application template](https://github.com/z7/hydra_app)

Requirements
------------

* Http server (Apache 2 with "AllowOverride All" recommended)
* PHP 5.3

Getting started
---------------

1. Download the [default application template](https://github.com/z7/hydra_app/zipball/master).
2. Extract it somewhere in your Apache web folder, for example in _hydra_app_. Make sure the application is able to create __data__ and __hydra__ subfolders.
3. Insert the following lines in __web/index.php__ just before ```$app->run();```

```php
$app->route('GET', 'hello/%name', function($name) {
  return "Hello, $name!";
});
```

Open http://localhost/hydra_app/hello/John in your browser to see execution result.

See our [wiki](https://github.com/z7/hydra/wiki) for more [usage samples](https://github.com/z7/hydra/wiki/Samples).


Features
--------

* __Strong security__: XSS, click-jacking, CSRF and code injection protection. Form and request data normalization.
* __Router__ with support for both, callback and __controller__ classes (using annotations).
* JSON and HTML __data dumping__ for super-quick web-service prototyping.
* __Form builder__ with guessers, allowing to directly edit any data with just one ```$app->form(array('data' => $data))``` call.
* __Service container__, including with the possibility to inject both services and factories (properties and methods) and extend/overwrite/unset them at run-time. No more problems with extending core classes.
* __Hooking system__ with intuitive execution flow control (weighted hooks) and auto-scanning of plugin hook files.
* Sample service implementations on the example of __MongoDB__ and __PDO__ classes.
* __Caching__ - to run even faster when in production.
* Simple __Requests/Response__ workflow with ability to stream content; hooks at every step of the flow with interruption abilities.
* __Session__, __Cookies__ and __Persistent Configuration__ - just set a property/value in these array-access enabled services and it will be there on subsequent request. Or you can use the IDE auto-completed object access syntax for known properties. Almost magic!
* [Twig](http://twig.sensiolabs.org/). Of course you can make your own PHP templates or even a template engine, but first, take a look at what Twig has to offer.
* [Monolog](https://github.com/Seldaek/monolog) __logging__ + FirePHP/ChromePHP support + Symfony's __Exceptions handler__ with debug/production presets = easy debugging.
* Extremely friendly with IDEs. All services and dynamic methods support auto-completion.
* Clean UI created using [Twitter Bootstrap](http://twitter.github.com/bootstrap/).
* Distributed in source code, as standalone __phar__ and the _z7/hydra_ __composer package__.


Why yet another framework?
--------------------------

I want to break some PHP stereotypes just like John Resig did with jQuery in the JavaScript world.

Hydra's concept was born during the prototyping of several REST applications. Initially I used Silex. At that moment I thought it was a very good micro-framework to quickly wire-up the RESTfull routes with some MongoDB and PDO code. Some time later it was obvious that, while the closures and route patterns were nice and cool, the service container wasn't that intuitive and powerful after all. No IDE auto-completion for services, strange syntax, bloated functionality providers, it didn't just work out-of-the-box like I wanted it to. Another thing, I noticed was the slowdowns determined by the missing of a caching routine and heavy components architecture, while I only needed basic IO, mostly already provided natively by PHP. Of course, there were nice concepts you'll find in Hydra as well...

My conclusion was that most of the frameworks, even tiny ones give too much attention to classical OOP patterns, while not taking advantage of some of the best things PHP has to offer. Some are over-bloated by reinventing better implementations of standard features that are already available in PHP. While there are some exceptions, most of them give very small added value while slowing things down. In the end it seemed less time consuming to just take the good components (like theming, logging, error handling) from the best players out there and rethink only the core to be thinner and more flexible.

Ingredients used:
* [Symfony Components](http://symfony.com/components) (Error/Exception Handlers, MimeType guessers)
* [Twig](http://twig.sensiolabs.org/)
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* [Monolog](https://github.com/Seldaek/monolog)


Why you called it "Hydra"?
--------------------------

Because of the vision. A hydra (mythological creature) can grow heads dynamically. 

We tried to apply this ad-hock growing to OOP. Until version 5.3 this was harder to achieve and before 5.0 impossible at all. Now, that we've got magic methods, closures, SPL and the classical interpreter features we had the opportunity to combine them all into a very flexible, yet simple dependency injection system. It's the cool run-time objects morphing brought to a new level.


Contributing
-------------

Hydra is right now in an early stage of development, so your feedback is VERY, VERY important.
Share, discuss, post issues and feature requests, write review. The best has yet to come!

If you want to contribute source code, please [leave a message in the tracker](https://github.com/z7/hydra/issues/new) or write directly to sandu@lungu.info.

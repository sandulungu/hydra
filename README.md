Hydra
=====

The cozy RESTfull PHP5.3 micro-framework.


Why yet another framework?
--------------------------

Hydra's concept was born during the prototyping of not-very-big REST application.

Initialy I used Silex. At that moment I thought it was the best micro-framework for me to quickly wire-up the RESTfull routes with some MongoDB and PDO code. Some time later it was obvious that, while the closures and route patterns were nice and cool, the service container wasn't that good after all. No IDE autocompletion for services, strange syntax, un-intuitive functionality providers, that just didn't work out-of-the-box. Another thing, I noticed was the slow router and heavy request/response architecture, while I only needed basic IO, mostly already provided natively by PHP. Of course, there were nice things, but that's not the point...

The point is that the very most of the frameworks, even tiny ones are just overbloated with reinventing better implementations of standard features that are already available in PHP. While there are some exceptions, most of them give very small added value and they slow everything down. Even worse, many of them are ported form other technologies or have fetish-OOP features that just don't fit well in the PHP world.


What is it like?
----------------

It's a little bit of Symfony, a little of Pimple, some Teiw, with a breeze of Bootstrap.

Compact, speedy, easy to learn and, most importantly, productive. Hope these words will be associated with Hydra soon. And maybe one day Hydra will be seen as PHP's jQuery lib.


What's the deal?
----------------

A micro-framework with everything you'll usualy want in just 300kB (including vendor libs).

What it has out of box:

* __Service container__, inluding with the possibility to inject methods and extend them at runtime. No more problems with extending core classes.
* __Hooking system__ with intuitive execution flow control (weighted hooks) and auto-scan of plugin hook files.
* __MongoDB__ and __PDO__ support - I like these, so they've got from me a total of 40 lines of code and ease of usage through the service container :)
* __Caching__ - to run even faster when in prod.
* __RESTfull router__ with support for both, callback and __controller__ classes.
* __Parameter normalization__ for safety and easy, multi-format __data dumping__ for super-quick web-service driven application prototyping.
* Simple __Requests/Response__ workflow with ability to stream content; hooks at every step with flow control and intreruption abilities.
* __Session__, __Cookies__ and __Persistent Configuration__ - just set a property/value in these array-access enabled services and it will be there on subsequent request. Magic!
* __[Twig](http://twig.sensiolabs.org/) template engine__ because native PHP templates, well, suck and Fabien's solution rulezzz.
* __[Monolog](https://github.com/Seldaek/monolog)__ + FirePHP/ChromePHP support and Symfony's __Exceptions handler__ with debug/production presets - for a nice time spending while debugging.
* Extremely friendly with IDE. All services and dynamic methods support autocompletion.
* Distribution as standalone __phar__ or as a __Composer package__.
* Clean UI created with [Twitter Bootstrap](http://twitter.github.com/bootstrap/).

All the features above are provided only as default solutions and may be easily reconfigured/extended/overwritten without even creating a new class. Just use services, methods injecting and hooks.


Why you called it "Hydra"?
--------------------------

Because of the vision. A hydra (mitological creature) can grow heads dynamically. 

We tried to apply this ad-hoc grouwing to OOP. Until version 5.3 this was harder to achieve and before 5.0 imposible at all. Now, that we've got magic methods, closures, SPL and the classical interpretor features we had the opportunity to combine them all into a very flexible, yet simple dependency injection system. It's some kind of multiple inheritance and runtime morphing of objects.


Want more?
----------

Hydra is right now in an early stage of development, so your feedback is VERY, VERY important.
Share, discuss, post issues and feature requests, write review. The best for PHP has yet to come and .
My email is __sandu(a)lungu.info__
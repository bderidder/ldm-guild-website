Assetic
=======

Assetic is a library dedicated to serving static resources as CSS files, Javascript or images.

In this guide we will explain some basic aspects of Assetic and the choices that were made in this particular project.

Introduction
------------

### Development versus Production Mode

Before we can explain the difference between running Assetic in development or in production mode we first have a look at a typical example of how Assetic is used in an HTML page.

Somewhere in the `<head>` tag you will typically find a code snippet like the one below:

~~~~
{% stylesheets filter='cssrewrite'
    'bundles/ladansebootstrap/less/themes/default/custom-bootstrap.less'
    'bundles/ladansesite/css/*'
    'bundles/ladansesite/less/ladanse.less'
%}
<link href="{{ asset_url }}" type="text/css" rel="stylesheet" media="screen" />
{% endstylesheets %}
~~~~

Note the `filter='cssrewrite'` which instructs Assetic to run the CSS Rewrite filter on all the resources mentioned in the list. You can also see how wildcards are allowed. Two of the resources point to a LESS file which will be translated to CSS by Assetic on the fly. More information on Assetic can be found [here](https://symfony.com/doc/current/assetic/asset_management.html).

When you run the above in development mode this will result in the following HTML:

~~~~
<link href="/css/637a9f8_custom-bootstrap_1.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_base_1.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_calendar_2.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_claims_3.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_faq_4.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_layout_5.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_menu-new_6.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_menu_7.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_static_8.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_part_2_viewEvent_9.css" type="text/css" rel="stylesheet" media="screen" />
<link href="/css/637a9f8_ladanse_3.css" type="text/css" rel="stylesheet" media="screen" />
~~~~

The use of the wildcard resource (`bundles/ladansesite/css/*`) results in several files being linked in the HTML. The LESS files also have been replaced by CSS files.

If you run the same code in a production environment you will see the following HTML:

~~~~
<link href="/css/637a9f8.css" type="text/css" rel="stylesheet" media="screen" />
~~~~

In production mode Assetic will concatenate all the CSS into one file and serve that. The contents of the CSS are identical between development mode and production model. It's only the way they are served to the browser that is optimized for production.

In development mode Assetic will also always apply the filters and compile the LESS into CSS on each request. This way any change you made in the code is immediately visible in the browser after a reload.

In production model Assetic wants you to pre-generate all the CSS and Javascript that has to be served to the browser. That is why you had to run the `assetic:dump` command. It will basically go through all the HTML templates and apply all filters and other required processing to the CSS and Javascript files and caches them on disk.

### Larger Volume of CSS and Javascript

Running Assetic in development mode is great if you have a small to moderate amount of CSS and Javascript. The overhead of applying the filters to each request is negligible.

If on the other hand you have a larger volume of CSS and Javascript running all those filters on each request can easily introduce a noticeable latency of 200 to 500 (or more) milliseconds.

### `assetic:dump` and `assetic:watch`

To avoid the latency introduced by a large volume of CSS and Javascript we can force Assetic to enable some of the production features even when in development model. This is done by setting the property `use_controller` to false in one of the configuration files.

Since the LDM Guild Website depends on libraries as bootstrap, font-awesome, jQuery, moment.js, AngularJS ... we have chosen to run Assetic partially in production mode during development by setting the above mentioned property to false.

As a result we always have to run `assetic:dump` after we made any change to any CSS, Javascript or `<link>` tags in our HTML. So by avoiding the additional latency of running all filters on all requests we now have to run an additional command in our build-test cycle. That is no real gain in productivity.

Luckily Assetic is smarter and also offers the `assetic:watch` command. 

If you run the following command, Assetic will constantly watch if any relevant file has changes and if it has it will dump the updated assets immediately.

~~~~
php bin/console assetic:dump --env=dev --no-debug
~~~~

Note that this command does not return immediately as it has to watch for changes. To stop if you have to type `CTRL-C` in the terminal.

When we start a development session we can now simply run the above command in a separate terminal and we don't have to care anymore about dumping the assets manually.

Elsewhere in this guide we shall see how you can automate this in PHPStorm.
NeoInvoice by Thomas Hunter
===

I started building NeoInvoice in 2010. It was hosted at www.neoinvoice.com. The plan
was to maybe one day make money from it, but I never really advertised or monetized
the app. Two years later, after never making a dime and paying about over $1,000 in
hosting costs, I finally realized it was costing more money than it was making me,
so I shut down the app and open sourced it.

What is NeoInvoice?
=

NeoInvoice is a set of software tools useful to most online / service businsses. It is
a single app, meant to be used by many different companies, each company having many
different users, all having their own login credentials.

Screenshots and Video
=

Visit this page for a video of me showing off all of the features, and some technical
explanation, as well as a bunch of high rez screenshots:
http://thomashunter.name/blog/neoinvoice-post-mortem/

Features
=

* Time Tracking
* Issue Tickets
* Email PDF Invoices
* Many Users
* Configurable User Roles
* OS X Dashboard Widget for Entering Time
* etc.

Technology Stack
=

NeoInvoice is a PHP, MySQL, Memcache/APC app. It runs on a Linux server, and has been run
using both lighttpd and apache.

Installation
=

* Set the root of the website to public/
* chmod 777 public/system/logs/ public/assets/logos/
* Update public/application/config/paypal_config.php with your paypal email address
* Update public/application/controllers/payment.php with your paypal email address
* Update public/config.php with your MySQL connection settings and domain names
* Update public/config.php with an email address for getting SQL dumps
* If using lighttpd, copy over the resources/lighttpd.conf file
* Set a salt for public/application/libraries/Rm_user.php
* Set a salt for public/application/models/company_model.php
* Setup CRON tasks for resources/cron.(daily|weekly).task.txt
* Import resources/schema.sql into your MySQL database
* Replace all instances of neoinvoice.com with your domain
* Either install Wordpress into public/docs/, or remove references to the Wordpress Model

Administration
=

I had a directory setup which was password protected, and contained the following scripts:

* APC Control Panel
* eAccelerator Control Panel
* Memcache Control Panel
* SQL Buddy
* phpinfo script

These five scripts were all I needed to administrate the server. There shouldn't be much
upkeep required, however. It takes care of itself pretty well.

What Sucks?
=

A lot sucks. I'll start making some tickets for all of the things I'm aware of which
is broken (if there is interest for anyone to fix the issues). I probably won't be
doing much development myself, but I don't mind facilitating a bunch of pull requests.

Git History
=

I was keeping track of the entire history of the project, and had near 400 commits,
but while doing some git history changing operations to remove sensitive data, bad
things started to happen. So, I flattened the repo and started from scratch. Most of
these files are 1 to 2 years old.

License
=

This application is released under a dual GPL/BSD license. You can use whichever one
you would prefer. This app makes use of several open source technologies, and they
all have their own licenses, and are of course not bound to the overall NeoInvoice
license. These projects usually have a LICENSE and README file in their directory.

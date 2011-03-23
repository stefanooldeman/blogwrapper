### This is a ZF app.

The goal is to have a sandbox where _I_ can experiment with ZF and implementing external services (APIs)

The Goal is to support many social media channels as also blogging services as: `Posterous`, `Thumblr`, and so on.
Actually this looks pretty much like a *blog aggregator application*.
The interesting part about supporting multiple services (mostly blog tools) is to build a general wrapper posts / articles so switching from a service should be easy.

#### add your credentials to the ./app/config.ini

all supported services are below this line

Posterous:

*   your 'personal' API Toke (generate one [here](http://posterous.com/manage/token "manage token")
*   your username (type:email)
*   and password


To be added:

1.	[Thumblr](http://www.tumblr.com/docs/en/api "Thumbl Api")
2.	_textile /markdown file bases_ (github)
3.	[Blogger](http://code.google.com/apis/blogger/docs/1.0/developers_guide_php.html "Google Blogger Api")


Dependencies:

*   Zend-Framework (1.x)
*   Dwoo. a PHP5 smarty based / like template engine

for these run

    git submodule init
    git submodule update



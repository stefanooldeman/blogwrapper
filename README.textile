h1. Ketchup!

kethcup has the goal to support many social media chanels as also blogging services as: posterous, thumblr, and so on.
apart from that I might implement the file based textile system.

but first and next on the roadmap is implementing a markdown interperter.

please note: This app is in `very` early development.

important. to get everything up and running!
*update:* ive added all the libraries, thus zend-framework. later on i might figure out about :submodules

h2. Posterous
Hey were using the full rest beta api 2.0 :) 
to get some results from your posterous account "see the api 2.0 docs":http://apibeta.posterous.com/

And add it some values to the ./app/config.ini
* your 'personal' API Token "generate one here":http://posterous.com/manage/token
* your username (type:email) 
* and password

Dependencies:
* Zend-Framework (1.10.8)
* Dwoo. a PHP5 smarty based / like template engine


thats it so far.
urlClean
----------

urlClean is used as an output modifier on urls to force them to validate, also useable on document ID's to create links to them.
accepts option value equal to the makeURL scheme parameter when used on document ID's instead of full urls

eg:
url: youtube.com/test?q=this and that -> //youtube.com/test?this%20and%20that
url: /test relative?var=with, params.&that -> /test%20relative?var=with%2C%20params%2E&amp;that
url: 2 -> /alias-to-your-page
[[+id:urlClean=`full`]] -> http://your-site.com/alias-to-document

AUTHOR: Jason Carney, DashMedia.com.au
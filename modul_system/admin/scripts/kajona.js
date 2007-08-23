//   (c) 2004-2006 by MulchProductions, www.mulchprod.de
//   (c) 2007 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
//       $Id$

//--- GLOBAL ELEMENTS, MOVER ----------------------------------------------------------------------------
var currentMouseXPos;
var currentMouseYPos;
//used for "the mover" ;)
var objToMove=null;
var objDiffX=0;
var objDiffY=0;

checkMousePosition = function (e) {
	if(document.all){
		currentMouseXPos = event.clientX + document.body.scrollLeft;
		currentMouseYPos = event.clientY + document.body.scrollTop;
    }
	else{
       currentMouseXPos = e.pageX;
	   currentMouseYPos = e.pageY;
    }

    if(objToMove != null) {
	    objToMove.style.left=currentMouseXPos-objDiffX+"px";
  		objToMove.style.top=currentMouseYPos-objDiffY+"px";
	}
}

var objMover = {
	mousePressed : 0,
	objPosX:0,
	objPosY:0,
	diffX:0,
	diffY:0,

	setMousePressed : function (obj){
	    objToMove=obj;
	    objDiffX=currentMouseXPos - objToMove.offsetLeft;
		objDiffY=currentMouseYPos - objToMove.offsetTop;
	},

	unsetMousePressed : function (){
        objToMove=null;
	}
}

//--- MISC ----------------------------------------------------------------------------------------------
function fold(id) {
	style = document.getElementById(id).style.display;
	if (style=='none') 	{
		document.getElementById(id).style.display='block';
    }
    else {
        document.getElementById(id).style.display='none';
    }
}

function foldImage(id, bildid, bild_da, bild_weg) {
	style = document.getElementById(id).style.display;
	if (style=='none') {
		document.getElementById(id).style.display='block';
        document.getElementById(bildid).src = bild_da;
    }
    else {
        document.getElementById(id).style.display='none';
        document.getElementById(bildid).src = bild_weg;
    }
}

function switchLanguage(strLanguageToLoad) {
    url = window.location.href;
    url = url.replace(/(\?|&)language=([a-z]+)/, "");
    if(url.indexOf('?') == -1)
        window.location.replace(url+'?language='+strLanguageToLoad);
    else
        window.location.replace(url+'&language='+strLanguageToLoad);
}

function addCss(file){
	var l=document.createElement("link");
	l.setAttribute("type", "text/css");
	l.setAttribute("rel", "stylesheet");
	l.setAttribute("href", file);
	document.getElementsByTagName("head").item(0).appendChild(l);
}

function addDownloadInput(idOfPrototype, nameOfCounterId) {
    //load inner html of prototype
    var uploadForm = document.getElementById(idOfPrototype).innerHTML;

    //calc new counter
    var counter = 0;
    while(document.getElementById(nameOfCounterId+'['+counter+']') != null)
        counter++;

    //set new id
    uploadForm = uploadForm.replace(new RegExp(nameOfCounterId+'\\[0\\]', "g"), nameOfCounterId+'['+counter+']');

    //and place in document
    var newNode = document.createElement("div");
    newNode.setAttribute("style", "display: inline;");
    newNode.innerHTML = uploadForm;

    var protoNode = document.getElementById(idOfPrototype)
    protoNode.parentNode.insertBefore(newNode, protoNode);

}

function inArray(needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] == needle) {
            return true;
        }
    }
    return false;
}

function addLoadEvent(func) {
	var oldonload = window.onload;
    if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if(oldonload) {
				oldonload();
			}
			func();
		};
	}
}

//--- RIGHTS-STUFF --------------------------------------------------------------------------------------
function checkRightMatrix() {
	//mode 1: inheritance
	if(document.getElementById('inherit').checked) {
		//loop over all checkboxes to disable them
		for(intI=0; intI<document.forms['rightsForm'].elements.length; intI++) {
			var objCurElement = document.forms['rightsForm'].elements[intI];
			if(objCurElement.type == 'checkbox') {
				if(objCurElement.id != 'inherit') {
					objCurElement.disabled = true;
					objCurElement.checked = false;
					strCurId = "inherit,"+objCurElement.id;
					if(document.getElementById(strCurId) != null) {
						if(document.getElementById(strCurId).value == '1') {
							objCurElement.checked = true;
						}
					}
				}
			}
		}
	}
	else {
		//mode 2: no inheritance, make all checkboxes editable
		for(intI=0; intI<document.forms['rightsForm'].elements.length; intI++) {
			var objCurElement = document.forms['rightsForm'].elements[intI];
			if(objCurElement.type == 'checkbox') {
				if(objCurElement.id != 'inherit')
					objCurElement.disabled = false;
			}
		}
	}
}

var kajonaAjaxHelper =  {
	
	arrayFilesToLoad : new Array(),
	arrayFilesLoaded : new Array(),
	bitPastOnload : false,
	
	onLoadHandlerFinal : function() {
		for(i=0;i<kajonaAjaxHelper.arrayFilesToLoad.length;i++) {
			if(kajonaAjaxHelper.arrayFilesToLoad[i] != null)
				kajonaAjaxHelper.addJavascriptFile(kajonaAjaxHelper.arrayFilesToLoad[i]);
		}
		kajonaAjaxHelper.bitPastOnload = true;
	},

	addJavascriptFile : function (file) {
		var l=document.createElement("script");
		l.setAttribute("type", "text/javascript");
		l.setAttribute("language", "javascript");
		l.setAttribute("src", file);
		document.getElementsByTagName("head").item(0).appendChild(l);	
		intCount = kajonaAjaxHelper.arrayFilesLoaded.length;
		kajonaAjaxHelper.arrayFilesLoaded[(intCount+1)] = file;
	},
	
	loadAjaxBase : function () {
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/utilities/utilities.js');
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/yahoo/yahoo.js');
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/event/event.js');
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/connection/connection.js');
	},
	
	
	loadDragNDropBase : function () {
		kajonaAjaxHelper.loadAjaxBase();
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/dom/dom.js');
		kajonaAjaxHelper.addFileToLoad('admin/scripts/yui/dragdrop/dragdrop.js');
		kajonaAjaxHelper.bitDndBaseLoaded = true;
	},
	
	addFileToLoad : function(fileName) {
		if(kajonaAjaxHelper.bitPastOnload) {
			if(!inArray(fileName, kajonaAjaxHelper.arrayFilesLoaded)) {
				kajonaAjaxHelper.addJavascriptFile(fileName);
			}
		}
		else {
			intCount = kajonaAjaxHelper.arrayFilesToLoad.length;
			kajonaAjaxHelper.arrayFilesToLoad[(intCount+1)] = fileName;
		}
	}
};

addLoadEvent(kajonaAjaxHelper.onLoadHandlerFinal);

var regularCallback = {
  success: function(o) { alert('save succeeded, response: '+o.responseText) },
  failure: function(o) { alert('save failed') }
};

var kajonaAdminAjax = {
	
	connectionObject : null,
	
	setAbsolutePosition : function (systemIdToMove, intNewPos, strIdOfList) {
		//load ajax libs
		kajonaAjaxHelper.loadAjaxBase();
		
		var postTarget = 'xml.php?admin=1&module=system&action=setAbsolutePosition';
		
		//concat to send all values
		var postBody = 'systemid='+systemIdToMove+'&listPos='+intNewPos;
		
		if(kajonaAdminAjax.connectionObject == null || !YAHOO.util.Connect.isCallInProgress(kajonaAdminAjax.connectionObject)) {
			kajonaAdminAjax.connectionObject = YAHOO.util.Connect.asyncRequest('POST', postTarget, regularCallback, postBody);
		}
	}
	
};


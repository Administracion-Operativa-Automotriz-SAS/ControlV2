//
// cross-browser schna
//
function setClip(layer, l, r, t, b) {
	if (isDom) {
	layer.style.width = r-l;
	layer.style.height = b-t;
	layer.style.clip = "rect("+t+","+r+","+b+","+l+")";  
	}
	else if(isNav4) {
	layer.clip.left = l; layer.clip.right = r;
	layer.clip.top = t;  layer.clip.bottom = b;
	} 
	else if (isIE4) {
	layer.style.pixelWidth = r-l;
	layer.style.pixelHeight = b-t;
	layer.style.clip = "rect("+t+","+r+","+b+","+l+")";
	}
}
function setClipHeight(layer, h) {
	if (isDom) {
	layer.style.height = h;
	}
	else if(isNav4) {
	layer.clip.height = h;
	} 
	else if (isIE4) {
	layer.style.pixelHeight = h;
	}
}
function setClipWidth(layer, w) {
	if (isDom) {
	layer.style.width = w;
	setClip(layer, 0, parseInt(layer.style.width), 0, layer.offsetHeight);
	}
	else if(isNav4) {
	layer.clip.width = w;
	} 
	else if (isIE4) {
	layer.style.pixelWidth = w;
	setClip(layer, 0, layer.style.pixelWidth, 0, layer.style.pixelHeight);
	}
}
function setLeft(layer, l) {
	if (isDom) {
	layer.style.left = l;
	}
	else if(isNav4) {
	layer.left = l;
	} 
	else if (isIE4) {
	layer.style.pixelLeft = l;
	}
}
function setTop(layer, t) {
	if (isDom) {
	layer.style.top = t;
	}
	else if(isNav4) {
	layer.top = t;
	} 
	else if (isIE4) {
	layer.style.pixelTop = t;
	}
}
function setVisibility(layer, v) {
	if (isDom) {
	layer.style.visibility = v;
	}
	else if(isNav4) {
	layer.visibility = v;
	} 
	else if (isIE4) {
	layer.style.visibility = v;
	}
}
function setZIndex(layer, z) {
	if (isDom) {
	layer.style.zIndex = z;
	}
	else if(isNav4) {
	layer.zIndex = z;
	} 
	else if (isIE4) {
	layer.style.zIndex = z;
	}
}
function getLeft(layer) {
	if (isDom) {
	return layer.offsetLeft;
	}
	else if(isNav4) {
	return layer.left;
	} 
	else if (isIE4) {
	return layer.style.pixelLeft;
	}
}
function getTop(layer) {
	if (isDom) {
	return layer.offsetTop;
	}
	else if(isNav4) {
	return layer.top;
	} 
	else if (isIE4) {
	return layer.style.pixelTop;
	}
}
function setLayerBgcolor(layer, b) {
	if (isDom) {
	layer.style.backgroundColor = b;	
	}
	else if(isNav4) {
	layer.bgColor = b;
	} 
	else if (isIE4) {
	layer.style.backgroundColor = b;
	}
}
function getHSV() {
  var ar = new Array(3);
  ar.h = 360-getLeft(eval(layerRef + openparen + "thumbH" + closeparen))+14;
  ar.s = (getTop(eval(layerRef + openparen + "thumbS" + closeparen))-65)/150;
  ar.v = (getTop(eval(layerRef + openparen + "thumbV" + closeparen))-65)/150;
  if(getTop(eval(layerRef + openparen + "thumbS" + closeparen)) == 214) ar.s = 1;
  if(getTop(eval(layerRef + openparen + "thumbV" + closeparen)) == 214) ar.v = 1;
  return ar;
}
function getHLS() {
  var ar = new Array(3);
  ar.h = 360-getLeft(eval(layerRef + openparen + "thumbH" + closeparen))+14;
  ar.l = (getTop(eval(layerRef + openparen + "thumbL" + closeparen))-65)/150;
  ar.s = (getTop(eval(layerRef + openparen + "thumbS2" + closeparen))-65)/150;
  if(getTop(eval(layerRef + openparen + "thumbL" + closeparen)) == 214) ar.l = 1;
  if(getTop(eval(layerRef + openparen + "thumbS2" + closeparen)) == 214) ar.s = 1;
  return ar;
}
function getRGB(initHex) {
	if (initHex && initHex != ""){
	change('HEX','HEX',initHex);
	}
	else {
	var ar = new Array(3);
	ar.r = Math.round((getTop(eval(layerRef + openparen + "thumbR" + closeparen))-35)/180*255);
	if(getTop(eval(layerRef + openparen + "thumbR" + closeparen)) == 214) ar.r = 255;
	ar.g = Math.round((getTop(eval(layerRef + openparen + "thumbG" + closeparen))-35)/180*255);
	if(getTop(eval(layerRef + openparen + "thumbG" + closeparen)) == 214) ar.g = 255;
	ar.b = Math.round((getTop(eval(layerRef + openparen + "thumbB" + closeparen))-35)/180*255);
	if(getTop(eval(layerRef + openparen + "thumbB" + closeparen)) == 214) ar.b = 255;
	return ar;
	}
}
function setHLS(h,l,s,format) {
  if(format != 0 && h != -1) setLeft(eval(layerRef + openparen + "thumbH" + closeparen), (360-h)+14);
  if(l != -1) setTop(eval(layerRef + openparen + "thumbL" + closeparen), Math.round(l*150)+65);
  if(s != -1) setTop(eval(layerRef + openparen + "thumbS2" + closeparen), Math.round(s*150)+65);
}
function setHSV(h,s,v,format) {
  if(format != 1 && h != -1) setLeft(eval(layerRef + openparen + "thumbH" + closeparen), (360-h)+14);
  if(s != -1) setTop(eval(layerRef + openparen + "thumbS" + closeparen), Math.round(s*150)+65);
  if(v != -1) setTop(eval(layerRef + openparen + "thumbV" + closeparen), Math.round(v*150)+65);
}
function setRGB(r,g,b) {
  if(r != -1) setTop(eval(layerRef + openparen + "thumbR" + closeparen), Math.round(r/255*180)+35);
  if(g != -1) setTop(eval(layerRef + openparen + "thumbG" + closeparen), Math.round(g/255*180)+35);
  if(b != -1) setTop(eval(layerRef + openparen + "thumbB" + closeparen), Math.round(b/255*180)+35);
}

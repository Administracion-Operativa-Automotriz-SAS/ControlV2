///////////////////////////////////////////////////////////////////////
// Helper functions used by this library

function _contains(outerlayer, innerlayer) {
	if (isDom) {
	if(innerlayer.offsetLeft < outerlayer.offsetLeft)
	return false;
	if(innerlayer.offsetTop < outerlayer.offsetTop)
	return false;
	if(innerlayer.offsetLeft + innerlayer.offsetWidth > outerlayer.offsetLeft + outerlayer.offsetWidth)
	return false;
	if(innerlayer.offsetTop + innerlayer.offsetHeight > outerlayer.offsetTop + outerlayer.offsetHeight)
	return false;
	return true;
	}
	else if(isNav4) {
	if(innerlayer.left < outerlayer.left) return false;
	if(innerlayer.top < outerlayer.top) return false;
	if(innerlayer.left + innerlayer.clip.width > outerlayer.left + outerlayer.clip.width) return false;
	if(innerlayer.top + innerlayer.clip.height > outerlayer.top + outerlayer.clip.height) return false;
	return true;
	} 
	else if (isIE4) {
	if(innerlayer.style.pixelLeft < outerlayer.style.pixelLeft)
	return false;
	if(innerlayer.style.pixelTop < outerlayer.style.pixelTop)
	return false;
	if(innerlayer.style.pixelLeft + innerlayer.style.pixelWidth > outerlayer.style.pixelLeft + outerlayer.style.pixelWidth)
	return false;
	if(innerlayer.style.pixelTop + innerlayer.style.pixelHeight > outerlayer.style.pixelTop + outerlayer.style.pixelHeight)
	return false;
	return true;
	}
}

///////////////////////////////////////////////////////////////////////
// The Behavior Object

function Behavior(drag) {
  this.mouseDownAction = null;
  this.mouseUpAction = null;
  this.mouseMoveAction = null;
  this.mouseOverAction = null;
  this.mouseOutAction = null;
  this.draggable = drag;
  this.setAction = setAction;
  this.applyBehavior = applyBehavior;
  this.hLock = false;
  this.vLock = false;
  this.useBounds = false;
  this.setBounds = setBounds;
  this.removeBounds = removeBounds;
  this.bounds = false;
  this.containers = false;
  this.addContainer = addContainer;
  this.update = _updateAll;
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: update

function _updateAll() {
	if (isDom) {
		for(var i = 0; i < document.getElementById.length; i++) {
			if(document.getElementById(i).behavior && document.getElementById(i).behavior == this){
			this.applyBehavior(document.getElementById(i));
			}
		}	
	}
	if(isNav4) {
		for(var i = 0; i < document.layers.length; i++) {
			if(document.layers[i].document.layers){
			this.update(document.layers[i].document);
			}
			if(document.layers[i].behavior && document.layers[i].behavior == this){
			this.applyBehavior(document.layers[i]);
			}
		}
	} 
	else if(isIE4) {
		for(var i = 0; i < document.all.length; i++) {
			if(document.all[i].behavior && document.all[i].behavior == this){
			this.applyBehavior(document.all[i]);
			}
		}
	}
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: containers

function addContainer(layer) {
  if(!this.containers) {
  this.containers = new Array();
  }
  this.containers[this.containers.length] = layer;
}

function removeContainer(layer) {
  for(var i = 0; i < this.containers.length; i++){
    if(this.containers[i] == layer){
      this.containers[i] = null;
    }
  }
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: setting bounds for dragging

function setBounds(l, r, t, b) {
  this.useBounds = true;
  if(this.bounds == false) this.bounds = new Array(4);
  this.bounds[0] = l;
  this.bounds[1] = r;
  this.bounds[2] = t;
  if (isDom){
 	if (isIE){
	this.bounds[3] = b + 8; 	
 	}
 	else {
	this.bounds[3] = b;
 	}
  }
  else if (isNav4 || isIE4){
  this.bounds[3] = b;
  }
}

function removeBounds() {
  this.useBounds = false;
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: setting action-event pairs

function setAction(action, func) {
  eval('switch(action) {'+
    'case "MOUSEDOWN": this.mouseDownAction = func; break;'+
    'case "MOUSEMOVE": this.mouseMoveAction = func; break;'+
    'case "MOUSEUP":   this.mouseUpAction = func;   break;'+
    'case "MOUSEOVER": this.mouseOverAction = func; break;'+
    'case "MOUSEOUT":  this.mouseOutAction = func;  break;'+
    'case "CONTAINERPUSH": this.containerPushAction = func; break;'+
    'case "CONTAINERPULL": this.containerPullAction = func; break;'+
  '}');
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: cross-browser helpers

function isShift(e) {
	if(window.event){
	return window.event.shiftKey;
	}
	else {
	return (e.modifiers & Event.SHIFT_MASK);
	}
}

function isAlt(e) {
	if(window.event){
	return window.event.altKey;
	}
	else {
	return (e.modifiers & Event.ALT_MASK);
	}
}

function isControl(e) {
	if(window.event){
	return window.event.ctrlKey;
	}
	else {
	return (e.modifiers & Event.CONTROL_MASK);
	}
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: applyBehavior

function applyBehavior(layer) {
  layer.behavior = this;
  layer.draggable = this.draggable;
  if(layer.captureEvents) {
    layer.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP|Event.MOUSEOVER|Event.MOUSEOUT);
    document.captureEvents(Event.MOUSEMOVE);
  }
  document.onmouseup = _clearDBJ;
  layer.onmousedown = _handleMouseDown;
  layer.onmouseup = _handleMouseUp;
  document.onmousemove = _handleMouseMove;
  layer.onmouseover = _handleMouseOver;
  layer.onmouseout = _handleMouseOut;
  layer.containers = this.containers;
  layer.containerPushAction = this.containerPushAction;
  layer.containerPullAction = this.containerPullAction;
  layer.vLock = this.vLock;
  layer.hLock = this.hLock;
  layer.bounds = new Array(4);
  layer.bounds[0] = this.bounds[0];
  layer.bounds[1] = this.bounds[1];
  layer.bounds[2] = this.bounds[2];
  layer.bounds[3] = this.bounds[3];
  layer.useBounds = this.useBounds;
  layer.mouseDownAction = this.mouseDownAction;
  layer.mouseUpAction = this.mouseUpAction;
  layer.mouseMoveAction = this.mouseMoveAction;
  layer.mouseOverAction = this.mouseOverAction;
  layer.mouseOutAction = this.mouseOutAction;
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: event handlers with routing/canceling

function _handleMouseOver(e) {
	if(window.event) {
		if(window.event.srcElement == this && window.event.srcElement.tagName == "DIV"){
		window.event.cancelBubble = true;
		}
		else if(window.event.srcElement == this){
		return;
		}
	} 
	else {
		if(e.target != this) {
		routeEvent(e);
		return;
		}
	}
	if(this.mouseOverAction){
	this.mouseOverAction(e, "mouseover");
	}
}

function _handleMouseOut(e) {
	if(window.event) {
		if(window.event.srcElement == this && window.event.srcElement.tagName == "DIV"){
		window.event.cancelBubble = true;
		}
		else if(window.event.srcElement == this){
		return;
		}
	} 
	else {
		if(e.target != this) {
		routeEvent(e);
		return;
		}
	}
	if(this.mouseOutAction){
	this.mouseOutAction(e, "mouseout");
	}
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: built-in dragging handlers

var _dbj = new Array(); // drag Object

function _handleMouseDown(e) {
	if(window.event) {
	var e = window.event;
	e.cancelBubble = true;
	} 
	else {
		if(e.handled){
		return true;
		}
	}
	if (window.event){
	layer = e.srcElement;
	}
	else {
	layer = this;
	}
	if(layer.mouseDownAction){
	layer.mouseDownAction(e, "mousedown");
	}
	if(!layer.draggable){
	return true;
	}
	if(layer.containers) {
	layer.wasContained = false;
		for(var i = 0; i < layer.containers.length; i++) {
			if(_contains(layer.containers[i], layer)) {
			layer.wasContained = layer.containers[i];
			break;
			}
		}
	}
	if(isDom) {
	layer.offsetX = e.clientX - layer.offsetLeft;
	layer.offsetY = e.clientY - layer.offsetTop;
	}
	else if(isNav4) {
	layer.offsetX = e.pageX - layer.left;
	layer.offsetY = e.pageY - layer.top;
	} 
	else if (isIE4){
	layer.offsetX = e.clientX - layer.style.pixelLeft;
	layer.offsetY = e.clientY - layer.style.pixelTop;
	}
_dbj.layer = layer;
_dbj.indrag = true;
	if(isNav4) {
	e.handled = true;
	return false; 
	}
}
function _handleMouseMove(e) {
var ret = false;
	if (window.event){
	window.event.cancelBubble = true;
	}
	else {
		if(!_dbj.layer) {
		_dbj.layer = this;
		}
		if(e.handled){
		return false;
		}	
	}
	if(!_dbj.layer) {
		if(window.event) {
			if(window.event.srcElement.mouseMoveAction){
			window.event.srcElement.mouseMoveAction(e, "mousemove");
			}
		return true;
		}
	}
	if(_dbj.layer.mouseMoveAction){
	ret = _dbj.layer.mouseMoveAction(e, "mousemove");
	}
	if(!_dbj.layer.draggable){
	return ret;
	}
	if(!_dbj.indrag){
	return true;
	}
	if(!_dbj.layer.vLock) {
	var dstY;
		if (isDom) {
		var theHeight = _dbj.layer.offsetHeight;
			if (window.event){
			var axisY = window.event.clientY;
			}
			else {
			var axisY = e.clientY;
			}
		}
		else if (isNav4){
		var theHeight = _dbj.layer.clip.height;
		var axisY = e.pageY;
		}
		else if (isIE4){
		var theHeight = _dbj.layer.pixelHeight;
		var axisY = window.event.clientY;		
		}		
		dstY = (axisY - _dbj.layer.offsetY);
		if((_dbj.layer.useBounds && (dstY >= _dbj.layer.bounds[2]) && (dstY + theHeight <= _dbj.layer.bounds[3])) || !_dbj.layer.useBounds){
			if (isDom){
			_dbj.layer.style.top = dstY;				
			}
			else if (isNav4){
			_dbj.layer.top = dstY;
			}
			else if (isIE4){
			_dbj.layer.style.pixelTop = dstY;
			}
		}
		else if(_dbj.layer.useBounds) {
			if(dstY < _dbj.layer.bounds[2]){
				if (isDom){
				_dbj.layer.style.top = _dbj.layer.bounds[2];					
				}
				else if (isNav4){
				_dbj.layer.top = _dbj.layer.bounds[2];
				}
				else if (isIE4){
				_dbj.layer.style.pixelTop = _dbj.layer.bounds[2];
				}
			}
			else {
				if (isDom){
				_dbj.layer.style.top = _dbj.layer.bounds[3] - theHeight;					
				}
				else if (isNav4){
				_dbj.layer.top = _dbj.layer.bounds[3] - theHeight;
				}
				else if (isIE){
				_dbj.layer.style.pixelTop = _dbj.layer.bounds[3] - theHeight;					
				}
			}
		}
	}
	if(!_dbj.layer.hLock) {
	var dstX;
		if (isDom) {
		var theWidth = _dbj.layer.offsetWidth;
			if (window.event){
			var axisX = window.event.clientX;
			}
			else {
			var axisX = e.clientX;
			}
		}
		else if (isNav4){
		var theWidth = _dbj.layer.clip.width;
		var axisX = e.pageX;
		}
		else if (isIE4){
		var theWidth = _dbj.layer.pixelWidth;
		var axisX = window.event.clientX;		
		}
		dstX = (axisX - _dbj.layer.offsetX);
		if((_dbj.layer.useBounds && (dstX + theWidth <= _dbj.layer.bounds[1]) && (dstX >= _dbj.layer.bounds[0])) || !_dbj.layer.useBounds){
			if (isDom){
			_dbj.layer.style.left = dstX;				
			}
			else if (isNav4){
			_dbj.layer.left = dstX;
			}
			else if (isIE4){
			_dbj.layer.style.pixelLeft = dstX;
			}
		}
		else if(_dbj.layer.useBounds) {
			if(dstX < _dbj.layer.bounds[0]){
				if (isDom){
				_dbj.layer.style.left = _dbj.layer.bounds[0];					
				}
				else if (isNav4){
				_dbj.layer.left = _dbj.layer.bounds[0];
				}
				else if (isIE4){
				_dbj.layer.style.pixelLeft = _dbj.layer.bounds[0];
				}				
			}
			else {
				if (isDom){
				_dbj.layer.style.left = _dbj.layer.bounds[1] - theWidth;					
				}
				else if (isNav4){
				_dbj.layer.left = _dbj.layer.bounds[1] - theWidth;
				}
				else if (isIE4){
				_dbj.layer.style.pixelLeft = _dbj.layer.bounds[1] - theWidth;					
				}
			}
		}
	}	
	if(! window.event) {
	e.handled = true;
	return false;
	}
}

function _clearDBJ() {
_dbj.indrag = false;
_dbj.layer = null;
}

function _handleMouseUp(e) {
	if(window.event) {
	window.event.cancelBubble = true;
	} 
	else {
		if(e.handled){
		return;
		}
	}
	if(!_dbj.layer) { // weren't just dragging
		if(this.mouseUpAction){
		this.mouseUpAction(e, "mouseup");
		return;
		}
	}
_clearDBJ();
	if(! window.event) {
	e.handled = true;
	return;
	}
}

///////////////////////////////////////////////////////////////////////
// Behaviors Object: the end
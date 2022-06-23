<html>
<head>
<title>Selector de Color</title>
<style type="text/css">
#pickerHelp 
{
	position: absolute;
	top: 8;
	left: 365;
	width: 25;
	height: 18;
	clip: rect(0,25,18,0);
}
#tabRGB 
{
	position: absolute;
	top: 5;
	left: 6;
	width: 53;
	height: 22;
	clip: rect(0,53,22,0);
	layer-background-image: url(tabRGB.gif);
	background-image: url(tabRGB.gif);
	z-index: 0;
}
#tabHLS 
{
	position: absolute;
	top: 5;
	left: 59;
	width: 53;
	height: 22;
	clip: rect(0,53,22,0);
	layer-background-image: url(tabHLS.gif);
	background-image: url(tabHLS.gif);
	z-index: 0;
}
#tabHSB 
{
	position: absolute;
	top: 5;
	left: 112;
	width: 53;
	height: 22;
	clip: rect(0,53,22,0);
	layer-background-image: url(tabHSB.gif);
	background-image: url(tabHSB.gif);
	z-index: 0;
}
#boxBevel 
{
	 position: absolute;
	 top: 25;
	 left: 5;
	 width: 385;
	 height: 213;
	 clip: rect(0,385,213,0);
	 layer-background-image: url(boxbevel.gif);
	 background-image: url(boxbevel.gif);
	 z-index: 1;
}
#hue {
 position: absolute;
 top: 40;
 left: 20;
 width: 360;
 height: 10;
 clip: rect(0,360,10,0);
 z-index: 3;
}
#saturation 
{
	 position: absolute;
	 top: 70;
	 left: 20;
	 width: 10;
	 height: 150;
	 clip: rect(0,10,150,0);
}
#brightness 
{
	 position: absolute;
	 top: 70;
	 left: 50;
	 width: 10;
	 height: 150;
	 clip: rect(0,10,150,0);
}
#lightness 
{
	 position: absolute;
	 top: 70;
	 left: 50;
	 width: 10;
	 height: 150;
	 clip: rect(0,10,150,0);
}
#red 
{
	 visibility: hidden;
	 position: absolute;
	 top: 40;
	 left: 20;
	 width: 10;
	 height: 180;
	 clip: rect(0,10,180,0);
}
#green 
{
	 visibility: hidden;
	 position: absolute;
	 top: 40;
	 left: 50;
	 width: 10;
	 height: 180;
	 clip: rect(0,10,180,0);
}
#blue 
{
	 visibility: hidden;
	 position: absolute;
	 top: 40;
	 left: 80;
	 width: 10;
	 height: 180;
	 clip: rect(0,10,180,0);
}
#thumbH 
{
	 position: absolute;
	 top: 50;
	 left: 120;
	 width: 11;
	 height: 17;
	 layer-background-image: url(thumb.gif);
	 background-image: url(thumb.gif);
	 clip: rect(0,11,17,0);
	 z-index: 10;
}
#thumbS 
{
	 visibility: hidden;
	 position: absolute;
	 top: 184;
	 left: 30;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbS.gif);
	 background-image: url(thumbS.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbV 
{
	 visibility: hidden;
	 position: absolute;
	 top: 184;
	 left: 60;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbB.gif);
	 background-image: url(thumbB.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbS2 
{
	 position: absolute;
	 top: 214;
	 left: 30;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbS.gif);
	 background-image: url(thumbS.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbL 
{
	 position: absolute;
	 top: 140;
	 left: 60;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbL.gif);
	 background-image: url(thumbL.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbR 
{
	 visibility: hidden;
	 position: absolute;
	 top: 44;
	 left: 30;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbR.gif);
	 background-image: url(thumbR.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbG 
{
	 visibility: hidden;
	 position: absolute;
	 top: 104;
	 left: 60;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbG.gif);
	 background-image: url(thumbG.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#thumbB 
{
	 visibility: hidden;
	 position: absolute;
	 top: 154;
	 left: 90;
	 width: 17;
	 height: 11;
	 layer-background-image: url(thumbB.gif);
	 background-image: url(thumbB.gif);
	 clip: rect(0,17,11,0);
	 z-index: 10;
}
#testLayer 
{
	 position: absolute;
	 top: 70;
	 left: 111;
	 width: 153;
	 height: 150;
	 clip: rect(0,153,150,0);
	 z-index: 10;
}
#webSafeLayer2 
{
	 position: absolute;
	 top: 70;
	 left: 273;
	 width: 35;
	 height: 35;
	 clip: rect(0,35,35,0);
	 z-index: 10;
}
#webSafeLayer 
{
	 position: absolute;
	 top: 70;
	 left: 308;
	 width: 35;
	 height: 35;
	 clip: rect(0,35,35,0);
	 z-index: 10;
}
#exclaim 
{
	 position: absolute;
	 visibility: hidden;
	 top: 80;
	 left: 356;
	 width: 13;
	 height: 13;
	 clip: rect(0,13,13,0);
	 z-index: 10;
	 layer-background-image: url(exclaim.gif);
	 background-image: url(exclaim.gif);
}
#testLayer2 
{
	 position: absolute;
	 visibility: hidden;
	 top: 185;
	 left: 110;
	 width: 153;
	 height: 35;
	 clip: rect(0,153,35,0);
	 z-index: 10;
}
#testLayer3 
{
	 position: absolute;
	 visibility: hidden;
	 top: 185;
	 left: 110;
	 width: 76;
	 height: 35;
	 clip: rect(0,76,35,0);
	 z-index: 10;
}
#testLayer4 
{
	 position: absolute;
	 visibility: hidden;
	 top: 185;
	 left: 186;
	 width: 77;
	 height: 35;
	 clip: rect(0,77,35,0);
	 z-index: 10;
}
#hlsLayer 
{
	 position: absolute;
	 top: 125;
	 left: 276;
	 width: 100;
	 height: 80;
	 clip: rect(0,100,80,0);
	 z-index: 10;
}
#rgbLayer 
{
	 visibility: hidden;
	 position: absolute;
	 top: 125;
	 left: 275;
	 width: 100;
	 height: 80;
	 clip: rect(0,100,80,0);
	 z-index: 10;
}
#hsvLayer 
{
	 visibility: hidden;
	 position: absolute;
	 top: 125;
	 left: 276;
	 width: 100;
	 height: 80;
	 clip: rect(0,100,80,0);
	 z-index: 10;
}
.main 
{
	 font-family: arial,helvetica;
	 font-size: 10pt;
}
.small 
{
	 font-family: arial,helvetica;
	 font-size: 9pt;
	 font-weight: bold;
}
.form 
{
	 font-family: courier;
	 font-size: 10pt;
}
</style>
<script language="Javascript">
var isIE = (navigator.userAgent.indexOf("MSIE") != -1) ? true : false;
var isNav4 = (document.layers) ? true : false;
var isIE4 = (document.all && ! document.getElementById) ? true : false;
var isDom = (document.getElementById) ? true : false;
var mode = 0, format = 2;

if (isDom)
{
	var layerRef = "document.getElementById";
	var openparen = '("';
	var closeparen = '")';
}
else if (isNav4)
{
	var layerRef = "document.layers";
	var openparen = '["';
	var closeparen = '"]';
}
else if (isIE4)
{
	var layerRef = "document.all";
	var openparen = '("';
	var closeparen = '")';
}
</script>
<script language="Javascript" src="colorspace.js"></script>
<script language="Javascript" src="getcolor.js"></script>
<script language="Javascript" src="behavior.js"></script>
<script language="Javascript">
//
// turns [0-255] number into web-safe equivalent
//
function webSafify(n) 
{
	  if(n < 26) return 0;
	  else if(n < 77) return 51;
	  else if(n < 128) return 102;
	  else if(n < 179) return 153;
	  else if(n < 230) return 204;
	  else return 255;
}

//
// Updates colors, thumbs, and forms
//
function update(e, type, override, r, g, b, initHex) 
{
	var hex, rgb, hsv, hls;

	if(!override) {
	//
	// convert everything to RGB
	//
		switch(format) 
		{
			case 0: hsv = getHSV(); rgb = HSVtoRGB(hsv.h,hsv.s,hsv.v); break;
			case 1: hls = getHLS(); rgb = HLStoRGB(hls.h,hls.l,hls.s); break; //executes first
			default: rgb = getRGB(initHex); break;
			}
		if (initHex)
		{
			return false;
		}
	}
	else 
	{
		rgb = new Array(3);
		rgb.r = r; rgb.g = g; rgb.b = b;
		}
//
// update forms
//
	switch(format) 
	{
	case 0:
			if(!hsv)
			{
				hsv = RGBtoHSV(rgb.r,rgb.g,rgb.b);
			}
			if (isDom)
			{
				var f = document.hsvForm;
			}
			else if (isNav4)
			{
				var f = document.layers["hsvLayer"].document.forms[0];
			}
			else if (isIE4)
			{
				var f = document.all.hsvForm;
			}
		f.H.value = Math.round(hsv.h);
		f.S.value = Math.round(hsv.s*100);
		f.V.value = Math.round(hsv.v*100);
		f = null;
		break;
	case 1:
			if(!hls)
			{
				hls = RGBtoHLS(rgb.r,rgb.g,rgb.b);
			}
			if (isDom)
			{
				var f = document.hlsForm;
			}
			else if (isNav4)
			{
				var f = document.layers["hlsLayer"].document.forms[0];
			}
			else if (isIE4)
			{
				var f = document.all.hlsForm;
			}
		f.H.value = Math.round(hls.h);
		f.L.value = Math.round(hls.l*100);
		f.S.value = Math.round(hls.s*100);
		f = null;
		break;
	default:
			if (isDom)
			{
				var f = document.rgbForm;
			}
			else if (isNav4)
			{
				var f = document.layers["rgbLayer"].document.forms[0];
			}
			else if (isIE4)
			{
				var f = document.all.rgbForm;
			}
		f.R.value = rgb.r;
		f.G.value = rgb.g;
		f.B.value = rgb.b;
		f = null;
		break;
	}

  //
  // update bgColor
  //
  if(!hex) 
  {
	  hex = RGBtoHex(rgb.r,rgb.g,rgb.b); //init color
  }
  document.rgbhsv.webcolor.value = hex;
  setLayerBgcolor(eval(layerRef + openparen + "testLayer" + closeparen), hex);

  //
  // do web-safe stuff
  //
  var rS,gS,bS,hex2;
  rS = webSafify(rgb.r);
  gS = webSafify(rgb.g);
  bS = webSafify(rgb.b);
  setLayerBgcolor(eval(layerRef + openparen + "webSafeLayer" + closeparen), hex2 = RGBtoHex(rS,gS,bS));
  setLayerBgcolor(eval(layerRef + openparen + "webSafeLayer2" + closeparen), hex);
  (hex2 == hex)? setVisibility(eval(layerRef + openparen + "exclaim" + closeparen), "hidden") : setVisibility(eval(layerRef + openparen + "exclaim" + closeparen), "visible");
  rS = gS = bS = hex2 = null;

  //
  // update other thumbs
  //
  if(override || format != 0) 
  {
    if(!hsv) 
	{
		hsv = RGBtoHSV(rgb.r,rgb.g,rgb.b);
    }
    setHSV(hsv.h,hsv.s,hsv.v,format);
  }
  if(override || format != 1) 
  {
    if(!hls) hls = RGBtoHLS(rgb.r,rgb.g,rgb.b);
    setHLS(hls.h,hls.l,hls.s,format);
  }
  if(override || format != 2) setRGB(rgb.r,rgb.g,rgb.b);

  //
  // update triadic or complementary colors, if necessary
  //
  if(format == 0) 
  {
    if(mode == 2) 
	{
      rgb = HSVtoRGB((hsv.h+120)%360,hsv.s,hsv.v);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer3" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
      rgb = HSVtoRGB((hsv.h+240)%360,hsv.s,hsv.v);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer4" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    } 
	else if(mode == 1) 
	{
      rgb = HSVtoRGB((hsv.h+180)%360,hsv.s,hsv.v);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer2" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    }
  } 
  else if(format == 1) 
  {
    if(mode == 2) 
	{
      rgb = HLStoRGB((hls.h+120)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer3" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
      rgb = HLStoRGB((hls.h+240)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer4" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    } else if(mode == 1) 
	{
      rgb = HLStoRGB((hls.h+180)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer2" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    }
  } 
  else 
  {
    if(!hls) hls = RGBtoHLS(rgb.r,rgb.g,rgb.b);
    if(mode == 2) 
	{
      rgb = HLStoRGB((hls.h+120)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer3" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
      rgb = HLStoRGB((hls.h+240)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer4" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    } 
	else if(mode == 1) 
	{
      rgb = HLStoRGB((hls.h+180)%360,hls.l,hls.s);
      setLayerBgcolor(eval(layerRef + openparen + "testLayer2" + closeparen), RGBtoHex(rgb.r,rgb.g,rgb.b));
    }
  }

  //
  // don't waste memory!
  //
  rgb = hls = hsv = null;
}

//
// text-input
//
function change(type, dst, val) 
{
  //
  // must correct values!
  //
  switch(type) 
  {
	case "HLS":
		switch(dst) 
		{
			case "H":
			  if(val < 0) val = 0; if(val > 359) val = 359;
			  setHLS(val,-1,-1);
			  break;
			case "L":
			  if(val < 0) val = 0; if(val > 100) val = 100;
			  setHLS(-1,val/100,-1);
			  break;
			case "S":
			  if(val < 0) val = 0; if(val > 100) val = 100;
			  setHLS(-1,-1,val/100);
			  break;
			default: break;
		}
    break;
  case "HSB":
		switch(dst) 
		{
			case "H":
			  if(val < 0) val = 0; if(val > 359) val = 359;
			  setHSV(val,-1,-1);
			  break;
			case "S":
			  if(val < 0) val = 0; if(val > 100) val = 100;
			  setHSV(-1,val/100,-1);
			  break;
			case "V":
			  if(val < 0) val = 0; if(val > 100) val = 100;
			  setHSV(-1,-1,val/100);
			  break;
			default: break;
		}
    break;
  case "RGB":
		switch(dst) 
		{
			case "R":
			  if(val < 0) val = 0; if(val > 255) val = 255;
			  setRGB(val,-1,-1);
			  break;
			case "G":
			  if(val < 0) val = 0; if(val > 255) val = 255;
			  setRGB(-1,val,-1);
			  break;
			case "B":
			  if(val < 0) val = 0; if(val > 255) val = 255;
			  setRGB(-1,-1,val);
			  break;
			default: break;
		}
    break;
  case "HEX":
    var rgb = HextoRGB(val);
    update(false, "", true, rgb.r, rgb.g, rgb.b, "");
    rgb = null;
    break;
  default: break;
  }
  update();
}

//
// When you change color spaces
//
function updateFormat(e,t,override,iformat,initHex) 
{
  if(override) format = iformat;
  setZIndex(eval(layerRef + openparen + "tabRGB" + closeparen), 0);
  setZIndex(eval(layerRef + openparen + "tabHLS" + closeparen), 0);
  setZIndex(eval(layerRef + openparen + "tabHSB" + closeparen), 0);
  if(format == 0 || format == 1) 
  {
    setTop(eval(layerRef + openparen + "testLayer" + closeparen), 70);
    setLeft(eval(layerRef + openparen + "testLayer" + closeparen), 80);
    setClipHeight(eval(layerRef + openparen + "testLayer" + closeparen), 150);
    setClipWidth(eval(layerRef + openparen + "testLayer" + closeparen), 183);
    setLeft(eval(layerRef + openparen + "testLayer2" + closeparen), 80);
    setClipWidth(eval(layerRef + openparen + "testLayer2" + closeparen), 183);
    setLeft(eval(layerRef + openparen + "testLayer3" + closeparen), 80);
    setClipWidth(eval(layerRef + openparen + "testLayer3" + closeparen), 91);
    setLeft(eval(layerRef + openparen + "testLayer4" + closeparen), 171);
    setClipWidth(eval(layerRef + openparen + "testLayer4" + closeparen), 92);
    setVisibility(eval(layerRef + openparen + "thumbH" + closeparen), "visible");
    if(format == 0) 
	{
      setZIndex(eval(layerRef + openparen + "tabHSB" + closeparen), 2);
      setVisibility(eval(layerRef + openparen + "thumbS" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "thumbV" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "thumbS2" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "thumbL" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "brightness" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "lightness" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "rgbLayer" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "hsvLayer" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "hlsLayer" + closeparen), "hidden");
    } 
	else 
	{
      setZIndex(eval(layerRef + openparen + "tabHLS" + closeparen), 2);
      setVisibility(eval(layerRef + openparen + "brightness" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "lightness" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "thumbS" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "thumbV" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "thumbS2" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "thumbL" + closeparen), "visible");
      setVisibility(eval(layerRef + openparen + "rgbLayer" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "hsvLayer" + closeparen), "hidden");
      setVisibility(eval(layerRef + openparen + "hlsLayer" + closeparen), "visible");
    }
    setVisibility(eval(layerRef + openparen + "thumbR" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbG" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbB" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "hue" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "saturation" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "brightness" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "red" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "green" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "blue" + closeparen), "hidden");
  } 
  else 
  {
    setVisibility(eval(layerRef + openparen + "rgbLayer" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "hsvLayer" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "hlsLayer" + closeparen), "hidden");
    setZIndex(eval(layerRef + openparen + "tabRGB" + closeparen), 2);
    setTop(eval(layerRef + openparen + "testLayer" + closeparen), 40);
    setLeft(eval(layerRef + openparen + "testLayer" + closeparen), 110);
    setClipHeight(eval(layerRef + openparen + "testLayer" + closeparen), 180);
    setClipWidth(eval(layerRef + openparen + "testLayer" + closeparen), 153);
    setLeft(eval(layerRef + openparen + "testLayer2" + closeparen), 110);
    setClipWidth(eval(layerRef + openparen + "testLayer2" + closeparen), 153);
    setLeft(eval(layerRef + openparen + "testLayer3" + closeparen), 110);
    setClipWidth(eval(layerRef + openparen + "testLayer3" + closeparen), 76);
    setLeft(eval(layerRef + openparen + "testLayer4" + closeparen), 186);
    setClipWidth(eval(layerRef + openparen + "testLayer4" + closeparen), 77);
    setVisibility(eval(layerRef + openparen + "thumbH" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbS" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbS2" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbL" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbV" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "thumbR" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "thumbG" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "thumbB" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "hue" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "saturation" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "brightness" + closeparen), "hidden");
    setVisibility(eval(layerRef + openparen + "red" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "green" + closeparen), "visible");
    setVisibility(eval(layerRef + openparen + "blue" + closeparen), "visible");
  }
  updateMode("","","",initHex);
}

//
// When you change hues (complementary,triadic)
//
function updateMode(e,t,imode,initHex) 
{
  // not CB
  mode = imode || document.rgbhsv.scheme.selectedIndex;
  switch(mode) 
  {
	  case 0:
		setVisibility(eval(layerRef + openparen + "testLayer2" + closeparen), "hidden");
		setVisibility(eval(layerRef + openparen + "testLayer3" + closeparen), "hidden");
		setVisibility(eval(layerRef + openparen + "testLayer4" + closeparen), "hidden");
		break;
	  case 1:
		setVisibility(eval(layerRef + openparen + "testLayer2" + closeparen), "visible");
		setVisibility(eval(layerRef + openparen + "testLayer3" + closeparen), "hidden");
		setVisibility(eval(layerRef + openparen + "testLayer4" + closeparen), "hidden");
		break;
	  case 2:
		setVisibility(eval(layerRef + openparen + "testLayer2" + closeparen), "hidden");
		setVisibility(eval(layerRef + openparen + "testLayer3" + closeparen), "visible");
		setVisibility(eval(layerRef + openparen + "testLayer4" + closeparen), "visible");
		break;
  }
  update("","","","","","",initHex);
}

//
// Move to complementary/triadic hue
//
function move() 
{
  var rgb;
	if (isNav4)
	{
		rgb = HextoRGB(DectoHex(this.bgColor))
	}
	else 
	{
		if (this.style.backgroundColor.indexOf("rgb") != -1)
		{
			//in case browser passes rgb(r g b), convert to hex
			var rgbArray = this.style.backgroundColor.substr(4, this.style.backgroundColor.length).split(",");
			var convertHex = RGBtoHex(parseInt(rgbArray[0]), parseInt(rgbArray[1]), parseInt(rgbArray[2]));
			rgb = HextoRGB(convertHex);
		}
		else 
		{
			rgb = HextoRGB(this.style.backgroundColor);
		}
	}
  update(false, "", true, rgb.r, rgb.g, rgb.b, "");
  rgb = null;
}

//
// Jump to nearest web-safe color
//
function jump()
{
  var rgb, hsv, hls;

  //
  // convert everything to RGB
  //
  switch(format) 
  {
	  case 0:
		hsv = getHSV();
		rgb = HSVtoRGB(hsv.h,hsv.s,hsv.v);
		break;
	  case 1:
		hls = getHLS();
		rgb = HLStoRGB(hls.h,hls.l,hls.s);
		break;
	  default:
		rgb = getRGB();
		break;
  }

  //
  // make web-safe
  //
  rgb.r = webSafify(rgb.r);
  rgb.g = webSafify(rgb.g);
  rgb.b = webSafify(rgb.b);

  //
  // update (with override)
  //
  update(false, "", true, rgb.r, rgb.g, rgb.b, "");

  //
  // clear memory
  //
  rgb = hsv = hls = null;
}

function gohsb() { updateFormat(false,false,true,0,""); }
function gohls() { updateFormat(false,false,true,1,""); }
function gorgb() { updateFormat(false,false,true,2,""); }

//
// Load behaviors, display everything OK
//
function init(Dato) 
{
  var initColor = Dato; //color that loads on first load of page
  db = new Behavior(true);
  db.setAction("MOUSEMOVE", update);
  db.vLock = true;
  db.setBounds(15,(15+360+10),0,1000);
  db.applyBehavior(eval(layerRef + openparen + "thumbH" + closeparen));
  db.vLock = false;
  db.hLock = true;
  db.setBounds(0,1000,65,(65+160));
  db.applyBehavior(eval(layerRef + openparen + "thumbS" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "thumbV" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "thumbS2" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "thumbL" + closeparen));
  db.setBounds(0,1000,35,(35+190));
  db.applyBehavior(eval(layerRef + openparen + "thumbR" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "thumbG" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "thumbB" + closeparen));
  db = null;
  db = new Behavior(false);
  db.setAction("MOUSEDOWN", gorgb);
  db.applyBehavior(eval(layerRef + openparen + "tabRGB" + closeparen));
  db.setAction("MOUSEDOWN", gohls);
  db.applyBehavior(eval(layerRef + openparen + "tabHLS" + closeparen));
  db.setAction("MOUSEDOWN", gohsb);
  db.applyBehavior(eval(layerRef + openparen + "tabHSB" + closeparen));
  db.setAction("MOUSEDOWN", jump);
  db.applyBehavior(eval(layerRef + openparen + "webSafeLayer" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "exclaim" + closeparen));
  db.setAction("MOUSEDOWN", move);
  db.applyBehavior(eval(layerRef + openparen + "testLayer2" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "testLayer3" + closeparen));
  db.applyBehavior(eval(layerRef + openparen + "testLayer4" + closeparen));
  db = null;
  updateFormat("","","","",initColor);
}
function pageReload()
{
	if (isNav4)
	{
		self.location.href = "index.html";
	}
}
function help()
{
	helpWin = window.open('help.html', 'help', 'width=600,height=400,menubar=no,resizable=yes,location=no,toolbar=no,scrollbars=yes,screenX=5,screenY=5,top=5,left=5')
	helpWin.focus();
}
</script>

<script language="Javascript">
function setWebcolor(formName, fieldName, includePound)
{
	var webcolor = document.rgbhsv.webcolor.value;
	if (includePound == 0)
	{
		webcolor = webcolor.substr(1,webcolor.length);
	}
	eval("window.opener.document." + formName + "." + fieldName + ".value = \"" + webcolor + "\"");
	self.close();
}
</script>

</head>
<body onload="init('#<?=$_GET['Dato']?>')" bgcolor="#CCCCCC" onResize="pageReload();">
<div id="boxBevel" name="boxBevel"></div>
<div id="tabRGB" name="tabRGB"></div>
<div id="tabHSB" name="tabHSB"></div>
<div id="tabHLS" name="tabHLS"></div>
<div id="hue" name="hue"><img src="spectrum.gif" width="360" height="10"></div>
<div id="saturation" name="saturation"><img src="grey3.gif" width="10" height="150"></div>
<div id="brightness" name="brightness"><img src="grey4.gif" width="10" height="150"></div>
<div id="lightness" name="lightness"><img src="grey5.gif" width="10" height="150"></div>
<div id="red" name="red"><img src="red.gif" width="10" height="180"></div>
<div id="green" name="green"><img src="green.gif" width="10" height="180"></div>
<div id="blue" name="blue"><img src="blue.gif" width="10" height="180"></div>
<div id="thumbH" name="thumbH"></div>
<div id="thumbL" name="thumbL"></div>
<div id="thumbS2" name="thumbS2"></div>
<div id="thumbS" name="thumbS"></div>
<div id="thumbV" name="thumbV"></div>
<div id="thumbR" name="thumbR"></div>
<div id="thumbG" name="thumbG"></div>
<div id="thumbB" name="thumbB"></div>
<div id="testLayer" name="testLayer"></div>
<div id="testLayer2" name="testLayer2"></div>
<div id="testLayer3" name="testLayer3"></div>
<div id="testLayer4" name="testLayer4"></div>
<div id="webSafeLayer" name="webSafeLayer"></div>
<div id="webSafeLayer2" name="webSafeLayer2"></div>
<div id="exclaim" name="exclaim"></div>

<div id="hlsLayer" name="hlsLayer">
<form name="hlsForm">
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="right" class="main">H:&nbsp;</td>
<td align="right" class="main"><input type="text" name="H" size="4" maxlength="3" class="form" onchange="change('HLS',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">L:&nbsp;</td>
<td align="right" class="main"><input type="text" name="L" size="4" maxlength="3" class="form" onchange="change('HLS',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">S:&nbsp;</td>
<td align="right" class="main"><input type="text" name="S" size="4" maxlength="3" class="form" onchange="change('HLS',this.name,this.value)"></td>
</tr>
</table>
</form>
</div>

<div id="hsvLayer" name="hsvLayer">
<form name="hsvForm">
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="right" class="main">H:&nbsp;</td>
<td align="right" class="main"><input type="text" name="H" size="4" maxlength="3" class="form" onchange="change('HSB',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">S:&nbsp;</td>
<td align="right" class="main"><input type="text" name="S" size="4" maxlength="3" class="form" onchange="change('HSB',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">B:&nbsp;</td>
<td align="right" class="main"><input type="text" name="V" size="4" maxlength="3" class="form" onchange="change('HSB',this.name,this.value)"></td>
</tr>
</table>
</form>
</div>

<div id="rgbLayer" name="rgbLayer">
<form name="rgbForm">
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="right" class="main">R:&nbsp;</td>
<td align="right" class="main"><input type="text" name="R" size="4" maxlength="3" class="form" onchange="change('RGB',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">G:&nbsp;</td>
<td align="right" class="main"><input type="text" name="G" size="4" maxlength="3" class="form" onchange="change('RGB',this.name,this.value)"></td>
</tr>
<tr>
<td align="right" class="main">B:&nbsp;</td>
<td align="right" class="main"><input type="text" name="B" size="4" maxlength="3" class="form" onchange="change('RGB',this.name,this.value)"></td>
</tr>
</table>
</form>
</div>
<script language="Javascript">
var tableDataTop = (isIE) ? 226 : 233;
var tableDataWidth = (isIE) ? 380 : 382;
document.write('<table border="0" cellpadding="0" cellspacing="0" width="' + tableDataWidth + '">');
</script>
<tr>
<script language="Javascript">
document.write('<td height="' + tableDataTop + '"></td>');
</script>
</tr>
<form onSubmit="return false;" name="rgbhsv">
<tr>
<td>
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center" class="main">Hues:&nbsp;</td>
	<td align="center" class="main"><select name="scheme" onchange="updateMode()">
	<option selected>single</option>
	<option>complementary</option>
	<option>triadic</option>
	</select>
	</td>
	</tr>
	</table>
</td>
<td align="right">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center" class="main">Hex:&nbsp;</td>
	<td align="center" class="main"><input type="text" size="8" maxlength="7" name="webcolor" class="form" onchange="change('HEX','HEX',this.value)"></td>
	<td align="center" class="main">&nbsp;<input type="submit" name="setHex" value="Seleccionar" onClick="setWebcolor('<?=$_GET['Forma']?>','<?=$_GET['Campo']?>',1);"></td>
	</tr>
	</table>
</td>
</tr>
</form>
</table>
</body>
</html>

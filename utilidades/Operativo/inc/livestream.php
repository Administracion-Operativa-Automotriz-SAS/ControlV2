<?php

/**
 * prepara el ingreso a localhost sin claves
 *
 * @version $Id$
 * @copyright 2009
 */
 class livestream
{
	var $canal='aoacolombia';
	var $alto=450;
	var $ancho=500;
	var $backgroundcolor='0x0033ff';
	var $backgroundalpha='1';
	var $backgroundgradientstrength='70';
	var $chromecolor='0x0099cc';
	var $headerbarglossenabled='true';
	var $controlbarglossenabled='true';
	var $chatinputglossenabled='true';
	var $uiwhite='true';
	var $uialpha='0.3';
	var $uiselectedalpha='0.9';
	var $dropshadowenabled='true';
	var $dropshadowhorizontaldistance='10';
	var $dropshadowverticaldistance='10';
	var $paddingleft ='5';
	var $paddingright ='5';
	var $paddingtop='5';
	var $paddingbottom='5';
	var $cornerradius='20';
	var $backtodirectoryurl = 'null';
	var $bannerurl ='null';
	var $bannertext = 'null';
	var $bannerwidth='320';
	var $bannerheight ='50';
	var $showviewers = 'false';
	var $embedenabled = 'false';
	var $chatenabled ='false';
	var $ondemandenabled ='false';
	var $programguideenabled ='false';
	var $fullscreenenabled = 'false';
	var $reportabuseenabled ='false';
	var $gridenabled ='false';
	var $initialison = 'true';
	var $initialismute = 'true';
	var $initialvolume ='10';
	var $contentid = 'null';
	var $initthumburl ='null';
	var $playeraspectwidth = '4';
	var $playeraspectheight = '3';
	var $moguluslogoenabled = 'false';
	var $wmode ='window';
	var $centrado =1;

	function mostrar()
	{
		if($this->canal)
		{
			if($this->centrado) echo "<center>";
			echo "<script src='http://static.livestream.com/scripts/playerv2.js?channel=$this->canal&layout=playerEmbedDefault&backgroundColor=$this->backgroundcolor".
					"&backgroundAlpha=$this->backgroundalpha&backgroundGradientStrength=$this->backgroundgradientstrength&chromeColor=$this->chromecolor".
					"&headerBarGlossEnabled=$this->headerbarglossenabled&controlBarGlossEnabled=$this->controlbarglossenabled&chatInputGlossEnabled=$this->chatinputglossenabled".
					"&uiWhite=$this->uiwhite&uiAlpha=$this->uialpha&uiSelectedAlpha=$this->uiselectedalpha&dropShadowEnabled=$this->dropshadowenabled".
					"&dropShadowHorizontalDistance=$this->dropshadowhorizontaldistance&dropShadowVerticalDistance=$this->dropshadowverticaldistance".
					"&paddingLeft=$this->paddingleft&paddingRight=$this->paddingright&paddingTop=$this->paddingtop&paddingBottom=$this->paddingbottom".
					"&cornerRadius=$this->cornerradius&backToDirectoryURL=$this->backtodirectoryurl&bannerURL=$this->bannerurl&bannerText=$this->bannertext".
					"&bannerWidth=$this->bannerwidth&bannerHeight=$this->bannerheight&showViewers=$this->showviewers&embedEnabled=$this->embedenabled".
					"&chatEnabled=$this->chatenabled&onDemandEnabled=$this->ondemandenabled&programGuideEnabled=$this->programguideenabled".
					"&fullScreenEnabled=$this->fullscreenenabled&reportAbuseEnabled=$this->reportabuseenabled&gridEnabled=$this->gridenabled".
					"&initialIsOn=$this->initialison&initialIsMute=$this->initialismute&initialVolume=$this->initialvolume&contentId=$this->contentid&initThumbUrl=$this->initthumburl".
					"&playeraspectwidth=$this->playeraspectwidth&playeraspectheight=$this->playeraspectheight&mogulusLogoEnabled=$this->moguluslogoenabled".
					"&width=$this->ancho&height=$this->alto&wmode=$this->wmode' type='text/javascript'></script>";
			if($this->centrado) echo "</center>";
		}
	}
}

class ustream
{
	var $canal='';
	var $alto=450;
	var $ancho=500;
	var $autoplay='true';
	var $centrado=1;
    var $nombre='';
    var $objeto='';

	function mostrar()
	{
		if($this->centrado) echo "<center>";
		echo "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' width='$this->ancho' height='$this->alto' id='$this->nombre'>
			<param name='flashvars' value='autoplay=$this->autoplay&amp;brand=embed&amp;cid=$this->canal'/>
			<param name='allowfullscreen' value='true'/>
			<param name='allowscriptaccess' value='false'/>
			<param name='movie' value='http://www.ustream.tv/flash/live/1/$this->canal'/>
			<embed flashvars='autoplay=$this->autoplay&amp;brand=embed&amp;cid=$this->canal'
			 width='$this->ancho' height='$this->alto'
			 allowfullscreen='true' allowscriptaccess='false'
			 id='$this->nombre' name='$this->objeto' src='http://www.ustream.tv/flash/live/1/$this->canal'
			 type='application/x-shockwave-flash' /></object>";
		if($this->centrado) echo "</center>";
	}
}

<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="400" height="320" id="utv962443"><param name="flashvars" value="autoplay=false&amp;brand=embed&amp;cid=3927702&amp;locale=en_US"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="movie" value="http://www.ustream.tv/flash/live/1/3927702"/><embed flashvars="autoplay=false&amp;brand=embed&amp;cid=3927702&amp;locale=en_US" width="400" height="320" allowfullscreen="true" allowscriptaccess="always" id="utv962443" name="utv_n_962807" src="http://www.ustream.tv/flash/live/1/3927702" type="application/x-shockwave-flash" /></object><a href="http://www.ustream.tv/" style="padding: 2px 0px 4px; width: 400px; background: #ffffff; display: block; color: #000000; font-weight: normal; font-size: 10px; text-decoration: underline; text-align: center;" target="_blank">Live Broadcasting by Ustream</a>
?>
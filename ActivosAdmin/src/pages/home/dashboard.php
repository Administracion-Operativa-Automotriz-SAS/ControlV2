
<?php

 $HThome = "
<template>

  
<div class='content'>

<div class='container-fluid'>

<div class='row'>


<div class='col-xl-3 col-md-6'>

 <v-card
    class='mx-auto'
    max-width='344'
    outlined
  >
    <v-list-item three-line>
      <v-list-item-content>
		 <v-list-item-subtitle>Novedad </v-list-item-subtitle>
      </v-list-item-content>

      <v-list-item-avatar
        tile
        size='80'
        color='grey'
      >
	     <v-img src='https://app.aoacolombia.com/Control/operativo/imagenes/000/610/dicono_f_callnew.png' aspect-ratio='1.7'></v-img>				  

	  </v-list-item-avatar>
    </v-list-item>

    <v-card-actions>
	
      <v-btn
      :loading='loading3'
      :disabled='loading3'
      color='blue-grey'
      class='ma-2 white--text'
      v-on:click='home()'
    >
      
      Ingresar
      <v-icon right dark>mdi-clipboard-flow</v-icon>
    </v-btn>
	
	
    </v-card-actions>
  </v-card>

 </div> 
 
 

<div class='col-xl-3 col-md-6'>

 <v-card
    class='mx-auto'
    max-width='344'
    outlined
  >
    <v-list-item three-line>
      <v-list-item-content>
		 <v-list-item-subtitle>Bucar sinestro</v-list-item-subtitle>
      </v-list-item-content>

      <v-list-item-avatar
        tile
        size='80'
        color='grey'
      >
	     <v-img src='https://app.aoacolombia.com/Control/operativo/imagenes/000/610/dicono_f_callnew.png' aspect-ratio='1.7'></v-img>				  

	  </v-list-item-avatar>
    </v-list-item>

    <v-card-actions>
      <v-btn text>Ingresar</v-btn>
    </v-card-actions>
  </v-card>

 </div> 
		  
		  
		  <div class='col-xl-3 col-md-6'>
		  
		  <v-card
				class='mx-auto'
				max-width='344'
				outlined
			  >
				<v-list-item three-line>
				  <v-list-item-content>
				                    <v-list-item-subtitle>Call center 2</v-list-item-subtitle>

				  </v-list-item-content>

				  <v-list-item-avatar
					tile
					size='80'
					color='grey'
				  >
				    <v-img src=' https://app.aoacolombia.com/Control/operativo/imagenes/000/580/dicono_f_grua_lupa.png' aspect-ratio='1.7'></v-img>				  
				  </v-list-item-avatar>
				</v-list-item>

				<v-card-actions>
				  <v-btn text>Ingresar </v-btn>
				</v-card-actions>
			  </v-card>
		  
		  </div> 
		  <div class='col-xl-3 col-md-6'>
		    <v-card
				class='mx-auto'
				max-width='344'
				outlined
			  >
				<v-list-item three-line>
				  <v-list-item-content>
				   
                  <v-list-item-subtitle>Sistema de calidad</v-list-item-subtitle>
				  </v-list-item-content>

				  <v-list-item-avatar
					tile
					size='80'
					color='grey'
				  >
				    <v-img src='https://app.aoacolombia.com/Control/operativo/imagenes/000/663/dicono_f_calidad.png' aspect-ratio='1.7'></v-img>				  
				  </v-list-item-avatar>
				</v-list-item>

				<v-card-actions>
				  <v-btn text>Ingresar </v-btn>
				</v-card-actions>
			  </v-card>
		  </div>
		  
		   <div class='col-xl-3 col-md-6'>
		    <v-card
				class='mx-auto'
				max-width='344'
				outlined
			  >
				<v-list-item three-line>
				  <v-list-item-content>
				   
                  <v-list-item-subtitle>PQR </v-list-item-subtitle>
				  </v-list-item-content>

				  <v-list-item-avatar
					tile
					size='80'
					color='grey'
				  >
				    <v-img src='https://app.aoacolombia.com/Control/operativo/imagenes/000/549/dicono_f_pqr_aoa_200.png'></v-img>				  
				  </v-list-item-avatar>
				</v-list-item>

				<v-card-actions>
				  <v-btn text>Ingresar </v-btn>
				</v-card-actions>
			  </v-card>
		  </div>
		  	   <div class='col-xl-3 col-md-6'>
		    <v-card
				class='mx-auto'
				max-width='344'
				outlined
			  >
				<v-list-item three-line>
				  <v-list-item-content>
				   
                  <v-list-item-subtitle>Monitor Aseguradora</v-list-item-subtitle>
				  </v-list-item-content>

				  <v-list-item-avatar
					tile
					size='80'
					color='grey'
				  >
				    <v-img src='https://app.aoacolombia.com/Control/operativo/imagenes/000/346/monitor_aseguradora.png'></v-img>				  
				  </v-list-item-avatar>
				</v-list-item>

				<v-card-actions>
				  <v-btn text>Ingresar </v-btn>
				</v-card-actions>
			  </v-card>
		  </div>
		  
		  
		  </div>
		  <div class='row'>
		  <div class='col-md-8'>
		  <div class='card'>
		  <div class='card-header'>
		  <h4 class='card-title'>Users Behavior</h4>
		  <p class='card-category'>24 Hours performance</p></div> 
		  <div class='card-body'><div id='div_1327617947632' class='ct-chart'>
		  <svg xmlns:ct='http://gionkunz.github.com/chartist-js/ct'
		  width='100%' height='245px' class='ct-chart-line' 
		  style='width: 100%; height: 245px;'>
		  <g class='ct-grids'><line y1='209.99998474121094' y2='209.99998474121094' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line><line y1='185.62498664855957' y2='185.62498664855957' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line>
		  <line y1='161.2499885559082' y2='161.2499885559082' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line>
		  <line y1='136.87499046325684' y2='136.87499046325684' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line>
		  <line y1='112.49999237060547' y2='112.49999237060547' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line>
		  <line y1='88.1249942779541' y2='88.1249942779541' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line><line y1='63.749996185302734' y2='63.749996185302734' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line>
		  <line y1='39.37499809265137' y2='39.37499809265137' x1='50' x2='654.0625' class='ct-grid ct-vertical'>
		  </line><line y1='15' y2='15' x1='50' x2='654.0625' class='ct-grid ct-vertical'></line></g><g><g class='ct-series ct-series-a'>
		  <path d='M50,140.044C78.765,132.081,107.53,124.393,136.295,116.156C165.06,107.919,193.824,90.881,222.589,90.562C251.354,90.244,280.119,90.39,308.884,90.075C337.649,89.76,366.414,78.392,395.179,74.962C423.943,71.533,452.708,71.207,481.473,67.162C510.238,63.118,539.003,39.862,567.768,39.862C596.533,39.862,625.298,40.35,654.063,40.594' class='ct-line'></path><line x1='50' y1='140.0437402153015' x2='50.01' y2='140.0437402153015' class='ct-point' ct:value='287' opacity='1'></line><line x1='136.29464285714286' y1='116.15624208450318' x2='136.30464285714285' y2='116.15624208450318' class='ct-point' ct:value='385' opacity='1'></line><line x1='222.58928571428572' y1='90.56249408721924' x2='222.5992857142857' y2='90.56249408721924' class='ct-point' ct:value='490' opacity='1'></line><line x1='308.88392857142856' y1='90.07499412536622' x2='308.89392857142855' y2='90.07499412536622' class='ct-point' ct:value='492' opacity='1'></line><line x1='395.17857142857144' y1='74.96249530792235' x2='395.18857142857144' y2='74.96249530792235' class='ct-point' ct:value='554' opacity='1'></line><line x1='481.47321428571433' y1='67.16249591827392' x2='481.4832142857143' y2='67.16249591827392' class='ct-point' ct:value='586' opacity='1'></line><line x1='567.7678571428571' y1='39.862498054504385' x2='567.7778571428571' y2='39.862498054504385' class='ct-point' ct:value='698' opacity='1'></line><line x1='654.0625' y1='40.59374799728394' x2='654.0725' y2='40.59374799728394' class='ct-point' ct:value='695' opacity='1'></line></g><g class='ct-series ct-series-b'><path d='M50,193.669C78.765,186.762,107.53,172.95,136.295,172.95C165.06,172.95,193.824,175.144,222.589,175.144C251.354,175.144,280.119,156.645,308.884,151.5C337.649,146.355,366.414,143.903,395.179,140.044C423.943,136.185,452.708,133.614,481.473,128.344C510.238,123.073,539.003,104.287,567.768,103.969C596.533,103.65,625.298,103.644,654.063,103.481' class='ct-line'></path><line x1='50' y1='193.6687360191345' x2='50.01' y2='193.6687360191345' class='ct-point' ct:value='67' opacity='1'></line><line x1='136.29464285714286' y1='172.94998764038087' x2='136.30464285714285' y2='172.94998764038087' class='ct-point' ct:value='152' opacity='1'></line><line x1='222.58928571428572' y1='175.14373746871948' x2='222.5992857142857' y2='175.14373746871948' class='ct-point' ct:value='143' opacity='1'></line><line x1='308.88392857142856' y1='151.49998931884767' x2='308.89392857142855' y2='151.49998931884767' class='ct-point' ct:value='240' opacity='1'></line><line x1='395.17857142857144' y1='140.0437402153015' x2='395.18857142857144' y2='140.0437402153015' class='ct-point' ct:value='287' opacity='1'></line><line x1='481.47321428571433' y1='128.34374113082885' x2='481.4832142857143' y2='128.34374113082885' class='ct-point' ct:value='335' opacity='1'></line><line x1='567.7678571428571' y1='103.9687430381775' x2='567.7778571428571' y2='103.9687430381775' class='ct-point' ct:value='435' opacity='1'></line><line x1='654.0625' y1='103.48124307632446' x2='654.0725' y2='103.48124307632446' class='ct-point' ct:value='437' opacity='1'></line></g><g class='ct-series ct-series-c'><path d='M50,204.394C78.765,197.081,107.53,182.456,136.295,182.456C165.06,182.456,193.824,193.669,222.589,193.669C251.354,193.669,280.119,188.117,308.884,183.675C337.649,179.233,366.414,168.672,395.179,163.687C423.943,158.703,452.708,156.372,481.473,151.744C510.238,147.116,539.003,135.329,567.768,135.169C596.533,135.009,625.298,135.006,654.063,134.925' class='ct-line'></path><line x1='50' y1='204.3937351799011' x2='50.01' y2='204.3937351799011' class='ct-point' ct:value='23' opacity='1'></line><line x1='136.29464285714286' y1='182.4562368965149' x2='136.30464285714285' y2='182.4562368965149' class='ct-point' ct:value='113' opacity='1'></line><line x1='222.58928571428572' y1='193.6687360191345' x2='222.5992857142857' y2='193.6687360191345' class='ct-point' ct:value='67' opacity='1'></line><line x1='308.88392857142856' y1='183.67498680114747' x2='308.89392857142855' y2='183.67498680114747' class='ct-point' ct:value='108' opacity='1'></line><line x1='395.17857142857144' y1='163.68748836517335' x2='395.18857142857144' y2='163.68748836517335' class='ct-point' ct:value='190' opacity='1'></line><line x1='481.47321428571433' y1='151.74373929977418' x2='481.4832142857143' y2='151.74373929977418' class='ct-point' ct:value='239' opacity='1'></line><line x1='567.7678571428571' y1='135.16874059677124' x2='567.7778571428571' y2='135.16874059677124' class='ct-point' ct:value='307' opacity='1'></line><line x1='654.0625' y1='134.9249906158447' x2='654.0725' y2='134.9249906158447' class='ct-point' ct:value='308' opacity='1'></line></g></g><g class='ct-labels'><foreignObject style='overflow: visible;' x='50' y='214.99998474121094' width='86.29464285714286' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>9:00AM</span></foreignObject><foreignObject style='overflow: visible;' x='136.29464285714286' y='214.99998474121094' width='86.29464285714286' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>12:00AM</span></foreignObject><foreignObject style='overflow: visible;' x='222.58928571428572' y='214.99998474121094' width='86.29464285714283' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>3:00PM</span></foreignObject><foreignObject style='overflow: visible;' x='308.88392857142856' y='214.99998474121094' width='86.29464285714289' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>6:00PM</span></foreignObject><foreignObject style='overflow: visible;' x='395.17857142857144' y='214.99998474121094' width='86.29464285714289' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>9:00PM</span></foreignObject><foreignObject style='overflow: visible;' x='481.47321428571433' y='214.99998474121094' width='86.29464285714278' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>12:00PM</span></foreignObject><foreignObject style='overflow: visible;' x='567.7678571428571' y='214.99998474121094' width='86.29464285714289' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 86px; height: 20px;'>3:00AM</span></foreignObject><foreignObject style='overflow: visible;' x='654.0625' y='214.99998474121094' width='30' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 30px; height: 20px;'>6:00AM</span></foreignObject><foreignObject style='overflow: visible;' y='185.62498664855957' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>0</span></foreignObject><foreignObject style='overflow: visible;' y='161.2499885559082' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>100</span></foreignObject><foreignObject style='overflow: visible;' y='136.87499046325684' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>200</span></foreignObject><foreignObject style='overflow: visible;' y='112.49999237060547' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>300</span></foreignObject><foreignObject style='overflow: visible;' y='88.1249942779541' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>400</span></foreignObject><foreignObject style='overflow: visible;' y='63.749996185302734' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>500</span></foreignObject><foreignObject style='overflow: visible;' y='39.37499809265137' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>600</span></foreignObject><foreignObject style='overflow: visible;' y='15' x='10' height='24.374998092651367' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 24px; width: 30px;'>700</span></foreignObject><foreignObject style='overflow: visible;' y='-15' x='10' height='30' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 30px; width: 30px;'>800</span></foreignObject></g></svg></div></div> <div class='card-footer'><div class='legend'><i class='fa fa-circle text-info'></i> Open
              <i class='fa fa-circle text-danger'></i> Click
              <i class='fa fa-circle text-warning'></i> Click Second Time
            </div> <hr> <div class='stats'><i class='fa fa-history'></i> Updated 3 minutes ago
            </div></div></div></div> <div class='col-md-4'><div class='card'><div class='card-header'><h4 class='card-title'>Email Statistics</h4> <p class='card-category'>Last Campaign Performance</p></div> <div class='card-body'><div id='div_1444025779654' class='ct-chart'><svg xmlns:ct='http://gionkunz.github.com/chartist-js/ct' width='100%' height='100%' class='ct-chart-pie' style='width: 100%; height: 100%;'><g class='ct-series ct-series-a'><path d='M229.583,217.559A117.5,117.5,0,0,0,160.518,5L160.518,122.5Z' class='ct-slice-pie' ct:value='40'></path></g><g class='ct-series ct-series-b'><path d='M91.454,217.559A117.5,117.5,0,0,0,229.915,217.318L160.518,122.5Z' class='ct-slice-pie' ct:value='20'></path></g><g class='ct-series ct-series-c'><path d='M160.518,5A117.5,117.5,0,0,0,91.786,217.8L160.518,122.5Z' class='ct-slice-pie' ct:value='40'></path></g><g><text dx='216.3930298391132' dy='104.3452451298836' text-anchor='middle' class='ct-label'>40%</text><text dx='160.51846313476562' dy='181.2499885559082' text-anchor='middle' class='ct-label'>20%</text><text dx='104.64389643041804' dy='104.34524512988358' text-anchor='middle' class='ct-label'>40%</text></g></svg></div></div> <div class='card-footer'><div class='legend'><i class='fa fa-circle text-info'></i> Open
              <i class='fa fa-circle text-danger'></i> Bounce
              <i class='fa fa-circle text-warning'></i> Unsubscribe
            </div> <hr> <div class='stats'><i class='fa fa-clock-o'></i> Campaign sent 2 days ago
            </div></div></div></div></div> <div class='row'><div class='col-md-6'><div class='card' chart-responsive-options='screen and (max-width: 640px),[object Object]'><div class='card-header'><h4 class='card-title'>2014 Sales</h4> <p class='card-category'>All products including Taxes</p></div> <div class='card-body'><div id='div_788775915955' class='ct-chart'><svg xmlns:ct='http://gionkunz.github.com/chartist-js/ct' width='100%' height='245px' class='ct-chart-bar' style='width: 100%; height: 245px;'><g class='ct-grids'><line y1='209.99998474121094' y2='209.99998474121094' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='188.33331976996527' y2='188.33331976996527' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='166.6666547987196' y2='166.6666547987196' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='144.99998982747394' y2='144.99998982747394' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='123.3333248562283' y2='123.3333248562283' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='101.66665988498264' y2='101.66665988498264' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='79.99999491373697' y2='79.99999491373697' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='58.33332994249133' y2='58.33332994249133' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='36.666664971245666' y2='36.666664971245666' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line><line y1='15' y2='15' x1='50' x2='497.55682373046875' class='ct-grid ct-vertical'></line></g><g><g class='ct-series ct-series-a'><line x1='63.64820098876953' x2='63.64820098876953' y1='209.99998474121094' y2='92.56666059705947' class='ct-bar' ct:value='542' opacity='1'></line><line x1='100.9446029663086' x2='100.9446029663086' y1='209.99998474121094' y2='114.01665891859267' class='ct-bar' ct:value='443' opacity='1'></line><line x1='138.24100494384766' x2='138.24100494384766' y1='209.99998474121094' y2='140.6666568332248' class='ct-bar' ct:value='320' opacity='1'></line><line x1='175.53740692138672' x2='175.53740692138672' y1='209.99998474121094' y2='40.9999979654948' class='ct-bar' ct:value='780' opacity='1'></line><line x1='212.83380889892578' x2='212.83380889892578' y1='209.99998474121094' y2='90.18332745022244' class='ct-bar' ct:value='553' opacity='1'></line><line x1='250.13021087646484' x2='250.13021087646484' y1='209.99998474121094' y2='111.8499924214681' class='ct-bar' ct:value='453' opacity='1'></line><line x1='287.4266128540039' x2='287.4266128540039' y1='209.99998474121094' y2='139.36665693495007' class='ct-bar' ct:value='326' opacity='1'></line><line x1='324.72301483154297' x2='324.72301483154297' y1='209.99998474121094' y2='115.96665876600477' class='ct-bar' ct:value='434' opacity='1'></line><line x1='362.01941680908203' x2='362.01941680908203' y1='209.99998474121094' y2='86.93332770453559' class='ct-bar' ct:value='568' opacity='1'></line><line x1='399.3158187866211' x2='399.3158187866211' y1='209.99998474121094' y2='77.8333284166124' class='ct-bar' ct:value='610' opacity='1'></line><line x1='436.61222076416016' x2='436.61222076416016' y1='209.99998474121094' y2='46.199997558593736' class='ct-bar' ct:value='756' opacity='1'></line><line x1='473.9086227416992' x2='473.9086227416992' y1='209.99998474121094' y2='16.08333324856227' class='ct-bar' ct:value='895' opacity='1'></line></g><g class='ct-series ct-series-b'><line x1='73.64820098876953' x2='73.64820098876953' y1='209.99998474121094' y2='120.73332505967882' class='ct-bar' ct:value='412' opacity='1'></line><line x1='110.9446029663086' x2='110.9446029663086' y1='209.99998474121094' y2='157.349988861084' class='ct-bar' ct:value='243' opacity='1'></line><line x1='148.24100494384766' x2='148.24100494384766' y1='209.99998474121094' y2='149.33332282172307' class='ct-bar' ct:value='280' opacity='1'></line><line x1='185.53740692138672' x2='185.53740692138672' y1='209.99998474121094' y2='84.33332790798612' class='ct-bar' ct:value='580' opacity='1'></line><line x1='222.83380889892578' x2='222.83380889892578' y1='209.99998474121094' y2='111.8499924214681' class='ct-bar' ct:value='453' opacity='1'></line><line x1='260.13021087646484' x2='260.13021087646484' y1='209.99998474121094' y2='133.51665739271374' class='ct-bar' ct:value='353' opacity='1'></line><line x1='297.4266128540039' x2='297.4266128540039' y1='209.99998474121094' y2='144.99998982747394' class='ct-bar' ct:value='300' opacity='1'></line><line x1='334.72301483154297' x2='334.72301483154297' y1='209.99998474121094' y2='131.13332424587674' class='ct-bar' ct:value='364' opacity='1'></line><line x1='372.01941680908203' x2='372.01941680908203' y1='209.99998474121094' y2='130.2666576470269' class='ct-bar' ct:value='368' opacity='1'></line><line x1='409.3158187866211' x2='409.3158187866211' y1='209.99998474121094' y2='121.16665835910374' class='ct-bar' ct:value='410' opacity='1'></line><line x1='446.61222076416016' x2='446.61222076416016' y1='209.99998474121094' y2='72.19999552408854' class='ct-bar' ct:value='636' opacity='1'></line><line x1='483.9086227416992' x2='483.9086227416992' y1='209.99998474121094' y2='59.4166631910536' class='ct-bar' ct:value='695' opacity='1'></line></g></g><g class='ct-labels'><foreignObject style='overflow: visible;' x='50' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Jan</span></foreignObject><foreignObject style='overflow: visible;' x='87.29640197753906' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Feb</span></foreignObject><foreignObject style='overflow: visible;' x='124.59280395507812' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Mar</span></foreignObject><foreignObject style='overflow: visible;' x='161.8892059326172' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Apr</span></foreignObject><foreignObject style='overflow: visible;' x='199.18560791015625' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Mai</span></foreignObject><foreignObject style='overflow: visible;' x='236.4820098876953' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Jun</span></foreignObject><foreignObject style='overflow: visible;' x='273.7784118652344' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Jul</span></foreignObject><foreignObject style='overflow: visible;' x='311.07481384277344' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Aug</span></foreignObject><foreignObject style='overflow: visible;' x='348.3712158203125' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Sep</span></foreignObject><foreignObject style='overflow: visible;' x='385.66761779785156' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Oct</span></foreignObject><foreignObject style='overflow: visible;' x='422.9640197753906' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Nov</span></foreignObject><foreignObject style='overflow: visible;' x='460.2604217529297' y='214.99998474121094' width='37.29640197753906' height='20'><span class='ct-label ct-horizontal ct-end' xmlns='http://www.w3.org/2000/xmlns/' style='width: 37px; height: 20px;'>Dec</span></foreignObject><foreignObject style='overflow: visible;' y='188.33331976996527' x='10' height='21.66666497124566' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>0</span></foreignObject><foreignObject style='overflow: visible;' y='166.6666547987196' x='10' height='21.66666497124566' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>100</span></foreignObject><foreignObject style='overflow: visible;' y='144.99998982747394' x='10' height='21.666664971245666' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>200</span></foreignObject><foreignObject style='overflow: visible;' y='123.33332485622829' x='10' height='21.666664971245652' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>300</span></foreignObject><foreignObject style='overflow: visible;' y='101.66665988498264' x='10' height='21.666664971245666' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>400</span></foreignObject><foreignObject style='overflow: visible;' y='79.99999491373697' x='10' height='21.666664971245666' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>500</span></foreignObject><foreignObject style='overflow: visible;' y='58.33332994249133' x='10' height='21.666664971245638' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>600</span></foreignObject><foreignObject style='overflow: visible;' y='36.666664971245666' x='10' height='21.666664971245666' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>700</span></foreignObject><foreignObject style='overflow: visible;' y='15' x='10' height='21.666664971245666' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 22px; width: 30px;'>800</span></foreignObject><foreignObject style='overflow: visible;' y='-15' x='10' height='30' width='30'><span class='ct-label ct-vertical ct-start' xmlns='http://www.w3.org/2000/xmlns/' style='height: 30px; width: 30px;'>900</span></foreignObject></g></svg></div></div> <div class='card-footer'><div class='legend'><i class='fa fa-circle text-info'></i> Tesla Model S
              <i class='fa fa-circle text-danger'></i> BMW 5 Series
            </div> <hr> <div class='stats'><i class='fa fa-check'></i> Data information certified
            </div></div></div></div> <div class='col-md-6'><div class='card'><!----> <div class='card-header'><h5 class='title'>Tasks</h5> <p class='category'>Backend development</p></div> <div class='card-body'> <table class='table'><thead></thead> <tbody><tr><td><div class='form-check'><label for='9fd87140f89c8' class='form-check-label'><input id='9fd87140f89c8' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Sign contract for 'What are conference organizers afraid of?'</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr><tr><td><div class='form-check'><label for='8dcb57b477f1f' class='form-check-label'><input id='8dcb57b477f1f' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Lines From Great Russian Literature? Or E-mails From My Boss?</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr><tr><td><div class='form-check'><label for='14d1840d8557a' class='form-check-label'><input id='14d1840d8557a' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr><tr><td><div class='form-check'><label for='e45f30fa8b5f9' class='form-check-label'><input id='e45f30fa8b5f9' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Create 4 Invisible User Experiences you Never Knew About</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr><tr><td><div class='form-check'><label for='981f0cd12380b' class='form-check-label'><input id='981f0cd12380b' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Read 'Following makes Medium better'</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr><tr><td><div class='form-check'><label for='dbeb474f568ac' class='form-check-label'><input id='dbeb474f568ac' type='checkbox' class='form-check-input'> <span class='form-check-sign'></span></label> <span></span></div></td> <td>Unfollow 5 enemies from twitter</td> <td class='td-actions text-right'><button type='button' class='btn-simple btn btn-xs btn-info has-tooltip'><i class='fa fa-edit'></i></button> <button type='button' class='btn-simple btn btn-xs btn-danger has-tooltip'><i class='fa fa-times'></i></button></td></tr></tbody></table> <div class='footer'><hr> <div class='stats'><i class='fa fa-history'></i> Updated 3 minutes ago
            </div></div></div> <!----></div></div></div></div></div>
			
			
			</template>

  <script type='module' src='src/pages/home/dashboard.js'></script>

";

   echo html_entity_decode($HThome, ENT_NOQUOTES, "UTF-8");


?>
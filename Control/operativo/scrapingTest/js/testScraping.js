const puppeteer = require('puppeteer')

( async ()=>{
	const browser = await puppeteer.launch()
	
	const page =  await browser.newPage() 
	
	await page.goto('https://www.runt.com.co/consultaCiudadana/#/consultaVehiculo')
	
	await page.screenshot({ path: 'pdfWebServicesApp/capturaPantalla.jpg' })
	
	await browser.close()
	 
})()
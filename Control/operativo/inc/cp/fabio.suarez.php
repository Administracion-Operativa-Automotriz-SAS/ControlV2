<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPSczJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzEwOCc7X1NFU1NJT05bJ05vbWJyZSddPSdGYWJpbyBzdWFyZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FwdHVyYSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDT05UUk9MIE9QRVJBVElWTyc=' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPSc0JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzI2Myc7X1NFU1NJT05bJ05vbWJyZSddPSdGYWJpbyBTdWFyZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FsbGNlbnRlcic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDQUxMIENFTlRFUic=' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPSc1JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzEyNyc7X1NFU1NJT05bJ05vbWJyZSddPSdGQUJJTyBORUxTT04gU1VBUkVaIEdPTUVaJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPSc2JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzEwNic7X1NFU1NJT05bJ05vbWJyZSddPSdGQUJJTyBORUxTT04gU1VBUkVaIEdPTUVaJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2ZhY3R1cmFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0ZBQ1RVUkFDSU9OJw==' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPSc3JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzE3JztfU0VTU0lPTlsnTm9tYnJlJ109J0ZhYmlvIFN1YXJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19vcGVyYXRpdm8nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nSkVGRSBPUEVSQVRJVk8n' >JEFE OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPScxMCc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxNjYnO19TRVNTSU9OWydOb21icmUnXT0nRmFiaW8gU3VhcmV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX29maWNpbmEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nRElSRUNUT1IgT0ZJQ0lOQSc=' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPScxNSc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxMSc7X1NFU1NJT05bJ05vbWJyZSddPSdGYWJpbyBTdWFyZXMgJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2FwcG1vdmlsJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0FQUCBNT1ZJTCc=' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPScyMyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPSc2MSc7X1NFU1NJT05bJ05vbWJyZSddPSdGQUJJTyBORUxTT04nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J29wZXJhcmlvJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J09QRVJBUklPIEZMT1RBUyc=' >OPERARIO FLOTAS</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nZmFiaW8uc3VhcmV6JztfU0VTU0lPTlsnVXNlciddPScyNyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNCc7X1NFU1NJT05bJ05vbWJyZSddPSdGQUJJTyBORUxTT04gU1VBUkVaIEdPTUVaJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2plZmVmbG90YSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdKRUZBVFVSQSBERSBGTE9UQVMn' >JEFATURA DE FLOTAS</option>";
				echo "</select>";
			?>
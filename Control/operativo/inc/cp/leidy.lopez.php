<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzMnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTQ3JztfU0VTU0lPTlsnTm9tYnJlJ109J0xleWRpIGxvcGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2NhcHR1cmEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQ09OVFJPTCBPUEVSQVRJVk8n' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzQnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTIxJztfU0VTU0lPTlsnTm9tYnJlJ109J0xlaWR5IFlvaGFuYSBMb3BleiBCb2JhZGlsbGEnO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FsbGNlbnRlcic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDQUxMIENFTlRFUic=' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzUnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMzE2JztfU0VTU0lPTlsnTm9tYnJlJ109J0xleWRpIGxvcGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzYnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjkxJztfU0VTU0lPTlsnTm9tYnJlJ109J0xleWRpIGxvcGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2ZhY3R1cmFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0ZBQ1RVUkFDSU9OJw==' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzknO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTInO19TRVNTSU9OWydOb21icmUnXT0nTGV5ZGkgbG9wZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY29udGFkb3InO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQ09OVEFET1In' >CONTADOR</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzEwJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzI1OCc7X1NFU1NJT05bJ05vbWJyZSddPSdMZXlkaSBsb3Bleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19vZmljaW5hJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0RJUkVDVE9SIE9GSUNJTkEn' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nbGVpZHkubG9wZXonO19TRVNTSU9OWydVc2VyJ109JzM0JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzIxJztfU0VTU0lPTlsnTm9tYnJlJ109J0xleWRpIGxvcGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2NvbnN1bHRhJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NPTlNVTFRBIFJFU1RSSU5HSURBIDEn' >CONSULTA RESTRINGIDA 1</option>";
				echo "</select>";
			?>
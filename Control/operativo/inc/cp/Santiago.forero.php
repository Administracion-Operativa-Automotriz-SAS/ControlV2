<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPSczJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzE2Nic7X1NFU1NJT05bJ05vbWJyZSddPSdTYW50aWFnbyBGb3Jlcm8nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FwdHVyYSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDT05UUk9MIE9QRVJBVElWTyc=' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPSc0JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzMwNSc7X1NFU1NJT05bJ05vbWJyZSddPSdTYW50aWFnbyBGb3Jlcm8nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FsbGNlbnRlcic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDQUxMIENFTlRFUic=' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPSc1JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzMzOCc7X1NFU1NJT05bJ05vbWJyZSddPSdTYW50aWFnbyBGb3Jlcm8nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fYXV0b3JpemFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0FVVE9SSVpBQ0lPTkVTJw==' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPSc2JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzMxMSc7X1NFU1NJT05bJ05vbWJyZSddPSdTYW50aWFnbyBGb3Jlcm8nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fZmFjdHVyYWNpb24nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nRkFDVFVSQUNJT04n' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPScxMCc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNzknO19TRVNTSU9OWydOb21icmUnXT0nU2FudGlhZ28gRm9yZXJvJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX29maWNpbmEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nRElSRUNUT1IgT0ZJQ0lOQSc=' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPScxNSc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyMDMnO19TRVNTSU9OWydOb21icmUnXT0nU2FudGlhZ28gRm9yZXJvJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2FwcG1vdmlsJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0FQUCBNT1ZJTCc=' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nU2FudGlhZ28uZm9yZXJvJztfU0VTU0lPTlsnVXNlciddPScyMyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPSczMDQnO19TRVNTSU9OWydOb21icmUnXT0nU0FOVElBR08nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J29wZXJhcmlvJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J09QRVJBUklPIEZMT1RBUyc=' >OPERARIO FLOTAS</option>";
				echo "</select>";
			?>
<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzMnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nNTMnO19TRVNTSU9OWydOb21icmUnXT0nTHVpc2EgQ2FyZGVuYXMnO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FwdHVyYSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDT05UUk9MIE9QRVJBVElWTyc=' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzQnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTczJztfU0VTU0lPTlsnTm9tYnJlJ109J0x1aXNhIEZlcm5hbmRhIENhcmRlbmFzIFVycmVnbyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jYWxsY2VudGVyJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NBTEwgQ0VOVEVSJw==' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzUnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTY0JztfU0VTU0lPTlsnTm9tYnJlJ109J0x1aXNhIENhcmRlbmFzJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzYnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTQzJztfU0VTU0lPTlsnTm9tYnJlJ109J0x1aXNhIENhcmRlbmFzJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2ZhY3R1cmFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0ZBQ1RVUkFDSU9OJw==' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzcnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjQnO19TRVNTSU9OWydOb21icmUnXT0nTHVpc2EgQ2FyZGVuYXMnO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fb3BlcmF0aXZvJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0pFRkUgT1BFUkFUSVZPJw==' >JEFE OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzMyJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzUzJztfU0VTU0lPTlsnTm9tYnJlJ109J0x1aXNhIENhcmRlbmFzJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX3JlY2VwY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdSRUNFUENJT04gWSBTRVJWSUNJTyBBTCBDTElFTlRFJw==' >RECEPCION Y SERVICIO AL CLIENTE</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nTFVJU0EuQ0FSREVOQVMnO19TRVNTSU9OWydVc2VyJ109JzM0JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzgnO19TRVNTSU9OWydOb21icmUnXT0nTHVzYSBDYXJkZW5hcyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jb25zdWx0YSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDT05TVUxUQSBSRVNUUklOR0lEQSAxJw==' >CONSULTA RESTRINGIDA 1</option>";
				echo "</select>";
			?>
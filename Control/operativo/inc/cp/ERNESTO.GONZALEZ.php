<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nMyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPSczOSc7X1NFU1NJT05bJ05vbWJyZSddPSdKb3NlIEVybmVzdG8gZ29uemFsZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fY2FwdHVyYSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdDT05UUk9MIE9QRVJBVElWTyc=' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nNCc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNjAnO19TRVNTSU9OWydOb21icmUnXT0nRXJuZXN0byBHb256YWxleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jYWxsY2VudGVyJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NBTEwgQ0VOVEVSJw==' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nNSc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxMDEnO19TRVNTSU9OWydOb21icmUnXT0nSm9zZSBFcm5lc3RvIEdvbnphbGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nNic7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPSc2NSc7X1NFU1NJT05bJ05vbWJyZSddPSdKb3NlIEVybmVzdG8gR29uemFsZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fZmFjdHVyYWNpb24nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nRkFDVFVSQUNJT04n' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nNyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxMic7X1NFU1NJT05bJ05vbWJyZSddPSdKT1NFIEVSTkVTVE8gR09OWkFMRVogTlVTVEVTJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX29wZXJhdGl2byc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdKRUZFIE9QRVJBVElWTyc=' >JEFE OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nMTAnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nODUnO19TRVNTSU9OWydOb21icmUnXT0nSm9zZSBFcm5lc3RvIEdvbnphbGV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX29maWNpbmEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nRElSRUNUT1IgT0ZJQ0lOQSc=' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nMTUnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTM1JztfU0VTU0lPTlsnTm9tYnJlJ109J0VybmVzdG8gR29uemFsZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fYXBwbW92aWwnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQVBQIE1PVklMJw==' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nMjYnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMzMnO19TRVNTSU9OWydOb21icmUnXT0nRXJuZXN0byBHb256YWxleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jb29yZGNjJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NPT1JESU5BRE9SIENBTExDRU5URVIn' >COORDINADOR CALLCENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nRVJORVNUTy5HT05aQUxFWic7X1NFU1NJT05bJ1VzZXInXT0nMjcnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMzMnO19TRVNTSU9OWydOb21icmUnXT0nSk9TRSBFUk5FU1RPIEdPTlpBTEVaIE5VU1RFUyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19qZWZlZmxvdGEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nSkVGQVRVUkEgREUgRkxPVEFTJw==' >JEFATURA DE FLOTAS</option>";
				echo "</select>";
			?>
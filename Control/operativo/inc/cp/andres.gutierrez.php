<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxMTAnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIEd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jYXB0dXJhJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NPTlRST0wgT1BFUkFUSVZPJw==' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nNCc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNjUnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIGd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jYWxsY2VudGVyJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NBTEwgQ0VOVEVSJw==' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nNSc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNzInO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIEd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19hdXRvcml6YWNpb24nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQVVUT1JJWkFDSU9ORVMn' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nNic7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNTMnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIEd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19mYWN0dXJhY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdGQUNUVVJBQ0lPTic=' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nNyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyNic7X1NFU1NJT05bJ05vbWJyZSddPSdBbmRyZXMgR3V0aWVycmV6JztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX29wZXJhdGl2byc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdKRUZFIE9QRVJBVElWTyc=' >JEFE OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMTAnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjIyJztfU0VTU0lPTlsnTm9tYnJlJ109J0FuZHJlcyBHdXRpZXJyZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fb2ZpY2luYSc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdESVJFQ1RPUiBPRklDSU5BJw==' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMTUnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTQ5JztfU0VTU0lPTlsnTm9tYnJlJ109J0FuZHJlcyBndXRpZXJyZXonO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fYXBwbW92aWwnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQVBQIE1PVklMJw==' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMjMnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjQ5JztfU0VTU0lPTlsnTm9tYnJlJ109J0FORFJFUyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0nb3BlcmFyaW8nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nT1BFUkFSSU8gRkxPVEFTJw==' >OPERARIO FLOTAS</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMjYnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMzcnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIGd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jb29yZGNjJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NPT1JESU5BRE9SIENBTExDRU5URVIn' >COORDINADOR CALLCENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nMjcnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nNjAnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIGd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19qZWZlZmxvdGEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nSkVGQVRVUkEgREUgRkxPVEFTJw==' >JEFATURA DE FLOTAS</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nYW5kcmVzLmd1dGllcnJleic7X1NFU1NJT05bJ1VzZXInXT0nNDAnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjEnO19TRVNTSU9OWydOb21icmUnXT0nQW5kcmVzIEd1dGllcnJleic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb190cmFtaXRlX3ZoJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J1RSQU1JVEUgREUgVkVISUNVTE9TJw==' >TRAMITE DE VEHICULOS</option>";
				echo "</select>";
			?>
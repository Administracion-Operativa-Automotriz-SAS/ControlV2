<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPSczJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109Jzg0JztfU0VTU0lPTlsnTm9tYnJlJ109J0pvc2UgR2lyYWxkbyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19jYXB0dXJhJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0NPTlRST0wgT1BFUkFUSVZPJw==' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPSc1JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzIzMSc7X1NFU1NJT05bJ05vbWJyZSddPSdKb3NlIE1hdGVvIEdpcmFsZG8gUmVuZG9uJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPSc2JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzIxNCc7X1NFU1NJT05bJ05vbWJyZSddPSdKb3NlIE1hdGVvIEdpcmFsZG8gUmVuZG9uJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2ZhY3R1cmFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0ZBQ1RVUkFDSU9OJw==' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPSc3JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzI1JztfU0VTU0lPTlsnTm9tYnJlJ109J0pvc2UgTWF0ZW8gR2lyYWxkbyBSZW5kb24nO19TRVNTSU9OWydUYWJsYV91c3VhcmlvJ109J3VzdWFyaW9fb3BlcmF0aXZvJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0pFRkUgT1BFUkFUSVZPJw==' >JEFE OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPScxMCc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxODInO19TRVNTSU9OWydOb21icmUnXT0nSm9zZSBNYXRlbyBHaXJhbGRvIFJlbmRvbic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19vZmljaW5hJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0RJUkVDVE9SIE9GSUNJTkEn' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPScxNSc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScxMTQnO19TRVNTSU9OWydOb21icmUnXT0nSm9zZSBNYXRlbyBHaXJhbGRvIFJlbmRvbic7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19hcHBtb3ZpbCc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBUFAgTU9WSUwn' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSm9zZS5naXJhbGRvJztfU0VTU0lPTlsnVXNlciddPScyMyc7X1NFU1NJT05bJ0Rpc2VuYWRvciddPScwJztfU0VTU0lPTlsnSWRfYWx0ZXJubyddPScyMTYnO19TRVNTSU9OWydOb21icmUnXT0nSk9TRSBNQVRFTyc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0nb3BlcmFyaW8nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nT1BFUkFSSU8gRkxPVEFTJw==' >OPERARIO FLOTAS</option>";
				echo "</select>";
			?>
<?php
			/*Archvio inicial */
			echo "<select name='_cs_' id='_cs_' style='width:100px' onchange=\"crea_perfil(this.value,'_top')\"><option value=''>Cambiar Perfil</option>"; echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzMnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMTMwJztfU0VTU0lPTlsnTm9tYnJlJ109J0hlbnJ5IFN1YXphJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2NhcHR1cmEnO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQ09OVFJPTCBPUEVSQVRJVk8n' >CONTROL OPERATIVO</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzQnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjg0JztfU0VTU0lPTlsnTm9tYnJlJ109J0hlbnJ5IFN1YXphJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2NhbGxjZW50ZXInO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nQ0FMTCBDRU5URVIn' >CALL CENTER</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzUnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjk1JztfU0VTU0lPTlsnTm9tYnJlJ109J0hlbnJ5IFN1YXphJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2F1dG9yaXphY2lvbic7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBVVRPUklaQUNJT05FUyc=' >AUTORIZACIONES</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzYnO19TRVNTSU9OWydEaXNlbmFkb3InXT0nMCc7X1NFU1NJT05bJ0lkX2FsdGVybm8nXT0nMjczJztfU0VTU0lPTlsnTm9tYnJlJ109J0hlbnJ5IFN1YXphJztfU0VTU0lPTlsnVGFibGFfdXN1YXJpbyddPSd1c3VhcmlvX2ZhY3R1cmFjaW9uJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0ZBQ1RVUkFDSU9OJw==' >FACTURACION</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzEwJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzI0MSc7X1NFU1NJT05bJ05vbWJyZSddPSdIZW5yeSBTdWF6YSc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19vZmljaW5hJztfU0VTU0lPTlsnRW1haWwnXT0nJztfU0VTU0lPTlsnTmdydXBvJ109J0RJUkVDVE9SIE9GSUNJTkEn' >DIRECTOR OFICINA</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzE1JztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzE3NSc7X1NFU1NJT05bJ05vbWJyZSddPSdIZW5yeSBTdWF6YSc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpb19hcHBtb3ZpbCc7X1NFU1NJT05bJ0VtYWlsJ109Jyc7X1NFU1NJT05bJ05ncnVwbyddPSdBUFAgTU9WSUwn' >APP MOVIL</option>";
				echo "<option value='X1NFU1NJT05bJ05pY2snXT0nSEVOUlkuU1VBWkEnO19TRVNTSU9OWydVc2VyJ109JzIzJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzAnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzI3MSc7X1NFU1NJT05bJ05vbWJyZSddPSdIRU5SWSc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0nb3BlcmFyaW8nO19TRVNTSU9OWydFbWFpbCddPScnO19TRVNTSU9OWydOZ3J1cG8nXT0nT1BFUkFSSU8gRkxPVEFTJw==' >OPERARIO FLOTAS</option>";
				echo "</select>";
			?>
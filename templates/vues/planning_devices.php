<div id="calendarDevices" class="ui-corner-all ui-widget ui-widget-content">
	<table id="calendarDevices_table" class="calendar">
		<thead>
			<!--Entêtes du planning contenant les dates-->
			<tr>
				<th id="weekbeforeDevices" name="weekbeforeDevices" class="ui-widget-header ui-corner-all clickable"><i class="fa fa-chevron-circle-left fa-2x"></i></th>
				<?php
					$jour = array('Lun','Mar','Mer','Jeu','Ven','Sam','Dim');
					for ($thead=0; $thead<7; ++$thead){
						echo '<th id="day_'.$thead.'" class="ui-widget-header ui-corner-all thead">'.$Week_start->format('D').' '.$Week_start->format('d-M-Y').'</th>';//$Week_start->format('D')$jour[$thead]
						$Week_start->modify('+1 day');
					}
				?>
				<th id="weekafterDevices" name="weekafterDevices" class="ui-widget-header ui-corner-all clickable"><i class="fa fa-chevron-circle-right fa-2x"></i></th>
			</tr>
		</thead>
		<tbody>
			<?php
			//Modif Paul
				// Création dynamique du planning
				for ($rowindex=0; $rowindex<count($list_devices);++$rowindex) {
					// Création de la ligne <tr></tr> pour chaque devices
					echo '<tr id="row_'.$rowindex.'"class="ui-corner-all ui-state-default">';
					
					// Construction de la case pour chaque devices
					echo '<td id="device_row_'.$rowindex.'" class="ui-widget-header ui-corner-all"><div id="device_'.$rowindex.'" class="casedevice">'.$list_devices[$rowindex]['modele'].'<br/>'.$list_devices[$rowindex]['identifiant'].'</div></td>';
					
					// Construction planning en fonction des évènements dans la bdd
					// 7 jours donc 7 colonnes
					for ($colindex=0; $colindex<7; ++$colindex){
						$key_am=NULL;
						$key_pm=NULL;
						$key_am_pm=NULL;
						$draggable=0;
						foreach($list_eventsDevices as $key=>$events)
						{
							if ($events['date_start'] < $DateCompare->format('Y-m-d') && $events['date_end'] > $DateCompare->format('Y-m-d') && $events['appareil_id'] == $list_devices[$rowindex]['identifiant'])
							{//evenement sur plusieur jour en cours
								$key_am_pm=$key;
								$draggable=0;
							}
							if ($events['date_start'] == $DateCompare->format('Y-m-d') && $events['date_end'] > $DateCompare->format('Y-m-d') && $events['appareil_id'] == $list_devices[$rowindex]['identifiant'])
							{//debut d'un evenement sur plusieur jour
								if ($events['am_pm_start']=='pm'){
									$key_pm=$key;
								}else{
									$key_am_pm=$key;
									$draggable=1;
								}
							}	
							if ($events['date_start'] < $DateCompare->format('Y-m-d') && $events['date_end'] == $DateCompare->format('Y-m-d') && $events['appareil_id'] == $list_devices[$rowindex]['identifiant'])
							{//fin d'un evenement sur plusieur jour
								if ($events['am_pm_end']=='am'){
									$key_am=$key;
								}else{
									$key_am_pm=$key;
									$draggable=0;
								}
							}	
							if ($events['date_start'] == $DateCompare->format('Y-m-d') && $events['date_end'] == $DateCompare->format('Y-m-d') && $events['appareil_id'] == $list_devices[$rowindex]['identifiant'])
							{//evenement sur un jour
								if ($events['am_pm_start']=='am' && $events['am_pm_end']=='am'){
									$key_am=$key;
								}elseif($events['am_pm_start']=='pm' && $events['am_pm_end']=='pm'){
									$key_pm=$key;
								}else{
									$key_am_pm=$key;
									$draggable=1;
								}
							}							
						}
						echo '<td >';
						if(isset($key_am_pm) && !isset($key_am) && !isset($key_pm))
						{//journée complète
							if($draggable==1){
								echo '<div id="idbdd_'.$list_eventsDevices[$key_am_pm]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_am_pm" class="ui-corner-all ui-state-highlight ui-draggable ui-modify-device case_am_pm"><br/><span class="resafull">'.$list_eventsDevices[$key_am_pm]['utilisateur'].'<br/>'.$list_eventsDevices[$key_am_pm]['client'].' '.$list_eventsDevices[$key_am_pm]['ville'].'</span><br/><span class="memofull">'.$list_eventsDevices[$key_am_pm]['commentaire'].'</span></div>';
							}else{
								echo '<div id="idbdd_'.$list_eventsDevices[$key_am_pm]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_am_pm" class="ui-corner-all ui-state-highlight ui-modify-device case_am_pm"><br/><span class="resafull">'.$list_eventsDevices[$key_am_pm]['utilisateur'].'<br/>'.$list_eventsDevices[$key_am_pm]['client'].' '.$list_eventsDevices[$key_am_pm]['ville'].'</span><br/><span class="memofull">'.$list_eventsDevices[$key_am_pm]['commentaire'].'</span></div>';
							}
						}elseif(!isset($key_am_pm) && isset($key_am) && !isset($key_pm))
						{//Matinée				
							echo '<div id="idbdd_'.$list_eventsDevices[$key_am]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_am" class="ui-corner-all ui-state-highlight ui-modify-device case_am_only"><span class="resahalf">'.$list_eventsDevices[$key_am]['utilisateur'].'<br/>'.$list_eventsDevices[$key_am]['client'].' '.$list_eventsDevices[$key_am]['ville'].'</span><br/><span class="memohalf">'.$list_eventsDevices[$key_am]['commentaire'].'</span></div>';
							echo '<div id="day_'.$colindex.'_row_'.$rowindex.'_pm" class="ui-corner-all ui-state-default ui-create-device case_half_empty"><span class="resa">Après Midi<br/>Libre</span></div>';
						}elseif(!isset($key_am_pm) && !isset($key_am) && isset($key_pm))
						{//Aprés midi
							echo '<div id="day_'.$colindex.'_row_'.$rowindex.'_am" class="ui-corner-all ui-state-default ui-create-device case_half_empty"><span class="resa">Matinée <br/> Libre</span></div>';
							echo '<div id="idbdd_'.$list_eventsDevices[$key_pm]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_pm" class="ui-corner-all ui-state-highlight ui-modify-device case_pm_only"><span class="resahalf">'.$list_eventsDevices[$key_pm]['utilisateur'].'<br/>'.$list_eventsDevices[$key_pm]['client'].' '.$list_eventsDevices[$key_pm]['ville'].'</span><br/><span class="memohalf">'.$list_eventsDevices[$key_pm]['commentaire'].'</span></div>';
						}elseif(!isset($key_am_pm) && isset($key_am) && isset($key_pm))
						{//Matinée + Aprés midi
							echo '<div id="idbdd_'.$list_eventsDevices[$key_am]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_am" class="ui-corner-all ui-state-highlight ui-modify-device case_am_only"><span class="resahalf">'.$list_eventsDevices[$key_am]['utilisateur'].'<br/>'.$list_eventsDevices[$key_am]['client'].' '.$list_eventsDevices[$key_am]['ville'].'</span><br/><span class="memohalf">'.$list_eventsDevices[$key_am]['commentaire'].'</span></div>';
							echo '<div id="idbdd_'.$list_eventsDevices[$key_pm]['id_events'].'_day_'.$colindex.'_row_'.$rowindex.'_pm" class="ui-corner-all ui-state-highlight ui-modify-device case_am_only"><span class="resahalf">'.$list_eventsDevices[$key_pm]['utilisateur'].'<br/>'.$list_eventsDevices[$key_pm]['client'].' '.$list_eventsDevices[$key_pm]['ville'].'</span><br/><span class="memohalf">'.$list_eventsDevices[$key_pm]['commentaire'].'</span></div>';
						}elseif(!isset($key_am_pm) && !isset($key_am) && !isset($key_pm))
						{//journée libre
							echo '<div id="day_'.$colindex.'_row_'.$rowindex.'" class="ui-corner-all ui-state-default ui-create-device ui-droppable case_full_empty"><br/><br/>Libre</div>';
						}else{//conflit
							echo "conflit de réservation!";
						}
						echo '</td>';
						// Ajourt + 1 jour à DateCompare	
						$DateCompare->modify('+1 day');
					}
					// Ré-init DateCompare
					$DateCompare->modify('-7 day');
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
</div>

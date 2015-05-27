<?php

    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMe Google Maps Display Language File
    // Version: 1.21
    // Date:    02/09/2009
    // 
    //
    // TrackMe built by Staryon
    // For more information go to:
    // http://forum.xda-developers.com/showthread.php?t=340667
    //
    // Please feel free to modify the files to meet your needs.
    // Post comments and questions to the forum thread above.
    //
    //////////////////////////////////////////////////////////////////////////////

    $version_text                            = "1.21";

    if($language == "german")
    {
        $title_text                          	= "TrackMe Anzeige";
        $trip_button_text                    	= "Zeige Strecke";
        $location_button_text                	= "Zeige letzte Position";
	$location_button_text_off            	= "Live Tracking aus";
        $filter_button_text                  	= "Setze Wegpunktfilter";
        $filter_none_text                    	= "Alle Punkte";
        $filter_photo_comment_text           	= "Foto und Kommentar";
        $filter_photo_text                   	= "Foto";
        $filter_comment_text                 	= "Kommentar";
        $filter_last_20		                = "Die letzen 20 Punkte";
        $filter_daterange	                = "Zeige Datenbereich";
        $footer_text                         	= "Die Tracking Information wurde bereitgestellt von ";
        $incomplete_install_text             	= "Installation wurde nicht erfolgreich beendet! Die Intallationsdateien index.php und database.sql befinden sich noch im TrackMe Ordner. Um die Kartenanzeige zu aktivieren, bitte die Dateien install.php und database.sql aus dem TrackMe Ordner entfernen.";
        $no_data_text                        	= "Eine oder mehrere Tabellen in der Datenbank sind leer. TrackMe muss mindestens einmal auf dme Mobilen  Ger&#228;t gelaufen sein.";
        $database_fail_text                  	= "Fehler bei Verbindung zur Datenbank. Die Anzeige wurde abgebrochen.";
        $trip_none_text                      	= "keine";
        $trip_any_text                       	= "beliebige";
        $display_options_title_text          	= "Eigene Anzeige Einstellungen";
        $display_header_text              	= "Zeige Header";
	$display_showbearing_text	 	= "Zeige Richtungspfeile";
	$display_crosshair_text              	= "Zeige Fadenkreuz";
        $display_clickcenter_text            	= "Klick zum Zentrieren";
        $display_overview_text               	= "Zeige &Uuml;bersicht";
        $display_language_text               	= "W&auml;hle Sprache";
        $display_units_text                  	= "W&auml;hle Einheiten";
        $display_button_text                 	= "&Auml;nderungen aktivieren";
	$startdate_text				= "Von:";
	$enddate_text				= "Bis:";
	$trip_title				= "Strecke:";
	$filter_title				= "Filter:";
	$date_title				= "Zeitraum:";
        $tripsummary_title			= "Streckendaten";
        $tripstatus_title			= "Strecke";
	$summary_time				= "Zeit:";
	$summary_photos				= "Fotos:";
	$summary_comments			= "Kommentare:";
        // Public Display Information
        $user_button_text                    	= "Zeige Benutzer";
	$showconfig_button_text		 	= "Zeige Einstellungen";
	$showconfig_button_text_off	 	= "Schliesse Einstellungen";
        // Private Display Information
        $page_private			     	= "Page has been set to private. User ID and password required for access.";//trackmeIT
	$trip_data			     	= "Trip data for:"; //trackmeIT
        $login_text                          	= "Benutzername";
        $password_text                       	= "Passwort";
        $login_button_text                   	= "Anmelden";

        // Balloon Fields Information
        $user_balloon_text                   	= "Benutzer";
        $trip_balloon_text                   	= "Strecke";
        $time_balloon_text                   	= "Zeit";
        $speed_balloon_text                  	= "Geschwindigkeit";
        $altitude_balloon_text               	= "H&#246;he";
        $total_time_balloon_text             	= "Gesamtzeit";
        $avg_speed_balloon_text              	= "Durchschnittstempo";
        $total_distance_balloon_text         	= "Entfernung";
        $point_balloon_text                  	= "Punkt";
        $comment_balloon_text                	= "Kommentar";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "Meilen";
        $height_imperial_unit_balloon_text   	= "Fuss";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text     	= "Meter";
    }
    elseif($language == "french")
    {
        $title_text                          	= "Affichage TrackMe";
        $trip_button_text                    	= "Montrer Trajet";
        $location_button_text                	= "La derni&egrave;re position";
	$location_button_text_off            	= "Live Tracking Off";
        $filter_button_text                  	= "Appliquer le filtre";
        $filter_none_text                    	= "Tous les points";
        $filter_photo_comment_text           	= "Photos ou comentaire";
        $filter_photo_text                   	= "Photos";
        $filter_comment_text                 	= "Commentaire";
        $filter_last_20		                = "Les 20 derniers points";
        $filter_daterange	                = "Montrer pour les dates choisies";
        $footer_text                         	= "Suivi fourni par";
        $incomplete_install_text             	= "Installation non termin&egrave;e ! Les fichiers install.php et database.sql sont toujours pr&egrave;sents dans le dossier TrackMe. Effacez install.php et database.sql dans le dossier TrackMe pour afficher la carte.";
        $no_data_text                        	= "Une ou plusieurs tables TrackMe sont vides. Vous devez envoyer des positions depuis votre appareil Windows Mobile pour que l'affichage de la carte fonctionne correctement.";
        $database_fail_text                  	= "Echec de connexion &agrave; la base de donn&egrave;es. Affichage annul&egrave;.";
        $trip_none_text                      	= "Aucun";
        $trip_any_text                       	= "Tous";
        $display_options_title_text          	= "Options d'affichage personnalis&egrave;";
        $display_header_text              	= "Affichage En-t&ecirc;te";
        $display_crosshair_text              	= "Affichage centre";
        $display_clickcenter_text            	= "Cliquer pour centrer";
        $display_overview_text               	= "Aper&ccedil;u";
        $display_language_text               	= "Choix de la langue";
        $display_units_text                  	= "Choix des unit&egrave;s";
        $display_button_text                 	= "Activer les choix";
	$startdate_text				= "De:";
	$enddate_text				= "A:";
	$trip_title				= "Trajet:";
	$filter_title				= "Filtre:";
	$date_title				= "Date:";
        $tripsummary_title			= "R&egrave;sum&egrave; du trajet";
        $tripstatus_title			= "Statut";
	$summary_time				= "Dur&egrave;e totale:";
	$summary_photos				= "Photos:";
	$summary_comments			= "Commentaires:";
        // Public Display Information
        $user_button_text                    	= "Montrer utilisateur";
		$showconfig_button_text		= "Configuration";
		$showconfig_button_text_off	= "Hide Config";
        // Private Display Information
        $page_private			  	= "Page has been set to private. User ID and password required for access.";//trackmeIT
		$trip_data		     	= "Trip data for:"; //trackmeIT
        $login_text                          	= "Nom utilisateur";
        $password_text                       	= "Mot de passe";
        $login_button_text                   	= "Se connecter In";

        // Balloon Fields Information
        $user_balloon_text                   	= "Utilisateur";
        $trip_balloon_text                   	= "Trajet";
        $time_balloon_text                   	= "Heure";
        $speed_balloon_text                  	= "Vitesse";
        $altitude_balloon_text               	= "Altitude";
        $total_time_balloon_text             	= "Temps &egrave;coul&egrave;";
        $avg_speed_balloon_text              	= "Vitesse Moyenne";
        $total_distance_balloon_text         	= "Dist.";
        $point_balloon_text                  	= "Point";
        $comment_balloon_text                	= "Commentaires:";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "miles";
        $height_imperial_unit_balloon_text   	= "pieds";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text     	= "m&egrave;tres";
    }
    elseif($language == "spanish")
    {
        $title_text                          	= "Visor TrackMe";
        $trip_button_text                    	= "Mostrar";
        $location_button_text                	= "Ver tiempo real";
				$location_button_text_off            	= "Desactivar tiempo real";
        $filter_button_text                  	= "Mostrar";
        $filter_none_text                    	= "Todas las posiciones";
        $filter_photo_comment_text           	= "Solo con fotos y comentarios";
        $filter_photo_text                   	= "Solo con fotos";
        $filter_comment_text                 	= "Solo con comentarios";
        $filter_last_20		             				= "Ultimas 20 posiciones";
        $filter_daterange	             				= "For Date Range";
        $footer_text                         	= "Informacion proporcionada por";
        $incomplete_install_text             	= "¡No se pudo completar la instalaci&oacute;n correctamente! Los ficheros install.php y database.sql aún existen en el directorio TrackMe. Borre los archivos install.php y database.sql del directorio para poder utilizar este m&oacute;dulo.";
        $no_data_text                        	= "One or more TrackMe tables in your database are empty. You must generate data by running the TrackMe application on your Windows Mobile device before the map display will work properly.";
        $database_fail_text                  	= "Error al conectar a la base de datos. Proceso abortado.";
        $trip_none_text                      	= "Ninguna";
        $trip_any_text                       	= "Cualquiera";
        $display_options_title_text          	= "Opciones de visualizacion";
        $display_header_text              		= "Mostrar cabecera";
				$display_showbearing_text							= "Mostrar flechas direccion";
        $display_crosshair_text              	= "Mostrar cursor";
        $display_clickcenter_text            	= "Pulsar para centrar";
        $display_overview_text               	= "Mostrar overview";
        $display_language_text               	= "Seleccionar idioma";
        $display_units_text                  	= "Seleccionar unidad";
        $display_button_text                 	= "Aplicar cambios";
				$startdate_text												= "Desde:";
				$enddate_text													= "Para:";
				$trip_title														= "Ruta:";
				$filter_title													= "Filtrar:";
				$date_title														= "Fecha:";
        $tripsummary_title										= "Resumen de Viaje";
        $tripstatus_title											= "Estado actual";
				$summary_time													= "Tiempo total:";
				$summary_photos												= "Total Fotos:";
				$summary_comments											= "Total Comentarios:";
        // Public Display Information
        $user_button_text                    	= "Mostrar usuario";
				$showconfig_button_text		 						= "Mostrar configuracion";
				$showconfig_button_text_off	 					= "Ocultar configuracion";
        // Private Display Information
        $page_private													= "Acceso privado. Se requiere nombre de usuario y clave.";//trackmeIT
			  $trip_data			 											= "Informacion usuario:"; //trackmeIT
        $login_text                          	= "Usuario";
        $password_text                       	= "Clave";
        $login_button_text                		= "Entrar";

        // Balloon Fields Information
        $user_balloon_text                   	= "Usuario";
        $trip_balloon_text                   	= "Ruta";
        $time_balloon_text                   	= "Fecha";
        $speed_balloon_text                  	= "Velocidad";
        $altitude_balloon_text               	= "Altitud";
        $total_time_balloon_text             	= "Tiempo viajado";
        $avg_speed_balloon_text              	= "Velocidad media";
        $total_distance_balloon_text         	= "Distancia";
        $point_balloon_text                  	= "Posicion";
        $comment_balloon_text                	= "Comentarios";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "millas";
        $height_imperial_unit_balloon_text   	= "pies";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text     	= "metros";
    }
    elseif($language == "dutch")
    {
	$title_text 				= "TrackMe Display";
	$trip_button_text 			= "Toon reis";
	$location_button_text 			= "Live Tracking - AAN";
	$location_button_text_off            	= "Live Tracking - UIT";
	$filter_button_text 			= "Punten filter toepassen";
	$filter_none_text 			= "Alle punten";
	$filter_photo_comment_text 		= "Foto en commentaar";
	$filter_photo_text 			= "Foto punten";
	$filter_comment_text 			= "Aantekening punten";
	$filter_last_20 			= "Laatste 20 punten";
	$filter_daterange 			= "Datum bereik";
	$footer_text 				= "Tracking informatie verzorgd door ";
	$incomplete_install_text 		= "Installatie niet volledig uitgevoerd! De bestanden install.php en database.sql bestaan in de TrackMe map. Verwijder install.php en database.sql uit de TrackMe map om gebruik te maken van Trackme.";
	$no_data_text 				= "Een of meer TrackMe tabbellen in de database zijn leeg. Je moet eerst gegevens genereren met de TrackMe applicatie op je Windows Mobile apparaat voordat de kaart weergegeven kan worden.";
	$database_fail_text 			= "Verbinding maken met database mislukt. Weergeven afgebroken.";
        $trip_none_text                      	= "Geen";
        $trip_any_text                          = "Willekeurig";
	$display_options_title_text 		= "Weergave opties";
	$display_header_text 			= "Toon header";
	$display_showbearing_text		= "Toon richting indicatie";
	$display_crosshair_text 		= "Toon crosshair";
	$display_clickcenter_text 		= "Klik om te centreren";
	$display_overview_text 			= "Toon overzicht";
	$display_language_text 			= "Selecteer taal";
	$display_units_text 			= "Toon eenheden";
	$display_button_text 			= "Stel in";
	$startdate_text 			= "Van:";
	$enddate_text 				= "Tot:";
	$trip_title 				= "Reis:";
	$filter_title 				= "Filter:";
	$date_title 				= "Datum:";
	$tripsummary_title 			= "Reis overzicht";
	$tripstatus_title 			= "Huidige status";
	$summary_time 				= "Tijd:";
	$summary_photos 			= "Aantal foto's:";
	$summary_comments 			= "Aantal aantekeningen:";
	// Public Display Information
	$user_button_text 			= "Toon gebruiker";
	$showconfig_button_text		 	= "Toon Configuratie";
	$showconfig_button_text_off	 	= "Verberg Configuratie";
	// Private Display Information
        $page_private				= "Pagina ingesteld als prive. Gebruikersnaam en Wachtwoord zijn nodig voor toegang.";//trackmeIT
        $trip_data				= "Reis data van:"; //trackmeIT
	$login_text 				= "Gebruikersnaam";
	$password_text 				= "Wachtwoord";
	$login_button_text 			= "Inloggen";

	// Balloon Fields Information
	$user_balloon_text 			= "Gebruiker";
	$trip_balloon_text 			= "Reis";
	$time_balloon_text 			= "Tijd";
	$speed_balloon_text 			= "Snelheid";
	$altitude_balloon_text 			= "Hoogte";
	$total_time_balloon_text 		= "Reistijd";
	$avg_speed_balloon_text 		= "Gem. snelheid";
	$total_distance_balloon_text 		= "Afstand";
	$point_balloon_text 			= "Punt";
	$comment_balloon_text 			= "Commentaar";
	$speed_imperial_unit_balloon_text 	= "mph";
	$distance_imperial_unit_balloon_text 	= "Mijlen";
	$height_imperial_unit_balloon_text 	= "Voet";
	$speed_metric_unit_balloon_text 	= "km/h";
	$distance_metric_unit_balloon_text 	= "Km";
	$height_metric_unit_balloon_text	= "Meter";
	}
    elseif($language == "italian") //trackmeIT
    {
        $title_text                          	= "Visualizzatore TrackMe";
        $trip_button_text                    	= "Mostra Viaggio";
        $location_button_text                	= "Tracciamento reale";
	$location_button_text_off            	= "Disattiva Tracciamento in Tempo Reale";
        $filter_button_text                 	= "Mostra";
        $filter_none_text                    	= "Mostra Tutti i Punti";
        $filter_photo_comment_text           	= "Punti Foto e Commenti";
        $filter_photo_text                   	= "Punti Foto";
        $filter_comment_text                 	= "Punti Commenti";
        $filter_last_20		             	= "Gli Ultimi 20 Punti";
        $filter_daterange	             	= "Mostra Per Intervallo Date";
        $footer_text                         	= "Informazioni di Tracciamento fornite da";
        $incomplete_install_text             	= "Installazione non completata correttamente! I files install.php e database.sql sono ancora presenti nella cartella TrackMe. Eliminare install.php e database.sql dalla cartella TrackMe per abilitare questa finestra della mappa.";
        $no_data_text                        	= "Una o più tabelle di TracMe nel tuo database sono vuote. Devi generare dati utilizzando l'applicazione TrackMe sul tuo dispositivo Windows Mobile prima che la mappa venga visualizzata correttamente.";
        $database_fail_text                  	= "Connessione al database fallita. Schermata terminata.";
        $trip_none_text                      	= "Nessuno";
        $trip_any_text                       	= "Tutti";
        $display_options_title_text          	= "Personalizza Visualizzazione";
        $display_header_text              	= "Mostra Intestazione";
	$display_showbearing_text		= "Mostra Frecce di Direzione";
        $display_crosshair_text              	= "Mostra Mirino";
        $display_clickcenter_text            	= "Clicca per Centrare";
        $display_overview_text               	= "Mostra Anteprima";
        $display_language_text               	= "Lingua";
        $display_units_text                  	= "Unità di Misura";
        $display_button_text                 	= "Applica Visualizzazione";
	$startdate_text				= "Da:";
	$enddate_text				= "A:";
	$trip_title				= "Viaggio:";
	$filter_title				= "Filtro:";
	$date_title				= "Data:";
        $tripsummary_title			= "Sommario del Viaggio";
        $tripstatus_title			= "Stato Corrente";
	$summary_time				= "Tempo Totale:";
	$summary_photos				= "Foto Totali:";
	$summary_comments		 	= "Commenti Totali:";
	// Public Display Information
        $user_button_text                    	= "Mostra Utente";
	$showconfig_button_text		 	= "Mostra Configurazione";
	$showconfig_button_text_off	 	= "Nascondi Configurazione";		
        // Private Display Information
        $page_private				= "Accesso riservato. Sono richiesti Nome Utente e Password per l'accesso."; //trackmeIT
	$trip_data				= "Dati di viaggio di:"; //trackmeIT
        $login_text                          	= "Nome Utente";
        $password_text                       	= "Password";
        $login_button_text                   	= "Entra";
        

        // Balloon Fields Information
        $user_balloon_text                   	= "Utente";
        $trip_balloon_text                   	= "Viaggio";
        $time_balloon_text                   	= "Ora";
        $speed_balloon_text                  	= "Velocità";
        $altitude_balloon_text               	= "Altitudine";
        $total_time_balloon_text             	= "Tempo di Viaggio";
        $avg_speed_balloon_text              	= "Velocità Media";
        $total_distance_balloon_text         	= "Distanza";
        $point_balloon_text                  	= "Punto";
        $comment_balloon_text                	= "Commenti";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "miglia";
        $height_imperial_unit_balloon_text   	= "piedi";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text    	= "metri";
    }
    elseif($language == "slovak") //trackmeIT
    {
        $title_text                          	= "TrackMe Display";
        $trip_button_text                    	= "Zobrazit cestu";
        $location_button_text                	= "Živé sledovanie ZAP";
	      $location_button_text_off            	= "Živé sledovanie VYP";
        $filter_button_text                  	= "Zobrazit";
        $filter_none_text                    	= "Všetky body";
        $filter_photo_comment_text           	= "Fotky a Poznámky";
        $filter_photo_text                   	= "Fotky";
        $filter_comment_text                 	= "Poznámky";
        $filter_last_20		                    = "Posledných 20 bodov";
        $filter_daterange	                    = "Zobrazit rozsah dátumov";
        $footer_text                         	= "Sledovacie informácie poskytuje";
        $incomplete_install_text             	= "Inštalácia úspešne ukoncená. Vymažte súbory install.php a database.sql v adresári TrackMe pre správnu funkcnost TrackMe Display.";
        $no_data_text                        	= "Jedna alebo viac TrackMe tabuliek vo Vašej databáze sú prázdne. Musíte vygenerovat dáta pomocou aplikácie TrackMe na Vašom Windows Mobile ziariadení, aby táto aplikácia správne fungovala.";
        $database_fail_text                  	= "Zlyhalo pripojenie k databáze. Inštalácia prerušená.";
        $trip_none_text                      	= "Žiadna";
        $trip_any_text                       	= "Všetky";
        $display_options_title_text          	= "Užívatelské nastavenia";
        $display_header_text              	  = "Hlavicka web-straánky";
	      $display_showbearing_text		          = "Zobrazit šípky smeru";
        $display_crosshair_text              	= "Display Crosshair";
        $display_clickcenter_text            	= "Klik pre centrovanie";
        $display_overview_text               	= "Zobrazit prehlad";
        $display_language_text               	= "Nastavit jazyk";
        $display_units_text                  	= "Nastavit jednotky";
        $display_button_text                 	= "Uložit nastavenia";
	      $startdate_text				                = "Od:";
	      $enddate_text			                   	= "Do:";
	      $trip_title			                     	= "Cesta:";
	      $filter_title				                  = "Filter:";
	      $date_title		                    		= "Dátum:";
        $tripsummary_title		              	= "Štatistika cesty";
        $tripstatus_title			                = "Aktuálny stav";
	      $summary_time			                   	= "Celkový cas:";
	      $summary_photos				                = "Pocet fotiek:";
      	$summary_comments			                = "Pocet poznámok:";
				// Public Display Information
        $user_button_text                    	= "Zobrazit užívatela";
	      $showconfig_button_text		          	= "Zobrazit nastavenia";
      	$showconfig_button_text_off	     	    = "Skryt nastavenia";		
				// Private Display Information
        $page_private			     	              = "Stránka je nastavená ako súkromná. Pre vstup na stránky zadajte užívatelské meno a heslo.";
				// TrackmeIT
	      $trip_data			     	                = "Údaje cesty pre:";
        $login_text                          	= "Užívatelské meno";
        $password_text                       	= "Heslo";
        $login_button_text                   	= "Prihlásenie";
				// Balloon Fields Information
        $user_balloon_text                   	= "Užívatel";
        $trip_balloon_text                   	= "Cesta";
        $time_balloon_text                   	= "Cas";
        $speed_balloon_text                  	= "Rýchlost";
        $altitude_balloon_text               	= "Výška";
        $total_time_balloon_text             	= "Dlžka cesty";
        $avg_speed_balloon_text              	= "Priemerná rýchlost";
        $total_distance_balloon_text         	= "Celková vzdialenost";
        $point_balloon_text                  	= "Bod";
        $comment_balloon_text                	= "Poznámky";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "míle";
        $height_imperial_unit_balloon_text   	= "stopy";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text     	= "metre";
    }
    else
    {
        $title_text                          	= "TrackMe Display";
        $trip_button_text                    	= "Show Trip";
        $location_button_text                	= "Live Tracking On";
	$location_button_text_off            	= "Live Tracking Off";
        $filter_button_text                  	= "Show";
        $filter_none_text                    	= "All Points";
        $filter_photo_comment_text           	= "Photo and Comment";
        $filter_photo_text                   	= "Photo";
        $filter_comment_text                 	= "Comment";
        $filter_last_20		                = "Last 20 Points";
        $filter_daterange	                = "Show For Date Range";
        $footer_text                         	= "Tracking information provided by";
        $incomplete_install_text             	= "Installation not completed correctly! The files install.php and database.sql still exists in the TrackMe folder. Delete install.php and database.sql from the TrackMe folder to enable this map display.";
        $no_data_text                        	= "One or more TrackMe tables in your database are empty. You must generate data by running the TrackMe application on your Windows Mobile device before the map display will work properly.";
        $database_fail_text                  	= "Failed to connect to database. Display terminated.";
        $trip_none_text                      	= "None";
        $trip_any_text                       	= "Any";
        $display_options_title_text          	= "Custom Display Options";
        $display_header_text              	= "Display Header";
	$display_showbearing_text		= "Display Bearing Arrows";
        $display_crosshair_text              	= "Display Crosshair";
        $display_clickcenter_text            	= "Click to Center";
        $display_overview_text               	= "Display Overview";
        $display_language_text               	= "Select Language";
        $display_units_text                  	= "Select Units";
        $display_button_text                 	= "Set Display";
	$startdate_text				= "From:";
	$enddate_text				= "To:";
	$trip_title				= "Trip:";
	$filter_title				= "Filter:";
	$date_title				= "Date:";
        $tripsummary_title			= "Trip Summary";
        $tripstatus_title			= "Current Status";
	$summary_time				= "Total Time:";
	$summary_photos				= "Total Photos:";
	$summary_comments			= "Total Comments:";
	// Public Display Information
        $user_button_text                    	= "Show User";
	$showconfig_button_text		     	= "Show Config";
	$showconfig_button_text_off	     	= "Hide Config";		
        // Private Display Information
        $page_private			     	= "Page has been set to private. User ID and password required for access.";//trackmeIT
	$trip_data			     	= "Trip data for:"; //trackmeIT
        $login_text                          	= "User Name";
        $password_text                       	= "Password";
        $login_button_text                   	= "Log In";

        // Balloon Fields Information
        $user_balloon_text                   	= "User";
        $trip_balloon_text                   	= "Trip";
        $time_balloon_text                   	= "Time";
        $speed_balloon_text                  	= "Speed";
        $altitude_balloon_text               	= "Altitude";
        $total_time_balloon_text             	= "Time Traveled";
        $avg_speed_balloon_text              	= "Avg Speed";
        $total_distance_balloon_text         	= "Total Dist.";
        $point_balloon_text                  	= "Point";
        $comment_balloon_text                	= "Comments";
        $speed_imperial_unit_balloon_text    	= "mph";
        $distance_imperial_unit_balloon_text 	= "miles";
        $height_imperial_unit_balloon_text   	= "feet";
        $speed_metric_unit_balloon_text      	= "Km/h";
        $distance_metric_unit_balloon_text   	= "Km";
        $height_metric_unit_balloon_text     	= "meters";
    }

?>
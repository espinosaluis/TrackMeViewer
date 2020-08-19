	//////////////////////////////////////////////////////////////////////////////
	//
	// TrackMeViewer - Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android
	// Version: 3.5a
	// Date:    08/18/2020
	//
	// For more information go to:
	// http://forum.xda-developers.com/showthread.php?t=340667
	//
	// Please feel free to modify the files to meet your needs.
	// Post comments and questions to the forum thread above.
	//
	//////////////////////////////////////////////////////////////////////////////

var iconRed		= 'red-dot.png';
var iconGreen		= 'green-dot.png';
var iconLtBlue		= 'mm_20_gray.png';
var iconLtYellow	= 'mm_20_yellow.png';
var iconLtPurple	= 'mm_20_purple.png';
var iconPoint		= 'mm_00_yellow.png';
var arrowIcons		= [];
for (angle = 0; angle < 360; angle += 45) {
	arrowIcons.push('arrow' + angle + '.png');
}

function cookieAgreement(linecolor, showbearings, markertype, crosshair, clickcenter, language, units, tileprovider, tilePT, tripID, tripgroup, filterwith, filterstart, filterend, livetracking, chartdisplay, attributedisplay, interval, zoomlevel) {
	window.cookieconsent.initialise({
		"static": true,
		"revokable": true,
		"location": true,
		"palette": {
			"popup": { "background": "#edeff5", "text": "#838391" },
			"button": { "background": "#4b81e8" }
		},
		"theme": "classic",
		"type": "opt-out",
		"position": "top-left",
		"content": {
			"message": lang.get('cookie-msg'),
			"allow": lang.get('cookie-ok'),
			"deny": lang.get('cookie-nok'),
			"link": lang.get('cookie-more'),
			"href": "https://" + lang._code + ".wikipedia.org/wiki/cookie"
		},
		"onStatusChange": function(status, chosenBefore) {cookieSetRemove(status, linecolor, showbearings, markertype, crosshair, clickcenter, language, units, tileprovider, tilePT, trip, filterwith, filterstart, filterend, livetracking, chartdisplay, attributedisplay, interval, zoomlevel)}
	});
	cookieSetRemove(getCookieValue("cookieconsent_status"), linecolor, showbearings, markertype, crosshair, clickcenter, language, units, tileprovider, tilePT, tripID, tripgroup, filterwith, filterstart, filterend, livetracking, chartdisplay, attributedisplay, interval, zoomlevel);
	return;
}

function cookieSetRemove(status, linecolor, showbearings, markertype, crosshair, clickcenter, language, units, tileprovider, tilePT, tripID, tripgroup, filterwith, filterstart, filterend, livetracking, chartdisplay, attributedisplay, interval, zoomlevel) {
	if (status == "allow") {
		document.cookie = tripID           + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = tripgroup        + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = filterwith       + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = filterstart      + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = filterend        + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = livetracking     + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = chartdisplay     + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = attributedisplay + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = interval         + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = zoomlevel        + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = linecolor        + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = showbearings     + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = markertype       + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = crosshair        + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = clickcenter      + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = language         + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = units            + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = tileprovider     + "; SameSite=Strict; max-age=2147483647;";
		document.cookie = tilePT           + "; SameSite=Strict; max-age=2147483647;";
	}
	if (status == "deny") {
		document.cookie = tripID           + "; SameSite=Strict; max-age=0;";
		document.cookie = tripgroup        + "; SameSite=Strict; max-age=0;";
		document.cookie = filterwith       + "; SameSite=Strict; max-age=0;";
		document.cookie = filterstart      + "; SameSite=Strict; max-age=0;";
		document.cookie = filterend        + "; SameSite=Strict; max-age=0;";
		document.cookie = livetracking     + "; SameSite=Strict; max-age=0;";
		document.cookie = chartdisplay     + "; SameSite=Strict; max-age=0;";
		document.cookie = attributedisplay + "; SameSite=Strict; max-age=0;";
		document.cookie = interval         + "; SameSite=Strict; max-age=0;";
		document.cookie = zoomlevel        + "; SameSite=Strict; max-age=0;";
		document.cookie = linecolor        + "; SameSite=Strict; max-age=0;";
		document.cookie = showbearings     + "; SameSite=Strict; max-age=0;";
		document.cookie = markertype       + "; SameSite=Strict; max-age=0;";
		document.cookie = crosshair        + "; SameSite=Strict; max-age=0;";
		document.cookie = clickcenter      + "; SameSite=Strict; max-age=0;";
		document.cookie = language         + "; SameSite=Strict; max-age=0;";
		document.cookie = units            + "; SameSite=Strict; max-age=0;";
		document.cookie = tileprovider     + "; SameSite=Strict; max-age=0;";
		document.cookie = tilePT           + "; SameSite=Strict; max-age=0;";
		document.cookie = "navigationwidth=210px"  + "; SameSite=Strict; max-age=0;";
		document.cookie = "modtripWarning=true"    + "; SameSite=Strict; max-age=0;";
		document.cookie = "yesforallDecision=true" + "; SameSite=Strict; max-age=0;";
	}
	return;
}

function getCookieValue(a) {
	const b = document.cookie.match('(^|;)\\s*' + a + '\\s*=\\s*([^;]+)');
	return b ? b.pop() : '';
}

function decodeHtml(html) {
	return html.replace(/#/g, "%23");
}

function encodeHtml(html) {
	var txt = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}

async function submitTrip() {
	var response;
	if (document.getElementById('settripID').value == lang.get("trip-any")) {
		const ask = async () => {
			response = await Swal.fire({
				html: decodeHtml(lang.get('any-trip-question')),
				width: "400px",
				footer: lang.get('any-trip-warning'),
				title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
			});
		}
		await ask();
		if (!response.isConfirmed) return;
	}
	document.getElementById('setfilterwith').value = "";
	document.getElementById('setfilterstart').value = "";
	document.getElementById('setfilterend').value = "";
	document.form_attributes.submit();
	return;
}

function submitTripGroup() {
	document.getElementById('setfilterwith').value = "None";
	document.getElementById('setfilterstart').value = "";
	document.getElementById('setfilterend').value = "";
	tripIDSelect = document.getElementById("settripID");
	while (tripIDSelect.options.length > 0) { 
		tripIDSelect.remove(0); 
	}
	tripIDSelect.appendChild(document.createElement("option")); 
	document.form_attributes.submit();
	return;
}

async function deleteTrip() {
	settripID = document.getElementById('settripID').value;
	if (settripID == lang.get("trip-any")) {
		showInfoAndWait(lang.get('select-trip-information'));
		return;
	}
	var response;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('delete-trip-question')),
			width: "400px",
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	request = getURL() + '/requests.php?a=deletetripbyid&u=' + username + '&p=' + password + '&tripid=' + settripID + '&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('delete-trip-information')); });
	document.getElementById('setfilterwith').value = "";
	document.getElementById('setfilterstart').value = "";
	document.getElementById('setfilterend').value = "";
	document.form_attributes.submit();
	document.location.reload(true);
	return;
}

async function renameTrip() {
	settripID = document.getElementById('settripID').value;
	if (settripID == lang.get("trip-any")) {
		showInfoAndWait(lang.get('select-trip-information'));
		return;
	}
	var response;
	var newtripname;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('rename-trip-question') + "<br><br><input type=\"text\" id=\"newtripname\" name=\"newtripname\" style=\"width: 350px;\" />"),
			width: "400px",
			preConfirm: () => { newtripname = document.getElementById('newtripname').value },
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	newtripname = newtripname.replace(/[\\'"&#+]/g, ' ');
	request = getURL() + '/requests.php?a=renametripbyid&u=' + username + '&p=' + password + '&tripid=' + settripID + '&newname=' + newtripname + '&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('rename-trip-information')); });
	document.form_attributes.submit();
	document.location.reload(true);
	return;
}

async function deleteTripComments() {
	if (document.getElementById('settripID').value == lang.get("trip-any")) {
		showInfoAndWait(lang.get('select-trip-information'));
		return;
	}
	var response;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('delete-trip-comments-question')),
			width: "400px",
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	request = getURL() + '/requests.php?a=updatetripdata&u=' + username + '&p=' + password + '&tn=' + tripname + '&comments=&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('delete-trip-comments-information')); });
	document.location.reload(true);
	return;
}

async function changeTripComments() {
	if (document.getElementById('settripID').value == lang.get("trip-any")) {
		showInfoAndWait(lang.get('select-trip-information'));
		return;
	}
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	var response;
	var newtripcomments;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('change-trip-comments-question') + "<br><br><textarea rows=5 cols=30 type=\"textarea\" id=\"newtripcomments\" name=\"newtripcomments\">"),
			width: "400px",
			preConfirm: () => { newtripcomments = document.getElementById('newtripcomments').value },
			footer: modTripWarn,
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	newtripcomments = newtripcomments.replace(/[\\'"&#+]/g, ' ');
	request = getURL() + '/requests.php?a=updatetripdata&u=' + username + '&p=' + password + '&tn=' + tripname + '&comments=' + newtripcomments + '&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('change-trip-comments-information')); });
	document.location.reload(true);
	return;
}

async function deleteWaypoint(selWaypoint) {
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	if (getCookieValue("cookieconsent_status") == "allow") {
		yesforalldec = "<br><br><input type=\"checkbox\" id=\"yesforall\" name=\"yesforall\" />" + lang.get('yes-for-all-checkmark');
	} else {
		yesforalldec = "<input type=\"checkbox\" id=\"yesforall\" name=\"yesforall\" style=\"display: none;\" />";
	}
	yesforallDecision = getCookieValue("yesforallDecision");
	if (!yesforallDecision) {
		var response;
		var yesforall;
		const ask = async () => {
			response = await Swal.fire({
				html: decodeHtml(lang.get('delete-waypoint-question') + yesforalldec),
				width: "400px",
				preConfirm: () => { yesforall = document.getElementById('yesforall').checked },
				footer: modTripWarn,
				title: lang.get('button-question'), icon: "question", background: "#FFFFE8", showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
			});
		}
		await ask();
		if (yesforall && getCookieValue("cookieconsent_status") == "allow") document.cookie = "yesforallDecision=true; SameSite=Strict; max-age=86400;";
		if (!response.isConfirmed) return;
	}
	request = getURL() + '/requests.php?a=deletepositionbyid&u=' + username + '&p=' + password + '&tn=' +  tripname + '&positionid=' + selWaypoint + '&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('delete-waypoint-information')); });
	document.location.reload(true);
	return;
}

async function deleteWaypointComments(selWaypoint) {
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	var response;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('delete-waypoint-comments-question')),
			width: "400px",
			footer: modTripWarn,
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	request = getURL() + '/requests.php?a=updatepositiondata&u=' + username + '&p=' + password + '&id=' + selWaypoint + '&comments=&ignorelocking=0&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('delete-waypoint-comments-information')); });
	document.location.reload(true);
	return;
}

async function changeWaypointComments(selWaypoint) {
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	var response;
	var newwaypointcomments;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('change-waypoint-comments-question') + "<br><br><textarea rows=5 cols=30 type=\"textarea\" id=\"newwaypointcomments\" name=\"newwaypointcomments\">"),
			preConfirm: () => { newwaypointcomments = document.getElementById('newwaypointcomments').value },
			width: "400px",
			footer: modTripWarn,
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	newwaypointcomments = newwaypointcomments.replace(/[\\'"&#+]/g, ' ');
	request = getURL() + '/requests.php?a=updatepositiondata&u=' + username + '&p=' + password + '&id=' + selWaypoint + '&comments=' + newwaypointcomments + '&ignorelocking=0&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('change-waypoint-comments-information')); });
	document.location.reload(true);
	return;
}

async function deleteWaypointPhoto(selWaypoint) {
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	var response;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('delete-waypoint-photo-question')),
			width: "400px",
			footer: modTripWarn,
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	request = getURL() + '/requests.php?a=updateimageurl&u=' + username + '&p=' + password + '&id=' + selWaypoint + '&imageurl=&ignorelocking=0&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('delete-waypoint-photo-information')); });
	document.location.reload(true);
	return;
}

async function changeWaypointPhoto(selWaypoint) {
	modTripWarn = "";
	modtripWarning = getCookieValue("modtripWarning");
	if (!modtripWarning) {
		modTripWarn = lang.get('mod-trip-warning');
		if (getCookieValue("cookieconsent_status") == "allow") document.cookie = "modtripWarning=true; SameSite=Strict; max-age=86400;";
	}
	var response;
	var newwaypointphoto;
	var newname;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(lang.get('change-waypoint-photo-question') +
				"<br><br>" +
				"<input type=\"file\" accept=\".jpg, .bmp, .gif, .png\" id=\"newwaypointphoto\" name=\"newwaypointphoto\" />"),
			width: "400px",
			preConfirm: () => {
				newwaypointphoto = document.getElementById("newwaypointphoto").files[0];
				formData = new FormData();
				formData.append("uploadfile", newwaypointphoto);
				fileName = newwaypointphoto.name;
				newname = username + "_" + Date.now() + "." + fileName.substr(fileName.lastIndexOf('.') + 1);
				request = getURL() + '/upload.php?a=pic&u=' + username + '&p=' + password + '&newname=' + newname + '&db=8';
				$.ajax({url: request, type: 'post', async: false, 
					dataType: 'text', contentType: false, processData: false, data: formData, 
					error: function(jqXHR, textStatus, errorThrown) { if (debug2) console.log(textStatus + " | " + errorThrown); },
					success: function(data, textStatus, jqXHR) { if (debug2) console.log(textStatus + " | " + data); }
					});
			},
			footer: modTripWarn,
			title: lang.get('button-question'), icon: "question", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, showCancelButton: true, showCloseButton: true, confirmButtonText: lang.get('button-yes'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	if (!response.isConfirmed) return;
	request = getURL() + '/requests.php?a=updateimageurl&u=' + username + '&p=' + password + '&id=' + selWaypoint + '&imageurl=' + newname + '&ignorelocking=0&db=8';
	execrequest(request, function(result) { showResult(result, lang.get('change-waypoint-photo-information')); });
	document.location.reload(true);
	return;
}

function toggleDisplayOptions() {
	if (document.getElementById('setattributedisplay').checked) {
		document.getElementById('attribute_section').style.display = "inline";
	} else {
		document.getElementById('attribute_section').style.display = "none";
	}
	return;
}

function showChart() {
	document.getElementById('chartsection').style.display = "inline";
	if (charttype == "elevation") {
		document.getElementById('chartaltmax').style.display   = "inline-block";
		document.getElementById('chartaltmin').style.display   = "inline-block";
		document.getElementById('chartspeedmax').style.display = "none";
		document.getElementById('chartspeedmin').style.display = "none";
		document.getElementById('chartpitchmax').style.display = "none";
		document.getElementById('chartpitchmin').style.display = "none";
		chartlineColor = "#446600";
		chartfillColor = "#EEFFBB";
	} else if (charttype == "speed") {
		document.getElementById('chartaltmax').style.display   = "none";
		document.getElementById('chartaltmin').style.display   = "none";
		document.getElementById('chartspeedmax').style.display = "inline-block";
		document.getElementById('chartspeedmin').style.display = "inline-block";
		document.getElementById('chartpitchmax').style.display = "none";
		document.getElementById('chartpitchmin').style.display = "none";
		chartlineColor = "#660044";
		chartfillColor = "#CCEEFF";
	} else if (charttype == "pitch") {
		document.getElementById('chartaltmax').style.display   = "none";
		document.getElementById('chartaltmin').style.display   = "none";
		document.getElementById('chartspeedmax').style.display = "none";
		document.getElementById('chartspeedmin').style.display = "none";
		document.getElementById('chartpitchmax').style.display = "inline-block";
		document.getElementById('chartpitchmin').style.display = "inline-block";
		chartlineColor = "#CC8800";
		chartfillColor = "#FFEEAA";
	} else {
		// n/a
	}
	$('.chartsparkline').sparkline('html', { enableTagOptions: true, tagValuesAttribute: charttype+"values", tooltipFormatter: chartTooltipFormatter, lineColor: chartlineColor, fillColor: chartfillColor } );
	$('.chartsparkline').bind('sparklineRegionChange', chartPopupFormatter );
	if (charttype == "elevation") {
		document.getElementById('chartaltmax').style.display   = "inline-block";
		document.getElementById('chartaltmin').style.display   = "inline-block";
		document.getElementById('chartspeedmax').style.display = "none";
		document.getElementById('chartspeedmin').style.display = "none";
		document.getElementById('chartpitchmax').style.display = "none";
		document.getElementById('chartpitchmin').style.display = "none";
	} else if (charttype == "speed") {
		document.getElementById('chartaltmax').style.display   = "none";
		document.getElementById('chartaltmin').style.display   = "none";
		document.getElementById('chartspeedmax').style.display = "inline-block";
		document.getElementById('chartspeedmin').style.display = "inline-block";
		document.getElementById('chartpitchmax').style.display = "none";
		document.getElementById('chartpitchmin').style.display = "none";
	} else if (charttype == "pitch") {
		document.getElementById('chartaltmax').style.display   = "none";
		document.getElementById('chartaltmin').style.display   = "none";
		document.getElementById('chartspeedmax').style.display = "none";
		document.getElementById('chartspeedmin').style.display = "none";
		document.getElementById('chartpitchmax').style.display = "inline-block";
		document.getElementById('chartpitchmin').style.display = "inline-block";
	} else {
		// n/a
	}
	return;
}

function hideChart() {
	document.getElementById('chartsection').style.display = "none";
	return;
}

function chartTooltipFormatter(sparkline, options, fields) {
	if (useMetric) {
		dist  = "<b>" + lang.get('balloon-distance') + "</b>: " + fields.x.toFixed(2) + " " + lang.get('balloon-unit-distance-metric');
		alt   = "<b>" + lang.get('balloon-altitude') + "</b>: " + fields.y.toFixed(0) + " " + lang.get('balloon-unit-altitude-metric');
		speed = "<b>" + lang.get('balloon-speed')    + "</b>: " + fields.y.toFixed(1) + " " + lang.get('balloon-unit-speed-metric');
		pitch = "<b>" + lang.get('balloon-pitch')    + "</b>: " + fields.y.toFixed(0) + " " + "%";
	} else {
		dist  = "<b>" + lang.get('balloon-distance') + "</b>: " + fields.x.toFixed(2) + " " + lang.get('balloon-unit-distance-imperial');
		alt   = "<b>" + lang.get('balloon-altitude') + "</b>: " + fields.y.toFixed(0) + " " + lang.get('balloon-unit-altitude-imperial');
		speed = "<b>" + lang.get('balloon-speed')    + "</b>: " + fields.y.toFixed(1) + " " + lang.get('balloon-unit-speed-imperial');
		pitch = "<b>" + lang.get('balloon-pitch')    + "</b>: " + fields.y.toFixed(0) + " " + "%";
	}
	if (charttype == "elevation")  return "<span>" + alt   + "<br>" + dist + "</span>";
	else if (charttype == "speed") return "<span>" + speed + "<br>" + dist + "</span>";
	else if (charttype == "pitch") return "<span>" + pitch + "<br>" + dist + "</span>";
	else return "n/a";
}

function chartPopupFormatter(ev) {
	var sparkline = ev.sparklines[0];
	region = sparkline.getCurrentRegionFields();
	latLng = markers[sparkline.currentRegion].getLatLng();
	popupContent = markers[sparkline.currentRegion].getPopup().getContent();
	time = popupContent.substr(popupContent.search("<b>" + lang.get('balloon-time') + ": </b>"));
	time = time.substr(0, time.search('</td>') + 4);
	var popup = L.popup().setLatLng(latLng).setContent(time).openOn(map);
	return;
}

function elevationclick() {
	span = document.getElementById('chartelevation');
	span.className = "charttypeselected";
	span = document.getElementById('chartspeed');
	span.className = "charttype";
	span = document.getElementById('chartpitch');
	span.className = "charttype";
	charttype = "elevation";
	showChart();
	showChart();
	return;
}

function speedclick() {
	span = document.getElementById('chartspeed');
	span.className = "charttypeselected";
	span = document.getElementById('chartelevation');
	span.className = "charttype";
	span = document.getElementById('chartpitch');
	span.className = "charttype";
	charttype = "speed";
	showChart();
	showChart();
	return;
}

function pitchclick() {
	span = document.getElementById('chartpitch');
	span.className = "charttypeselected";
	span = document.getElementById('chartelevation');
	span.className = "charttype";
	span = document.getElementById('chartspeed');
	span.className = "charttype";
	charttype = "pitch";
	showChart();
	showChart();
	return;
}

function navigationwidthchange() {
	status = getCookieValue("cookieconsent_status");
	if (status == "allow") {
		navigationwidth = "navigationwidth=" + document.getElementById("navigationsectioncell").style.width;
		document.cookie = navigationwidth  + "; SameSite=Strict; max-age=2147483647;";
	}
	return;
}

function initInterval() {
	if (document.form_attributes.interval.value == "-") {
		document.form_attributes.interval.value = interval;
	}
	if (document.form_attributes.interval.value < 10) {
		alert("Minimum interval ist 10 seconds");
		t = 60;
		document.form_attributes.interval.value = 60;
	} else {
		t = document.form_attributes.interval.value;
	}
	k = setTimeout('showClock()', 1000);
	return;
}

function showClock() {
	t = t - 1;
	if (t == 0) {
		if (document.form_attributes.interval.value < 10) {
			alert("Minimum interval is 10 seconds");
			t = 60;
			document.form_attributes.interval.value = 60;
		} else {
			document.form_attributes.submit();
		}
	}
	document.form_attributes.seconds.value = t;
	k = setTimeout('showClock()', 1000);
	return;
}

async function showInfoAndWait(infotext) {
	var response;
	const ask = async () => {
		response = await Swal.fire({
			html: decodeHtml(infotext),
			width: "400px",
			title: lang.get('button-information'), icon: "info", background: "#FFFFE8", backdrop: false, allowOutsideClick: false, position: "bottom", showCancelButton: false, showCloseButton: true, confirmButtonText: lang.get('button-ok'), cancelButtonText: lang.get('button-cancel')
		});
	}
	await ask();
	return;
}

function Trip(name, user) {
	this.name = name;
	this.user = user;
	return;
}

Trip.prototype.lastMarker = function() {
	return markers[markers.length - 1];
}

Trip.prototype.appendMarker = function(data, icon, ID) {
	data.ID = ID;
	data.index = markers.length;
	data.trip = this;
	data.trip.name = data.tripname;
	if (icon === true)
		icon = getIcon(data);
	if (icon === false) {
		var theIcon = L.icon({iconUrl: iconPoint});
	} else {
		var theIcon = L.icon({iconUrl: icon, iconAnchor: [6, 10]});
	}
	var point = L.latLng(data.latitude, data.longitude);
	var marker = L.marker(point, {icon: theIcon}).addTo(map);

	data.date = fromISO(data.timestamp);
	if (markers.length > 0) {
		data.distance = calcDistance(this.lastMarker().getLatLng(), point);
		var totalTime = (data.date.getTime() - markers[0].data.date.getTime()) / 1000;
	} else {
		data.distance = 0;
		var totalTime = 0;
	}
	data.distanceToHere = this.totalDistance() + data.distance;
	data.totalTime = (leadingZeros(totalTime / 3600) + ':' +
			leadingZeros(totalTime / 60 % 60) + ':' +
			leadingZeros(totalTime % 60))
	marker.bindPopup(createMarkerText(data), {maxWidth: 500, className: 'balloon'});
	marker.on("click", function() {
		marker._popup.setContent(marker._popup.getContent().replace("@@@", markers.length.toString()));
		marker.openPopup();
	});
	if (bounds == null) {
		bounds = L.latLngBounds(marker.getLatLng().toBounds(200))
	} else {
		bounds.extend(marker.getLatLng());
	}
	marker.data = data;
	markers.push(marker);
	markersLatLng.push(marker.getLatLng());
	return marker;
}

Trip.prototype.end = function(name, color, any) {
	if (any) color = selectColor(Math.floor(Math.random() * 999));
	var polyline = L.polyline(markersLatLng, {color: color, weight: 3, opacity: 1}).bindTooltip(lang.get('trip-title') + ": " + name).addTo(map);
	markersLatLng = [];
	return;
}

Trip.prototype.openAMarkerPopup = function(no, next) {
	if (next > 0 && next <= markers.length) {
		map.closePopup(markers[no-1]._popup);
		markers[next-1]._popup.setContent(markers[next-1]._popup.getContent().replace("@@@", markers.length));
		markers[next-1].openPopup();
	}
	return;
}

Trip.prototype.totalDistance = function() {
	if (markers.length > 0)
		return this.lastMarker().data.distanceToHere;
	else
		return 0;
}

Trip.prototype.avgSpeed = function() {
	// Return m/s, the same as data.speed and this.totalSpeed
	if (markers.length > 0) {
		var totalTime = (this.lastMarker().data.date.getTime() - markers[0].data.date.getTime()) / 1000;
		return this.totalDistance() * 1000 / totalTime;
	} else {
		return 0;
	}
}

function getIcon(data) {
	if (data.index == 0) {
		return iconGreen;
	} else if (showBearings && 'bearing' in data) {
		var direction = Math.floor((data.bearing + 22.5)/45)%8;
		return arrowIcons[direction];
	} else if (data['photo']) {
		return iconLtYellow;
	} else if (data['comment']) {
		return iconLtPurple;
	} else {
		return iconLtBlue;
	}
}

function createMarkerText(data) {
	if (useMetric) {
		var speedUnit    = lang.get('balloon-unit-speed-metric');
		var altitudeUnit = lang.get('balloon-unit-altitude-metric');
		var distanceUnit = lang.get('balloon-unit-distance-metric');
	} else {
		var speedUnit    = lang.get('balloon-unit-speed-imperial');
		var altitudeUnit = lang.get('balloon-unit-altitude-imperial');
		var distanceUnit = lang.get('balloon-unit-distance-imperial');
	}
	var html = "";
	html += "<table class='balloon' cols='2'>";
	html += " <tr>";
	html += "  <td align='left'><b>" + lang.get('balloon-user') + ": </b>" + data.trip.user + "</td>";
	html += "  <td align='right'><b>" + lang.get('balloon-trip') + ": </b>" + data.trip.name + "</td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td colspan='2'><hr width='500'><\/td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td align='left'><b>" + lang.get('balloon-time') + ": </b>" + data.formattedTS + "</td>";
	html += "  <td align='right'><b>" + lang.get('balloon-total-time') + ": </b>" + data.totalTime + "</td>";
	html += " </tr>";
	speed = toMiles(data.speed * 3.6);
	avgSpeed = toMiles(data.trip.avgSpeed() * 3.6);
	totalDistance = toMiles(data.distanceToHere);
	altitude = data.altitude;
	latitude  = Math.abs(data.latitude);
	latgrad = Math.trunc(latitude);
	lattemp = latitude - latgrad;
	latmin = Math.trunc(lattemp * 60);
	latmin2 = lattemp *60;
	lattemp = lattemp - (latmin / 60);
	latsec = lattemp * 60 * 60;
	longitude = Math.abs(data.longitude);
	longrad = Math.trunc(longitude);
	lontemp = longitude - longrad;
	lonmin = Math.trunc(lontemp * 60);
	lonmin2 = lontemp * 60;
	lontemp = lontemp - (lonmin/60);
	lonsec = lontemp * 60 * 60;
	if (data.latitude > 0) {
		latdir = lang.get('compass-direction-north');
	} else {
		latdir = lang.get('compass-direction-south');
	}
	if (data.longitude > 0) {
		londir = lang.get('compass-direction-east');
	} else {
		londir = lang.get('compass-direction-west');
	}
	if (!useMetric) {
		altitude *= 3.2808399; // feet = 1 m
	}
	html += " <tr>";
	html += "  <td align='left'><b>" + lang.get('balloon-speed') + ": </b>" + speed.toFixed(2) + " " + speedUnit + "</td>";
	html += "  <td align='right'><b>" + lang.get('balloon-avg-speed') + ": </b>" + avgSpeed.toFixed(2) + " " + speedUnit + "</td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td align='left'><b>" + lang.get('balloon-altitude') + ": </b>" + altitude.toFixed(0) + " " + altitudeUnit + "</td>";
	html += "  <td align='right'><b>" + lang.get('balloon-total-distance') + ": </b>" + totalDistance.toFixed(3) + " " + distanceUnit + "</td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td align='left'><b>" + lang.get('balloon-latitude') + ": </b>" + latitude.toFixed(7) + "&deg;" + latdir + "<br>";
	html += " (" + latgrad + "&deg;" + latmin + "'" + latsec.toFixed(2) + "\"" + latdir;
	html += " - " + latgrad + "&deg;" + latmin2.toFixed(5) + "'" + latdir + ")</td>";
	html += "  <td align='right'><b>" + lang.get('balloon-longitude') + ": </b>" + longitude.toFixed(7) + "&deg;" + londir + "<br>";
	html += " (" + longrad + "&deg;" + lonmin + "'" + lonsec.toFixed(2) + "\"" + londir;
	html += " - " + longrad + "&deg;" + lonmin2.toFixed(5) + "'" + londir + ")</td>";
	html += " </tr>";
	if (data.comment) {
		html += " <tr>";
		html += "  <td colspan='2' align='left' width='400'><b>" + lang.get('balloon-comment') + ":</b> " + data.comment + "</td>";
		html += "</tr>";
	}
	if (data.photo) {
		html += " <tr>";
		html += "  <td colspan='2'><a href='" + data.photo + "' target='_blank'><img src='" + data.photo + "' width=400 border='1'></a></td>";
		html += " </tr>";
	}
	html += " <tr>";
	html += "  <td colspan='2' align='right'>&nbsp;</td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td align='center' colspan='2'>";
	html += "   <a href='javascript: trip.openAMarkerPopup(" + (data.index + 1) + ", " + (data.index + 0) + ");'>&nbsp;&nbsp;&nbsp;<<<&nbsp;&nbsp;&nbsp;</a>";
	html += "    &nbsp;" + lang.get('balloon-point') + " " + lang.get('balloon-point-val', data.index + 1, "@@@");
	html += "   <a href='javascript: trip.openAMarkerPopup(" + (data.index + 1) + ", " + (data.index + 2) + ");'>&nbsp;&nbsp;&nbsp;>>>&nbsp;&nbsp;&nbsp;</a></td>";
	html += " </tr>";
	html += " <tr>";
	html += "  <td colspan='2'>&nbsp;</td>";
	html += " </tr>";
	if (allowDBchange) {
		html += " <tr>";
		html += "  <td colspan='2' align='right'>";
		html += "   <button class=\"button\" onclick=\"deleteWaypoint(" + data.ID +");\">" + lang.get('delete-waypoint-button') + "</button>";
		html += "  </td>";
		html += " </tr>";
		if (data.comment == "") { dis = " disabled"; cla = "buttonreverse"; } else { dis = ""; cla = "button"; }
		html += " <tr>";
		html += "  <td>";
		html += "   <button class=\"" + cla + "\""+dis+" onclick=\"deleteWaypointComments(" + data.ID +");\">" + lang.get('delete-waypoint-comments-button') + "</button>";
		html += "  </td>";
		html += "  <td>";
		html += "   <button class=\"button\" onclick=\"changeWaypointComments(" + data.ID +");\">" + lang.get('change-waypoint-comments-button') + "</button>";
		html += "  </td>";
		html += " </tr>";
		if (data.photo == "") { dis = " disabled"; cla = "buttonreverse"; } else { dis = ""; cla = "button"; }
		html += " <tr>";
		html += "  <td>";
		html += "   <button class=\"" + cla + "\""+dis+" onclick=\"deleteWaypointPhoto(" + data.ID +");\">" + lang.get('delete-waypoint-photo-button') + "</button>";
		html += "  </td>";
		html += "  <td>";
		html += "   <button class=\"button\" onclick=\"changeWaypointPhoto(" + data.ID +");\">" + lang.get('change-waypoint-photo-button') + "</button>";
		html += "  </td>";
		html += " </tr>";
	}
	html += "</table>";
	return html
}

// Helper functions
function leadingZeros(number) {
	number = Math.floor(number)
	if (number < 10)
		return '0' + number
	else
		return number
}

function fromISO(isoDate) {
	var match = /(\d{4})-(\d?\d)-(\d?\d)[ _](\d?\d):(\d?\d):(\d?\d)/.exec(isoDate);
	return new Date(match[1], match[2] - 1, match[3], match[4], match[5], match[6]);
}

function getPathOnly(pathWithFile) {
	var pathArray = pathWithFile.substring(1).split('/');
	var pathOnly = "";
	for (i = 0; i < pathArray.length-1; i++) {
		pathOnly += "/" + pathArray[i];
	}
	return pathOnly;
}
function getPortWithColon(portNumber) {
	if (portNumber == "")
		return "";
	else
		return ":" + portNumber;
}

function getURL() {
	return document.location.protocol + '//' + document.location.hostname + getPortWithColon(document.location.port) + getPathOnly(document.location.pathname);
}

function showResult(result, message) {
	if (debug2) console.log(result); 
	if (result == "Result:0") showInfoAndWait(message);
	else showInfoAndWait(lang.get('operation-failed-information') + "<br>" + result);
	return;
}

function execrequest(url, callback) {
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200)
			callback(req.responseText);
	};
	req.open('GET', url, false);
	req.send(null);
	return;
}

/* Math helper functions */
function toMiles(distance) {
	if (!useMetric) distance *= 0.621371192;
	return distance
}

function toRadians (angle) {
	return angle * (Math.PI / 180);
}

function toDegrees (angle) {
	return angle * (180 / Math.PI);
}

function calcDistance(point1, point2) {
	lat1 = toRadians(point1.lat);
	lat2 = toRadians(point2.lat);
	delta_lon = toRadians(point1.lng - point2.lng);
	if (Math.abs(lat1 - lat2) < 0.0000001 && Math.abs(delta_lon) < 0.0000001)
		return 0;
	dist = Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(delta_lon);
	// Previous it was 1.1515 statue miles/min (1 min = 1/60 deg)
	// This is the corresponding radius in kilometers
	return Math.acos(dist) * 6370.69349; // Average Earth radius in km
}

function selectColor(number) {
	const hue = number * 137.508; // use golden angle approximation
	return `hsl(${hue}, 100%, 50%)`;
}

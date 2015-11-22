// Put your custom code here

$( document ).ready(function() {

	var sitesData;
	getListing();

	$('#editForm,#editInsertForm').on('submit', function (e) {
		var postData = $(this).serialize();
		console.log('#editForm,#editInsertForm: Post DATA: '+postData);
		post(postData,true);
		$("#collapSet").children().trigger("collapse");
		return false;
	});

	$('#tasker').on('submit', function (e) {
		var postData = $('#tasker').serialize();
		console.log('#tasker: Post DATA: '+postData);
		post(postData, true);
		$("#collapSet").children().trigger("collapse");
		return false;
	});

	$('#taskerMulti').on('submit', function (e) {
		var postData = $('#taskerMulti').serialize();
		console.log('#taskerMulti: Post DATA: '+postData);
		post(postData, true);
		$("#collapSet").children().trigger("collapse");
		return false;
	});

	$('#editId').on('change', function (e) {
		console.log("Selected site ID: " + this.value + " For editing");
		populateSiteEditor(this.value);
	});

	$('#siteId').on('change', function (e) {
		console.log("Selected site ID: " + this.value + " For Tasking");
		var myId = this.value;
		$.each(sitesData, function(key,value) {
			if (value.id == myId) {
				$("#siteTaskType").text("Tyyppi: "+sitesData[key].siteType);
				$("#siteTaskType").show("fast");
				return false;
			}
		});
	});

	// Get multiselection siteType and filter the sites data for checkboxes.
	$('#siteSelectType').on('change', function (e) {
		console.log("Selected site Type: " + this.value + " For Target selections");
		var myType = this.value;
		var container = $('#multiCheckboxes');
		container.empty();
		container.controlgroup("refresh");
		container.append('<legend>Valitse kohteet:</legend>');

		$.each(sitesData, function(key,value) {
			if (value.siteType == myType) {
				$('<input />', { type: 'checkbox', id: 'cb'+value.id, 'class': 'multiSelectCb', value: value.id, name: 'include[]', 'data-mini': true }).appendTo(container);
				$('<label />', { 'for': 'cb'+value.id, text: value.siteName }).appendTo(container);
			}
		});
		container.controlgroup("refresh");
		$("#locationMultiSelect").trigger("create");
	});

	$('#newLocation,#locationMultiSelect').bind('expand', function () {
		resetEditForm();
	});

	$('#locationEdit,#locationSelect').bind('expand', function () {
		// Repopulate select options
		console.log("When Expand, sitesData entries: " + sitesData.length);
		resetEditForm();
		populateSiteSelector();
	});


	function post(pData,fetch) {
        pData += "&userId="+userId;
		console.log('Posting DATA: '+pData);
		$.ajax({
			type: 'post',
			url: 'inc/dbStore.php',
			data: pData,
			success: function (data) {
				console.log('form was submitted DATA:'+data.message);
				if (fetch == true) getListing();
			}
		});
	}

	function getListing() {
		$.post("inc/dbStore.php", { action: "select", userId: userId })
			.done(function( data ) {
				var tmpData = data.data;
				if (tmpData.length > 0) {
					sitesData = data.data;
					tmpData = "";
				}
				console.log( "getListing() data Length: " + sitesData.length );
				populateSiteSelector();
			});
	}

	function populateSiteSelector() {
		console.log("populateSiteSelector() Entries: " + sitesData.length);
		
		var options = $("#siteId,#editId");	// Selectors

		if (sitesData.length == 0) {
			console.log("Received Nothing, disabling the select menu.");
			options.empty($("<option />"));
			options.append($("<option />").val("").text("Ei kohteita talletettuna."));
		} else {
			options.empty($("<option />"));
			options.append($("<option />").val("").text("Valitse Kohde"));
			$.each(sitesData, function(key,value) {
				console.log("Appending to select ID: " + value.id + " | Name: " + value.siteName);
				options.append($("<option />").val(value.id).text(value.siteName));
			});
		}
		options.selectmenu("refresh", true);

	}

	function populateSiteEditor(siteId) {
		var site;
		$.each(sitesData, function(key,value) {
			if (value.id == siteId) {
				site = sitesData[key];
				return false;
			}
		});
		console.log("JSON.stringify(site["+siteId+"]) : " + JSON.stringify(site));

		$("#editId").val(siteId);
		$("#siteName").val(site.siteName);
		$("#siteType").val(site.siteType.toLowerCase()).selectmenu("refresh");
		$("#siteAddress").val(site.siteAddress);
		$("#siteZip").val(site.siteZip);
		$("#siteCity").val(site.siteCity);
		$("#siteDelete").attr('checked',false);
		$("#siteDelete").checkboxradio("refresh");
	}

	function resetEditForm() {
		// Reset the form values
		$("#siteInsertName,#siteInsertAddress,#siteInsertZip,#siteInsertCity,#siteInsertType").val("");
		$("#siteName,#siteAddress,#siteZip,#siteCity,#siteType,#taskType,#taskNote").val("");
		$("#siteInsertType,#siteType,#taskType").selectmenu('refresh', true);
		$("#siteTaskType").text("");
		// Multi select form
		$("#taskSelectNote").val("");
		$('#siteSelectType,#taskSelectType').val("").selectmenu('refresh', true);
		$("#multiCheckboxes").html("");
	}

	function createCheckboxes(boxData) {
		var topicContainer = $('ul#detourDiv');
		topicContainer.empty();
		$.each(data, function (iteration, item) {
		    topicContainer.append(
		        $(document.createElement("li"))
		        .append(
		                $(document.createElement("input")).attr({
		                    type: 'checkbox',
		                    id: 'detour-' + iteration,
		                    name: iteration,
		                    value: "aaaaaaa"
		                })
		        )
		        .append(
		                $(document.createElement('label')).attr({
		                    'for': 'detour-' + iteration
		                })
		                .text(item)
		        ))

		    alert(item);
		});
	}

});

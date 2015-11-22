// Put your custom code here

$( document ).ready(function() {

	var sitesData;
	getListing();

	// $("#dateStart,#dateEnd").datepicker();

	$('#editForm,#editInsertForm').on('submit', function (e) {
		var postData = $('#editForm').serialize();
		e.preventDefault();
		post(postData);
	});

	$('#tasker').on('submit', function (e) {
		var postData = $('#tasker').serialize();
		e.preventDefault();
		post(postData);
	});

	$('#siteName').on('change', function () {
		console.log("Selected site ID: " + this.value + " For editing");
		populateSiteEditor(this.value);
	});

	// $('#editForm,#tasker').on('submit', function (e) {
	function post(postData) {
		$.ajax({
			type: 'post',
			url: 'dbStore.php',
			data: postData,
			success: function (data) {
				console.log('form was submitted DATA:'+data.message);
				populateSiteSelector(data.data);
			}
		});
	}

	function getListing() {
		$.post("dbStore.php", { action: "select" })
			.done(function( data ) {
				console.log( "Initial data Loaded: " + data );
				sitesData = data;
				console.log( "Initial data Length: " + sitesData.length );
				populateSiteSelector(data);
			});
	}

	function populateSiteSelector(result) {
		var options = $("#siteId,#siteName");	// Selector
		if (result.length == 0) {
			console.log("Received Nothing, disabling the select menu.");
			options.empty($("<option />"));
			options.prepend($("<option />").val("").text("Ei kohteita talletettuna."));
			options.attr("disabled","disabled");
			// options.selectmenu("disable");
		} else {
			$.each(result.data, function(key,value) {
				// console.log("Received ID: " + value.id + " | siteName: " + value.siteName + " | City: " + value.siteCity);
				options.append($("<option />").val(value.id).text(value.siteName));
			});
		}
	}

	function populateSiteEditor(siteId) {
		var site;
		$.each(sitesData.data, function(key,value) {
			if (value.id == siteId) {
				site = sitesData.data[key];
				return false;
			}
		});
		console.log("JSON.stringify(site["+siteId+"]) : " + JSON.stringify(site));
		// $.each(site, function(key,value) {
		// 	console.log("Received ID: " + siteId + " key: "+key+" | siteName: " + value.siteName + " | City: " + value.siteCity);
		// });
	}

});
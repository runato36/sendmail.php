$(document).ready(function()
{
	if(performance.navigation.type == 2)
  {
		location.reload(true);
	}
  var myData = getData();

	$("form").submit(function( event )
	{
		myData = getData();
		console.log(myData);
		event.preventDefault();
		let addressee = $("#addressee").val();
		$.ajax
		({
			url: 'sendmail.php',
			method: "post",
			data:
			{
				myData: JSON.stringify(myData),
				addressee: addressee,
			},
			success: function (response)
			{
				console.log(response);
				let returnData = $.parseJSON(response);
				console.log(returnData);
				if(returnData.status === 200)
				{
					window.location = returnData.redirectURL;
				}
				if (returnData.status == 501)
				{
					window.location = returnData.redirectURL;
				}
			},
			fail: function (response)
			{
				console.log(response);
				let returnData = $.parseJSON(response);
				console.log(returnData);
				if(returnData.status == '501')
				{
					window.location = returnData.redirectURL;
				}
				else
				{
					window.location = returnData.redirectURL;
				}
			}
		})
	});
 })

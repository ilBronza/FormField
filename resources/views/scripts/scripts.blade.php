@include('formfield::scripts.sortable')

<script type="text/javascript">
jQuery(document).ready(function($)
{
	//START FETCHER

	function ajaxFetcher(url, target, data = null, append = false)
	{
		$.ajax({
			url : url,
			type : 'POST',
			data : data,
			success: function(response, status, jqXHR)
			{
				//if is HTML append or replace
				if(jqXHR.getResponseHeader('content-type'))
				{
					if(append)
						$(target).append(response);

					else
						$(target).html(response);					
				}

			},
			error: function(response)
			{
				console.log(response);
			}
		});
	}

	function ajaxFetcherChanged(element)
	{
		let name = $(element).attr('name');
		let value = $(element).val();

		let url = $(element).data('fetcher-url') + '?' + name + '=' + value;

		if(typeof url === 'undefined')
			alert('imposta url nel fetcher ' + $(element).attr('name'));

		let target = $(element).data('fetcher-target');
		if(typeof target === 'undefined')
			alert('imposta target nel fetcher ' + $(element).attr('name'));

		let append = $(element).data('fetcher-append');

		ajaxFetcher(url, target, null, append);

	}

    window.dblClickCopySelect = function(option)
    {
        alert('qweqwe');

	}

    window.dblClickCopy = function(target, value)
	{
        var $temp = $("<div>");

        $(target).parent().append($temp);

        $temp.attr("contenteditable", true)
            .html(value).select()
            .on("focus", function() { document.execCommand('selectAll',false,null); })
            .focus();

        document.execCommand("copy");
        $temp.remove();
    }

    $('body').on('dblclick', '.dblclickcopy', function()
    {
        var content = $(this).next();
        var label = $(this).text().trim();
        var value = $(this).text().trim();

        var element = null;

        if( (element = $(content).find('input')).length != 0)
        {
            if(element.length == 1)
                window.dblClickCopy(element, value = $(element).val());
            else
				window.dblClickCopy(element, value = $(content).find('input:checked').siblings('label').text());
		}
        else if( (element = $(content).find('select')).length != 0)
            window.dblClickCopy(element, value = $(element).find(":selected").text());
        else
        {
            alert('non trovato elemento tra input e select');

            return ;
		}

        return window.addSuccessNotification(label + ' copiato!<br />[' + value + ']');
    });


    $('body').on('change', '.ajaxfetcher', function()
	{
		ajaxFetcherChanged(this);
	});

	$('body').on('keypress', '.ajaxfetcher', function(e)
	{
		if(e.which == 13)
		{
			e.preventDefault();
			$(this).blur();
		}
	});

	//END FETCHER


	//START JSON FIELD

	function uuidv4() {
	  return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
	    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
	  );
	}

	$('.fakejsonvalues input, .fakejsonvalues textarea, .fakejsonvalues select').each(function()
	{
		$(this).attr('disabled', 'disabled');
	})

	$('body').on('click', '.addjsonfake', function(e)
	{
		let addjson = $(this).closest('.uk-form-controls').find('.addjson').first();

		$(addjson).click();
	});

	$('body').on('click', '.addjson', function(e)
	{
		let container = $(this).siblings('.valuescontainer').first();
		let content = $(this).siblings('.valuestemplate').first().children('div');
		let uuid = uuidv4();

		cloned = content.clone();

		$(cloned).find('input, select, textarea').each(function()
		{
			let newName = $(this).attr('name');
			newName = newName.replace('counter', "'" + uuid + "'");

			$(this).attr('name', newName);
			$(this).removeAttr('disabled');
		});

		$(cloned).css('display', 'flex');

		$(container).append(cloned);

		setIndexOnValuescontainer();
	});

	$('body').on('click', '.jsonvaluesremover', function(e)
	{
		$(this).closest('.jsonvalues').remove();
	});

	$('.valuestemplate input').bind('invalid', function() {
		return false;
	});

	function setIndexOnValuescontainer()
	{
		$('.valuescontainer').each(function()
		{
			let counter = 0;

			$(this).find('.jsonvalues').each(function()
			{
				$(this).find('input, textarea, select').each(function()
				{
					let name = $(this).attr('name');
					let nameParts = name.split("[]");

					let openingChar = name.indexOf("[");
					let closingChar = name.indexOf("]");

					let firstPart = name.substring(0, openingChar);
					let lastPart = name.substring(closingChar + 1, name.length);

					let postingName = firstPart + '[' + counter + ']' + lastPart;

					$(this).attr('name', postingName);
				});

				counter ++;
			})
		})		
	}

	$('body').on('submit', 'form', function(e)
	{
		setIndexOnValuescontainer();
	});
	//END JSON FIELD
});
</script>
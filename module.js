M.local_blended ={
		Y:null,
		params:null,
		transaction : [],
		init : function(Y,params)
		{

					var fields = Y.all('#ac-userid');
					fields.each( function (field)
							{
							field.plug(Y.Plugin.AutoComplete, {
							    resultFilters    : 'phraseMatch',
							    resultHighlighter: 'phraseMatch',
							    source           : params['userlist']
							  });
							widgets = Y.all('.yui3-widget.yui3-aclist.yui3-widget-positioned');
							widgets.each(function(widg)
								{
								style= widg.setStyle('width',null);
								});
							});
		}
}
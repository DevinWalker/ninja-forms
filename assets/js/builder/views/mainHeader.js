define( [], function() {
	var view = Marionette.ItemView.extend({
		tagName: 'div',
		template: '#nf-tmpl-main-header',

		onRender: function() {
			jQuery( this.el ).unwrap();
		}
	});

	return view;
} );
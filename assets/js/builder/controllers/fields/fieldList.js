/**
 * Listens to our app channel to add the individual List Fields.
 *
 * @package Ninja Forms builder
 * @subpackage Main App
 * @copyright (c) 2015 WP Ninjas
 * @since 3.0
 */
define( [ 'models/app/optionRepeaterCollection' ], function( ListOptionCollection ) {
    var controller = Marionette.Object.extend( {
        initialize: function() {
            this.listenTo( nfRadio.channel( 'option-repeater-option-label' ), 'update:option', this.updateOptionLabel );
            this.listenTo( nfRadio.channel( 'option-repeater-option-value' ), 'update:option', this.updateOptionValue );
        
            /*
             * When we init our model, convert our options from an array of objects to a collection of models.
             */
            this.listenTo( nfRadio.channel( 'fields-list' ), 'init:fieldModel', this.convertOptions );
        },

        updateOptionLabel: function( e, model, dataModel, settingModel ) {

            if( 'list' != fieldTypeData[ dataModel.get( 'type' ) ].parentType ) return;

            if( model.get( 'manual_value' ) ) return;

            value = jQuery.slugify( model.get( 'label' ), { separator: '-' } );

            model.set( 'value', value );
            model.trigger( 'change', model );

            // TODO: Set focus on value input
            // jQuery( e.target ).closest( 'nf-table-row' ).find( '*[data-id="value"] ).focus();
        },

        updateOptionValue: function( e, model, dataModel, settingModel ) {

            if( 'list' != fieldTypeData[ dataModel.get( 'type' ) ].parentType ) return;

            model.set( 'manual_value', true );
        },

        convertOptions: function( fieldModel ) {
            /*
             * Our options are stored in our database as objects, not collections.
             * Before we attempt to render them, we need to convert them to a collection if they aren't already one.
             */ 
            var options = fieldModel.get( 'options' );

            var settingModel = nfRadio.channel( 'fields' ).request( 'get:settingModel', 'options' );

            if ( false == options instanceof Backbone.Collection ) {
                options = new ListOptionCollection( [], { settingModel: settingModel } );
                options.add( fieldModel.get( 'options' ) );
                fieldModel.set( 'options', options, { silent: true } );
            }
        }

    });

    return controller;
} );
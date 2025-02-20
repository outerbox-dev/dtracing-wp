import { SelectVehicleForm } from './components/select-vehicle-form';
// import './filter-profiles/filter-profiles';

/**
 * This is the start of your custom scripts.
 *
 * While this is currently just a single file you should not add custom scripts in here if you don't have to.
 * Instead create a folder/file structure suitable to the project and import those files in here.
 *
 */

/*
 * === How do I extend on the scripts ===
 *
 * EXAMPLE BELOW
 * If you want to add a new slider script this is how it could look like.

    Example file:
		src/javascript/component/slickslider.js

		In main.js:
		*/

// $( () => {
// 	selectVehicleFormInit();
// } );

jQuery( document ).ready( () => {
	// eslint-disable-next-line
	const selectVehicleForms = document.querySelectorAll( '.js-select-vehicle-form' );

	if ( selectVehicleForms.length > 0 ) {
		selectVehicleForms.forEach( selectVehicleForm => {
			new SelectVehicleForm( selectVehicleForm );
		} );
	}
} );

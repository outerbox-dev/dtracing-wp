export class SelectVehicleForm {
	constructor( element ) {
		this.element = element;
		this.makeSelectContainer = element.querySelector( '.js-select-vehicle-dropdown-container--make' );
		this.modelSelectContainer = element.querySelector( '.js-select-vehicle-dropdown-container--model' );
		this.yearSelectContainer = element.querySelector( '.js-select-vehicle-dropdown-container--year' );
		this.trimSelectContainer = element.querySelector( '.js-select-vehicle-dropdown-container--trim' );
		this.toggleButton = document.querySelector( '.js-toggle-vehicle-form-button' );
		this.toggleButtonCount = this.toggleButton.querySelector( '.js-toggle-vehicle-form-button-count' );
		this.isFormHiddenClass = 'is-hidden-on-small-screens';
		this.isFormHiddenOnScrollClass = 'is-hidden-on-scroll';
		this.toggleButtonFormIsVisibleClass = 'toggle-vehicle-form-button--form-visible';
		this.isFormSubmitting = false;
		this.closeOnScrollTrashold = 400;
		this.mobileScreenSize = 768;

		this.makeSelect = this.makeSelectContainer.querySelector( '.js-select-vehicle-dropdown' );
		this.modelSelect = this.modelSelectContainer.querySelector( '.js-select-vehicle-dropdown' );
		this.yearSelect = this.yearSelectContainer.querySelector( '.js-select-vehicle-dropdown' );
		this.trimSelect = this.trimSelectContainer.querySelector( '.js-select-vehicle-dropdown' );

		this.modelDefaultOption = this.modelSelect.querySelector( 'option' );
		this.yearDefaultOption = this.yearSelect.querySelector( 'option' );
		this.trimDefaultOption = this.trimSelect.querySelector( 'option' );

		this.submitButton = element.querySelector( '.js-select-vehicle-submit-button' );

		this.containerDisabledClass = 'select-vehicle-form__dropdown-container--disabled';
		this.invalidValueClass = 'select-vehicle-form__dropdown-container--invalid-value';

		this.popUpBlock = document.querySelector( '.select-vehicle-form__popup' );
		this.popUpBlockContent = document.querySelector( '.select-vehicle-form__popup-content' );

		// eslint-disable-next-line
		this.localVal = localStorage;
		this.filterPopupButton = element.querySelector( '.select-vehicle-form__title' );
		this.filterCars = this.localVal.getItem( 'savedVehicles' ) !== null ? JSON.parse( this.localVal.getItem( 'savedVehicles' ) || '[]' ) : [];

		this.activeCar = this.localVal.getItem( 'activeCar' ) !== null && +this.localVal.getItem( 'activeCar' ) < this.filterCars.length ? +this.localVal.getItem( 'activeCar' ) : 0;
		this.localVal.setItem( 'activeCar', this.activeCar );

		const numberOfCars = this.filterCars.length;
		element.querySelector( '.select-vehicle-form__saved-cars' ).textContent = numberOfCars;

		// Fill in all the options
		this.setMakeOptions();

		// Check if client has any personalization set and set those values initially.
		this.make = null;
		this.model = null;
		this.year = null;
		this.trim = null;
		this.setInitialOptions();

		// Move on to next dropdown once the first one is selected / reselected.
		this.connectForms();

		// Now let's make sure the search button does what we want, it should save the personalization and redirect user to search page.
		this.submitButton.addEventListener( 'click', this.handleSubmit.bind( this ) );

		wp.hooks.addAction( 'qalaElasticCore.action.afterSavePersonalization', 'qalaElasticCore', () => {
			this.submitStop();
			window.location = window.DtRacing.shop_page;
		} );

		this.setupToggleButton();
		this.closeOnScrollPoint();
	}

	init() {
		this.popUpToggle();
		this.fetchUserVehiclesFromDb();
		this.updateSavedCarsNumber();
		this.onSearchShop();
		
	}

	fetchUserVehiclesFromDb() {
		
		// Create a FormData object
		const formData = new FormData();
		formData.append('action', 'get_user_vehicle_data_modal');

		fetch(DtRacing.ajaxurl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
		})
		.then(response => response.json())
		.then(result => {
				if (result.success && result.data.vehicle) {
						// Add fetched vehicles to filterCars
						result.data.vehicle.forEach(vehicle => {
								// Avoid adding duplicates
								const isDuplicate = this.filterCars.some(item =>
										item.vehicle.make === vehicle.make &&
										item.vehicle.model === vehicle.model &&
										item.vehicle.year === vehicle.year &&
										item.vehicle.trim === vehicle.trim &&
										item.vehicle.id === vehicle.id
								);

								if (!isDuplicate) {
										this.filterCars.push({
												vehicle: {
														make: vehicle.make,
														model: vehicle.model,
														year: vehicle.year,
														trim: vehicle.trim,
														id: vehicle.id,
												},
										});
								}
						});

						// Save updated vehicles to localStorage
						this.localVal.setItem('savedVehicles', JSON.stringify(this.filterCars));

						// Update the UI with the new vehicle list
						//this.updateSavedCarsNumber();
				} else {
						console.error('Error fetching user vehicles:', result.data);
				}
		})
		.catch(error => console.error('Error fetching user vehicles:', error));
}


	/**
	 * Set number of saved cars to span.
	 */
	popUpToggle() {
		document.addEventListener( 'click', e => {
			// e.stopPropagation();
			if (
				e.target.closest( '.select-vehicle-form__popup-wrapper' ) &&
				! e.target.closest( '.select-vehicle-form__popup-content' ) &&
				! document.querySelector( '.select-vehicle-form__popup' ).classList.contains( 'hide' )
			) {
				document.querySelector( '.select-vehicle-form__popup' ).classList.add( 'hide' );
				document.querySelector( 'body' ).classList.remove( 'no-scroll' );
			} else if ( this.filterPopupButton.contains( e.target ) ) {
				this.popUpBlock.classList.remove( 'hide' );
				document.querySelector( 'body' ).classList.add( 'no-scroll' );
			}
		} );
	}

	/**
	 * Set number of saved cars to span.
	 */
	updateSavedCarsNumber() {
		const numberOfCars = this.filterCars.length;

		if ( numberOfCars === 0 ) {
			document.querySelector( '.select-vehicle-form__popup-active' ).classList.add( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-saved' ).classList.add( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-empty' ).classList.remove( 'hide' );
		} else if ( numberOfCars === 1 ) {
			document.querySelector( '.select-vehicle-form__popup-active' ).classList.remove( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-saved' ).classList.add( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-empty' ).classList.add( 'hide' );
		} else {
			document.querySelector( '.select-vehicle-form__popup-active' ).classList.remove( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-saved' ).classList.remove( 'hide' );
			document.querySelector( '.select-vehicle-form__popup-empty' ).classList.add( 'hide' );
		}

		this.element.querySelector( '.select-vehicle-form__saved-cars' ).textContent = numberOfCars;

		document.querySelectorAll( '.select-vehicle-form__popup-item' ).forEach( el => {
			el.remove();
		} );

		this.filterCars.forEach( ( item, index ) => {
			const liElement = document.createElement( 'div' );
			const closeBtn = document.createElement( 'span' );

			liElement.addEventListener( 'click', e => {
				e.preventDefault();
				if ( e.target.tagName !== 'SPAN' ) {
					this.onChangeActiveCar( index );
				}
			} );

			// Delete car from saved
			closeBtn.addEventListener( 'click', () => this.deleteSavedCar( index ) );

			if ( index === this.activeCar ) {
				liElement.classList.add( 'active' );
			}

			liElement.classList.add( 'select-vehicle-form__popup-item' );
			liElement.dataset.index = index;
			liElement.dataset.id = item.vehicle.id;

			// Construct text from the vehicle object
			const vehicleText = `${ item.vehicle.year } ${ item.vehicle.make } ${ item.vehicle.model } ${ item.vehicle.trim }`;

			liElement.textContent = vehicleText;
			liElement.appendChild( closeBtn );

			// Append li to the ul
			if ( index === this.activeCar ) {
				document.querySelector( '.select-vehicle-form__popup-active' ).appendChild( liElement );
			} else {
				document.querySelector( '.select-vehicle-form__popup-saved' ).appendChild( liElement );
			}
		} );
	}
	
	/**
	 * Delete cars and update local storage
	 *
	 * @param {string} index
	 */

	deleteSavedCar(index) {
		const car = this.filterCars[index];

		// Check if the vehicle has an ID (assume it comes from the database if it has an ID)
		if (car && car.vehicle.id) {
				// Use the vehicle ID from the car object
				const vehicleId = car.vehicle.id;

				// Create form data for the AJAX request
				const formData = new FormData();
				formData.append('action', 'delete_user_vehicle_data_modal');
				formData.append('id', vehicleId);

				fetch(DtRacing.ajaxurl, {
						method: 'POST',
						body: formData,
						credentials: 'same-origin',
				})
				.then(response => response.json())
				.then(result => {
						if (result.success) {
								// Only remove from the filterCars array if deletion was successful
								this.removeCarFromUI(index);
						} else {
								console.error('Error deleting vehicle:', result.data);
								this.removeCarFromUI(index);
						}
				})
				.catch(error => {
						console.error('Network error while deleting vehicle:', error);
				});
		} else {
				// Remove the vehicle from the filterCars array if it only exists in local storage
				this.removeCarFromUI(index);
		}
}

removeCarFromUI(index) {
	const car = this.filterCars[index];

	if (car && car.vehicle.id) {
			// Database vehicle (has an ID)
			const vehicleId = car.vehicle.id;

			// Remove the vehicle from the account page UI
			const accountPageElement = document.querySelector(`.saved-vehicle-item[data-vehicle-id="${vehicleId}"]`);
			if (accountPageElement) {
					accountPageElement.remove();
			}

			// Remove the vehicle from the modal (header) UI
			const modalVehicleElement = document.querySelector(`.select-vehicle-form__popup-item[data-id="${vehicleId}"]`);
			if (modalVehicleElement) {
					modalVehicleElement.remove();
			}
	} else {
			// Local storage vehicle (use index as identifier)
			const localStorageVehicleElement = document.querySelector(`.saved-vehicle-item[data-vehicle-id="local-${index}"]`);
			if (localStorageVehicleElement) {
					localStorageVehicleElement.remove();
			}

			const modalLocalStorageVehicleElement = document.querySelector(`.select-vehicle-form__popup-item[data-id="local-${index}"]`);
			if (modalLocalStorageVehicleElement) {
					modalLocalStorageVehicleElement.remove();
			}
	}

	// Remove the car from the filterCars array
	this.filterCars.splice(index, 1);

	// Update local storage only for local storage vehicles
	this.localVal.setItem('savedVehicles', JSON.stringify(this.filterCars));

	// Handle the active car and local storage update
	if (index === this.activeCar) {
			this.activeCar = 0;
			this.localVal.setItem('activeCar', this.activeCar);
	}

	// If there are no saved cars, disable dropdowns and reset
	if (this.filterCars.length === 0) {
			this.makeSelect.querySelector(`option[disabled]`).selected = true;
			this.modelSelect.querySelector(`option[disabled]`).selected = true;
			this.yearSelect.querySelector(`option[disabled]`).selected = true;
			this.trimSelect.querySelector(`option[disabled]`).selected = true;

			this.disableSelect(this.modelSelectContainer, this.modelSelect);
			this.disableSelect(this.yearSelectContainer, this.yearSelect);
			this.disableSelect(this.trimSelectContainer, this.trimSelect);
	}

	// Update saved cars number in the modal and UI
	this.updateSavedCarsNumber();
}


onSearchShop() {
	const searchForm = document.querySelector('.select-vehicle-form__search-form');

	if (searchForm && this.filterCars[this.activeCar]) {
			// Set the values for the form based on the active car's details
			const activeCar = this.filterCars[this.activeCar].vehicle;

			// Format and sanitize the selected values
			const formatValue = (value) => {
					return value.trim().toLowerCase().replace(/\s+/g, '-').replace(/\+/g, '-');
			};

			const formattedMake = formatValue(activeCar.make);
			const formattedModel = formatValue(activeCar.model);
			const formattedYear = formatValue(activeCar.year);
			const formattedTrim = formatValue(activeCar.trim);

			// Combine the categories into a single 'product_cat' parameter with an OR logic (comma separated)
			let productCategories = [];

			if (formattedMake) productCategories.push(formattedMake);
			if (formattedModel) productCategories.push(formattedModel);
			if (formattedYear) productCategories.push(formattedYear);
			if (formattedTrim) productCategories.push(formattedTrim);

			// Update the hidden product_cat input field with the formatted categories
			if (productCategories.length > 0) {
					searchForm.querySelector('input[name="product_cat"]').value = productCategories.join('~');
			}

			// Set the search term from the active car's make
			//searchForm.querySelector('input[name="s"]').value = formattedMake;
	}
}
	
/**
 * Delete cars and update local storage
 *
 * @param {string} index
 */
onChangeActiveCar( index ) {
	this.activeCar = index;
	this.filterCars.forEach( ( car, carIndex ) => {
		if ( index === carIndex ) {
			// Set all actual values
			this.make = car.vehicle.make;
			this.model = car.vehicle.model;
			this.year = car.vehicle.year;
			this.trim = car.vehicle.trim;
		}
	} );

	this.setModelOptionsFor( this.make );
	this.setYearOptionsFor( this.make, this.model );
	this.setTrimOptionsFor( this.make, this.model, this.year );

	// Enable all forms
	this.enableSelect( this.modelSelectContainer, this.modelSelect );
	this.enableSelect( this.yearSelectContainer, this.yearSelect );
	this.enableSelect( this.trimSelectContainer, this.trimSelect );

	// Set the values in dropdowns.
	this.makeSelect.querySelector( `option[value="${ this.make }"]` ).selected = true;
	this.modelSelect.querySelector( `option[value="${ this.model }"]` ).selected = true;
	this.yearSelect.querySelector( `option[value="${ this.year }"]` ).selected = true;
	this.trimSelect.querySelector( `option[value="${ this.trim }"]` ).selected = true;

	this.localVal.setItem( 'activeCar', index );
	this.updateSavedCarsNumber();
	this.submitButton.click();

	// eslint-disable-next-line
	console.log('save items');
}

/**
 * Used to initially set all make options in this.makeSelect.
 */
setMakeOptions() {
	this.init();

	const options = [];
	Object.keys( window.DtRacing.vehicles ).forEach( make => {
		const option = document.createElement( 'option' );
		option.value = make;
		option.innerHTML = make;
		options.push( option );
	} );
	this.makeSelect.append( ...options );

	this.enableSelect( this.makeSelectContainer, this.makeSelect );
}

	/**
	 * Setups up the initial values of the forms.
	 *
	 * @return {void}
	 */
	setInitialOptions() {

		if ( ! this.filterCars[ this.activeCar ] ) {
			return;
		}

		const userVehicle = this.filterCars[ this.activeCar ].vehicle;

		// Set all actual values
		this.make = userVehicle.make;
		this.model = userVehicle.model;
		this.year = userVehicle.year;
		this.trim = userVehicle.trim;

		// Set all available options
		this.setModelOptionsFor( this.make );
		this.setYearOptionsFor( this.make, this.model );
		this.setTrimOptionsFor( this.make, this.model, this.year );

		// Enable all forms
		this.enableSelect( this.modelSelectContainer, this.modelSelect );
		this.enableSelect( this.yearSelectContainer, this.yearSelect );
		this.enableSelect( this.trimSelectContainer, this.trimSelect );

		// Set the values in dropdowns.
		this.makeSelect.querySelector( `option[value="${ this.make }"]` ).selected = true;
		this.modelSelect.querySelector( `option[value="${ this.model }"]` ).selected = true;
		this.yearSelect.querySelector( `option[value="${ this.year }"]` ).selected = true;
		this.trimSelect.querySelector( `option[value="${ this.trim }"]` ).selected = true;
	}

	/**
	 * Sets all available options for Make.
	 *
	 * @param {string} make
	 */
	setModelOptionsFor( make ) {
		if ( ! window.DtRacing.vehicles[ make ] ) {
			return;
		}

		const options = [ this.modelDefaultOption ];
		Object.keys( window.DtRacing.vehicles[ make ] ).forEach( model => {
			const option = document.createElement( 'option' );
			option.value = model;
			option.innerHTML = model;
			options.push( option );
		} );
		this.modelSelect.innerHTML = '';
		this.modelSelect.append( ...options );
	}

	/**
	 * Sets all available options for Year.
	 *
	 * @param {string} make
	 * @param {string} model
	 */
	setYearOptionsFor( make, model ) {
		if ( ! window.DtRacing.vehicles[ make ][ model ] ) {
			return;
		}

		const options = [ this.yearDefaultOption ];
		Object.keys( window.DtRacing.vehicles[ make ][ model ] ).forEach( year => {
			const option = document.createElement( 'option' );
			option.value = year;
			option.innerHTML = year;
			options.push( option );
		} );
		this.yearSelect.innerHTML = '';
		this.yearSelect.append( ...options );
	}

	/**
	 * Sets all available options for Trim.
	 *
	 * @param {string} make
	 * @param {string} model
	 * @param {string} year
	 */
	setTrimOptionsFor( make, model, year ) {
		if ( ! window.DtRacing.vehicles[ make ][ model ][ year ] ) {
			return;
		}

		const options = [ this.trimDefaultOption ];
		window.DtRacing.vehicles[ make ][ model ][ year ].forEach( trim => {
			const option = document.createElement( 'option' );
			option.value = trim;
			option.innerHTML = trim;
			options.push( option );
		} );
		this.trimSelect.innerHTML = '';
		this.trimSelect.append( ...options );
	}

	connectForms() {
		// Make dropdown resets all other forms
		this.makeSelect.addEventListener( 'change', e => {
			// Set all internal values correctly (reset everything)
			this.make = e.target.value;
			this.model = null;
			this.year = null;
			this.trim = null;

			// Set next form options
			this.setModelOptionsFor( this.make );

			// Disable all forms except make.
			this.enableSelect( this.modelSelectContainer, this.modelSelect );
			this.disableSelect( this.yearSelectContainer, this.yearSelect );
			this.disableSelect( this.trimSelectContainer, this.trimSelect );

			this.makeSelectContainer.classList.remove( this.invalidValueClass );
			this.modelSelectContainer.classList.remove( this.invalidValueClass );
			this.yearSelectContainer.classList.remove( this.invalidValueClass );
			this.trimSelectContainer.classList.remove( this.invalidValueClass );
		} );

		// Model dropdown resets year and trim forms.
		this.modelSelect.addEventListener( 'change', e => {
			// Set all internal values correctly (reset everything)
			this.model = e.target.value;
			this.year = null;
			this.trim = null;

			// Set next form options
			this.setYearOptionsFor( this.make, this.model );

			// Disable all forms except make.
			this.enableSelect( this.yearSelectContainer, this.yearSelect );
			this.disableSelect( this.trimSelectContainer, this.trimSelect );

			this.modelSelectContainer.classList.remove( this.invalidValueClass );
			this.yearSelectContainer.classList.remove( this.invalidValueClass );
			this.trimSelectContainer.classList.remove( this.invalidValueClass );
		} );

		// Year dropdown resets year and trim forms.
		this.yearSelect.addEventListener( 'change', e => {
			// Set all internal values correctly (reset everything)
			this.year = e.target.value;
			this.trim = null;

			// Set next form options
			this.setTrimOptionsFor( this.make, this.model, this.year );

			// Disable all forms except make.
			this.enableSelect( this.trimSelectContainer, this.trimSelect );

			this.yearSelectContainer.classList.remove( this.invalidValueClass );
			this.trimSelectContainer.classList.remove( this.invalidValueClass );
		} );

		// Trim dropdown just sets the value
		this.trimSelect.addEventListener( 'change', e => {
			// Set all internal values correctly (reset everything)
			this.trim = e.target.value;

			this.trimSelectContainer.classList.remove( this.invalidValueClass );
		} );
	}

	/**
	 * Setup handling of the Search (submit) button. This is called when the button is clicked.
	 *
	 * @param {Event} e Click event.
	 * @return {void}
	 */

handleSubmit(e) {
	e.preventDefault(); // Prevent default form submission

	// Reset previous validation messages
	this.makeSelect.setCustomValidity('');
	this.modelSelect.setCustomValidity('');
	this.yearSelect.setCustomValidity('');
	this.trimSelect.setCustomValidity('');

	this.makeSelectContainer.classList.remove(this.invalidValueClass);
	this.modelSelectContainer.classList.remove(this.invalidValueClass);
	this.yearSelectContainer.classList.remove(this.invalidValueClass);
	this.trimSelectContainer.classList.remove(this.invalidValueClass);

	// Validate each dropdown value
	if (!this.make) {
			this.makeSelect.setCustomValidity('Please select your car\'s make');
			this.makeSelect.reportValidity();
			this.makeSelectContainer.classList.add(this.invalidValueClass);
			return;
	}

	if (!this.model) {
			this.modelSelect.setCustomValidity(`Please select your ${this.make} model`);
			this.modelSelect.reportValidity();
			this.modelSelectContainer.classList.add(this.invalidValueClass);
			return;
	}

	if (!this.year) {
			this.yearSelect.setCustomValidity(`Please select your ${this.make} ${this.model} year`);
			this.yearSelect.reportValidity();
			this.yearSelectContainer.classList.add(this.invalidValueClass);
			return;
	}

	if (!this.trim) {
			this.trimSelect.setCustomValidity(`Please select your ${this.year} ${this.make} ${this.model} trim`);
			this.trimSelect.reportValidity();
			this.trimSelectContainer.classList.add(this.invalidValueClass);
			return;
	}

	// Update the hidden input field in the second form with the selected car data
	const productCatInput = document.querySelector('.select-vehicle-form__search-form input[name="product_cat"]');
	if (productCatInput) {
			// Format and sanitize the selected values to create the product_cat value
			const formatValue = (value) => {
					return value.trim().toLowerCase().replace(/\s+/g, '-').replace(/\+/g, '-');
			};

			let productCategories = [];

			if (this.make) productCategories.push(formatValue(this.make));
			if (this.model) productCategories.push(formatValue(this.model));
			if (this.year) productCategories.push(formatValue(this.year));
			if (this.trim) productCategories.push(formatValue(this.trim));

			// Set the combined product_cat value to the hidden input
			productCatInput.value = productCategories.join('~');
	}
	//this.submitStart();
	// Submit the second form programmatically
	const searchForm = document.querySelector('.select-vehicle-form__search-form');
	if (searchForm && searchForm.classList.contains('shop')) {
		const isDuplicate = this.filterCars.some( item =>
			item.vehicle.make === this.make &&
			item.vehicle.model === this.model &&
			item.vehicle.year === this.year &&
			item.vehicle.trim === this.trim
		);

		if ( ! isDuplicate ) {
			this.filterCars.push( {
				vehicle: {
					make: this.make,
					model: this.model,
					year: this.year,
					trim: this.trim,
				},
			} );

			this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
			this.activeCar = this.filterCars.length - 1;
		} else {
			const vehicleIndex = this.filterCars.findIndex( item =>
				item.vehicle.make === this.make &&
				item.vehicle.model === this.model &&
				item.vehicle.year === this.year &&
				item.vehicle.trim === this.trim
			);

			if ( vehicleIndex !== -1 ) {
				this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
				this.activeCar = vehicleIndex;
			}
		}
		this.localVal.setItem( 'activeCar', this.activeCar );

		// this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
		// this.activeCar = this.filterCars.length - 1;
		// this.localVal.setItem( 'activeCar', this.activeCar );

		// wp.hooks.doAction( 'qalaElasticCore.action.savePersonalization', {
		//   vehicle: {
		//     make: this.make,
		//     model: this.model,
		//     year: this.year,
		//     trim: this.trim,
		//   },
		// } );

			this.updateSavedCarsNumber();
			searchForm.submit();
	} else {
		this.submitStart();
	}
}







	/**
	 * Enables a particular select.
	 *
	 * @param {HTMLElement} container
	 * @param {HTMLElement} select
	 * @return {void}
	 */
	enableSelect( container, select ) {
		container.classList.remove( this.containerDisabledClass );
		select.disabled = false;
		select.selectedIndex = 0;
	}

	/**
	 * Disables a particular select.
	 *
	 * @param {HTMLElement} container
	 * @param {HTMLElement} select
	 * @return {void}
	 */
	disableSelect( container, select ) {
		container.classList.add( this.containerDisabledClass );
		select.disabled = true;
		select.selectedIndex = 0;
	}

	submitStart() {
		this.submitButton.disabled = true;
		this.isFormSubmitting = true;

		const isDuplicate = this.filterCars.some( item =>
			item.vehicle.make === this.make &&
			item.vehicle.model === this.model &&
			item.vehicle.year === this.year &&
			item.vehicle.trim === this.trim
		);

		if ( ! isDuplicate ) {
			this.filterCars.push( {
				vehicle: {
					make: this.make,
					model: this.model,
					year: this.year,
					trim: this.trim,
				},
			} );

			this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
			this.activeCar = this.filterCars.length - 1;
		} else {
			const vehicleIndex = this.filterCars.findIndex( item =>
				item.vehicle.make === this.make &&
				item.vehicle.model === this.model &&
				item.vehicle.year === this.year &&
				item.vehicle.trim === this.trim
			);

			if ( vehicleIndex !== -1 ) {
				this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
				this.activeCar = vehicleIndex;
			}
		}
		this.localVal.setItem( 'activeCar', this.activeCar );

		// this.localVal.setItem( 'savedVehicles', JSON.stringify( this.filterCars ) );
		// this.activeCar = this.filterCars.length - 1;
		// this.localVal.setItem( 'activeCar', this.activeCar );

		wp.hooks.doAction( 'qalaElasticCore.action.savePersonalization', {
			vehicle: {
				make: this.make,
				model: this.model,
				year: this.year,
				trim: this.trim,
			},
		} );

		this.updateSavedCarsNumber();
	}

	submitStop() {
		this.submitButton.disabled = false;
		this.isFormSubmitting = false;
	}

	setupToggleButton() {
		// Set the indicator state if user has personalizations.
		const userVehicle = window.QalaElasticCore.readLastPersonalization( 'vehicle' );

		if ( userVehicle && userVehicle.make ) {
			this.toggleButtonCount.classList.remove( 'is-hidden' );
		} else {
			this.element.classList.remove( this.isFormHiddenClass );
		}

		// Set the initial state of the button.
		if ( ! this.element.classList.contains( this.isFormHiddenClass ) ) {
			this.toggleButton.classList.add( this.toggleButtonFormIsVisibleClass );
		}

		// Toggle form's visibility on click.
		this.toggleButton.addEventListener( 'click', () => {
			if (
				this.element.classList.contains( this.isFormHiddenClass ) ||
				this.element.classList.contains( this.isFormHiddenOnScrollClass )
			) {
				this.element.classList.remove( this.isFormHiddenClass );
				this.toggleButton.classList.add( this.toggleButtonFormIsVisibleClass );
			} else {
				this.element.classList.add( this.isFormHiddenClass );
				this.toggleButton.classList.remove( this.toggleButtonFormIsVisibleClass );
			}
			this.element.classList.remove( this.isFormHiddenOnScrollClass );
		} );
	}

	closeOnScrollPoint() {
		const that = this;
		let hasRun = false;

		window.addEventListener( 'scroll', () => {
			if ( window.innerWidth < this.mobileScreenSize && window.scrollY > this.closeOnScrollTrashold && ! hasRun ) {
				jQuery( this.element ).slideUp( {
					complete() {
						this.classList.add( that.isFormHiddenOnScrollClass );
						jQuery( this ).removeAttr( 'style' );
					},
				} );

				hasRun = true;
			}
		} );
	}
}

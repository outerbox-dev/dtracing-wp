document.addEventListener('DOMContentLoaded', function () {
	const saveButton = document.querySelector('.js-garage-vehicle-submit-button');
	if (sessionStorage.getItem('reloadNeeded')) {
		sessionStorage.removeItem('reloadNeeded');  // Clear the flag
		setTimeout(function() {
			window.location.reload(true); 
		}, 100)

	}
	if (saveButton) {
			saveButton.addEventListener('click', function (e) {
					e.preventDefault();

					// Gather selected vehicle data
					const makeSelect = document.querySelector('.garage-vehicle-form__dropdown--make');
					const modelSelect = document.querySelector('.garage-vehicle-form__dropdown--model');
					const yearSelect = document.querySelector('.garage-vehicle-form__dropdown--year');
					const trimSelect = document.querySelector('.garage-vehicle-form__dropdown--trim');

					const make = makeSelect.value;
					const model = modelSelect.value;
					const year = yearSelect.value;
					const trim = trimSelect.value;

					// Check if any of the fields are not selected
					if (makeSelect.selectedIndex === 0 || modelSelect.selectedIndex === 0 || yearSelect.selectedIndex === 0 || trimSelect.selectedIndex === 0) {
						alert('Please select valid vehicle options (make, model, year, and trim).');
						return;  // Prevent saving if validation fails
					}

					// Prepare vehicle data to send via AJAX
					const vehicle = {
							make: make,
							model: model,
							year: year,
							trim: trim,
					};

					// Send AJAX request to save vehicle data
					fetch(vehicleData.ajaxurl + '?action=save_user_vehicle_data', {
							method: 'POST',
							headers: {
									'Content-Type': 'application/json',
							},
							body: JSON.stringify({ vehicle }),
							credentials: 'same-origin',
					})
							.then(response => response.json())
							.then(result => {
									if (result.success) {
										// Set a flag in sessionStorage to trigger the second reload after the page reloads
										sessionStorage.setItem('reloadNeeded', 'true');
										// Perform the first reload
										window.location.reload();
									} else {
											console.error('Error saving vehicle data:', result.data);
									}
							})
							.catch(error => console.error('Error:', error));
			});
	}
});


document.addEventListener('DOMContentLoaded', function () {
	const savedVehiclesList = document.querySelector('.js-saved-vehicles-list');
	
	// Fetch saved vehicles from local storage
	const localStorageVehicles = localStorage.getItem('savedVehicles');
	const filterCars = localStorageVehicles ? JSON.parse(localStorageVehicles) : [];

	// Fetch saved vehicles from the database (already handled in the server-side PHP)
	const savedDbVehicles = Array.from(document.querySelectorAll('.saved-vehicle-item[data-vehicle-id]')); // Convert NodeList to Array

	// Function to check for duplicate vehicles between local storage and DB
	function isDuplicate(vehicle, vehicleList) {
			return vehicleList.some(dbVehicle => 
					dbVehicle.dataset.vehicleId === String(vehicle.vehicle.id)
			);
	}

	// Add local storage vehicles to the saved vehicles list if not duplicated
	filterCars.forEach((car, index) => {
			if (car.vehicle && !isDuplicate(car, savedDbVehicles)) {
					const vehicleText = `${car.vehicle.make} ${car.vehicle.model} ${car.vehicle.year} ${car.vehicle.trim}`;
					const vehicleItem = document.createElement('div');
					vehicleItem.classList.add('saved-vehicle-item');
					vehicleItem.dataset.vehicleId = car.vehicle.id ? car.vehicle.id : `local-${index}`; // Use correct ID from local storage vehicle
					vehicleItem.innerHTML = `
							${vehicleText}
							<span class="delete-vehicle js-delete-vehicle" style="cursor:pointer; color:red;">&times;</span>
					`;
					savedVehiclesList.appendChild(vehicleItem);
			}
	});

	// Event listener for deleting both DB and local storage vehicles
	if (savedVehiclesList) {
		savedVehiclesList.addEventListener('click', function (event) {
			if (event.target.classList.contains('js-delete-vehicle')) {
				const vehicleElement = event.target.closest('.saved-vehicle-item');
				const vehicleId = vehicleElement.dataset.vehicleId;
		
				if (vehicleId.startsWith('local-')) {
					// This is a local storage vehicle
					const localVehicleIndex = vehicleId.replace('local-', ''); // Get the index from vehicleId
					filterCars.splice(localVehicleIndex, 1); // Remove the vehicle from local storage array
					localStorage.setItem('savedVehicles', JSON.stringify(filterCars)); // Update local storage
					vehicleElement.remove(); // Remove from the UI
					window.location.reload();
				} else if (vehicleId) {
					// This is a database vehicle
					// Handle vehicle deletion from the database via AJAX
					fetch(vehicleData.ajaxurl + '?action=delete_user_vehicle_data', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							vehicle_id: vehicleId,
						}),
						credentials: 'same-origin',
					})
					.then(response => response.json())
					.then(result => {
						if (result.success) {
							// After successful deletion from DB, remove from local storage if needed
							const localVehicleIndex = filterCars.findIndex(car => String(car.vehicle.id) === vehicleId);
							if (localVehicleIndex !== -1) {
								filterCars.splice(localVehicleIndex, 1);
								localStorage.setItem('savedVehicles', JSON.stringify(filterCars));
							}
							// Remove from the UI
							vehicleElement.remove();
							window.location.reload();
						} else {
							console.error('Error deleting vehicle:', result.data);
							filterCars.splice(vehicleId, 1); // Remove the vehicle from local storage array
							localStorage.setItem('savedVehicles', JSON.stringify(filterCars)); // Update local storage
							vehicleElement.remove(); // Remove from the UI
							window.location.reload();
						}
					})
					.catch(error => console.error('Error:', error));
				}
			}
		});
	}
	
	

});

function removeVehicleFromLocalStorage(vehicleId) {
	let savedVehicles = JSON.parse(localStorage.getItem('savedVehicles')) || [];
	savedVehicles = savedVehicles.filter(vehicle => vehicle.vehicle.id !== vehicleId);
	localStorage.setItem('savedVehicles', JSON.stringify(savedVehicles));
}

function updateSavedCarsNumber() {
	const savedCarsCount = document.querySelectorAll('.select-vehicle-form__popup-item').length;

	const activeCarSection = document.querySelector('.select-vehicle-form__popup-active');
	const savedCarsSection = document.querySelector('.select-vehicle-form__popup-saved');
	const emptySection = document.querySelector('.select-vehicle-form__popup-empty');
	const savedCarsText = document.querySelector('.select-vehicle-form__saved-cars');

	if (savedCarsCount === 0) {
			activeCarSection.classList.add('hide');
			savedCarsSection.classList.add('hide');
			emptySection.classList.remove('hide');
	} else if (savedCarsCount === 1) {
			activeCarSection.classList.remove('hide');
			savedCarsSection.classList.add('hide');
			emptySection.classList.add('hide');
	} else {
			activeCarSection.classList.remove('hide');
			savedCarsSection.classList.remove('hide');
			emptySection.classList.add('hide');
	}

	if (savedCarsText) {
			savedCarsText.textContent = savedCarsCount;
	}
}

function clearAllVehicles() {
	localStorage.clear(); // Clears all local storage
	//savedVehiclesList.innerHTML = ''; // Clears the display
	console.log("All local storage cleared and display refreshed.");
}
if (document.getElementById('clearVehiclesButton')) {
	document.getElementById('clearVehiclesButton').addEventListener('click', clearAllVehicles);
}









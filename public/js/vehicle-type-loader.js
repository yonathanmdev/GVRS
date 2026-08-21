/* =========================================================
   VEHICLE TYPE LOADER (shared)

   Used by:
     - vehicle-type-cascade.js   (registration form)
     - vehicle-edit-modal inline script (edit form)

   Endpoint:
     GET /vehicles-car-types?brand=...

   Response:
     [
         { id, name, service_name, measurement }
     ]
   ========================================================= */

window.VehicleTypeLoader = (function () {
    'use strict';

    /**
     * Loads vehicle types for a given brand into typeSelect.
     *
     * @param {string|number} brandId
     * @param {HTMLSelectElement} typeSelect
     * @param {HTMLInputElement|null} serviceDisplay
     * @param {HTMLInputElement|null} measurementDisplay
     * @param {string|number|null} selectedTypeId - pre-select this type after load
     * @returns {Promise<void>}
     */
    async function load(
        brandId,
        typeSelect,
        serviceDisplay,
        measurementDisplay,
        selectedTypeId = null
    ) {

        if (!typeSelect) {
            throw new Error('VehicleTypeLoader: typeSelect is required');
        }

        /*
         * Reset dependent fields
         */
        typeSelect.innerHTML =
            '<option value="" disabled selected>በመጫን ላይ...</option>';

        typeSelect.disabled = true;

        if (serviceDisplay) serviceDisplay.value = '';
        if (measurementDisplay) measurementDisplay.value = '';

        if (!brandId) {

            typeSelect.innerHTML =
                '<option value="" disabled selected>መጀመሪያ ብራንድ ይምረጡ</option>';

            return;
        }

        const url =
            window.BASE_URL +
            '/vehicles-car-types?brand=' +
            encodeURIComponent(brandId);

        let response;

        try {

            response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

        } catch (networkError) {

            typeSelect.innerHTML =
                '<option value="" disabled selected>መረጃውን መጫን አልተቻለም</option>';

            throw networkError;
        }

        if (!response.ok) {

            typeSelect.innerHTML =
                '<option value="" disabled selected>መረጃውን መጫን አልተቻለም</option>';

            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();

        typeSelect.innerHTML =
            '<option value="" disabled selected>ይምረጡ</option>';

        (data || []).forEach(function (item) {

            const option = document.createElement('option');

            option.value = item.id;
            option.textContent = item.name;
            option.dataset.serviceName = item.service_name || '';
            option.dataset.measurement = item.measurement || '';

            if (
                selectedTypeId !== null &&
                String(item.id) === String(selectedTypeId)
            ) {
                option.selected = true;
            }

            typeSelect.appendChild(option);
        });

        typeSelect.disabled = false;

        /*
         * Populate service/measurement for whichever
         * option ended up selected (placeholder or matched).
         */
        const selectedOption =
            typeSelect.options[typeSelect.selectedIndex];

        if (selectedOption) {

            if (serviceDisplay) {
                serviceDisplay.value =
                    selectedOption.dataset.serviceName || '';
            }

            if (measurementDisplay) {
                measurementDisplay.value =
                    selectedOption.dataset.measurement || '';
            }
        }
    }

    return { load: load };

})();
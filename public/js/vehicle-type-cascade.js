(function () {
    'use strict';

    const brandSelect        = document.getElementById('car_brand_select');
    const typeSelect         = document.getElementById('car_type_select');
    const serviceDisplay     = document.getElementById('car_service_name_display');
    const measurementDisplay = document.getElementById('car_measurement_display');
    const capacityInput      = document.getElementById('car_capacity');

    if (!brandSelect || !typeSelect) return;

    /*
     * Rebuilds the capacity placeholder from whatever
     * measurement is currently set (e.g. "በኩንታል", "በሰው").
     * Falls back to a generic placeholder when no
     * measurement is known yet.
     */
    function updateCapacityPlaceholder() {

    if (!capacityInput) return;

    let measurement =
        measurementDisplay?.value?.trim();

    if (measurement && measurement.length > 1) {
        measurement = measurement.slice(1);
    }

    capacityInput.placeholder =
        measurement
            ? 'ለምሳሌ፦ 12 ' + measurement
            : 'ለምሳሌ፦ 12';
}

    brandSelect.addEventListener('change', function () {

        VehicleTypeLoader
            .load(this.value, typeSelect, serviceDisplay, measurementDisplay)
            .then(updateCapacityPlaceholder)
            .catch(function () {

                if (window.Swal) {

                    Swal.fire({
                        icon: 'error',
                        text: 'ዝርዝሩን መጫን አልተቻለም',
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });
    });

    typeSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        serviceDisplay.value = opt?.dataset.serviceName || '';
        measurementDisplay.value = opt?.dataset.measurement || '';

        updateCapacityPlaceholder();
    });

})();
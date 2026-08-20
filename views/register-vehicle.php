<?php
use App\Helpers\ViewHelper;

$is_vehicle_page = true;
?>

<style>
    /* =========================================================
       VEHICLE REGISTRATION - PROFESSIONAL / ELEVATED UI
       ========================================================= */

    :root {
        --vehicle-primary: #4e73df;
        --vehicle-primary-dark: #3b5fc4;
        --vehicle-success: #1cc88a;
        --vehicle-warning: #f6c23e;
        --vehicle-danger: #e74a3b;

        --vehicle-text: #2f3b52;
        --vehicle-muted: #858da0;

        --vehicle-border: #e5e9f2;
        --vehicle-bg: #f6f8fc;
        --vehicle-card: #ffffff;

        --vehicle-radius: 12px;

        --vehicle-shadow:
            0 4px 12px rgba(31, 45, 61, 0.06),
            0 12px 30px rgba(31, 45, 61, 0.08);

        --vehicle-shadow-hover:
            0 8px 18px rgba(31, 45, 61, 0.08),
            0 18px 40px rgba(31, 45, 61, 0.10);
    }


    /* =========================================================
       PAGE
       ========================================================= */

    #vehicle-register-form {
        color: var(--vehicle-text);
    }


    /* =========================================================
       MAIN CARDS
       ========================================================= */

    #vehicle-register-form > .card,
    #vehicle-summary-panel {
        border: 1px solid rgba(229, 233, 242, 0.95);
        border-radius: var(--vehicle-radius);
        background: var(--vehicle-card);
        box-shadow: var(--vehicle-shadow) !important;
        overflow: hidden;

        transition:
            box-shadow .25s ease,
            transform .25s ease;
    }

    #vehicle-register-form > .card:hover,
    #vehicle-summary-panel:hover {
        box-shadow: var(--vehicle-shadow-hover) !important;
    }


    .card-header {
        min-height: 64px;
        padding: .9rem 1.25rem;

        border-bottom: 1px solid var(--vehicle-border);

        background:
            linear-gradient(
                180deg,
                #ffffff 0%,
                #fbfcfe 100%
            ) !important;
    }


    .card-header .card-title {
        color: var(--vehicle-text);
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: .1px;
    }


    .card-header > i {
        width: 34px;
        height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: rgba(78, 115, 223, .09);

        font-size: .95rem;
    }


    .card-body {
        padding: 1.35rem;
    }


    /* =========================================================
       FORM LABELS
       ========================================================= */

    #vehicle-register-form label,
    #vehicleDetailsModal label {
        color: #465166;
        font-size: .84rem;
        font-weight: 600;
        margin-bottom: .45rem;
    }


    #vehicle-register-form .form-group {
        margin-bottom: 1rem;
    }


    .required-mark {
        margin-left: 2px;
        color: var(--vehicle-danger);
        font-weight: 700;
    }


    /* =========================================================
       FORM CONTROLS
       ========================================================= */

    #vehicle-register-form .form-control,
    #vehicleDetailsModal .form-control {
        min-height: 43px;

        border: 1px solid #dfe4ed;
        border-radius: 8px;

        background-color: #fff;
        color: #2f3b52;

        font-size: .88rem;

        box-shadow:
            inset 0 1px 2px rgba(31, 45, 61, .025);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background-color .2s ease;
    }


    #vehicle-register-form .form-control:hover,
    #vehicleDetailsModal .form-control:hover {
        border-color: #cbd3e1;
    }


    #vehicle-register-form .form-control:focus,
    #vehicleDetailsModal .form-control:focus {
        border-color: var(--vehicle-primary);

        background-color: #fff;

        box-shadow:
            0 0 0 .18rem rgba(78, 115, 223, .11),
            0 3px 8px rgba(31, 45, 61, .04);
    }


    #vehicle-register-form select.form-control,
    #vehicleDetailsModal select.form-control {
        cursor: pointer;
    }


    #vehicleDetailsModal .form-control::placeholder {
        color: #b0b7c6;
        font-weight: 400;
    }


    /* input-group unit suffix (price / capacity) */

    #vehicleDetailsModal .input-group .input-group-text {
        min-height: 43px;

        border: 1px solid #dfe4ed;
        border-left: 0;
        border-radius: 0 8px 8px 0;

        background: #f4f6fb;
        color: #7d879c;

        font-size: .8rem;
        font-weight: 600;
    }


    #vehicleDetailsModal .input-group .form-control {
        border-right: 0;
        border-radius: 8px 0 0 8px;
    }


    #vehicleDetailsModal .input-group .form-control:focus ~ .input-group-text,
    #vehicleDetailsModal .input-group:focus-within .input-group-text {
        border-color: var(--vehicle-primary);
    }


    /* =========================================================
       OWNERSHIP CATEGORY
       ========================================================= */

    #vehicle-register-form .ownership-title {
        display: flex;
        align-items: center;
        gap: .55rem;

        margin-bottom: .75rem;
    }


    #vehicle-register-form .ownership-title::before {
        content: '';

        width: 4px;
        height: 20px;

        border-radius: 10px;

        background: var(--vehicle-primary);
    }


    #vehicle-register-form .btn-group-toggle {
        display: flex;
        width: 100%;
        gap: .65rem;
    }


    #vehicle-register-form .btn-group-toggle .btn {
        position: relative;

        min-height: 82px;

        padding: .85rem .65rem;
        margin: 0 !important;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        border: 1px solid #dfe4ed !important;
        border-radius: 10px !important;

        background: #fff;
        color: #596579;

        font-size: .84rem;
        font-weight: 600;

        box-shadow:
            0 2px 5px rgba(31, 45, 61, .035);

        transition:
            all .2s ease;
    }


    #vehicle-register-form .btn-group-toggle .btn:hover {
        transform: translateY(-2px);

        border-color: rgba(78, 115, 223, .45) !important;

        color: var(--vehicle-primary);

        box-shadow:
            0 6px 14px rgba(31, 45, 61, .08);
    }


    #vehicle-register-form .btn-group-toggle .btn i {
        width: 34px;
        height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        margin-bottom: .4rem !important;

        border-radius: 9px;

        background: #f1f4fb;
        color: var(--vehicle-primary);

        font-size: .95rem;

        transition: all .2s ease;
    }


    #vehicle-register-form .btn-group-toggle .btn.active {
        color: #fff !important;

        background:
            linear-gradient(
                135deg,
                var(--vehicle-primary),
                var(--vehicle-primary-dark)
            ) !important;

        border-color: var(--vehicle-primary) !important;

        box-shadow:
            0 7px 16px rgba(78, 115, 223, .23);
    }


    #vehicle-register-form .btn-group-toggle .btn.active i {
        background: rgba(255, 255, 255, .16);
        color: #fff;
    }


    /* =========================================================
       CATEGORY GROUPS
       ========================================================= */

    #vehicle-register-form .category-group {
        position: relative;

        margin-top: 1rem;
        padding: 1.15rem 1.2rem;

        border: 1px solid #e3e7ef;
        border-radius: 11px;

        background:
            linear-gradient(
                135deg,
                #fbfcff 0%,
                #f7f9fd 100%
            );

        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .9),
            0 4px 12px rgba(31, 45, 61, .035);
    }


    #vehicle-register-form .category-group::before {
        content: '';

        position: absolute;
        left: 0;
        top: 14px;
        bottom: 14px;

        width: 3px;

        border-radius: 0 5px 5px 0;

        background: var(--vehicle-primary);
    }


    #vehicle-register-form .category-group label {
        color: #4b566b;
    }


    /* =========================================================
       ACTION AREA
       ========================================================= */

    #vehicle-register-form > hr {
        margin: 1.5rem 0 1rem;

        border-top: 1px solid var(--vehicle-border);
    }


    #vehicle-submit-btn {
        min-height: 44px;

        border: 0;
        border-radius: 8px;

        font-size: .87rem;
        font-weight: 600;

        background:
            linear-gradient(
                135deg,
                var(--vehicle-primary),
                var(--vehicle-primary-dark)
            );

        box-shadow:
            0 5px 12px rgba(78, 115, 223, .18);

        transition:
            transform .2s ease,
            box-shadow .2s ease,
            opacity .2s ease;
    }


    #vehicle-submit-btn:not(:disabled):hover {
        transform: translateY(-2px);

        box-shadow:
            0 8px 18px rgba(78, 115, 223, .25);
    }


    #vehicle-submit-btn:disabled {
        cursor: not-allowed;
        opacity: .55;
        box-shadow: none;
    }


    /* =========================================================
       SUMMARY PANEL
       ========================================================= */

    #vehicle-summary-panel {
        position: sticky;
        top: 1rem;
    }


    #vehicle-summary-panel .card-body {
        padding: 1.2rem 1.25rem;
    }


    #vehicle-summary-panel .summary-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 1rem;

        padding: .75rem 0;

        border-bottom: 1px dashed #e2e6ee;

        font-size: .84rem;
    }


    #vehicle-summary-panel .summary-row:first-child {
        padding-top: .2rem;
    }


    #vehicle-summary-panel .summary-row:last-of-type {
        border-bottom: none;
    }


    #vehicle-summary-panel .summary-label {
        color: #8a93a5;
        font-weight: 500;
    }


    #vehicle-summary-panel .summary-value {
        max-width: 60%;

        color: #354158;

        font-weight: 700;
        text-align: right;

        overflow-wrap: anywhere;
    }


    #vehicle-summary-panel .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 32px;

        padding: .35rem .85rem;

        border-radius: 999px;

        font-size: .75rem;
        font-weight: 700;

        box-shadow:
            0 2px 5px rgba(31, 45, 61, .04);
    }


    #vehicle-summary-panel .status-pill::before {
        content: '';

        width: 7px;
        height: 7px;

        margin-right: .45rem;

        border-radius: 50%;

        background: currentColor;
    }


    #vehicle-summary-panel .status-pill.pending {
        background: #fff7df;
        color: #9b7412;

        border: 1px solid #f5e4a9;
    }


    #vehicle-summary-panel .status-pill.ready {
        background: #e9faf3;
        color: #16845c;

        border: 1px solid #bfeeda;
    }


    #vehicle-summary-panel hr {
        border-top: 1px solid var(--vehicle-border);
    }


    #vehicle-summary-panel .text-muted {
        color: #8b94a6 !important;
        line-height: 1.65;
    }


    /* =========================================================
       MODAL SHELL
       ========================================================= */

    #vehicleDetailsModal .modal-dialog {
        max-width: 920px;
    }


    #vehicleDetailsModal .modal-content {
        border: 0;
        border-radius: 14px;

        overflow: hidden;

        box-shadow:
            0 15px 35px rgba(20, 30, 50, .15),
            0 30px 70px rgba(20, 30, 50, .16);
    }


    #vehicleDetailsModal .modal-header {
        min-height: 64px;

        padding: .85rem 1.25rem;

        border-bottom: 1px solid var(--vehicle-border);

        background:
            linear-gradient(
                135deg,
                #ffffff,
                #f8faff
            );
    }


    #vehicleDetailsModal .modal-header-text {
        display: flex;
        flex-direction: column;
        gap: .1rem;
    }


    #vehicleDetailsModal .modal-title {
        display: flex;
        align-items: center;

        color: var(--vehicle-text);

        font-size: .98rem;
        font-weight: 700;
    }


    #vehicleDetailsModal .modal-subtitle {
        margin: 0;
        padding-left: 44px;

        color: var(--vehicle-muted);
        font-size: .76rem;
        font-weight: 500;
    }


    #vehicleDetailsModal .modal-title i {
        width: 34px;
        height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        margin-right: .5rem;

        border-radius: 9px;

        background: rgba(78, 115, 223, .09);
    }


    #vehicleDetailsModal .close {
        width: 34px;
        height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        margin: 0;
        padding: 0;

        border-radius: 8px;

        color: #7d8799;
        opacity: 1;

        transition: all .2s ease;
    }


    #vehicleDetailsModal .close:hover {
        background: #f1f3f7;
        color: #3d475a;
    }


    #vehicleDetailsModal .modal-body {
        padding: 1.4rem;
        background: #fbfcfe;

        max-height: 75vh;
        overflow-y: auto;
    }


    #vehicleDetailsModal .modal-footer {
        padding: .9rem 1.25rem;

        border-top: 1px solid var(--vehicle-border);

        background: #fbfcfe;
    }


    #vehicleDetailsModal .modal-footer-hint {
        margin: 0;
        margin-right: auto;

        color: var(--vehicle-muted);
        font-size: .76rem;
    }


    /* =========================================================
       VEHICLE FORM SECTIONS (POPUP FIELDS)
       ========================================================= */

    #vehicleDetailsModal .vehicle-section {
        position: relative;

        margin-bottom: 1.35rem;
        padding: 1.25rem 1.35rem;

        border: 1px solid #e3e7ef;
        border-radius: 12px;

        background: #ffffff;

        box-shadow:
            0 1px 2px rgba(31, 45, 61, .03),
            0 6px 16px rgba(31, 45, 61, .035);
    }


    #vehicleDetailsModal .vehicle-section::before {
        content: '';

        position: absolute;

        left: 0;
        top: 14px;
        bottom: 14px;

        width: 3px;

        border-radius: 0 5px 5px 0;

        background: var(--vehicle-primary);
    }


    #vehicleDetailsModal .vehicle-section:last-child {
        margin-bottom: 0;
    }


    #vehicleDetailsModal .vehicle-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;

        width: 100%;

        margin-bottom: .3rem;
        padding-bottom: .65rem;

        border-bottom: 1px solid var(--vehicle-border);
    }


    #vehicleDetailsModal .vehicle-section-title-main {
        display: flex;
        align-items: center;

        color: #3d4b65;

        font-size: .92rem;
        font-weight: 700;
    }


    #vehicleDetailsModal .vehicle-section-title-main i {
        width: 30px;
        height: 30px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        margin-right: .55rem;

        border-radius: 8px;

        background: rgba(78, 115, 223, .09);
        color: var(--vehicle-primary);

        font-size: .82rem;
    }


    #vehicleDetailsModal .vehicle-section-badge {
        padding: .2rem .6rem;

        border-radius: 999px;

        background: #f1f4fb;
        color: #7787a8;

        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }


    #vehicleDetailsModal .vehicle-section-desc {
        margin: .45rem 0 1.1rem;

        color: var(--vehicle-muted);
        font-size: .78rem;
        line-height: 1.55;
    }


    #vehicleDetailsModal .vehicle-section .form-row {
        margin-left: -.5rem;
        margin-right: -.5rem;
    }


    #vehicleDetailsModal .vehicle-section .form-row > .form-group {
        padding-left: .5rem;
        padding-right: .5rem;
    }


    #vehicleDetailsModal .vehicle-section .form-group {
        margin-bottom: 1.1rem;
    }


    #vehicleDetailsModal .vehicle-section .form-group:last-child,
    #vehicleDetailsModal .vehicle-section .form-row:last-child > .form-group {
        margin-bottom: 0;
    }


    #vehicleDetailsModal .vehicle-section label {
        display: flex;
        align-items: center;

        color: #505b6f;

        font-size: .79rem;
        font-weight: 600;
    }


    #vehicleDetailsModal .field-hint {
        margin-top: .35rem;

        color: #9aa2b3;
        font-size: .71rem;
    }


    /* =========================================================
       FINAL SUBMIT BUTTON
       ========================================================= */

    #vehicle-final-submit-btn {
        min-height: 42px;

        padding-left: 1.5rem;
        padding-right: 1.5rem;

        border-radius: 8px;

        font-size: .82rem;
        font-weight: 600;

        background:
            linear-gradient(
                135deg,
                #1cc88a,
                #159b6c
            );

        border-color: transparent;

        box-shadow:
            0 4px 10px rgba(28, 200, 138, .18);

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }


    #vehicle-final-submit-btn:hover {
        transform: translateY(-1px);

        box-shadow:
            0 7px 15px rgba(28, 200, 138, .25);
    }


    /* =========================================================
       MOBILE RESPONSIVENESS
       ========================================================= */

    @media (max-width: 991.98px) {

        #vehicle-summary-panel {
            position: static;
            margin-top: 1rem;
        }


        .card-body {
            padding: 1.15rem;
        }


        #vehicleDetailsModal .modal-dialog {
            max-width: calc(100% - 1rem);
            margin: .5rem auto;
        }
    }


    @media (max-width: 767.98px) {

        #vehicle-register-form .btn-group-toggle {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: .6rem;
        }


        #vehicle-register-form .btn-group-toggle .btn {
            width: 100%;
            min-height: 78px;
        }


        #vehicle-register-form .category-group {
            padding: 1rem;
        }


        #vehicle-summary-panel .summary-row {
            align-items: center;
        }


        #vehicleDetailsModal .modal-body {
            padding: 1rem;
        }


        #vehicleDetailsModal .modal-footer {
            display: flex;

            flex-wrap: wrap;

            gap: .5rem;
        }


        #vehicleDetailsModal .modal-footer-hint {
            width: 100%;
            margin-right: 0;
            margin-bottom: .35rem;
        }


        #vehicleDetailsModal .modal-footer > button {
            margin: 0 !important;
        }


        #vehicleDetailsModal .vehicle-section {
            padding: 1rem;
        }


        #vehicleDetailsModal .modal-subtitle {
            padding-left: 0;
        }
    }


    @media (max-width: 575.98px) {

        .card-header {
            min-height: 58px;
            padding: .8rem 1rem;
        }


        .card-header .card-title {
            font-size: .92rem;
        }


        .card-body {
            padding: 1rem;
        }


        #vehicle-register-form .btn-group-toggle {
            grid-template-columns: 1fr 1fr;
        }


        #vehicle-register-form .btn-group-toggle .btn {
            min-height: 74px;

            padding: .65rem .4rem;

            font-size: .73rem;
        }


        #vehicle-register-form .btn-group-toggle .btn i {
            width: 30px;
            height: 30px;

            font-size: .82rem;
        }


        #vehicle-summary-panel .summary-row {
            flex-direction: column;
            align-items: flex-start;

            gap: .25rem;
        }


        #vehicle-summary-panel .summary-value {
            max-width: 100%;
            text-align: left;
        }


        #vehicle-summary-panel .text-center {
            text-align: left !important;
        }


        #vehicle-summary-panel .status-pill {
            width: 100%;
        }


        #vehicleDetailsModal .modal-dialog {
            max-width: calc(100% - .5rem);
            margin: .25rem auto;
        }


        #vehicleDetailsModal .modal-header {
            padding: .75rem 1rem;
        }


        #vehicleDetailsModal .modal-body {
            padding: .9rem;
        }


        #vehicleDetailsModal .modal-footer {
            padding: .75rem;
        }


        #vehicleDetailsModal .vehicle-section {
            padding: .9rem;
        }


        #vehicleDetailsModal .vehicle-section-title-main {
            font-size: .86rem;
        }


        #vehicle-final-submit-btn {
            width: 100%;
        }
    }


    /* =========================================================
       EXTRA SMALL DEVICES
       ========================================================= */

    @media (max-width: 380px) {

        #vehicle-register-form .btn-group-toggle {
            grid-template-columns: 1fr;
        }
    }


    /* =========================================================
       ACCESSIBILITY
       ========================================================= */

    #vehicle-register-form button:focus-visible,
    #vehicle-register-form input:focus-visible,
    #vehicle-register-form select:focus-visible,
    #vehicleDetailsModal button:focus-visible,
    #vehicleDetailsModal input:focus-visible,
    #vehicleDetailsModal select:focus-visible {
        outline: 2px solid rgba(78, 115, 223, .35);
        outline-offset: 2px;
    }
</style>

<section class="content">
  <div class="container-fluid">
<div class="row">

    <!-- =====================================================
         MAIN REGISTRATION CARD
         ===================================================== -->

    <div class="col-12 col-lg-8 mb-4">

        <div class="card shadow-sm">

            <div class="card-header bg-white d-flex align-items-center">

                <i class="fas fa-car-side text-primary mr-2"></i>

                <h3 class="card-title mb-0">
                    ተሽከርካሪ ምዝገባ
                </h3>

            </div>


            <div class="card-body">

                <form id="vehicle-register-form" novalidate>

                    <?= \App\Helpers\Csrf::field(); ?>


                    <input type="hidden"
                           id="branch_id"
                           name="branch_id"
                           value="">


                    <input type="hidden"
                           id="zone_id"
                           name="zone_id"
                           value="">


                    <!-- =================================================
                         OWNERSHIP CATEGORY
                         ================================================= -->

                    <div class="form-group">

                        <div class="ownership-title">

                            <label class="mb-0 font-weight-bold">
                                ተሽከርካሪ የሚመዘግቡለትን ተቋም ይምረጡ
                            </label>

                        </div>


                        <div class="btn-group btn-group-toggle d-flex"
                             data-toggle="buttons">

                            <!-- REGIONAL -->

                            <label class="btn btn-outline-primary flex-fill">

                                <input type="radio"
                                       name="ownership_category"
                                       value="regional"
                                       data-category="regional">

                                <i class="fas fa-landmark d-block"></i>

                                <span>
                                    የክልል ተጠሪ ተቋም
                                </span>

                            </label>


                            <!-- INSTITUTION -->

                            <label class="btn btn-outline-primary flex-fill">

                                <input type="radio"
                                       name="ownership_category"
                                       value="institution"
                                       data-category="institution">

                                <i class="fas fa-building d-block"></i>

                                <span>
                                    ተጠሪ መ/ቤት
                                </span>

                            </label>


                            <!-- DEPARTMENT -->

                            <label class="btn btn-outline-primary flex-fill">

                                <input type="radio"
                                       name="ownership_category"
                                       value="department"
                                       data-category="department">

                                <i class="fas fa-sitemap d-block"></i>

                                <span>
                                    መምሪያ
                                </span>

                            </label>


                            <!-- WOREDA -->

                            <label class="btn btn-outline-primary flex-fill">

                                <input type="radio"
                                       name="ownership_category"
                                       value="woreda"
                                       data-category="woreda">

                                <i class="fas fa-map-marker-alt d-block"></i>

                                <span>
                                    ወረዳ
                                </span>

                            </label>

                        </div>

                    </div>


                    <!-- =================================================
                         REGIONAL
                         ================================================= -->

                    <div class="form-group category-group"
                         id="group-regional"
                         style="display:none;">

                        <label>
                            የክልል ተጠሪ ተቋም
                        </label>


                        <select class="form-control final-select"
                                id="regional_bureau_select">

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>


                            <?php foreach ($bureaus as $bureau): ?>

                                <option value="<?= $bureau['uuid'] ?>">
                                    <?= ViewHelper::e($bureau['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- =================================================
                         INSTITUTION
                         ================================================= -->

                    <div class="form-group category-group"
                         id="group-institution"
                         style="display:none;">

                        <label>
                            እናት መ/ቤት
                        </label>


                        <select class="form-control"
                                id="inst_bureau_select"
                                data-loads="#institution_select"
                                data-endpoint="/vehicles-institutions"
                                data-param="bureau_id">

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>


                            <?php foreach ($bureaus as $bureau): ?>

                                <option value="<?= $bureau['uuid'] ?>">
                                    <?= ViewHelper::e($bureau['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>


                        <label class="mt-3"
                               id="institution-label"
                               style="display:none;">

                            ተጠሪ መ/ቤት

                        </label>


                        <select class="form-control final-select"
                                id="institution_select"
                                style="display:none;"
                                disabled>

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>

                        </select>

                    </div>


                    <!-- =================================================
                         DEPARTMENT / MEMRIYA
                         ================================================= -->

                    <div class="form-group category-group"
                         id="group-memriya"
                         style="display:none;">

                        <label>
                            መምሪያ
                        </label>


                        <select class="form-control"
                                id="memriya_select">

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>


                            <?php foreach ($memriya as $memriya): ?>

                                <option value="<?= $memriya['uuid'] ?>">
                                    <?= ViewHelper::e($memriya['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>


                        <label class="mt-3"
                               id="memriya-zone-label"
                               style="display:none;">

                            ዞን/ከተማ አስተዳደር

                        </label>


                        <select class="form-control"
                                id="memriya_zone_select"
                                style="display:none;">

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>


                            <?php foreach ($zones as $zone): ?>

                                <option value="<?= $zone['uuid'] ?>">
                                    <?= ViewHelper::e($zone['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- =================================================
                         WOREDA
                         ================================================= -->

                    <div class="form-group category-group"
                         id="group-woreda"
                         style="display:none;">

                        <label>
                            ዞን
                        </label>


                        <select class="form-control"
                                id="woreda_zone_select"
                                data-loads="#woreda_select"
                                data-endpoint="/vehicles-woredas"
                                data-param="zone_id">

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>


                            <?php foreach ($zones as $zone): ?>

                                <option value="<?= $zone['uuid'] ?>">
                                    <?= ViewHelper::e($zone['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>


                        <label class="mt-3"
                               id="woreda-label"
                               style="display:none;">

                            ወረዳ/ክ/ከተማ

                        </label>


                        <select class="form-control final-select"
                                id="woreda_select"
                                style="display:none;"
                                disabled>

                            <option value=""
                                    disabled
                                    selected>
                                ይምረጡ
                            </option>

                        </select>

                    </div>


                    <!-- =================================================
                         ACTION
                         ================================================= -->

                    <hr>


                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <small class="text-muted mb-2 mr-3">

                            <i class="fas fa-info-circle mr-1"></i>

                            ከመረጡ በኋላ ለመመዝገብ
                            የተሽከርካሪ መረጃ ያስገቡ
                            የሚለውን ይጫኑ።

                        </small>


                        <button type="button"
                                class="btn btn-primary px-4"
                                id="vehicle-submit-btn"
                                disabled
                                data-toggle="modal"
                                data-target="#vehicleDetailsModal">

                            <i class="fas fa-plus mr-1"></i>

                            የተሽከርካሪ መረጃ ያስገቡ

                        </button>

                    </div>


                    <!-- =================================================
                         VEHICLE DETAILS MODAL (POPUP)
                         ================================================= -->

                    <div class="modal fade"
                         id="vehicleDetailsModal"
                         tabindex="-1"
                         role="dialog"
                         aria-hidden="true">

                        <div class="modal-dialog modal-lg modal-dialog-centered"
                             role="document">

                            <div class="modal-content">


                                <!-- MODAL HEADER -->

                                <div class="modal-header">

                                    <div class="modal-header-text">

                                        <h5 class="modal-title">

                                            <i class="fas fa-car mr-2 text-primary"></i>

                                            የተሽከርካሪ መረጃ

                                        </h5>


                                        <p class="modal-subtitle">
                                            ትክክለኛ የተሽከርካሪ ዝርዝር መረጃ ያስገቡ
                                        </p>

                                    </div>


                                    <button type="button"
                                            class="close"
                                            data-dismiss="modal"
                                            aria-label="Close">

                                        <span aria-hidden="true">
                                            &times;
                                        </span>

                                    </button>

                                </div>


                                <!-- MODAL BODY -->

                                <div class="modal-body">

                                    <fieldset id="vehicle-fields">


                                        <!-- =================================================
                                             SECTION 1 · IDENTIFICATION
                                             ================================================= -->

                                        <div class="vehicle-section">

                                            <div class="vehicle-section-title">

                                                <div class="vehicle-section-title-main">

                                                    <i class="fas fa-id-card"></i>

                                                    <span>
                                                        መለያ መረጃ
                                                    </span>

                                                </div>


                                                <span class="vehicle-section-badge">
                                                    1 / 2
                                                </span>

                                            </div>


                                            <p class="vehicle-section-desc">
                                                ተሽከርካሪውን ለመለየት የሚያገለግሉ ቁልፍ
                                                ቁጥሮች እና መደበኛ መረጃዎች።
                                            </p>


                                            <div class="form-row">

                                                <div class="form-group col-md-4">

                                                    <label>
                                                        ብራንድ
                                                        <span class="required-mark">*</span>
                                                    </label>

                                                    <select class="form-control"
                                                            id="car_brand_select"
                                                            data-loads="#car_type_select"
                                                            data-endpoint="/vehicles-car-types"
                                                            data-param="brand"
                                                            required>

                                                        <option value=""
                                                                disabled
                                                                selected>
                                                            ይምረጡ
                                                        </option>

                                                        <?php foreach ($brands as $brand): ?>

                                                            <option value="<?= ViewHelper::e($brand['brand_id']) ?>">
                                                                <?= ViewHelper::e($brand['brand_name']) ?>
                                                            </option>

                                                        <?php endforeach; ?>

                                                    </select>

                                                </div>


                                                <div class="form-group col-md-4">

                                                    <label>
                                                  የተሽከርካሪው/ ማሽነሪው ዓይነት
                                                        <span class="required-mark">*</span>
                                                    </label>

                                                    <select class="form-control"
                                                            id="car_type_select"
                                                            name="vehicle_type"
                                                            required
                                                            disabled>

                                                        <option value=""
                                                                disabled
                                                                selected>
                                                            መጀመሪያ ብራንድ ይምረጡ
                                                        </option>

                                                    </select>

                                                </div>


                                                <div class="form-group col-md-4">

                                                    <label>
                                                        የአገልግሎት ዓይነት 
                                                        
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           id="car_service_name_display"
                                                           readonly
                                                           tabindex="-1">

                                                    <div class="field-hint">
                                                        ከመረጡት ዓይነት ራሱ በራሱ ይሞላል
                                                    </div>

                                                </div>


                                                <div class="form-group col-md-4">

                                                    <label>
                                                        ሰሌዳ ቁጥር
                                                        <span class="required-mark">*</span>
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           name="plate_number"
                                                           placeholder="ለምሳሌ፦ AA-12345"
                                                           required>

                                                </div>
                                                <div class="form-group col-md-4">

                                                    <label>
                                                        ሞዴል
                                                     
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           name="model"
                                                           id="model"
                                                           placeholder="ለምሳሌ፦ 2019"
                                                           >

                                                </div>


                                                <div class="form-group col-md-4">

                                                    <label>
                                                        ቻንሲ ቁጥር
                                                       
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           name="chassis_number"
                                                           placeholder="የቻንሲ ቁጥር ያስገቡ"
                                                           >

                                                </div>


                                                <div class="form-group col-md-4">

                                                    <label>
                                                        የሞተር ቁጥር
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           name="engine_number"
                                                           placeholder="የሞተር ቁጥር ያስገቡ">

                                                </div>

                                            </div>

                                        </div>


                                        <!-- =================================================
                                             SECTION 2 · CAPACITY, VALUE & STATUS
                                             ================================================= -->

                                        <div class="vehicle-section">

                                            <div class="vehicle-section-title">

                                                <div class="vehicle-section-title-main">

                                                    <i class="fas fa-file-invoice-dollar"></i>

                                                    <span>
                                                        አቅም፣ ዋጋ እና ሁኔታ
                                                    </span>

                                                </div>


                                                <span class="vehicle-section-badge">
                                                    2 / 2
                                                </span>

                                            </div>


                                            <p class="vehicle-section-desc">
                                                የተሽከርካሪውን የመጫን አቅም፣ የግዢ/ምርት
                                                ዓመት፣ ግምታዊ ዋጋ እና አሁናዊ ሁኔታ ያስገቡ።
                                            </p>


                                            <div class="form-row">

                     


                                                <div class="form-group col-md-2">

                                                    <label>
                                                        መለኪያ 
                                                        
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           id="car_measurement_display"
                                                           readonly
                                                           tabindex="-1">

                                                </div>

                           <div class="form-group col-md-4">

                                                    <label>
                                                        የመጫን አቅም
                                                         <span class="required-mark">*</span>
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           name="capacity"
                                                           placeholder="ለምሳሌ፦ 5 ኩንታል" required>

                                                    <div class="field-hint">
                                                        በኩንታል / በሰው / በፈረስ ጉልበት ይግለጹ
                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6">

                                                    <label>
                                                        ተሽከርካሪ/ማሽነሪ ግምታዊ ዋጋ 
                                                         <span class="required-mark">*</span>
                                                    </label>

                                                    <div class="input-group">

                                                        <input type="number"
                                                               class="form-control"
                                                               name="estimated_price"
                                                               step="any"
                                                               min="0"
                                                               placeholder="1500000" required>

                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                ብር
                                                            </span>
                                                        </div>

                                                    </div>

                                                </div>


                                                <div class="form-group col-md-3">

                                                    <label>
                                                        የተመረተበት ዓ.ም 
                                                         <span class="required-mark">*</span>
                                                    </label>

                                                    <input type="number"
                                                           class="form-control"
                                                           name="manufactured_year"
                                                           id="manufactured_year"
                                                           min="1950"
                                                          max="<?php echo date('Y'); ?>"
                                                           placeholder="ዓ.ም" require>

                                                </div>


                                                <div class="form-group col-md-3">

                                                    <label>
                                                        የተገዛበት ዓ.ም 
                                                         <span class="required-mark">*</span>
                                                    </label>

                                                    <input type="number"
                                                           class="form-control"
                                                           name="purchase_year"
                                                           id="purchase_year"
                                                           min="1950"
                                                          max="<?php echo date('Y'); ?>"
                                                           placeholder="ዓ.ም" required>

                                                </div>


                                                <div class="form-group col-md-6">

                                                    <label>
                                                        አሁናዊ ሁኔታ 
                                                         <span class="required-mark">*</span>                            
                                                    </label>

                                                    <select class="form-control"
                                                            name="vehicle_status" required>

                                                        <option value=""
                                                                selected
                                                                disabled>
                                                            ይምረጡ
                                                        </option>

                                                        <option value="active">
                                                            በአካል ያለ
                                                        </option>

                                                        <option value="lost">
                                                            የጠፋ
                                                        </option>

                                                        <option value="destroyed">
                                                            የወደመ
                                                        </option>

                                                        <option value="disposed">
                                                            የተወገደ
                                                        </option>

                                                    </select>

                                                </div>

                                            </div>

                                        </div>

                                    </fieldset>

                                </div>


                                <!-- =================================================
                                     MODAL FOOTER
                                     ================================================= -->

                                <div class="modal-footer justify-content-end">

                                    <p class="modal-footer-hint">

                                        <i class="fas fa-asterisk mr-1"
                                           style="font-size:.6rem; color:#e74a3b;"></i>

                                        የተከዋወኑ መስኮች ግዴታ ናቸው

                                    </p>


                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            data-dismiss="modal">

                                        <i class="fas fa-times mr-1"></i>

                                        ዝጋ

                                    </button>


                                    <button type="submit"
                                            class="btn btn-success px-4"
                                            id="vehicle-final-submit-btn">

                                        <i class="fas fa-save mr-1"></i>

                                        መዝግብ

                                    </button>

                                </div>


                            </div>

                        </div>

                    </div>

                    <!-- =====================================================
                         END MODAL
                         ===================================================== -->

                </form>

            </div>

        </div>

    </div>


    <!-- =====================================================
         SUMMARY SIDEBAR
         ===================================================== -->

    <div class="col-12 col-lg-4 mb-4">

        <div class="card shadow-sm"
             id="vehicle-summary-panel">

            <div class="card-header bg-white d-flex align-items-center">

                <i class="fas fa-clipboard-list text-primary mr-2"></i>

                <h3 class="card-title mb-0">
                    የምዝገባ ሁኔታ
                </h3>

            </div>


            <div class="card-body">

                <div class="summary-row">

                    <span class="summary-label">
                        ምድብ
                    </span>

                    <span class="summary-value"
                          id="summary-category">

                        አልተመረጠም

                    </span>

                </div>


                <div class="summary-row">

                    <span class="summary-label">
                        የተመረጠው ተቋም
                    </span>

                    <span class="summary-value"
                          id="summary-office">

                        —

                    </span>

                </div>


                <div class="summary-row">

                    <span class="summary-label">
                        ዞን
                    </span>

                    <span class="summary-value"
                          id="summary-zone">

                        —

                    </span>

                </div>


                <div class="text-center mt-3">

                    <span class="status-pill pending"
                          id="summary-status">

                        ተጨማሪ ምርጫ ያስፈልጋል

                    </span>

                </div>


                <hr>


                <p class="text-muted small mb-0">

                    <i class="fas fa-info-circle mr-1"></i>

                    ተቋም/ወረዳ/ዞን/መምሪያ ከተመረጠ በኋላ
                    ለመረጡት ተቋም ብዙ ተሽከርካሪዎችን መመዝገብ ይችላሉ።

                </p>

            </div>

        </div>

    </div>

</div>

  </div>
</section>
<script nonce="<?= $GLOBALS['nonce'] ?? '' ?>">

    /* =========================================================
       SUMMARY UPDATER
       ========================================================= */

    (function () {

        const categoryLabels = {

            regional: 'የክልል ተጠሪ ተቋም',

            institution: 'ተጠሪ መ/ቤት',

            department: 'መምሪያ',

            woreda: 'ወረዳ'

        };


        const summaryCategory =
            document.getElementById(
                'summary-category'
            );


        const summaryOffice =
            document.getElementById(
                'summary-office'
            );


        const summaryZone =
            document.getElementById(
                'summary-zone'
            );


        const summaryStatus =
            document.getElementById(
                'summary-status'
            );


        const openModalBtn =
            document.getElementById(
                'vehicle-submit-btn'
            );


        function selectedText(select) {

            if (!select || !select.value) {
                return '—';
            }


            const opt =
                select.options[
                    select.selectedIndex
                ];


            return opt
                ? opt.textContent.trim()
                : '—';
        }


        function refreshSummary() {

            const checked =
                document.querySelector(
                    'input[name="ownership_category"]:checked'
                );


            summaryCategory.textContent =
                checked
                    ? categoryLabels[checked.value]
                    : 'አልተመረጠም';


            let office = '—';
            let zone = '—';


            if (checked) {

                switch (checked.value) {

                    case 'regional':

                        office =
                            selectedText(
                                document.getElementById(
                                    'regional_bureau_select'
                                )
                            );

                        break;


                    case 'institution':

                        office =
                            selectedText(
                                document.getElementById(
                                    'institution_select'
                                )
                            );

                        break;


                    case 'department':

                        office =
                            selectedText(
                                document.getElementById(
                                    'memriya_select'
                                )
                            );


                        zone =
                            selectedText(
                                document.getElementById(
                                    'memriya_zone_select'
                                )
                            );

                        break;


                    case 'woreda':

                        office =
                            selectedText(
                                document.getElementById(
                                    'woreda_select'
                                )
                            );


                        zone =
                            selectedText(
                                document.getElementById(
                                    'woreda_zone_select'
                                )
                            );

                        break;

                }

            }


            summaryOffice.textContent = office;

            summaryZone.textContent = zone;


            const ready =
                !openModalBtn.disabled;


            summaryStatus.textContent =
                ready
                    ? 'ለምዝገባ ዝግጁ ነው'
                    : 'ተጨማሪ ምርጫ ያስፈልጋል';


            summaryStatus.classList.toggle(
                'ready',
                ready
            );


            summaryStatus.classList.toggle(
                'pending',
                !ready
            );

        }


        document.addEventListener(
            'change',
            function (e) {

                if (
                    e.target.closest(
                        '#vehicle-register-form'
                    )
                ) {

                    setTimeout(
                        refreshSummary,
                        50
                    );

                }

            }
        );


        refreshSummary();

    })();

</script>
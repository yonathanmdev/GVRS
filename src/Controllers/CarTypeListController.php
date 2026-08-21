<?php

namespace App\Controllers;

use App\Models\CarTypeListModel;
use App\Helpers\Csrf;

class CarTypeListController extends BaseController
{
    /**
     * የዝርዝር ገጽ ማሳያ
     */
    public function index()
    {
        $model = new CarTypeListModel($this->db);
        $typeLists = $model->getCarTypeLists();

        $this->render('car-type-list', [
            'typeLists'   => $typeLists,
            'currentLang' => $_SESSION['lang'] ?? 'am'
        ]);
    }

    /**
     * አዲስ መረጃ የመመዝገቢያ Process
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/car-type-list');
            exit;
        }

        // CSRF Verification
        $token = $_POST['csrf_token'] ?? $_POST['_token'] ?? '';
        if (!Csrf::verify($token)) {
            $_SESSION['error'] = 'ደህንነቱ ያልተጠበቀ ጥያቄ (Invalid CSRF Token)!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        if (!isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = 'እባክዎ መጀመሪያ ወደ ሲስተሙ ይግቡ።';
            header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/login');
            exit;
        }

        // Inputs Sanitization
        $cartype     = filter_input(INPUT_POST, 'cartype', FILTER_SANITIZE_SPECIAL_CHARS);
        $carcatagory = filter_input(INPUT_POST, 'carcatagory', FILTER_SANITIZE_SPECIAL_CHARS);
        $cartype     = trim($cartype ?? '');
        $carcatagory = trim($carcatagory ?? '');

        $allowedCategories = ['vehicle', 'machine'];

        if (empty($cartype) || !in_array($carcatagory, $allowedCategories, true)) {
            $_SESSION['error'] = 'እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ይሙሉ!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        $model = new CarTypeListModel($this->db);

        // Duplication Check
        if ($model->isDuplicate($cartype)) {
            $_SESSION['error'] = 'ይህ የመኪና/ማሽን አይነት ቀደም ሲል የተመዘገበ ነው!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        // UUID Generation
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $saved = $model->createCarType([
            'uuid'          => $uuid,
            'cartype'       => $cartype,
            'carcatagory'   => $carcatagory,
            'registered_by' => (int) $_SESSION['user']['id']
        ]);

        if ($saved) {
            $_SESSION['success'] = 'የመኪና/ማሽን አይነቱ በተሳካ ሁኔታ ተመዝግቧል!';
        } else {
            $_SESSION['error'] = 'መረጃውን መመዝገብ አልተቻለም። እባክዎ እንደገና ይሞክሩ።';
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
        exit;
    }

    /**
     * መረጃ የማስተካከያ (Update) Process
     */
    public function updateProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/car-type-list');
            exit;
        }

        $token = $_POST['csrf_token'] ?? $_POST['_token'] ?? '';
        if (!Csrf::verify($token)) {
            $_SESSION['error'] = 'Invalid CSRF token!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        $uuid        = filter_input(INPUT_POST, 'uuid', FILTER_SANITIZE_SPECIAL_CHARS);
        $cartype     = filter_input(INPUT_POST, 'cartype', FILTER_SANITIZE_SPECIAL_CHARS);
        $carcatagory = filter_input(INPUT_POST, 'carcatagory', FILTER_SANITIZE_SPECIAL_CHARS);
        $userId      = (int) ($_SESSION['user']['id'] ?? 0);

        $cartype     = trim($cartype ?? '');
        $carcatagory = trim($carcatagory ?? '');

        if ($uuid && !empty($cartype) && in_array($carcatagory, ['vehicle', 'machine'], true)) {
            $model   = new CarTypeListModel($this->db);
            $updated = $model->updateCarType([
                'uuid'        => $uuid,
                'cartype'     => $cartype,
                'carcatagory' => $carcatagory,
                'updated_by'  => $userId
            ]);

            if ($updated) {
                $_SESSION['success'] = "መረጃው በትክክል ተስተካክሏል!";
            } else {
                $_SESSION['error'] = "ምንም የተቀየረ መረጃ የለም ወይም ማስተካከል አልተቻለም።";
            }
        } else {
            $_SESSION['error'] = "እባክዎ የተላኩትን መረጃዎች በትክክል ያረጋግጡ።";
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
        exit;
    }

    /**
     * መረጃ የማጥፊያ (Delete) Process
     */
    public function deleteProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/car-type-list');
            exit;
        }

        $token = $_POST['csrf_token'] ?? $_POST['_token'] ?? '';
        if (!Csrf::verify($token)) {
            $_SESSION['error'] = 'Invalid CSRF token!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        $uuid   = filter_input(INPUT_POST, 'uuid', FILTER_SANITIZE_SPECIAL_CHARS);
        $userId = (int) ($_SESSION['user']['id'] ?? 0);

        if ($uuid && $userId) {
            $model   = new CarTypeListModel($this->db);
            $deleted = $model->deleteCarType($uuid, $userId);

            if ($deleted) {
                $_SESSION['success'] = "መረጃው በተሳካ ሁኔታ ተሰርዟል!";
            } else {
                $_SESSION['error'] = "መረጃውን መሰረዝ አልተቻለም።";
            }
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
        exit;
    }
}
<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;

class DashboardController extends BaseController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        AuthHelper::checkRole(['system_admin','admin','mgmt','officer']);
        // ዳታውን ወደ ቪው ማስተላለፍ
        $currentLang = $_SESSION['lang'] ?? 'am';
        $data = [
            'title'                => 'GVRS - ዳሽቦርድ',
            'currentLang' => $currentLang
        ];

        $this->render('dashboard', $data);
    }
}
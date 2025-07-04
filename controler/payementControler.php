<?php
include_once '../model/payement.php';
class payementControler
{

    public function index() {
        require_once '../view/payementView.php';
    }


    public function paydette($dette_id, $montant, $dette, $date)
    {
        if (
            !empty($dette_id) || !empty($montant) || !empty($date)
        ) {
            $dette_id = htmlspecialchars(strip_tags($dette_id));
            $montant = htmlspecialchars(strip_tags($montant));
            $date = htmlspecialchars(strip_tags($date));

            $montant = $dette-$montant;
            $payementModel = new PayementModel();
            $payementModel->payerDette($dette_id, $montant, $date);
        } else {
            echo "aucin donnes entrez";
        }
    }
}

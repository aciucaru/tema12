<?php

require_once __DIR__ . '/../repo/produs.repo.php';
require_once __DIR__ . '/../utils/logging.inc.php';

function valideazaSiAdaugaInCos()
{
    static $logger = new Logger(__FILE__);

    if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit']) and isset($_POST['produs_id']))
    {
        $idProdus = $_POST['produs_id'];

        if(isset($_SESSION['cos']))
            array_push($_SESSION['cos'], $_POST);
        else
        {
            $_SESSION['cos'] = array();
            array_push($_SESSION['cos'], $_POST);
        }
        $logger->log("adaugat produs in cos: id: $idProdus");
    }
}

function iaProduseleDinCos(): array
{
    static $logger = new Logger(__FILE__);

    $sirProduseCos = [];

    $repoProduse = new ProdusRepo();

    if(isset($_SESSION['cos']))
    {
        $idCurent = -1;
        $produsCurent = new Produs(-1, "", 0.0, 0);
        foreach($_SESSION['cos'] as $produs)
        {
            $idCurent = htmlspecialchars($produs['produs_id']);
            $produsCurent = $repoProduse->iaProdus($idCurent);
            array_push($sirProduseCos, $produsCurent);
        }
        $logger->log("iaProduseleDinCos");
    }

    return $sirProduseCos;
}

function calculeazaTotalCos(): float
{
    static $logger = new Logger(__FILE__);
    $logger->log("calculeazaTotalCos");

    $pretTotal = 0.0;

    $produseCos = iaProduseleDinCos();

    foreach($produseCos as $produs)
    {
        $pretTotal += $produs->pret;
    }

    return $pretTotal;
}

?>
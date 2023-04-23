<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/client.php';
require_once __DIR__ . '/../model/produs.php';

require_once __DIR__ . '/../utils/logging.inc.php';

enum TipInputClient: string
{
    case Nume = "Nume";
    case Username = "Username";
    case Email = "Email";
    case Parola = "Parola";
    case Comenzi = "Comenzi";
}

// clasa de baza folosita ca tip comun pentru diferitele tipuri de reguli de validare
class ReguliValidare
{
    // daca input-ul este obligatoriu sau nu, propr. comuna pt. toate input-urile
    public bool $campObligatoriu = false;
}

class ReguliValidareNume extends ReguliValidare
{
    public int $lungimeMinima = 3;
    public int $lungimeMaxima = 255;
}

class ReguliValidareUsername extends ReguliValidare
{
    public int $lungimeMinima = 3;
    public int $lungimeMaxima = 255;
}

class ReguliValidareParola extends ReguliValidare
{
    public int $lungimeMinima = 4; // prea mic, e doar de exemplu
    public int $lungimeMaxima = 255;
}

function valideazaInputNume(string $numeCamp, ReguliValidareNume $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);

    $logger->log("valideazaInputNume: inceput rularea, validare: $numeCamp");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if (isset($numeCamp) && !empty($numeCamp))
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($camp) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputNume: $numeCamp este prea scurt");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($camp) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputNume: $numeCamp este prea lung");
                        return false;
                    }

                    // 3. verificare daca string-ul contine caractere speciale (interzise pt. nume de persoane)
                    if(strpbrk($camp, ',;-()[]{}~!@#$%^&*?') === false)
                    {
                        $logger->log("valideazaInputUsername: $numeCamp contine caractere ilegale (,;-()[]{}~!@#$%^&*?)");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputNume: input $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputNume: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputUsername(string $numeCamp, ReguliValidareUsername $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputUsername: inceput rularea, validare: $numeCamp");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if (isset($camp) && !empty($camp))
            {
                if(isset($_POST[$camp]) && !empty($_POST[$camp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($camp) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputUsername: $numeCamp este prea scurt");
                        return false;
                    }

                    // 2. verificare a doua regula: lungimea maxima
                    if (strlen($camp) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputUsername: $numeCamp este prea lung");
                        return false;
                    }

                    // 3. verificare daca string-ul contine caractere speciale (interzise pt. username)
                    if(strpbrk($camp, ',;-()[]{}~!@#$%^&*?') === false)
                    {
                        $logger->log("valideazaInputUsername: $numeCamp contine caractere ilegale: ,;-()[]{}~!@#$%^&*?");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputUsername: $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputUsername: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputEmail(string $numeCamp, ReguliValidare $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputEmail: inceput rularea, validare: $numeCamp");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if (isset($camp) && !empty($camp))
            {
                if(isset($_POST[$camp]) && !empty($_POST[$camp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    if(filter_var($camp, FILTER_VALIDATE_EMAIL) === false)
                    {
                        $logger->log("valideazaInputEmail: $numeCamp nu este o adresa de email valida");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputEmail: $numeCamp nu exista in POST");
                    return false;
                }
            }
            else
            {
                $logger->log("valideazaInputEmail: $numeCamp: este obligatoriu");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputParola(string $numeCampParola, ReguliValidareParola $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputNume: inceput rularea, validare: $numeCampParola");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if (isset($numeCampParola) && !empty($numeCampParola))
            {
                if(isset($_POST[$numeCampParola]) && !empty($_POST[$numeCampParola]))
                {
                    // curatam inputurile de posibile atacuri XSS
                    $campParola = htmlspecialchars($_POST[$numeCampParola]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($campParola) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputNume: inceput rularea, validare: $numeCampParola");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($campParola) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputNume: $numeCampParola este prea lung");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputNume: input $numeCampParola nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputNume: $numeCampParola este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

// functie ce valideaza toate input-urile necesare paginii 'regsiter'
function valideazaInputuriRegister(): ?Client
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriRegister: inceput rularea");
    $inputurileSuntValide = true;

    // variabile folosite pt. a cosntrui un nou obiect de tip Client
    $id = -1;
    $nume = "";
    $username = "";
    $email = "";
    $hashParola = "";
    $comenzi = "";

    $inputuriDeValidat =
    [
        'nume' =>
        [
            'tip' => TipInputClient::Nume,
            'reguliValidare' => new ReguliValidareNume()
        ],

        'username' =>
        [
            'tip' => TipInputClient::Username,
            'reguliValidare' => new ReguliValidareUsername()
        ],

        'email' =>
        [
            'tip' => TipInputClient::Email,
            'reguliValidare' => new ReguliValidare()
        ],

        'parola' =>
        [
            'tip' => TipInputClient::Parola,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputClient::Nume:
                if(valideazaInputNume($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $nume = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputClient::Username:
                if(valideazaInputUsername($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $username = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputClient::Email:
                if(valideazaInputEmail($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $email = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputClient::Parola:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parola = htmlspecialchars($_POST[$numeInput]);
                    // $parola = $_POST[$numeInput];
                    $hashParola = password_hash($parola, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true)
    {
        $logger->log("valideazaInputuriRegister: validare cu succes");
        return new Client(
            $id,
            $nume,
            $username,
            $email,
            $hashParola,
            $comenzi
        );
    }

    else
    {
        $logger->log("valideazaInputuriRegister: validare esuata");
        return null;
    }
}

// functie ce valideaza toate input-urile necesare paginii 'register'
function valideazaInputuriLogin(): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriLogin: inceput rularea");

    $inputurileSuntValide = true;

    $inputuriDeValidat =
    [
        'username' =>
        [
            'tip' => TipInputClient::Username,
            'reguliValidare' => new ReguliValidareUsername()
        ],

        'parola' =>
        [
            'tip' => TipInputClient::Parola,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputClient::Username:
                if(valideazaInputUsername($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $username = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputClient::Parola:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parola = htmlspecialchars($_POST[$numeInput]);
                    $hashParola = password_hash($parola, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true)
    {
        $logger->log("valideazaInputuriLogin: validare cu succes");
        return true;
    }

    else
    {
        $logger->log("valideazaInputuriLogin: validare esuata");
        return false;
    }
}

?>
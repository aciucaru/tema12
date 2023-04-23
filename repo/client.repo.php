<?php

require_once __DIR__ . '/../model/client.php';
require_once __DIR__ . '/../utils/logging.inc.php';

class ClientRepo
{
    private $conn;

    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(__FILE__);
        
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'tema12';
        
        $this->conn = mysqli_connect($servername, $username, $password, $dbname);

        if(!$this->conn)
        {
            $this->logger->log("ClientRepo::construct: nu s-a putu realiza conexiunea la baza de date");
            die('Could not connect to database' . mysqli_connect_error());
        }
    }

    public function adaugaClient($client): int
    {
        $this->logger->log("ClientRepo::adaugaClient: inceput rularea");

        $clientId = -1;

        if(isset($client))
        {
            $query = "INSERT INTO clienti (nume, username, email, parola, comenzi)
                        VALUES ('$client->nume', '$client->username', '$client->email', '$client->hashParola', '$client->comenzi');";

            $result = mysqli_query($this->conn, $query);
            if($result !== false)
            {
                $clientId = mysqli_insert_id($this->conn);
                $this->logger->log("ClientRepo::adaugaClient: client adaugat cu succes, id = $clientId");
            }
            else
            {
                $this->logger->log("ClientRepo::adaugaClient: clientul nu s-a putut adauga");
            }
        }
        else
        {
            $this->logger->log("ClientRepo::adaugaClient: obiectul 'client' este nul");
        }

        return $clientId;
    }

    // functie ce verifica daca exista in baza de date un client cu username-ul si parola specificate,
    // iar daca exista returneaza un obiect de tip Client cu informatiile din baza de date
    public function verificaClient(string $username, string $parola): ?Client
    {
        $this->logger->log("ClientRepo::verificaClient: inceput rularea");

        if(isset($username) and isset($parola))
        {
            $query = "SELECT * from clienti WHERE username='$username';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            if($numarRezultate === 1)
            {
                $dateClient = mysqli_fetch_assoc($rezultat);

                if( password_verify($parola, $dateClient['parola']) )
                {
                    $client = new Client(
                        $dateClient['client_id'],
                        $dateClient['nume'],
                        $dateClient['username'],
                        $dateClient['email'],
                        $dateClient['parola'],
                        $dateClient['comenzi']
                    );

                    $this->logger->log("ClientRepo::verificaClient: username si parola validate");
                    return $client;
                }
                else
                {
                    $this->logger->log("ClientRepo::verificaClient: parola gresita");
                    return null;
                }

            }
            else
            {
                $this->logger->log("ClientRepo::verificaClient: username sau parola gresit");
                return null;
            }
        }
        else
        {
            $this->logger->log("ClientRepo::verificaClient: argumente nule sau goale");
            return null;
        }
    }

    public function iaTotiClientii(): array
    {
        $this->logger->log("ClientRepo::iaTotiClientii: inceput rularea");

        $sirClienti = [];

        $query = 'SELECT * from clienti';
        $result = mysqli_query($this->conn, $query);

        if($result !== false)
        {
            $clientCurent = new Client(-1, "", "", "", "", "");

            while($client = mysqli_fetch_array($result))
            {
                $clientCurent = new Client(
                                            $client['client_id'],
                                            $client['nume'],
                                            $client['username'],
                                            $client['email'],
                                            '', // parola nu se trimite mai departe catre browser
                                            $client['comenzi']
                                            );

                array_push($sirClienti, $clientCurent);
            }
            $this->logger->log("ClientRepo::iaTotiClientii: inceput rularea");
        }
        else
            $this->logger->log("ClientRepo::iaTotiClientii: clientii nu s-au putut lua");

        return $sirClienti;
    }

    public function contineUsername(string $nume): bool
    {
        $this->logger->log("ClientRepo::contineUsername: inceput rularea");

        if(isset($nume))
        {
            $query = "SELECT * from clienti WHERE username='$nume';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            if($numarRezultate === 0)
            {
                $this->logger->log("ClientRepo::contineUsername: nu s-a gasit username-ul");
                return false; // numele nu exista in baza de date
            }
            else
            {
                $this->logger->log("ClientRepo::contineUsername: username-ul a fost gasit");
                return true; // numele exista deja in baza de date
            }
        }
        else
        {
            $this->logger->log("ClientRepo::contineUsername: nu s-a gasit username-ul");
            return false; // numele nu exista in baza de date
        }
    }

    public function contineEmail(string $email): bool
    {
        $this->logger->log("ClientRepo::contineEmail: inceput rularea");

        if(isset($email))
        {
            $query = "SELECT * from clienti WHERE nume='$email';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            if($numarRezultate === 0)
            {
                $this->logger->log("ClientRepo::contineEmail: nu s-a gasit email-ul");
                return false; // email-ul nu exista in baza de date
            }
            else
            {
                $this->logger->log("ClientRepo::contineEmail: email-ul a fost gasit");
                return true; // email-ul exista deja in baza de date
            }
        }
        else
        {
            $this->logger->log("ClientRepo::contineEmail: email-ul a fost gasit");
            return false; // email-ul nu exista in baza de date
        }
    }

}
?>
<?php

class TileData
{
    private string $apiUrl;
    private array $items = [];
    private string $errorMessage = '';

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
        $this->fetchData(); // hned při vytvoření instance si fetchneme data z API
    }

    private function fetchData(): void
    {
        // fetchneme si data z API (API vrací JSON)
        $response = @file_get_contents($this->apiUrl); // @ potlačí warningy (kdyby třeba API bylo nedostupné)
        $data = json_decode($response, true);

        // jen kontrolujeme, zda jsme dostali 'success' při requestu – pokud ne, throwneme chybu
        if ($data && ($data['status'] ?? '') === 'success') {
            $this->items = $data['data']; // datovou část (s SQL odpovědí) uložíme do $items, později ji můžeme iterovat
        } else {
            // přiřadíme prázdný array, aby PHP neřvalo warningy ohledně undefined var
            // (od PHP 7 je to pain, jednodušší je to potlačit přes error_reporting, ale debugging je pak čistší bez warnings)
            $this->items = [];
            // pokud jsme nedostali success (ale error), tak vyhodíme error message o chybě serveru
            // (což fakticky je pravda, protože to je chyba API části :D)
            $this->errorMessage = $data['message'] ?? 'Internal Server Error.';
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}

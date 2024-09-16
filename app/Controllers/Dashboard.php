<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        // Diretório onde os ficheiros estão localizados localmente
        $directory = FCPATH . 'uploads'; // FCPATH é uma constante que representa o caminho da raiz pública do projeto.
        $files = []; // Inicializa um array vazio para armazenar os nomes dos ficheiros.

        // Verificação da existência da pasta
        if (is_dir($directory)) {
            // `scandir` obtém uma lista de todos os ficheiros e diretórios no diretório especificado.
            // `array_diff` remove os diretórios '.' e '..' da lista.
            $files = array_diff(scandir($directory), ['.', '..']);
        }

        
        $data = [
            'files' => $files, 
        ];

        
        echo view('templates/header', $data); 
        echo view('dashboard', $data);
        echo view('templates/footer'); 
    }

    //--------------------------------------------------------------------
}
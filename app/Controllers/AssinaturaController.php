<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use SoapClient;
use SoapFault;

class AssinaturaController extends Controller
{
    // Método que carrega a página inicial com o formulário para inserir telemóvel e PIN
    public function index()
    {
        return view('assinatura_form');  // Renderiza uma view com o formulário de assinatura (ex: telemóvel e PIN)
    }

    // Método responsável por processar a assinatura digital do documento
    public function processarAssinatura()
    {
        try {
            // Caminho para o ficheiro WSDL necessário para a conexão SOAP
            $wsdl = FCPATH . 'app/third_party/wsdl/servico_assinatura.wsdl'; // 'FCPATH' representa o caminho raiz do projeto
    
            // Inicializa o SoapClient, que será usado para a comunicação com o serviço SOAP
            $client = new SoapClient($wsdl);
    
            // Obtém o nome do ficheiro enviado através do formulário (usando método POST)
            $file = $this->request->getPost('file');
            
            // Codifica o conteúdo do ficheiro em base64 para ser enviado através do serviço SOAP
            $documento = base64_encode(file_get_contents(FCPATH . 'uploads/' . $file));  // Caminho para o ficheiro a assinar
    
            // Parâmetros necessários para a chamada SOAP, incluindo o número de telemóvel e o PIN
            // Nota: 'numeroTelemovel' e 'pin' podem ser dinamicamente capturados de um formulário ou sessão
            $params = [
                'numeroTelemovel' => '912345678',  // Exemplo de número de telemóvel (pode ser capturado do formulário)
                'documento' => $documento,  // Documento codificado a ser assinado
                'pin' => '1234',  // Exemplo de PIN (também pode ser capturado do formulário)
            ];
    
            // Chamada ao método 'AssinarDocumento' do serviço SOAP, passando os parâmetros
            $response = $client->AssinarDocumento($params);
    
            // Verifica se a resposta indica sucesso na assinatura
            if ($response->status == 'SUCCESS') {
                // Descodifica o documento assinado recebido e guarda-o no servidor
                $signedDocument = base64_decode($response->signedDocument);
                file_put_contents(FCPATH . 'uploads/assinado_' . $file, $signedDocument);  // Guarda o ficheiro assinado
                echo "Documento assinado com sucesso!";
            } else {
                // Se a assinatura falhar, exibe a mensagem de erro retornada pelo serviço
                echo "Erro na assinatura: " . $response->errorMessage;
            }
    
        } catch (SoapFault $e) {
            // Captura exceções SOAP e exibe a mensagem de erro
            echo "Erro SOAP: " . $e->getMessage();
        }
    }
}
<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Users extends Controller
{
    // Método para exibir a página de login e processar a autenticação do usuário
    public function index()
    {
        $data = []; 
        helper(['form']); 

        // Verifica se a requisição é do tipo POST (enviado pelo formulário de login)
        if ($this->request->getMethod() == 'post') {
            // Regras de validação para email e password
            $rules = [
                'email' => 'required|min_length[6]|max_length[50]|valid_email',
                'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
            ];

            // Mensagens de erro personalizadas
            $errors = [
                'password' => [
                    'validateUser' => 'Email or Password não correspondem'
                ]
            ];

            // Valida os dados com as regras e mensagens de erro definidas
            if (!$this->validate($rules, $errors)) {
                $data['validation'] = $this->validator; // Passa os erros de validação para a view
                log_message('error', 'Validation failed: ' . print_r($this->validator->getErrors(), true));
            } else {
                // Se a validação for bem-sucedida, procura o utilizador pelo email
                $model = new UserModel();
                $user = $model->where('email', $this->request->getVar('email'))->first();

                // Define a sessão do utilizador e redireciona para o dashboard
                $this->setUserSession($user);
                return redirect()->to('/dashboard');
            }
        }

        // Carrega as views com os dados fornecidos
        echo view('templates/header', $data);
        echo view('login', $data);
        echo view('templates/footer');
    }

    // Método privado para definir a sessão do utilizador
    private function setUserSession($user)
    {
        // Dados da sessão do utilizador
        $data = [
            'id' => $user['id'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'isLoggedIn' => true,
        ];

        // Define os dados na sessão
        session()->set($data);
        return true;
    }

    // Método para exibir a página de registo e processar o registo do utilizador
    public function register()
    {
        $data = []; 
        helper(['form']); 

        
        if ($this->request->getMethod() == 'POST') {
            // Regras de validação para os campos de registo
            $rules = [
                'firstname' => 'required|min_length[3]|max_length[20]',
                'lastname' => 'required|min_length[3]|max_length[20]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]|max_length[255]',
                'password_confirm' => 'matches[password]',
            ];

            // Valida os dados com as regras definidas
            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator; // Passa os erros de validação para a view
            } else {
                // Se a validação for bem-sucedida, guarda os dados do novo utilizador
                $model = new UserModel();
                $newData = [
                    'firstname' => $this->request->getVar('firstname'),
                    'lastname' => $this->request->getVar('lastname'),
                    'email' => $this->request->getVar('email'),
                    'password' => $this->request->getVar('password'),
                ];
                $model->save($newData); // Guarda o utilizador na base de dados

                // Define uma mensagem de sucesso e redireciona para a página de login
                session()->setFlashdata('success', 'Registo efetuado com sucesso');
                return redirect()->to('/Users');
            }
        }

        // Carrega as views com os dados fornecidos
        echo view('templates/header', $data);
        echo view('register', $data);
        echo view('templates/footer');
    }

    // Método para exibir e atualizar o perfil do utilizador
    public function profile()
    {
        $data = []; 
        helper(['form']); 
        $model = new UserModel();

        
        if ($this->request->getMethod() == 'post') {
            // Regras de validação para os campos de perfil
            $rules = [
                'firstname' => 'required|min_length[3]|max_length[20]',
                'lastname' => 'required|min_length[3]|max_length[20]',
            ];

            // Se a senha for fornecida, adiciona regras de validação para password e confirmação
            if ($this->request->getPost('password') != '') {
                $rules['password'] = 'required|min_length[8]|max_length[255]';
                $rules['password_confirm'] = 'matches[password]';
            }

            // Valida os dados com as regras definidas
            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator; // Passa os erros de validação para a view
            } else {
                // Se a validação for bem-sucedida, atualiza os dados do perfil
                $newData = [
                    'id' => session()->get('id'),
                    'firstname' => $this->request->getPost('firstname'),
                    'lastname' => $this->request->getPost('lastname'),
                ];
                if ($this->request->getPost('password') != '') {
                    $newData['password'] = $this->request->getPost('password');
                }
                $model->save($newData); // Guarda as alterações na base de dados

                // Define uma mensagem de sucesso e redireciona para a página de perfil
                session()->setFlashdata('success', 'Perfil atualizado com sucesso');
                return redirect()->to('/profile');
            }
        }

        // Procura os dados do utilizador atual e passa para a view
        $data['user'] = $model->where('id', session()->get('id'))->first();
        echo view('templates/header', $data);
        echo view('profile');
        echo view('templates/footer');
    }

    // Método para realizar logout e destruir a sessão
    public function logout()
    {
        session()->destroy(); // Destrói a sessão do utilizador
        return redirect()->to('/'); // Redireciona para a página inicial
    }

    //--------------------------------------------------------------------
}



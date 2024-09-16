<?php

namespace App\Validation;

use App\Models\UserModel;

class UserRules
{
    // Método para validar o utilizador com base no email e password fornecidos
    public function validateUser(string $str, string $fields, array $data)
    {
        // Cria uma instância do modelo UserModel para aceder a base de dados
        $model = new UserModel();
        
        // Procura o utilizador com o email fornecido
        $user = $model->where('email', $data['email'])->first();

        // Verifica se o utilizador não foi encontrado
        if (!$user) {
            return false; // Retorna falso se o utilizador não existir
        }

        // Verifica se a senha fornecida corresponde à senha armazenada (com hash)
        return password_verify($data['password'], $user['password']);
    }
}

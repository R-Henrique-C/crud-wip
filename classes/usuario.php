<?php
class Usuario {
    private $db;

    // Chama o método de conexão da classe 'DataBase'
    //e a insere no atributo 'db'
    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function listarUsuarios() {
        $usuarios = array(); // Cria a lista dos usuários no atributo 'usuarios'

        // Prepare a consulta SQL para listar todos os usuários
        $sql = "SELECT * FROM usuarios";

        // Retorna o resultado da query no atributo 'result'
        $result = $this->db->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            $result->close();
        }

        return $usuarios;
    }

    public function adicionarUsuario($nome, $email, $senha, $caminhoImagem) {
        // Verifique se o email já está em uso
        if ($this->verificarEmailExistente($email)) {
            return false; // Email já está em uso, não é possível adicionar o usuário.
        }

        // Hash da senha para maior segurança
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Inserir um novo usuário na tabela
        $sql = "INSERT INTO usuarios (usu_nome, usu_email, usu_senha, usu_foto_perfil) VALUES (?, ?, ?, ?)";
        // Usar '?' evita que alguem faça um SQLInject no sistema

        // A variável 'stmt' é uma variavel convencionada por programadores para enviar comandos SQL
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $caminhoImagem);
        // Usar o 'ssss' porque todos os atributos tem a tipagem de VarChar (String) 

        if ($stmt->execute()) {
            return true; // Usuário adicionado com sucesso.
        } else {
            return false; // Erro ao adicionar o usuário.
        }
    }

    public function verificarEmailExistente($email) {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE usu_email = ?";
        // Conta a quantidade de ocorrências de emails com o resultado de '?'
        
        $stmt = $this->db->prepare($sql); // Chama o banco e executa o SQL
        $stmt->bind_param("s", $email); // Envia a tipagem dos atributos de SQL
        $stmt->execute(); // Executa o comando
        $result = $stmt->get_result(); // Pega o resultado de 'stmt'
        $row = $result->fetch_assoc(); // Guarda o resultado em uma linha 'row'
        
        return $row['total'] > 0; // Retorna apenas se o email estiver disponível (0)
    }
}
?>
<?php
class Index
{
    public static function validaLogin($sessao, $tempo)
    {
        $tempo_maximo = 30 * 60; // 30 minutos

        if ($sessao && isset($tempo)) {
            if (time() - $tempo > $tempo_maximo) {
                return false;
            } else {
                $_SESSION['login_time'] = time();
                return true;
            }
        }
        return false;
    }

    public static function logOut()
    {
        // Destrói a sessão
        session_unset();
        session_destroy();
        
        // Redireciona para a página de login
        header('Location: ./');
        exit;
    }
    
    public static function login($data)
    {
        if (!isset($data['login']) || !isset($data['senha'])) {
            $_SESSION['msg'] = "<p id='aviso'>Preencha todos os campos</p>";
            return false;
        }

        // Limpeza dos valores coletados
        $login = trim(htmlspecialchars($data['login']));
        $senha = $data['senha']; // Não precisa de htmlspecialchars para senha

        if (empty($login) || empty($senha)) {
            $_SESSION['msg'] = "<p id='aviso'>Preencha todos os campos</p>";
            return false;
        }

        try {
            $db = DB::connect();
            
            // Usando prepared statements para evitar SQL Injection
            $stmt = $db->prepare("SELECT id, nm_usuario, login, senha FROM usuarios WHERE login = ? LIMIT 1");
            $stmt->execute([$login]);
            $usuario = $stmt->fetchObject();
            
            if ($usuario && password_verify($senha, $usuario->senha)) {
                // Regenera o ID da sessão para segurança
                session_regenerate_id(true);
                
                $_SESSION['data_user'] = [
                    'id' => $usuario->id,
                    'nm_usuario' => $usuario->nm_usuario,
                    'login' => $usuario->login
                ];
                $_SESSION['login_time'] = time();
                return true;
            } else {
                $_SESSION['msg'] = "<p id='aviso'>Login ou senha incorreto</p>";
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['msg'] = "<p id='aviso'>Erro no sistema. Tente novamente.</p>";
            return false;
        }
    }
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
ob_start();

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

    public static function login($data)
    {
        if (!isset($data['login']) || !isset($data['senha'])) {
            $_SESSION['msg'] = "<p id='aviso'>Preencha todos os campos</p>";
            return false;
        }

        $login = trim(htmlspecialchars($data['login']));
        $senha = $data['senha'];

        if (empty($login) || empty($senha)) {
            $_SESSION['msg'] = "<p id='aviso'>Preencha todos os campos</p>";
            return false;
        }

        try {
            $db = DB::connect();

            // Consulta com JOIN para carregar dados do perfil
            $stmt = $db->prepare("
                SELECT 
                    u.id, 
                    u.nm_usuario, 
                    u.login, 
                    u.senha,
                    u.perfil_id,
                    p.nome AS perfil_nome,
                    p.especialidade
                FROM usuarios u
                LEFT JOIN perfis p ON u.perfil_id = p.id
                WHERE u.login = ? AND u.ativo = 1
                LIMIT 1
            ");
            $stmt->execute([$login]);
            $usuario = $stmt->fetch(PDO::FETCH_OBJ);

            if ($usuario && password_verify($senha, $usuario->senha)) {
                session_regenerate_id(true);

                $_SESSION['data_user'] = [
                    'id' => $usuario->id,
                    'nm_usuario' => $usuario->nm_usuario,
                    'login' => $usuario->login,
                    'perfil_id' => $usuario->perfil_id,
                    'perfil_nome' => $usuario->perfil_nome,
                    'especialidade' => $usuario->especialidade
                ];
                $_SESSION['login_time'] = time();
                return true;
            } else {
                $_SESSION['msg'] = "Login ou senha incorreto";
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['msg'] = "Erro no sistema. Tente novamente.";
            return false;
        }
    }

    public static function logOut()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }

        header("Location: ../");
        exit;
    }
}
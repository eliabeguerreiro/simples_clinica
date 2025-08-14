<?php



// if(Usuarios::verificar($conn, $headers)){  }  
class Index
{


    public static function validaLogin($sessao)
    {
        $tempo_maximo = 30 * 60;


        if($sessao){
            $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
            return false;

        }elseif(time() - $sessao['login_time'] > $tempo_maximo){
            $_SESSION['msg'] = '<p>Sua sessão expirou. Faça login novamente.</p>';
            return false;
   
        }else{
            $_SESSION['login_time'] = time();
            return true;
        }
        
    }


    public static function logOut()
    {
    
        // Destrói a sessão
        session_unset();
        session_destroy();
    
        // Define uma mensagem de sucesso
        $_SESSION['msg'] = 'Usuário deslogado com sucesso.';
    
        // Redireciona para a página de login
        header('Location: ../');
        exit;
    }
    
    public static function login($data)
    {

        if(isset($data)){

            //limpeza dos valores coletados
            $login = addslashes(htmlspecialchars($data['login'])) ?? '';
            $senha = addslashes(htmlspecialchars($data['senha'])) ?? '';

            $db = DB::connect();
            $rs = $db->prepare("SELECT login, senha FROM usuarios WHERE login = '{$login}' LIMIT 1");
            $rs->execute();
            $obj = $rs->fetchObject();
            $rows = $rs->rowCount();


            if ($rows > 0) {
                $passDB        = $obj->senha;
                $validPassword = password_verify($senha, $passDB) ? true : false;
            }else{
                $validPassword = false;
            }

            if($validPassword){

                $rs = $db->prepare("SELECT id, nm_usuario FROM usuarios WHERE login = '{$login}' LIMIT 1");
                $rs->execute();
                $obj = $rs->fetchObject();

                $obj = (array)$obj;
                $_SESSION['data_user'] = $obj;
                $_SESSION['login_time'] = time();
                return true;
                
            }else{$_SESSION['msg'] =  "<p id='aviso'>login ou senha incorreto</p>";}

        }else{
            $_SESSION['msg'] =  "faltam informações";
            exit;
        }
    }
   
}
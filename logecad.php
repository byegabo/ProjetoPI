<?php
include('conexao.php');

$modo = isset($_POST['modo']) ? $_POST['modo'] : 'login';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($modo == 'cadastro') {
        if (isset($_POST['nick'], $_POST['email'], $_POST['senha'], $_POST['confirmar_senha'], $_POST['data_nascimento'], $_FILES['foto_perfil'])) {
            $nick = $_POST['nick'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $confirmar_senha = $_POST['confirmar_senha'];
            $data_nascimento = $_POST['data_nascimento'];

            if ($_FILES["foto_perfil"]["error"] == 0) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["foto_perfil"]["tmp_name"]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    $erro = "O arquivo não é uma imagem.";
                    $uploadOk = 0;
                }

                if (file_exists($target_file)) {
                    $erro = "Desculpe, esse arquivo já existe.";
                    $uploadOk = 0;
                }

                if ($_FILES["foto_perfil"]["size"] > 500000) { 
                    $erro = "Desculpe, o arquivo é muito grande.";
                    $uploadOk = 0;
                }

                if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $erro = "Desculpe, apenas arquivos JPG, JPEG, PNG & GIF são permitidos.";
                    $uploadOk = 0;
                }

                if ($uploadOk == 1) {
                    if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
                        $foto_perfil = $target_file;
                    } else {
                        $erro = "Desculpe, houve um erro ao fazer o upload do seu arquivo.";
                    }
                }
            } else {
                $erro = "Erro no upload da foto de perfil.";
            }

            if ($senha !== $confirmar_senha) {
                $erro = "As senhas não coincidem!";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "SELECT * FROM usuarios WHERE email = '$email'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $erro = "E-mail já cadastrado!";
                } else {
                    $sql = "INSERT INTO usuarios (nick, email, senha, data_nascimento, foto_perfil) VALUES ('$nick', '$email', '$senha_hash', '$data_nascimento', '$foto_perfil')";
                    if ($conn->query($sql) === TRUE) {
                        setcookie('modo', 'login', time() + (86400 * 30), "/");
                        header("Location: logecad.php");
                        exit();
                    } else {
                        $erro = "Erro ao cadastrar o usuário: " . $conn->error;
                    }
                }
            }
        } else {
            $erro = "Preencha todos os campos!";
        }
    } else if ($modo == 'login') {
        if (isset($_POST['email'], $_POST['senha'])) {
            if (strlen($_POST['email']) == 0) {
                $erro = "Preencha seu e-mail";
            } else if (strlen($_POST['senha']) == 0) {
                $erro = "Preencha sua senha";
            } else {
                $email = $conn->real_escape_string($_POST['email']);
                $senha = $_POST['senha'];

                $sql_code = "SELECT * FROM usuarios WHERE email = '$email'";
                $sql_query = $conn->query($sql_code) or die("Falha na execução do código SQL: " . $conn->error);

                if ($sql_query->num_rows == 1) {
                    $usuario = $sql_query->fetch_assoc();

                    if (password_verify($senha, $usuario['senha'])) {
                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        $_SESSION['id'] = $usuario['id'];
                        $_SESSION['nome'] = $usuario['nick'];
                        $_SESSION['foto_perfil'] = $usuario['foto_perfil'];

                        setcookie('usuario_email', $email, time() + (86400 * 30), "/"); 
                        setcookie('usuario_nome', $usuario['nick'], time() + (86400 * 30), "/");

                        header("Location: pagina_inicial.php");
                        exit();
                    } else {
                        $erro = "Falha ao logar! E-mail ou senha incorretos";
                    }
                } else {
                    $erro = "Falha ao logar! E-mail ou senha incorretos";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Cadastro</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="tela_login">
        <?php if ($modo == 'cadastro'): ?>
            <h1>Cadastro</h1>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="nick" placeholder="Seu Nick" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <input type="password" name="confirmar_senha" placeholder="Confirmar Senha" required>
                <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>
                <input type="file" name="foto_perfil" accept="image/*" required>
                <button type="submit">Cadastrar</button>
                <input type="hidden" name="modo" value="cadastro">
                <?php if(isset($erro)): ?>
                    <div class="erro"><?php echo $erro; ?></div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <h1>Login</h1>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
                <input type="hidden" name="modo" value="login">
                <p class="erro"><?php echo $erro; ?></p>
            </form>
        <?php endif; ?>

        <div class="alternar">
            <?php if ($modo == 'cadastro'): ?>
                <p>Já tem uma conta? <a style="text-decoration:none; color:blueviolet" href="javascript:void(0)" onclick="document.forms[0].modo.value='login'; document.forms[0].submit();">Faça login</a></p>
            <?php else: ?>
                <p>Não tem uma conta? <a style="text-decoration:none; color:blueviolet" href="javascript:void(0)" onclick="document.forms[0].modo.value='cadastro'; document.forms[0].submit();">Cadastre-se</a></p>
            <?php endif; ?>
            <p><a style="text-decoration:none; color:blueviolet" href="redsenha.php">Esqueci minha senha</a></p>
        </div>
    </div>
</body>
</html>
